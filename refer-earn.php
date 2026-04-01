<?php
session_start();
require_once 'common/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();
$user_id = (int) $_SESSION['user_id'];

// Base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$domain   = $_SERVER['HTTP_HOST'];
$base_url = $protocol . $domain;

// Default messages
$message = '';
$message_type = '';

// ----------------------------------------
// Get user details
// ----------------------------------------
$user_query = "SELECT id, username, full_name, email, profile_image, referral_code FROM users WHERE id = ? LIMIT 1";
$stmt = $db->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();

if ($user_result->num_rows === 0) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$user = $user_result->fetch_assoc();

$display_name   = !empty($user['full_name']) ? $user['full_name'] : $user['username'];
$profile_image  = !empty($user['profile_image'])
    ? $user['profile_image']
    : 'https://ui-avatars.com/api/?name=' . urlencode($display_name) . '&background=003B95&color=fff';

$referral_code = trim((string)($user['referral_code'] ?? ''));

// ----------------------------------------
// Generate referral code if not exists
// ----------------------------------------
if ($referral_code === '') {
    $clean_username = preg_replace('/[^a-zA-Z0-9]/', '', (string)$user['username']);
    $base_code = strtoupper(substr($clean_username, 0, 5));

    if ($base_code === '') {
        $base_code = 'USER';
    }

    do {
        $random_num = rand(100, 999);
        $new_code = $base_code . $random_num;

        $check_query = "SELECT id FROM users WHERE referral_code = ? LIMIT 1";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bind_param("s", $new_code);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
    } while ($check_result->num_rows > 0);

    $referral_code = $new_code;

    $update_query = "UPDATE users SET referral_code = ? WHERE id = ?";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bind_param("si", $referral_code, $user_id);
    $update_stmt->execute();
}

// Referral link
$referral_link = $base_url . "/register.php?ref=" . urlencode($referral_code);

// ----------------------------------------
// Get referral statistics
// ----------------------------------------
$stats_query = "
    SELECT 
        (SELECT COUNT(*) FROM referrals WHERE referrer_id = ?) AS total_referrals,
        (SELECT COUNT(*) FROM referrals WHERE referrer_id = ? AND status = 'active') AS completed_referrals,
        (SELECT COALESCE(SUM(reward_amount), 0) FROM referral_rewards WHERE user_id = ? AND status = 'credited') AS total_earned,
        (SELECT COALESCE(SUM(reward_amount), 0) FROM referral_rewards WHERE user_id = ? AND status = 'pending') AS pending_amount
";
$stmt = $db->prepare($stats_query);
$stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$total_referrals     = (int)($stats['total_referrals'] ?? 0);
$completed_referrals = (int)($stats['completed_referrals'] ?? 0);
$total_earned        = (float)($stats['total_earned'] ?? 0);
$pending_amount      = (float)($stats['pending_amount'] ?? 0);

// ----------------------------------------
// Get referral history
// ----------------------------------------
$history_query = "
    SELECT 
        r.*,
        u.username AS referred_user,
        u.full_name AS referred_name,
        u.profile_image AS referred_image,
        u.created_at AS joined_date
    FROM referrals r
    INNER JOIN users u ON r.referred_id = u.id
    WHERE r.referrer_id = ?
    ORDER BY r.created_at DESC
    LIMIT 10
";
$stmt = $db->prepare($history_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$history_result = $stmt->get_result();

$referral_history = [];
while ($row = $history_result->fetch_assoc()) {
    $referral_history[] = $row;
}

// ----------------------------------------
// Handle referral email form submission
// ----------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['refer_email'])) {
    $refer_email = trim($_POST['refer_email']);

    if ($refer_email === '') {
        $message = 'Please enter email address';
        $message_type = 'error';
    } elseif (!filter_var($refer_email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address';
        $message_type = 'error';
    } elseif (!empty($user['email']) && strtolower($refer_email) === strtolower($user['email'])) {
        $message = 'You cannot refer yourself';
        $message_type = 'error';
    } else {
        // Check if email already registered
        $check_query = "SELECT id FROM users WHERE email = ? LIMIT 1";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bind_param("s", $refer_email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $message = 'This email is already registered';
            $message_type = 'error';
        } else {
            // Optional: prevent duplicate pending/sent referral email spam
            $duplicate_query = "SELECT id FROM referral_emails WHERE referrer_id = ? AND email = ? LIMIT 1";
            $duplicate_stmt = $db->prepare($duplicate_query);
            $duplicate_stmt->bind_param("is", $user_id, $refer_email);
            $duplicate_stmt->execute();
            $duplicate_result = $duplicate_stmt->get_result();

            if ($duplicate_result->num_rows > 0) {
                $message = 'Referral already sent to this email';
                $message_type = 'error';
            } else {
                $subject = "You're invited to StayEase!";

                $email_message = "Hello,\n\n"
                    . $display_name . " invited you to join StayEase.\n\n"
                    . "Use the link below to register and get ₹100 OFF on your first booking.\n\n"
                    . "Register here:\n"
                    . $referral_link . "\n\n"
                    . "Or use referral code: " . $referral_code . "\n\n"
                    . "Enjoy your stay!\n\n"
                    . "StayEase Team";

                $headers  = "From: StayEase <noreply@pgmitra.in>\r\n";
                $headers .= "Reply-To: noreply@pgmitra.in\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

                $mail_status = @mail($refer_email, $subject, $email_message, $headers);

                if ($mail_status) {
                    $insert_query = "INSERT INTO referral_emails (referrer_id, email, referral_code, status) VALUES (?, ?, ?, 'sent')";
                    $insert_stmt = $db->prepare($insert_query);
                    $insert_stmt->bind_param("iss", $user_id, $refer_email, $referral_code);
                    $insert_stmt->execute();

                    $message = "Referral sent to " . htmlspecialchars($refer_email) . "! They'll get ₹100 off on first booking.";
                    $message_type = 'success';
                } else {
                    $insert_query = "INSERT INTO referral_emails (referrer_id, email, referral_code, status) VALUES (?, ?, ?, 'failed')";
                    $insert_stmt = $db->prepare($insert_query);
                    $insert_stmt->bind_param("iss", $user_id, $refer_email, $referral_code);
                    $insert_stmt->execute();

                    $message = "Email sending failed. Please try again.";
                    $message_type = 'error';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Refer & Earn - PG Mitra</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: #F7F9FC;
    color: #1E2A3A;
}

/* Desktop Header */
.desktop-header {
    background: #003B95;
    padding: 0 80px;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.logo {
    font-size: 28px;
    font-weight: 800;
    color: white;
    letter-spacing: -0.5px;
}

.logo span {
    color: #FFB700;
}

.desktop-nav {
    display: flex;
    gap: 32px;
    align-items: center;
}

.desktop-nav a {
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 0;
    transition: all 0.2s;
    border-bottom: 2px solid transparent;
}

.desktop-nav a:hover, .desktop-nav a.active {
    border-bottom-color: #FFB700;
}

.desktop-nav a i {
    font-size: 18px;
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 16px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.user-avatar:hover {
    background: rgba(255,255,255,0.3);
}

.user-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.user-avatar i {
    font-size: 20px;
    color: white;
}

/* Mobile Header */
.mobile-header {
    display: none;
    background: #003B95;
    padding: 12px 16px;
    position: sticky;
    top: 0;
    z-index: 1000;
    align-items: center;
    justify-content: space-between;
}

.mobile-header .logo {
    font-size: 20px;
    color: white;
}

/* Main Container */
.main-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 80px;
}

/* Mobile Container */
.mobile-container {
    display: none;
    padding: 0 0 80px 0;
    background: #F7F9FC;
    min-height: 100vh;
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #003B95 0%, #0066CC 100%);
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.hero-section::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -20%;
    width: 250px;
    height: 250px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.hero-content {
    position: relative;
    z-index: 10;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.hero-text h1 {
    font-size: 28px;
    font-weight: 800;
    color: white;
    margin-bottom: 8px;
}

.hero-text p {
    font-size: 14px;
    color: rgba(255,255,255,0.9);
}

.hero-icon {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-icon i {
    font-size: 32px;
    color: #FFB700;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    transition: all 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: #EFF6FF;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.stat-icon i {
    font-size: 24px;
    color: #003B95;
}

.stat-value {
    font-size: 28px;
    font-weight: 800;
    color: #1E2A3A;
}

.stat-label {
    font-size: 13px;
    color: #6B7280;
    margin-top: 6px;
}

/* Referral Card */
.referral-card {
    background: linear-gradient(135deg, #003B95 0%, #4F46E5 100%);
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 32px;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,59,149,0.2);
}

.referral-card::before {
    content: '';
    position: absolute;
    top: -20px;
    right: -20px;
    width: 150px;
    height: 150px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.referral-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}

.referral-header-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.referral-header-icon i {
    font-size: 24px;
}

.referral-header-text h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 4px;
}

.referral-header-text p {
    font-size: 13px;
    opacity: 0.9;
}

.referral-code-box {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    padding: 16px 20px;
    margin: 20px 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid rgba(255,255,255,0.2);
}

.referral-code-label {
    font-size: 11px;
    opacity: 0.8;
    margin-bottom: 4px;
}

.referral-code-value {
    font-size: 28px;
    font-weight: 800;
    letter-spacing: 2px;
    font-family: monospace;
}

.copy-btn {
    background: white;
    color: #003B95;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.copy-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.referral-note {
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    padding: 12px;
    margin: 16px 0;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Share Buttons */
.share-buttons {
    display: flex;
    gap: 12px;
    margin-top: 20px;
}

.share-btn {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.share-btn:hover {
    transform: translateY(-2px);
}

.share-btn.whatsapp { background: #25D366; color: white; }
.share-btn.facebook { background: #1877F2; color: white; }
.share-btn.twitter { background: #1DA1F2; color: white; }

/* Invite Form */
.invite-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.invite-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 16px;
}

.input-group {
    display: flex;
    gap: 12px;
}

.input-group input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    transition: all 0.2s;
}

.input-group input:focus {
    outline: none;
    border-color: #003B95;
    box-shadow: 0 0 0 3px rgba(0,59,149,0.1);
}

.input-group button {
    padding: 12px 24px;
    background: #003B95;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.input-group button:hover {
    background: #002E7A;
}

/* Steps Section */
.steps-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.steps-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
}

.step-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #F9FAFB;
    border-radius: 12px;
    margin-bottom: 12px;
}

.step-number {
    width: 48px;
    height: 48px;
    background: #EFF6FF;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 20px;
    color: #003B95;
}

.step-content h4 {
    font-weight: 700;
    margin-bottom: 4px;
}

.step-content p {
    font-size: 13px;
    color: #6B7280;
}

/* History Section */
.history-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.history-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
}

.history-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #F9FAFB;
    border-radius: 12px;
    margin-bottom: 8px;
}

.history-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #EFF6FF;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    font-weight: 600;
    color: #003B95;
}

.history-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.history-info {
    flex: 1;
}

.history-name {
    font-weight: 600;
    margin-bottom: 2px;
}

.history-date {
    font-size: 11px;
    color: #9CA3AF;
}

.history-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.status-completed {
    background: #DCFCE7;
    color: #166534;
}

.status-pending {
    background: #FEF3C7;
    color: #92400E;
}

/* Terms Section */
.terms-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

details summary {
    font-weight: 600;
    cursor: pointer;
    color: #1E2A3A;
}

details summary i {
    color: #003B95;
    margin-right: 8px;
}

.terms-content {
    margin-top: 16px;
    font-size: 13px;
    color: #6B7280;
    line-height: 1.6;
}

.terms-content p {
    margin-bottom: 8px;
}

/* Alert */
.alert {
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-success {
    background: #DCFCE7;
    border: 1px solid #10B981;
    color: #166534;
}

.alert-error {
    background: #FEE2E2;
    border: 1px solid #EF4444;
    color: #991B1B;
}

.alert i {
    font-size: 18px;
}

/* Toast */
.toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: #1F2937;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    z-index: 3000;
    animation: slideUp 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.toast.success { background: #10B981; }
.toast.error { background: #EF4444; }

/* Mobile Bottom Nav */
.mobile-bottom-nav {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    border-top: 1px solid #E5E7EB;
    padding: 10px 20px;
    z-index: 1000;
}

/* Animations */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateX(-50%) translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .desktop-header {
        display: none;
    }
    
    .mobile-header {
        display: flex;
    }
    
    .main-container {
        display: none;
    }
    
    .mobile-container {
        display: block;
    }
    
    .mobile-bottom-nav {
        display: block;
    }
    
    .hero-section {
        margin: 16px;
        padding: 20px;
    }
    
    .hero-content {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }
    
    .hero-text h1 {
        font-size: 22px;
    }
    
    .hero-icon {
        width: 60px;
        height: 60px;
    }
    
    .hero-icon i {
        font-size: 28px;
    }
    
    .stats-grid {
        gap: 12px;
        padding: 0 16px;
    }
    
    .stat-card {
        padding: 16px;
    }
    
    .stat-value {
        font-size: 22px;
    }
    
    .referral-card {
        margin: 0 16px 20px 16px;
        padding: 20px;
    }
    
    .referral-code-value {
        font-size: 22px;
    }
    
    .share-buttons {
        flex-wrap: wrap;
    }
    
    .share-btn {
        flex: 1;
        min-width: 100px;
    }
    
    .invite-section,
    .steps-section,
    .history-section,
    .terms-section {
        margin: 0 16px 20px 16px;
        padding: 20px;
    }
    
    .input-group {
        flex-direction: column;
    }
    
    .input-group button {
        width: 100%;
    }
    
    .step-item {
        flex-direction: column;
        text-align: center;
    }
    
    .history-item {
        flex-wrap: wrap;
    }
    
    .mobile-container {
        padding-bottom: 80px;
    }
}
</style>
</head>
<body>

<!-- Desktop Header -->
<div class="desktop-header">
    <div class="header-top">
        <div class="logo">PG<span>Mitra</span></div>
        <div class="desktop-nav">
            <a href="/home"><i class="fas fa-home"></i> Home</a>
            <a href="/search"><i class="fas fa-search"></i> Search</a>
            <a href="/bookings"><i class="fas fa-ticket-alt"></i> Bookings</a>
            <a href="/saved-rooms"><i class="fas fa-heart"></i> Saved</a>
            <a href="/profile" class="active"><i class="fas fa-user"></i> Profile</a>
        </div>
        <div class="user-menu">
            <div class="user-avatar" onclick="window.location.href='/profile'">
                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile">
            </div>
        </div>
    </div>
</div>

<!-- Mobile Header -->
<div class="mobile-header">
    <div class="logo">PG<span>Mitra</span></div>
    <div class="user-avatar" onclick="window.location.href='/profile'">
        <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile">
    </div>
</div>

<!-- Desktop Layout -->
<div class="main-container">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Refer & Earn ₹100</h1>
                <p>Invite friends and earn rewards together</p>
            </div>
            <div class="hero-icon">
                <i class="fas fa-gift"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-value"><?php echo $total_referrals; ?></div>
            <div class="stat-label">Total Referrals</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value"><?php echo $completed_referrals; ?></div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
            <div class="stat-value">₹<?php echo number_format($total_earned, 0); ?></div>
            <div class="stat-label">Earned</div>
        </div>
    </div>

    <!-- Alert Message -->
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'error'; ?>">
        <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
        <span><?php echo htmlspecialchars($message); ?></span>
    </div>
    <?php endif; ?>

    <!-- Referral Card -->
    <div class="referral-card">
        <div class="referral-header">
            <div class="referral-header-icon">
                <i class="fas fa-share-alt"></i>
            </div>
            <div class="referral-header-text">
                <h3>Share & Earn ₹100</h3>
                <p>Get ₹100 for each friend who books</p>
            </div>
        </div>

        <div class="referral-code-box">
            <div>
                <div class="referral-code-label">Your Referral Code</div>
                <div class="referral-code-value"><?php echo htmlspecialchars($referral_code); ?></div>
            </div>
            <button class="copy-btn" onclick="copyReferralCode('<?php echo htmlspecialchars($referral_code); ?>')">
                <i class="far fa-copy"></i> Copy
            </button>
        </div>

        <div class="referral-note">
            <i class="fas fa-info-circle"></i>
            <span>Your friend gets up to 5% off on first booking</span>
        </div>

        <div class="share-buttons">
            <button class="share-btn whatsapp" onclick="shareViaWhatsapp('<?php echo htmlspecialchars($referral_code); ?>')">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </button>
            <button class="share-btn facebook" onclick="shareViaFacebook('<?php echo htmlspecialchars($referral_code); ?>')">
                <i class="fab fa-facebook-f"></i> Facebook
            </button>
            <button class="share-btn twitter" onclick="shareViaTwitter('<?php echo htmlspecialchars($referral_code); ?>')">
                <i class="fab fa-twitter"></i> Twitter
            </button>
        </div>
    </div>

    <!-- Invite via Email -->
    <div class="invite-section">
        <h3 class="invite-title"><i class="fas fa-envelope mr-2"></i> Invite via Email</h3>
        <form method="POST" class="input-group">
            <input type="email" name="refer_email" placeholder="Enter friend's email" required>
            <button type="submit">Send Invite</button>
        </form>
    </div>

    <!-- How It Works -->
    <div class="steps-section">
        <h3 class="steps-title"><i class="fas fa-rocket mr-2"></i> How It Works</h3>
        <div class="step-item">
            <div class="step-number">1</div>
            <div class="step-content">
                <h4>Share Your Code</h4>
                <p>Share your unique referral code with friends</p>
            </div>
        </div>
        <div class="step-item">
            <div class="step-number">2</div>
            <div class="step-content">
                <h4>Friend Registers</h4>
                <p>They sign up using your code and book a room</p>
            </div>
        </div>
        <div class="step-item">
            <div class="step-number">3</div>
            <div class="step-content">
                <h4>Earn Rewards</h4>
                <p>Get ₹100 credited to your wallet</p>
            </div>
        </div>
    </div>

    <!-- Referral History -->
    <?php if (!empty($referral_history)): ?>
    <div class="history-section">
        <h3 class="history-title"><i class="fas fa-history mr-2"></i> Referral History</h3>
        <?php foreach ($referral_history as $referral): ?>
        <div class="history-item">
            <div class="history-avatar">
                <?php if (!empty($referral['referred_image'])): ?>
                    <img src="<?php echo htmlspecialchars($referral['referred_image']); ?>" alt="<?php echo htmlspecialchars($referral['referred_name'] ?? $referral['referred_user']); ?>">
                <?php else: ?>
                    <?php
                    $name = $referral['referred_name'] ?? $referral['referred_user'] ?? 'U';
                    echo strtoupper(substr($name, 0, 1));
                    ?>
                <?php endif; ?>
            </div>
            <div class="history-info">
                <div class="history-name"><?php echo htmlspecialchars($referral['referred_name'] ?? $referral['referred_user']); ?></div>
                <div class="history-date">Joined <?php echo !empty($referral['joined_date']) ? date('d M Y', strtotime($referral['joined_date'])) : 'N/A'; ?></div>
            </div>
            <div>
                <span class="history-status <?php echo ($referral['status'] === 'active') ? 'status-completed' : 'status-pending'; ?>">
                    <?php echo ucfirst(htmlspecialchars($referral['status'] ?? 'pending')); ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Terms & Conditions -->
    <div class="terms-section">
        <details>
            <summary><i class="fas fa-file-alt"></i> Terms & Conditions</summary>
            <div class="terms-content">
                <p>• ₹100 credited after friend's first successful booking</p>
                <p>• Friend gets ₹100 off on their first booking</p>
                <p>• Referral rewards credited within 24 hours of booking</p>
                <p>• Maximum reward per referral: ₹100</p>
                <p>• No limit on number of referrals</p>
                <p>• Rewards can be used for future bookings</p>
                <p>• Referral code cannot be changed once generated</p>
            </div>
        </details>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <!-- Hero Section -->
    <div class="hero-section" style="margin: 16px;">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Refer & Earn ₹100</h1>
                <p>Invite friends, earn rewards</p>
            </div>
            <div class="hero-icon">
                <i class="fas fa-gift"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid" style="padding: 0 16px;">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-value"><?php echo $total_referrals; ?></div>
            <div class="stat-label">Referrals</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value"><?php echo $completed_referrals; ?></div>
            <div class="stat-label">Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
            <div class="stat-value">₹<?php echo number_format($total_earned, 0); ?></div>
            <div class="stat-label">Earned</div>
        </div>
    </div>

    <!-- Alert Message -->
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'error'; ?>" style="margin: 0 16px 20px 16px;">
        <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
        <span><?php echo htmlspecialchars($message); ?></span>
    </div>
    <?php endif; ?>

    <!-- Referral Card -->
    <div class="referral-card" style="margin: 0 16px 20px 16px;">
        <div class="referral-header">
            <div class="referral-header-icon">
                <i class="fas fa-share-alt"></i>
            </div>
            <div class="referral-header-text">
                <h3>Share & Earn ₹100</h3>
                <p>Get ₹100 per booking</p>
            </div>
        </div>

        <div class="referral-code-box">
            <div>
                <div class="referral-code-label">Your Code</div>
                <div class="referral-code-value"><?php echo htmlspecialchars($referral_code); ?></div>
            </div>
            <button class="copy-btn" onclick="copyReferralCode('<?php echo htmlspecialchars($referral_code); ?>')">
                <i class="far fa-copy"></i> Copy
            </button>
        </div>

        <div class="share-buttons">
            <button class="share-btn whatsapp" onclick="shareViaWhatsapp('<?php echo htmlspecialchars($referral_code); ?>')">
                <i class="fab fa-whatsapp"></i>
            </button>
            <button class="share-btn facebook" onclick="shareViaFacebook('<?php echo htmlspecialchars($referral_code); ?>')">
                <i class="fab fa-facebook-f"></i>
            </button>
            <button class="share-btn twitter" onclick="shareViaTwitter('<?php echo htmlspecialchars($referral_code); ?>')">
                <i class="fab fa-twitter"></i>
            </button>
        </div>
    </div>

    <!-- Invite via Email -->
    <div class="invite-section" style="margin: 0 16px 20px 16px;">
        <h3 class="invite-title">Invite via Email</h3>
        <form method="POST" class="input-group">
            <input type="email" name="refer_email" placeholder="Friend's email" required>
            <button type="submit">Send</button>
        </form>
    </div>

    <!-- How It Works -->
    <div class="steps-section" style="margin: 0 16px 20px 16px;">
        <h3 class="steps-title">How It Works</h3>
        <div class="step-item">
            <div class="step-number">1</div>
            <div class="step-content">
                <h4>Share Code</h4>
                <p>Share your unique referral code</p>
            </div>
        </div>
        <div class="step-item">
            <div class="step-number">2</div>
            <div class="step-content">
                <h4>Friend Books</h4>
                <p>Friend registers and books</p>
            </div>
        </div>
        <div class="step-item">
            <div class="step-number">3</div>
            <div class="step-content">
                <h4>Get Rewards</h4>
                <p>₹100 credited to wallet</p>
            </div>
        </div>
    </div>

    <!-- Referral History -->
    <?php if (!empty($referral_history)): ?>
    <div class="history-section" style="margin: 0 16px 20px 16px;">
        <h3 class="history-title">Referral History</h3>
        <?php foreach (array_slice($referral_history, 0, 3) as $referral): ?>
        <div class="history-item">
            <div class="history-avatar">
                <?php if (!empty($referral['referred_image'])): ?>
                    <img src="<?php echo htmlspecialchars($referral['referred_image']); ?>" alt="<?php echo htmlspecialchars($referral['referred_name'] ?? $referral['referred_user']); ?>">
                <?php else: ?>
                    <?php
                    $name = $referral['referred_name'] ?? $referral['referred_user'] ?? 'U';
                    echo strtoupper(substr($name, 0, 1));
                    ?>
                <?php endif; ?>
            </div>
            <div class="history-info">
                <div class="history-name"><?php echo htmlspecialchars($referral['referred_name'] ?? $referral['referred_user']); ?></div>
                <div class="history-date"><?php echo !empty($referral['joined_date']) ? date('d M Y', strtotime($referral['joined_date'])) : 'N/A'; ?></div>
            </div>
            <div>
                <span class="history-status <?php echo ($referral['status'] === 'active') ? 'status-completed' : 'status-pending'; ?>">
                    <?php echo ucfirst(htmlspecialchars($referral['status'] ?? 'pending')); ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (count($referral_history) > 3): ?>
        <p class="text-center text-sm text-gray-500 mt-3">+<?php echo count($referral_history) - 3; ?> more referrals</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Terms & Conditions -->
    <div class="terms-section" style="margin: 0 16px 20px 16px;">
        <details>
            <summary>Terms & Conditions</summary>
            <div class="terms-content">
                <p>• ₹100 credited after friend's first booking</p>
                <p>• Friend gets ₹100 off on first booking</p>
                <p>• No limit on number of referrals</p>
            </div>
        </details>
    </div>
</div>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav">
    <div style="display: flex; justify-content: space-around; align-items: center;">
        <div onclick="goToPage('home')" style="text-align: center; cursor: pointer;">
            <i class="fas fa-home" style="font-size: 22px; color: #9CA3AF;"></i>
            <div style="font-size: 11px; margin-top: 4px;">Home</div>
        </div>
        <div onclick="goToPage('search')" style="text-align: center; cursor: pointer;">
            <i class="fas fa-search" style="font-size: 22px; color: #9CA3AF;"></i>
            <div style="font-size: 11px; margin-top: 4px;">Search</div>
        </div>
        <div onclick="goToPage('bookings')" style="text-align: center; cursor: pointer;">
            <i class="fas fa-ticket-alt" style="font-size: 22px; color: #9CA3AF;"></i>
            <div style="font-size: 11px; margin-top: 4px;">Bookings</div>
        </div>
        <div onclick="goToPage('saved-rooms')" style="text-align: center; cursor: pointer;">
            <i class="fas fa-heart" style="font-size: 22px; color: #9CA3AF;"></i>
            <div style="font-size: 11px; margin-top: 4px;">Saved</div>
        </div>
        <div onclick="goToPage('profile')" style="text-align: center; cursor: pointer;">
            <i class="fas fa-user" style="font-size: 22px; color: #003B95;"></i>
            <div style="font-size: 11px; margin-top: 4px;">Profile</div>
        </div>
    </div>
</div>

<script>
function goToPage(page) {
    if (page === 'home') window.location.href = '/home';
    else if (page === 'search') window.location.href = '/search';
    else if (page === 'bookings') window.location.href = '/bookings';
    else if (page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if (page === 'profile') window.location.href = '/profile';
}

function copyReferralCode(code) {
    if (!navigator.clipboard) {
        showToast('Clipboard not supported', 'error');
        return;
    }
    navigator.clipboard.writeText(code)
        .then(() => showToast('Referral code copied!', 'success'))
        .catch(() => showToast('Failed to copy code', 'error'));
}

function shareViaWhatsapp(code) {
    const BASE_URL = <?php echo json_encode($base_url); ?>;
    const text = encodeURIComponent(
        `Join PG Mitra using my referral code "${code}" and get ₹100 off on your first booking! Register here: ${BASE_URL}/register.php?ref=${code}`
    );
    window.open(`https://wa.me/?text=${text}`, '_blank');
}

function shareViaFacebook(code) {
    const BASE_URL = <?php echo json_encode($base_url); ?>;
    const url = encodeURIComponent(`${BASE_URL}/register.php?ref=${code}`);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
}

function shareViaTwitter(code) {
    const BASE_URL = <?php echo json_encode($base_url); ?>;
    const text = encodeURIComponent(
        `Join PG Mitra with my referral code "${code}" and get ₹100 off on your first booking! ${BASE_URL}/register.php?ref=${code}`
    );
    window.open(`https://twitter.com/intent/tweet?text=${text}`, '_blank');
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

</body>
</html>