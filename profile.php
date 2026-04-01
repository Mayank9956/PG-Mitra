<?php
require_once 'common/auth.php';

$user = requireAuth($conn);

// Direct user_id from auth
$user_id = $user['id'];

// If full_name is empty, use username
$display_name = !empty($user['full_name']) ? $user['full_name'] : $user['username'];

// ==============================
// USER STATS
// ==============================
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM bookings WHERE user_id = ?) as total_bookings,
    (SELECT COUNT(*) FROM user_favorites WHERE user_id = ?) as total_favorites,
    (SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE user_id = ? AND status = 'confirmed') as total_spent";

$stmt = $conn->prepare($stats_query);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// ==============================
// PROFILE IMAGE
// ==============================
$profile_image = !empty($user['profile_image']) 
    ? $user['profile_image'] 
    : 'https://ui-avatars.com/api/?name=' . urlencode($display_name) . '&background=003B95&color=fff';

// ==============================
// AADHAR CHECK (OPTIONAL)
// ==============================
$has_aadhar = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>My Profile - PG Mitra</title>
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

/* Profile Header */
.profile-header {
    background: linear-gradient(135deg, #003B95 0%, #0066CC 100%);
    border-radius: 20px;
    padding: 40px 32px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.profile-header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -20%;
    width: 250px;
    height: 250px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.profile-content {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 32px;
}

.profile-image-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
}

.profile-image {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.edit-profile-btn {
    position: absolute;
    bottom: 5px;
    right: 5px;
    background: white;
    color: #003B95;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border: 2px solid white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.2s;
}

.edit-profile-btn:hover {
    transform: scale(1.1);
    background: #FFB700;
    color: #003B95;
}

.profile-info h1 {
    font-size: 28px;
    font-weight: 700;
    color: white;
    margin-bottom: 8px;
}

.profile-info .member-since {
    color: rgba(255,255,255,0.8);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.verified-badge {
    background: #10B981;
    color: white;
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
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
    width: 48px;
    height: 48px;
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
    line-height: 1.2;
}

.stat-label {
    font-size: 13px;
    color: #6B7280;
    margin-top: 6px;
}

/* Info Card */
.info-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.info-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.info-header h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1E2A3A;
}

.edit-btn {
    background: #F3F4F6;
    color: #4B5563;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.edit-btn:hover {
    background: #003B95;
    color: white;
}

.info-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid #F0F2F5;
}

.info-row:last-child {
    border-bottom: none;
}

.info-icon {
    width: 40px;
    height: 40px;
    background: #F9FAFB;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.info-icon i {
    font-size: 18px;
    color: #003B95;
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 12px;
    color: #6B7280;
    margin-bottom: 4px;
}

.info-value {
    font-size: 15px;
    font-weight: 600;
    color: #1E2A3A;
}

/* Menu Grid */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 32px;
}

.menu-item {
    background: white;
    border-radius: 16px;
    padding: 20px 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid #E5E7EB;
    position: relative;
}

.menu-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border-color: #003B95;
}

.menu-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.menu-icon.blue { background: #EFF6FF; color: #003B95; }
.menu-icon.purple { background: #F5F3FF; color: #8B5CF6; }
.menu-icon.orange { background: #FFF7ED; color: #F97316; }
.menu-icon.green { background: #ECFDF5; color: #10B981; }
.menu-icon.red { background: #FEF2F2; color: #EF4444; }

.menu-icon i {
    font-size: 24px;
}

.menu-title {
    font-size: 14px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 4px;
}

.menu-subtitle {
    font-size: 11px;
    color: #6B7280;
}

.menu-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: #EF4444;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 20px;
    min-width: 20px;
    text-align: center;
}

.menu-badge.success {
    background: #10B981;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 2000;
    align-items: center;
    justify-content: center;
}

.modal.show {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 20px;
    max-width: 500px;
    width: 90%;
    max-height: 85vh;
    overflow-y: auto;
    padding: 24px;
    animation: slideUp 0.3s ease;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #E5E7EB;
}

.modal-title {
    font-size: 20px;
    font-weight: 700;
}

.modal-close {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #F3F4F6;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.modal-close:hover {
    background: #E5E7EB;
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 6px;
    color: #374151;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #003B95;
    box-shadow: 0 0 0 3px rgba(0,59,149,0.1);
}

.form-input[readonly] {
    background: #F9FAFB;
    cursor: not-allowed;
}

.form-hint {
    font-size: 12px;
    color: #9CA3AF;
    margin-top: 4px;
}

/* Buttons */
.btn {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.btn-primary {
    background: #003B95;
    color: white;
    flex: 1;
}

.btn-primary:hover {
    background: #002E7A;
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-secondary {
    background: #F3F4F6;
    color: #4B5563;
    flex: 1;
}

.btn-secondary:hover {
    background: #E5E7EB;
}

.btn-group {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

/* Logout Modal Specific */
.logout-modal-icon {
    width: 80px;
    height: 80px;
    background: #FEF2F2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.logout-modal-icon i {
    font-size: 40px;
    color: #EF4444;
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
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 1024px) {
    .main-container {
        padding: 24px 32px;
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
    
    .profile-header {
        margin: 16px;
        padding: 24px 20px;
    }
    
    .profile-content {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }
    
    .profile-image-wrapper {
        width: 100px;
        height: 100px;
    }
    
    .profile-info h1 {
        font-size: 22px;
    }
    
    .stats-grid {
        gap: 12px;
        margin: 0 16px 20px 16px;
    }
    
    .stat-card {
        padding: 16px;
    }
    
    .stat-value {
        font-size: 22px;
    }
    
    .info-card {
        margin: 0 16px 20px 16px;
        padding: 20px;
    }
    
    .menu-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin: 0 16px 32px 16px;
    }
    
    .menu-item {
        padding: 16px;
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
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-content">
            <div class="profile-image-wrapper">
                <img src="<?php echo $profile_image; ?>" class="profile-image" id="profileImage" alt="<?php echo htmlspecialchars($display_name); ?>">
                <div class="edit-profile-btn" onclick="editProfile()">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($display_name); ?></h1>
                <div class="member-since">
                    <i class="far fa-calendar-alt"></i>
                    Member since <?php echo date('M Y', strtotime($user['created_at'])); ?>
                    <?php if($has_aadhar): ?>
                    <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-value"><?php echo $stats['total_bookings'] ?? 0; ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-value"><?php echo $stats['total_favorites'] ?? 0; ?></div>
            <div class="stat-label">Saved Rooms</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="stat-value">₹<?php echo number_format($stats['total_spent'] ?? 0); ?></div>
            <div class="stat-label">Total Spent</div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="info-card">
        <div class="info-header">
            <h3>Contact Information</h3>
            <button onclick="editContact()" class="edit-btn">
                <i class="fas fa-edit"></i> Edit
            </button>
        </div>
        
        <div class="info-row">
            <div class="info-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Email Address</div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-icon">
                <i class="fas fa-phone-alt"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Phone Number</div>
                <div class="info-value"><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not added'; ?></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="menu-grid">
        <div class="menu-item" onclick="goPage('bookings')">
            <div class="menu-icon blue">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="menu-title">My Bookings</div>
            <div class="menu-subtitle">View all bookings</div>
            <?php if(($stats['total_bookings'] ?? 0) > 0): ?>
            <span class="menu-badge"><?php echo $stats['total_bookings']; ?></span>
            <?php endif; ?>
        </div>

        <div class="menu-item" onclick="goPage('saved-rooms')">
            <div class="menu-icon purple">
                <i class="fas fa-bookmark"></i>
            </div>
            <div class="menu-title">Saved Rooms</div>
            <div class="menu-subtitle">Your favorites</div>
            <?php if(($stats['total_favorites'] ?? 0) > 0): ?>
            <span class="menu-badge"><?php echo $stats['total_favorites']; ?></span>
            <?php endif; ?>
        </div>

        <div class="menu-item" onclick="goPage('refer-earn')">
            <div class="menu-icon green">
                <i class="fas fa-gift"></i>
            </div>
            <div class="menu-title">Refer & Earn</div>
            <div class="menu-subtitle">Get ₹100</div>
        </div>

        <div class="menu-item" onclick="goPage('add-aadhar')">
            <div class="menu-icon orange">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="menu-title">Aadhar</div>
            <div class="menu-subtitle"><?php echo $has_aadhar ? 'Verified' : 'Verify now'; ?></div>
            <?php if($has_aadhar): ?>
            <span class="menu-badge success">✓</span>
            <?php endif; ?>
        </div>

        <div class="menu-item" onclick="goPage('subscription')">
            <div class="menu-icon blue">
                <i class="fas fa-crown"></i>
            </div>
            <div class="menu-title">Subscription</div>
            <div class="menu-subtitle">Manage your plan</div>
        </div>

        <div class="menu-item" onclick="goPage('support')">
            <div class="menu-icon purple">
                <i class="fas fa-headset"></i>
            </div>
            <div class="menu-title">Support</div>
            <div class="menu-subtitle">24/7 help</div>
        </div>

        <div class="menu-item" onclick="goPage('settings')">
            <div class="menu-icon orange">
                <i class="fas fa-cog"></i>
            </div>
            <div class="menu-title">Settings</div>
            <div class="menu-subtitle">Preferences</div>
        </div>

        <div class="menu-item" onclick="showLogoutModal()">
            <div class="menu-icon red">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="menu-title">Logout</div>
            <div class="menu-subtitle">Sign out</div>
        </div>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <!-- Profile Header -->
    <div class="profile-header" style="margin: 16px;">
        <div class="profile-content">
            <div class="profile-image-wrapper">
                <img src="<?php echo $profile_image; ?>" class="profile-image" id="mobileProfileImage" alt="<?php echo htmlspecialchars($display_name); ?>">
                <div class="edit-profile-btn" onclick="editProfile()">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($display_name); ?></h1>
                <div class="member-since">
                    <i class="far fa-calendar-alt"></i>
                    Since <?php echo date('M Y', strtotime($user['created_at'])); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid" style="margin: 0 16px 20px 16px;">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-value"><?php echo $stats['total_bookings'] ?? 0; ?></div>
            <div class="stat-label">Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-value"><?php echo $stats['total_favorites'] ?? 0; ?></div>
            <div class="stat-label">Saved</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="stat-value">₹<?php echo number_format($stats['total_spent'] ?? 0); ?></div>
            <div class="stat-label">Spent</div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="info-card" style="margin: 0 16px 20px 16px;">
        <div class="info-header">
            <h3>Contact Info</h3>
            <button onclick="editContact()" class="edit-btn">
                <i class="fas fa-edit"></i> Edit
            </button>
        </div>
        
        <div class="info-row">
            <div class="info-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Email</div>
                <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-icon">
                <i class="fas fa-phone-alt"></i>
            </div>
            <div class="info-content">
                <div class="info-label">Phone</div>
                <div class="info-value"><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'Not added'; ?></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions - All 8 menu items including Refer & Earn -->
    <div class="menu-grid" style="margin: 0 16px 32px 16px;">
        <div class="menu-item" onclick="goPage('bookings')">
            <div class="menu-icon blue">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="menu-title">Bookings</div>
            <div class="menu-subtitle">View all</div>
        </div>

        <div class="menu-item" onclick="goPage('saved-rooms')">
            <div class="menu-icon purple">
                <i class="fas fa-bookmark"></i>
            </div>
            <div class="menu-title">Saved</div>
            <div class="menu-subtitle">Favorites</div>
        </div>

        <!-- Refer & Earn - Now present in mobile version -->
        <div class="menu-item" onclick="goPage('refer-earn')">
            <div class="menu-icon green">
                <i class="fas fa-gift"></i>
            </div>
            <div class="menu-title">Refer & Earn</div>
            <div class="menu-subtitle">Get ₹100</div>
        </div>

        <div class="menu-item" onclick="goPage('add-aadhar')">
            <div class="menu-icon orange">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="menu-title">Aadhar</div>
            <div class="menu-subtitle">Verify</div>
        </div>

        <div class="menu-item" onclick="goPage('subscription')">
            <div class="menu-icon blue">
                <i class="fas fa-crown"></i>
            </div>
            <div class="menu-title">Subscribe</div>
            <div class="menu-subtitle">Get benefits</div>
        </div>

        <div class="menu-item" onclick="goPage('support')">
            <div class="menu-icon purple">
                <i class="fas fa-headset"></i>
            </div>
            <div class="menu-title">Support</div>
            <div class="menu-subtitle">24/7 help</div>
        </div>

        <div class="menu-item" onclick="goPage('settings')">
            <div class="menu-icon orange">
                <i class="fas fa-cog"></i>
            </div>
            <div class="menu-title">Settings</div>
            <div class="menu-subtitle">Preferences</div>
        </div>

        <div class="menu-item" onclick="showLogoutModal()">
            <div class="menu-icon red">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="menu-title">Logout</div>
            <div class="menu-subtitle">Sign out</div>
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

<!-- Edit Contact Modal -->
<div id="editContactModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Edit Contact</h2>
            <div class="modal-close" onclick="hideEditContactModal()">
                <i class="fas fa-times"></i>
            </div>
        </div>
        
        <form id="editContactForm" onsubmit="updateContact(event)">
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" 
                       class="form-input" placeholder="Enter your full name">
            </div>
            
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                       class="form-input" placeholder="Enter your email" readonly>
                <div class="form-hint">Email address cannot be changed</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                       class="form-input" placeholder="Enter your phone number" maxlength="10">
                <div class="form-hint">Enter 10 digit mobile number</div>
            </div>
            
            <div class="btn-group">
                <button type="button" class="btn btn-secondary" onclick="hideEditContactModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveContactBtn">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Logout Modal -->
<div id="logoutModal" class="modal">
    <div class="modal-content" style="max-width: 380px; text-align: center;">
        <div class="logout-modal-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h2 class="modal-title">Logout?</h2>
        <p class="text-gray-500 text-sm mb-6">Are you sure you want to logout from your account?</p>
        <div class="btn-group">
            <button class="btn btn-secondary" onclick="hideLogoutModal()">Cancel</button>
            <button class="btn btn-primary" style="background: #EF4444;" onclick="logout()">Yes, Logout</button>
        </div>
    </div>
</div>

<!-- Hidden file input for profile image upload -->
<input type="file" id="profileUpload" accept="image/*" style="display: none;" onchange="uploadProfileImage(this)">

<script>
function goPage(page) {
    window.location.href = page;
}

function goToPage(page) {
    if (page === 'home') window.location.href = '/home';
    else if (page === 'search') window.location.href = '/search';
    else if (page === 'bookings') window.location.href = '/bookings';
    else if (page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if (page === 'profile') window.location.href = '/profile';
}

// Edit Contact Modal
function editContact() {
    document.getElementById('editContactModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function hideEditContactModal() {
    document.getElementById('editContactModal').classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Update Contact
function updateContact(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('editContactForm'));
    const saveBtn = document.getElementById('saveContactBtn');
    
    const full_name = formData.get('full_name');
    const phone = formData.get('phone');
    
    if(phone && !/^\d{10}$/.test(phone)) {
        showToast('Please enter a valid 10-digit phone number', 'error');
        return;
    }
    
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
    saveBtn.disabled = true;
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/api/update-contact', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        saveBtn.innerHTML = 'Save Changes';
        saveBtn.disabled = false;
        
        if(this.status == 200) {
            try {
                const response = JSON.parse(this.responseText);
                if(response.success) {
                    showToast('Contact information updated!', 'success');
                    hideEditContactModal();
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast(response.message || 'Update failed', 'error');
                }
            } catch(e) {
                showToast('Invalid response from server', 'error');
            }
        } else {
            showToast('Server error. Please try again.', 'error');
        }
    };
    
    xhr.onerror = function() {
        saveBtn.innerHTML = 'Save Changes';
        saveBtn.disabled = false;
        showToast('Connection error. Please try again.', 'error');
    };
    
    const params = `full_name=${encodeURIComponent(full_name)}&phone=${encodeURIComponent(phone)}`;
    xhr.send(params);
}

// Profile Image Upload
function editProfile() {
    document.getElementById('profileUpload').click();
}

function uploadProfileImage(input) {
    if(input.files && input.files[0]) {
        const file = input.files[0];
        
        if(!file.type.match('image.*')) {
            showToast('Please select an image file', 'error');
            return;
        }
        
        if(file.size > 5 * 1024 * 1024) {
            showToast('File size must be less than 5MB', 'error');
            return;
        }
        
        const formData = new FormData();
        formData.append('profile_image', file);
        
        showToast('Uploading...', 'info');
        
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/upload-profile-image', true);
        xhr.onload = function() {
            if(this.status == 200) {
                try {
                    const response = JSON.parse(this.responseText);
                    if(response.success) {
                        const timestamp = new Date().getTime();
                        document.getElementById('profileImage').src = response.image_url + '?' + timestamp;
                        if(document.getElementById('mobileProfileImage')) {
                            document.getElementById('mobileProfileImage').src = response.image_url + '?' + timestamp;
                        }
                        showToast('Profile picture updated!', 'success');
                    } else {
                        showToast(response.message || 'Upload failed', 'error');
                    }
                } catch(e) {
                    showToast('Invalid response from server', 'error');
                }
            }
        };
        xhr.send(formData);
    }
}

// Logout Modal
function showLogoutModal() {
    document.getElementById('logoutModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function hideLogoutModal() {
    document.getElementById('logoutModal').classList.remove('show');
    document.body.style.overflow = 'auto';
}

function logout() {
    showToast('Logging out...', 'info');
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '/api/logout', true);
    xhr.onload = function() {
        if(this.status == 200) {
            showToast('Logged out successfully!', 'success');
            setTimeout(() => {
                window.location.href = "login";
            }, 1000);
        }
    };
    xhr.send();
}

// Toast Notification
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = 'toast';
    
    let icon = 'fa-info-circle';
    if(type === 'success') icon = 'fa-check-circle';
    if(type === 'error') icon = 'fa-exclamation-circle';
    
    toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 2000);
}

// Close modals when clicking outside
window.onclick = function(event) {
    const logoutModal = document.getElementById('logoutModal');
    const editModal = document.getElementById('editContactModal');
    
    if (event.target == logoutModal) {
        hideLogoutModal();
    }
    if (event.target == editModal) {
        hideEditContactModal();
    }
}
</script>

</body>
</html>