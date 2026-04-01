<?php
session_start();
require_once 'common/db_connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$user_id = $_SESSION['user_id'];

// Get user details
$user_query = "SELECT username, full_name, email, phone, profile_image, wallet_balance FROM users WHERE id = ?";
$stmt = $db->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$display_name = !empty($user['full_name']) ? $user['full_name'] : $user['username'];
$profile_image = !empty($user['profile_image']) ? $user['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($display_name) . '&background=003B95&color=fff';
$wallet_balance = $user['wallet_balance'] ?? 0;

// Get saved bank cards
$cards_query = "SELECT * FROM bank_cards WHERE user_id = ? ORDER BY is_default DESC, created_at DESC";
$stmt = $db->prepare($cards_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cards_result = $stmt->get_result();

$saved_cards = [];
while($row = $cards_result->fetch_assoc()) {
    $row['account_number_masked'] = 'XXXX' . substr($row['account_number'], -4);
    unset($row['account_number']);
    $saved_cards[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Withdraw Money - PG Mitra</title>
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
    background: #F3F4F6;
    color: #1E2A3A;
}

.desktop-header {
    background: #003B95;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.header-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 80px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
}

.logo {
    font-size: 28px;
    font-weight: 800;
    color: white;
    letter-spacing: -0.5px;
    text-decoration: none;
    display: inline-block;
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
    gap: 20px;
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
    text-decoration: none;
}

.back-btn {
    width: 36px;
    height: 36px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.back-btn:hover {
    background: rgba(255,255,255,0.3);
}

.back-btn i {
    color: white;
    font-size: 18px;
}

/* Main Container - Desktop */
.main-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 40px 80px;
    display: flex;
    gap: 48px;
}

/* Sidebar - Desktop */
.sidebar {
    width: 280px;
    flex-shrink: 0;
}

.profile-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    border: 1px solid #E5E7EB;
    margin-bottom: 24px;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 0 auto 16px;
    overflow: hidden;
    border: 3px solid #003B95;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-name {
    font-size: 18px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 4px;
}

.profile-email {
    font-size: 12px;
    color: #6B7280;
    word-break: break-all;
}

.sidebar-menu {
    background: white;
    border-radius: 16px;
    padding: 8px 0;
    border: 1px solid #E5E7EB;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.sidebar-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: #4B5563;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
    cursor: pointer;
}

.sidebar-item:hover {
    background: #F9FAFB;
    color: #003B95;
}

.sidebar-item.active {
    background: #EFF6FF;
    color: #003B95;
    border-right: 3px solid #003B95;
}

.sidebar-item i {
    width: 20px;
    font-size: 16px;
}

/* Content Area */
.content-area {
    flex: 1;
    background: white;
    border-radius: 16px;
    padding: 32px;
    border: 1px solid #E5E7EB;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.content-header {
    margin-bottom: 32px;
    padding-bottom: 20px;
    border-bottom: 1px solid #E5E7EB;
}

.content-header h1 {
    font-size: 24px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 8px;
}

.content-header p {
    color: #6B7280;
    font-size: 14px;
}

/* Wallet Card */
.wallet-card {
    background: linear-gradient(135deg, #003B95 0%, #0066CC 100%);
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 32px;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0,59,149,0.2);
}

.wallet-card::before {
    content: '';
    position: absolute;
    top: -20px;
    right: -20px;
    width: 150px;
    height: 150px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.wallet-balance {
    font-size: 36px;
    font-weight: 800;
    margin-top: 8px;
}

.wallet-label {
    font-size: 13px;
    opacity: 0.9;
}

/* Method Toggle */
.method-toggle {
    display: flex;
    gap: 16px;
    margin-bottom: 28px;
}

.method-option {
    flex: 1;
    padding: 16px;
    border: 2px solid #E5E7EB;
    border-radius: 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}

.method-option:hover {
    border-color: #003B95;
    background: #F9FAFB;
}

.method-option.active {
    border-color: #003B95;
    background: #EFF6FF;
}

.method-option i {
    font-size: 28px;
    color: #9CA3AF;
    margin-bottom: 8px;
}

.method-option.active i {
    color: #003B95;
}

.method-option span {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #6B7280;
}

.method-option.active span {
    color: #003B95;
}

/* Form Groups */
.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #1E2A3A;
    margin-bottom: 8px;
}

.form-label i {
    color: #003B95;
    margin-right: 6px;
}

.form-input, .form-select {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid #D1D5DB;
    border-radius: 12px;
    font-size: 14px;
    transition: all 0.2s;
    background: white;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #003B95;
    box-shadow: 0 0 0 3px rgba(0,59,149,0.1);
}

/* Bank Card Option */
.bank-card-option {
    background: white;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.bank-card-option:hover {
    border-color: #003B95;
    background: #F9FAFB;
}

.bank-card-option.selected {
    border-color: #003B95;
    background: #EFF6FF;
}

.bank-card-option .bank-name {
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 4px;
}

.bank-card-option .account-details {
    font-size: 13px;
    color: #6B7280;
}

.default-badge {
    background: #10B981;
    color: white;
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 20px;
    margin-left: 8px;
}

/* Quick Amounts */
.quick-amounts {
    display: flex;
    gap: 10px;
    margin-top: 12px;
    flex-wrap: wrap;
}

.quick-amount {
    background: #F3F4F6;
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 600;
    color: #4B5563;
    cursor: pointer;
    transition: all 0.2s;
}

.quick-amount:hover {
    background: #003B95;
    color: white;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    background: #003B95;
    color: white;
    padding: 16px;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.submit-btn:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.submit-btn.loading {
    position: relative;
    color: transparent;
}

.submit-btn.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin-left: -10px;
    margin-top: -10px;
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 0.8s linear infinite;
}

/* Info Alert */
.info-alert {
    background: #EFF6FF;
    border-radius: 12px;
    padding: 14px 16px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid #BFDBFE;
}

.info-alert i {
    color: #003B95;
    font-size: 18px;
    flex-shrink: 0;
}

.info-alert p {
    color: #1E40AF;
    font-size: 13px;
    line-height: 1.5;
    margin: 0;
}

/* History Section */
.history-section {
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #E5E7EB;
}

.history-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.history-title i {
    color: #003B95;
}

.history-item {
    background: #F9FAFB;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    border-left: 4px solid;
}

.history-item.pending { border-left-color: #F59E0B; }
.history-item.processing { border-left-color: #003B95; }
.history-item.completed { border-left-color: #10B981; }
.history-item.failed { border-left-color: #EF4444; }
.history-item.cancelled { border-left-color: #6B7280; }

.history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.history-amount {
    font-size: 18px;
    font-weight: 700;
    color: #1E2A3A;
}

.history-status {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.status-pending { background: #FEF3C7; color: #92400E; }
.status-processing { background: #EFF6FF; color: #003B95; }
.status-completed { background: #DCFCE7; color: #166534; }
.status-failed { background: #FEE2E2; color: #991B1B; }
.status-cancelled { background: #F3F4F6; color: #4B5563; }

.history-method {
    font-size: 13px;
    color: #6B7280;
    margin-bottom: 4px;
}

.history-date {
    font-size: 11px;
    color: #9CA3AF;
}

.cancel-btn {
    color: #EF4444;
    font-size: 12px;
    cursor: pointer;
    margin-top: 8px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: none;
    border: none;
}

.cancel-btn:hover {
    text-decoration: underline;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
}

.empty-icon {
    width: 60px;
    height: 60px;
    background: #F3F4F6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.empty-icon i {
    font-size: 24px;
    color: #9CA3AF;
}

.empty-state p {
    font-size: 13px;
    color: #6B7280;
}

/* Mobile Container */
.mobile-container {
    display: none;
    padding: 0 0 80px 0;
    background: #F7F9FC;
    min-height: 100vh;
}

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
.toast.info { background: #003B95; }

@keyframes spin {
    to { transform: rotate(360deg); }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 1024px) {
    .header-container {
        padding: 0 32px;
    }
    
    .main-container {
        padding: 32px;
        gap: 32px;
    }
}

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
    
    .mobile-container {
        padding-bottom: 80px;
    }
    
    .wallet-card {
        margin: 0 16px 20px 16px;
        padding: 20px;
    }
    
    .wallet-balance {
        font-size: 28px;
    }
    
    .method-toggle {
        gap: 12px;
    }
    
    .method-option {
        padding: 12px;
    }
    
    .method-option i {
        font-size: 24px;
    }
    
    .quick-amounts {
        gap: 8px;
    }
    
    .quick-amount {
        padding: 6px 12px;
        font-size: 12px;
    }
}
</style>
</head>
<body>

<!-- Desktop Header - Booking.com Style -->
<div class="desktop-header">
    <div class="header-container">
        <div class="header-content">
            <a href="/home" class="logo">PG<span>Mitra</span></a>
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
</div>

<!-- Mobile Header -->
<div class="mobile-header">
    <div class="back-btn" onclick="goBack()">
        <i class="fas fa-arrow-left"></i>
    </div>
    <a href="/home" class="logo">PG<span>Mitra</span></a>
    <div class="user-avatar" onclick="window.location.href='/profile'">
        <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile">
    </div>
</div>

<!-- Desktop Layout with Sidebar -->
<div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="profile-card">
            <div class="profile-avatar">
                <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="<?php echo htmlspecialchars($display_name); ?>">
            </div>
            <div class="profile-name"><?php echo htmlspecialchars($display_name); ?></div>
            <div class="profile-email"><?php echo htmlspecialchars($user['email']); ?></div>
        </div>
        
        <div class="sidebar-menu">
            <div class="sidebar-item" onclick="goToPage('profile')">
                <i class="fas fa-user"></i>
                <span>Profile Overview</span>
            </div>
            <div class="sidebar-item" onclick="goToPage('bookings')">
                <i class="fas fa-calendar-alt"></i>
                <span>My Bookings</span>
            </div>
            <div class="sidebar-item" onclick="goToPage('saved-rooms')">
                <i class="fas fa-heart"></i>
                <span>Saved Rooms</span>
            </div>
            <div class="sidebar-item" onclick="goToPage('change-password')">
                <i class="fas fa-lock"></i>
                <span>Change Password</span>
            </div>
            <div class="sidebar-item" onclick="goToPage('bank-cards')">
                <i class="fas fa-credit-card"></i>
                <span>Bank Cards</span>
            </div>
            <div class="sidebar-item active" onclick="goToPage('withdraw')">
                <i class="fas fa-money-bill-wave"></i>
                <span>Withdraw Money</span>
            </div>
            <div class="sidebar-item" onclick="goToPage('settings')">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </div>
            <div class="sidebar-item" onclick="showLogoutModal()">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">
        <div class="content-header">
            <h1>Withdraw Money</h1>
            <p>Transfer funds from your wallet to your bank account</p>
        </div>

        <!-- Wallet Card -->
        <div class="wallet-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="wallet-label">Available Balance</p>
                    <p class="wallet-balance" id="walletBalance">₹<?php echo number_format($wallet_balance); ?></p>
                </div>
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="info-alert">
            <i class="fas fa-info-circle"></i>
            <p>Withdrawals are processed within 24-48 hours. Minimum withdrawal amount is ₹100 and maximum is ₹50,000 per transaction.</p>
        </div>

        <!-- Method Toggle -->
        <div class="method-toggle">
            <div class="method-option active" onclick="setMethod('bank')" id="methodBank">
                <i class="fas fa-university"></i>
                <span>Bank Transfer</span>
            </div>
            <div class="method-option" onclick="setMethod('upi')" id="methodUpi">
                <i class="fas fa-mobile-alt"></i>
                <span>UPI Transfer</span>
            </div>
        </div>

        <form id="withdrawForm" onsubmit="submitWithdraw(event)">
            <!-- Bank Method Fields -->
            <div id="bankFields">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-credit-card"></i> Select Bank Account</label>
                    <?php if(empty($saved_cards)): ?>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-3">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                            <span class="text-sm text-yellow-700">No bank cards added yet.</span>
                            <a href="bank-cards.php" class="text-sm text-[#003B95] font-semibold mt-2 inline-block">Add Bank Card →</a>
                        </div>
                    <?php else: ?>
                        <?php foreach($saved_cards as $card): ?>
                        <div class="bank-card-option" onclick="selectCard(this, <?php echo $card['id']; ?>)">
                            <input type="radio" name="bank_card_id" value="<?php echo $card['id']; ?>" style="display: none;" <?php echo $card['is_default'] ? 'checked' : ''; ?>>
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="bank-name">
                                        <?php echo htmlspecialchars($card['bank_name']); ?>
                                        <?php if($card['is_default']): ?>
                                        <span class="default-badge">Default</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="account-details">
                                        <?php echo htmlspecialchars($card['account_number_masked']); ?> • <?php echo htmlspecialchars($card['full_name']); ?>
                                    </div>
                                    <div class="account-details mt-1">
                                        IFSC: <?php echo htmlspecialchars($card['ifsc_code']); ?>
                                    </div>
                                </div>
                                <i class="fas fa-check-circle text-2xl <?php echo $card['is_default'] ? 'text-[#003B95]' : 'text-gray-300'; ?>"></i>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- UPI Method Fields -->
            <div id="upiFields" style="display: none;">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-mobile-alt"></i> UPI ID</label>
                    <input type="text" id="upiId" class="form-input" placeholder="e.g., name@okhdfcbank">
                    <div class="text-xs text-gray-400 mt-1">Enter your UPI ID (e.g., 9876543210@okhdfcbank)</div>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-user"></i> Account Holder Name</label>
                    <input type="text" id="accountHolder" class="form-input" placeholder="Enter name as per bank">
                </div>
            </div>

            <!-- Amount -->
            <div class="form-group">
                <label class="form-label"><i class="fas fa-rupee-sign"></i> Amount</label>
                <input type="number" id="amount" class="form-input" placeholder="Enter amount" min="100" max="50000" step="100" required>
                
                <!-- Quick Amounts -->
                <div class="quick-amounts">
                    <span class="quick-amount" onclick="setAmount(500)">₹500</span>
                    <span class="quick-amount" onclick="setAmount(1000)">₹1,000</span>
                    <span class="quick-amount" onclick="setAmount(2000)">₹2,000</span>
                    <span class="quick-amount" onclick="setAmount(5000)">₹5,000</span>
                    <span class="quick-amount" onclick="setAmount(10000)">₹10,000</span>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fas fa-arrow-right"></i> Withdraw Now
            </button>
        </form>

        <!-- Withdrawal History -->
        <div class="history-section">
            <h3 class="history-title">
                <i class="fas fa-history"></i>
                Withdrawal History
            </h3>
            <div id="historyContainer">
                <div class="text-center py-8">
                    <div class="empty-icon mx-auto">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                    <p class="text-sm text-gray-500">Loading history...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <!-- Wallet Card Mobile -->
    <div class="wallet-card" style="margin: 16px 16px 20px 16px;">
        <div class="flex items-center justify-between">
            <div>
                <p class="wallet-label">Available Balance</p>
                <p class="wallet-balance" id="mobileWalletBalance">₹<?php echo number_format($wallet_balance); ?></p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-wallet text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Method Toggle Mobile -->
    <div class="method-toggle" style="margin: 0 16px 20px 16px;">
        <div class="method-option active" onclick="setMethodMobile('bank')" id="mobileMethodBank">
            <i class="fas fa-university"></i>
            <span>Bank</span>
        </div>
        <div class="method-option" onclick="setMethodMobile('upi')" id="mobileMethodUpi">
            <i class="fas fa-mobile-alt"></i>
            <span>UPI</span>
        </div>
    </div>

    <form id="mobileWithdrawForm" onsubmit="submitWithdrawMobile(event)" style="margin: 0 16px;">
        <!-- Bank Method Fields Mobile -->
        <div id="mobileBankFields">
            <div class="form-group">
                <label class="form-label">Select Bank Account</label>
                <?php if(empty($saved_cards)): ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-3">
                        <p class="text-sm text-yellow-700">No bank cards added yet.</p>
                        <a href="bank-cards.php" class="text-sm text-[#003B95] font-semibold mt-2 inline-block">Add Bank Card →</a>
                    </div>
                <?php else: ?>
                    <?php foreach($saved_cards as $card): ?>
                    <div class="bank-card-option" onclick="selectCardMobile(this, <?php echo $card['id']; ?>)">
                        <input type="radio" name="mobile_bank_card_id" value="<?php echo $card['id']; ?>" style="display: none;" <?php echo $card['is_default'] ? 'checked' : ''; ?>>
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="bank-name">
                                    <?php echo htmlspecialchars($card['bank_name']); ?>
                                    <?php if($card['is_default']): ?>
                                    <span class="default-badge">Default</span>
                                    <?php endif; ?>
                                </div>
                                <div class="account-details">
                                    <?php echo htmlspecialchars($card['account_number_masked']); ?>
                                </div>
                            </div>
                            <i class="fas fa-check-circle text-xl <?php echo $card['is_default'] ? 'text-[#003B95]' : 'text-gray-300'; ?>"></i>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- UPI Method Fields Mobile -->
        <div id="mobileUpiFields" style="display: none;">
            <div class="form-group">
                <label class="form-label">UPI ID</label>
                <input type="text" id="mobileUpiId" class="form-input" placeholder="name@okhdfcbank">
            </div>
            <div class="form-group">
                <label class="form-label">Account Holder Name</label>
                <input type="text" id="mobileAccountHolder" class="form-input" placeholder="Enter name">
            </div>
        </div>

        <!-- Amount Mobile -->
        <div class="form-group">
            <label class="form-label">Amount (₹)</label>
            <input type="number" id="mobileAmount" class="form-input" placeholder="Enter amount" min="100" max="50000" step="100" required>
            <div class="quick-amounts">
                <span class="quick-amount" onclick="setAmountMobile(500)">₹500</span>
                <span class="quick-amount" onclick="setAmountMobile(1000)">₹1,000</span>
                <span class="quick-amount" onclick="setAmountMobile(2000)">₹2,000</span>
                <span class="quick-amount" onclick="setAmountMobile(5000)">₹5,000</span>
            </div>
        </div>

        <button type="submit" class="submit-btn" id="mobileSubmitBtn" style="margin-bottom: 20px;">
            <i class="fas fa-arrow-right"></i> Withdraw
        </button>
    </form>

    <!-- Withdrawal History Mobile -->
    <div class="history-section" style="margin: 20px 16px 80px 16px; background: white; border-radius: 16px; padding: 20px;">
        <h3 class="history-title">
            <i class="fas fa-history"></i>
            Withdrawal History
        </h3>
        <div id="mobileHistoryContainer">
            <div class="text-center py-8">
                <div class="empty-icon mx-auto">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <p class="text-sm text-gray-500">Loading...</p>
            </div>
        </div>
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

<!-- Logout Modal -->
<div id="logoutModal" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; max-width: 380px; width: 90%; padding: 24px; text-align: center;">
        <div style="width: 60px; height: 60px; background: #FEF2F2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <i class="fas fa-sign-out-alt" style="font-size: 24px; color: #EF4444;"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 8px;">Logout?</h3>
        <p style="color: #6B7280; font-size: 14px; margin-bottom: 24px;">Are you sure you want to logout from your account?</p>
        <div style="display: flex; gap: 12px;">
            <button onclick="hideLogoutModal()" style="flex: 1; padding: 12px; background: #F3F4F6; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Cancel</button>
            <button onclick="logout()" style="flex: 1; padding: 12px; background: #EF4444; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Logout</button>
        </div>
    </div>
</div>

<script>
let currentMethod = 'bank';
let selectedCardId = null;
let mobileSelectedCardId = null;

// Load withdrawal history on page load
document.addEventListener('DOMContentLoaded', function() {
    loadWithdrawalHistory();
    
    // Initialize default selections
    const defaultCard = document.querySelector('#bankFields .bank-card-option input[checked]');
    if(defaultCard) {
        const cardDiv = defaultCard.closest('.bank-card-option');
        if(cardDiv) {
            cardDiv.classList.add('selected');
            cardDiv.querySelector('i').className = 'fas fa-check-circle text-2xl text-[#003B95]';
            selectedCardId = defaultCard.value;
        }
    }
    
    const mobileDefaultCard = document.querySelector('#mobileBankFields .bank-card-option input[checked]');
    if(mobileDefaultCard) {
        const cardDiv = mobileDefaultCard.closest('.bank-card-option');
        if(cardDiv) {
            cardDiv.classList.add('selected');
            cardDiv.querySelector('i').className = 'fas fa-check-circle text-xl text-[#003B95]';
            mobileSelectedCardId = mobileDefaultCard.value;
        }
    }
});

// Set withdrawal method - Desktop
function setMethod(method) {
    currentMethod = method;
    
    document.getElementById('methodBank').classList.toggle('active', method === 'bank');
    document.getElementById('methodUpi').classList.toggle('active', method === 'upi');
    
    document.getElementById('bankFields').style.display = method === 'bank' ? 'block' : 'none';
    document.getElementById('upiFields').style.display = method === 'upi' ? 'block' : 'none';
}

// Set withdrawal method - Mobile
function setMethodMobile(method) {
    currentMethod = method;
    
    document.getElementById('mobileMethodBank').classList.toggle('active', method === 'bank');
    document.getElementById('mobileMethodUpi').classList.toggle('active', method === 'upi');
    
    document.getElementById('mobileBankFields').style.display = method === 'bank' ? 'block' : 'none';
    document.getElementById('mobileUpiFields').style.display = method === 'upi' ? 'block' : 'none';
}

// Select bank card - Desktop
function selectCard(element, cardId) {
    selectedCardId = cardId;
    
    document.querySelectorAll('#bankFields .bank-card-option').forEach(opt => {
        opt.classList.remove('selected');
        const icon = opt.querySelector('i');
        if(icon) icon.className = 'fas fa-check-circle text-2xl text-gray-300';
    });
    
    element.classList.add('selected');
    const icon = element.querySelector('i');
    if(icon) icon.className = 'fas fa-check-circle text-2xl text-[#003B95]';
    
    const radio = element.querySelector('input[type="radio"]');
    if(radio) radio.checked = true;
}

// Select bank card - Mobile
function selectCardMobile(element, cardId) {
    mobileSelectedCardId = cardId;
    
    document.querySelectorAll('#mobileBankFields .bank-card-option').forEach(opt => {
        opt.classList.remove('selected');
        const icon = opt.querySelector('i');
        if(icon) icon.className = 'fas fa-check-circle text-xl text-gray-300';
    });
    
    element.classList.add('selected');
    const icon = element.querySelector('i');
    if(icon) icon.className = 'fas fa-check-circle text-xl text-[#003B95]';
    
    const radio = element.querySelector('input[type="radio"]');
    if(radio) radio.checked = true;
}

// Set amount - Desktop
function setAmount(amount) {
    document.getElementById('amount').value = amount;
}

// Set amount - Mobile
function setAmountMobile(amount) {
    document.getElementById('mobileAmount').value = amount;
}

// Submit withdrawal - Desktop
function submitWithdraw(event) {
    event.preventDefault();
    
    const amount = parseFloat(document.getElementById('amount').value);
    const submitBtn = document.getElementById('submitBtn');
    
    if(!amount || amount < 100) {
        showToast('Minimum withdrawal amount is ₹100', 'error');
        return;
    }
    
    if(amount > 50000) {
        showToast('Maximum withdrawal amount is ₹50,000', 'error');
        return;
    }
    
    const currentBalance = parseFloat('<?php echo $wallet_balance; ?>');
    if(amount > currentBalance) {
        showToast('Insufficient balance', 'error');
        return;
    }
    
    let data = {
        amount: amount,
        withdrawal_method: currentMethod
    };
    
    if(currentMethod === 'bank') {
        if(!selectedCardId) {
            showToast('Please select a bank account', 'error');
            return;
        }
        data.bank_card_id = selectedCardId;
    } else {
        const upiId = document.getElementById('upiId').value;
        const accountHolder = document.getElementById('accountHolder').value;
        
        if(!upiId) {
            showToast('Please enter UPI ID', 'error');
            return;
        }
        
        if(!accountHolder) {
            showToast('Please enter account holder name', 'error');
            return;
        }
        
        data.upi_id = upiId;
        data.account_holder = accountHolder;
    }
    
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    
    fetch('/api/withdraw', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        
        if(result.success) {
            showToast(result.message, 'success');
            document.getElementById('walletBalance').innerHTML = '₹' + result.new_balance.toLocaleString('en-IN');
            if(document.getElementById('mobileWalletBalance')) {
                document.getElementById('mobileWalletBalance').innerHTML = '₹' + result.new_balance.toLocaleString('en-IN');
            }
            document.getElementById('amount').value = '';
            if(currentMethod === 'upi') {
                document.getElementById('upiId').value = '';
                document.getElementById('accountHolder').value = '';
            }
            loadWithdrawalHistory();
        } else {
            showToast(result.message, 'error');
        }
    })
    .catch(error => {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        showToast('Connection error. Please try again.', 'error');
    });
}

// Submit withdrawal - Mobile
function submitWithdrawMobile(event) {
    event.preventDefault();
    
    const amount = parseFloat(document.getElementById('mobileAmount').value);
    const submitBtn = document.getElementById('mobileSubmitBtn');
    
    if(!amount || amount < 100) {
        showToast('Minimum withdrawal amount is ₹100', 'error');
        return;
    }
    
    if(amount > 50000) {
        showToast('Maximum withdrawal amount is ₹50,000', 'error');
        return;
    }
    
    const currentBalance = parseFloat('<?php echo $wallet_balance; ?>');
    if(amount > currentBalance) {
        showToast('Insufficient balance', 'error');
        return;
    }
    
    let data = {
        amount: amount,
        withdrawal_method: currentMethod
    };
    
    if(currentMethod === 'bank') {
        if(!mobileSelectedCardId) {
            showToast('Please select a bank account', 'error');
            return;
        }
        data.bank_card_id = mobileSelectedCardId;
    } else {
        const upiId = document.getElementById('mobileUpiId').value;
        const accountHolder = document.getElementById('mobileAccountHolder').value;
        
        if(!upiId) {
            showToast('Please enter UPI ID', 'error');
            return;
        }
        
        if(!accountHolder) {
            showToast('Please enter account holder name', 'error');
            return;
        }
        
        data.upi_id = upiId;
        data.account_holder = accountHolder;
    }
    
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    
    fetch('/api/withdraw', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        
        if(result.success) {
            showToast(result.message, 'success');
            document.getElementById('mobileWalletBalance').innerHTML = '₹' + result.new_balance.toLocaleString('en-IN');
            if(document.getElementById('walletBalance')) {
                document.getElementById('walletBalance').innerHTML = '₹' + result.new_balance.toLocaleString('en-IN');
            }
            document.getElementById('mobileAmount').value = '';
            if(currentMethod === 'upi') {
                document.getElementById('mobileUpiId').value = '';
                document.getElementById('mobileAccountHolder').value = '';
            }
            loadWithdrawalHistory();
        } else {
            showToast(result.message, 'error');
        }
    })
    .catch(error => {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        showToast('Connection error. Please try again.', 'error');
    });
}

// Load withdrawal history
function loadWithdrawalHistory() {
    fetch('/api/withdraw')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('historyContainer');
            const mobileContainer = document.getElementById('mobileHistoryContainer');
            
            if(data.success && data.data && data.data.length > 0) {
                let html = '';
                data.data.forEach(withdrawal => {
                    const date = new Date(withdrawal.created_at);
                    const formattedDate = date.toLocaleDateString('en-IN', { 
                        day: '2-digit', month: 'short', year: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    });
                    
                    const methodIcon = withdrawal.withdrawal_method === 'bank' ? 'fa-university' : 'fa-mobile-alt';
                    
                    html += `
                        <div class="history-item ${withdrawal.status}">
                            <div class="history-header">
                                <span class="history-amount">₹${withdrawal.amount.toLocaleString('en-IN')}</span>
                                <span class="history-status status-${withdrawal.status}">${withdrawal.status.toUpperCase()}</span>
                            </div>
                            <div class="history-method">
                                <i class="fas ${methodIcon} mr-1"></i>
                                ${withdrawal.withdrawal_method === 'bank' ? (withdrawal.bank_name || 'Bank Transfer') : (withdrawal.upi_id || 'UPI Transfer')}
                            </div>
                            <div class="history-date">
                                <i class="far fa-clock mr-1"></i>${formattedDate}
                            </div>
                            ${withdrawal.status === 'pending' ? `
                                <button class="cancel-btn" onclick="cancelWithdrawal(${withdrawal.id})">
                                    <i class="fas fa-times-circle"></i> Cancel Request
                                </button>
                            ` : ''}
                        </div>
                    `;
                });
                if(container) container.innerHTML = html;
                if(mobileContainer) mobileContainer.innerHTML = html;
            } else {
                const emptyHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <p>No withdrawal history yet</p>
                    </div>
                `;
                if(container) container.innerHTML = emptyHTML;
                if(mobileContainer) mobileContainer.innerHTML = emptyHTML;
            }
        })
        .catch(error => {
            console.error('Error loading history:', error);
        });
}

// Cancel withdrawal
function cancelWithdrawal(withdrawalId) {
    if(!confirm('Are you sure you want to cancel this withdrawal request?')) return;
    
    fetch('/api/withdraw', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ withdrawal_id: withdrawalId })
    })
    .then(response => response.json())
    .then(result => {
        if(result.success) {
            showToast('Withdrawal cancelled successfully', 'success');
            document.getElementById('walletBalance').innerHTML = '₹' + result.new_balance.toLocaleString('en-IN');
            if(document.getElementById('mobileWalletBalance')) {
                document.getElementById('mobileWalletBalance').innerHTML = '₹' + result.new_balance.toLocaleString('en-IN');
            }
            loadWithdrawalHistory();
        } else {
            showToast(result.message, 'error');
        }
    });
}

// Navigation functions
function goBack() {
    window.history.back();
}

function goToPage(page) {
    if(page === 'home') window.location.href = '/home';
    else if(page === 'search') window.location.href = '/search';
    else if(page === 'bookings') window.location.href = '/bookings';
    else if(page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if(page === 'profile') window.location.href = '/profile';
    else if(page === 'change-password') window.location.href = '/change-password';
    else if(page === 'bank-cards') window.location.href = '/bank-cards';
    else if(page === 'withdraw') window.location.href = '/withdraw';
    else if(page === 'settings') window.location.href = '/settings';
    else window.location.href = '/' + page;
}

// Logout functions
function showLogoutModal() {
    document.getElementById('logoutModal').style.display = 'flex';
}

function hideLogoutModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

function logout() {
    showToast('Logging out...', 'info');
    
    fetch('/api/logout', {
        method: 'POST'
    })
    .then(() => {
        showToast('Logged out successfully!', 'success');
        setTimeout(() => {
            window.location.href = '/login';
        }, 1000);
    })
    .catch(() => {
        window.location.href = '/login';
    });
}

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    let icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('logoutModal');
    if (event.target == modal) {
        hideLogoutModal();
    }
}
</script>

</body>
</html>
