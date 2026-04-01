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
$user_query = "SELECT id, username, full_name, email, phone, profile_image, wallet_balance, created_at FROM users WHERE id = ?";
$stmt = $db->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$display_name = !empty($user['full_name']) ? $user['full_name'] : $user['username'];
$profile_image = !empty($user['profile_image']) ? $user['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($display_name) . '&background=003B95&color=fff';
$wallet_balance = $user['wallet_balance'] ?? 0;

// Handle messages
$message = '';
$message_type = '';
if(isset($_SESSION['settings_message'])) {
    $message = $_SESSION['settings_message'];
    $message_type = $_SESSION['settings_message_type'];
    unset($_SESSION['settings_message']);
    unset($_SESSION['settings_message_type']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Settings - PG Mitra</title>
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

/* Page Header */
.page-header {
    margin-bottom: 32px;
}

.page-header h1 {
    font-size: 28px;
    font-weight: 800;
    color: #1E2A3A;
    margin-bottom: 8px;
}

.page-header p {
    color: #6B7280;
    font-size: 14px;
}

/* Wallet Card */
.wallet-card {
    background: linear-gradient(135deg, #003B95 0%, #0066CC 100%);
    border-radius: 24px;
    padding: 24px;
    margin-bottom: 32px;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,59,149,0.2);
}

.wallet-card::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -20%;
    width: 200px;
    height: 200px;
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

.wallet-actions {
    display: flex;
    gap: 12px;
    margin-top: 20px;
    position: relative;
    z-index: 10;
}

.wallet-btn {
    flex: 1;
    background: rgba(255,255,255,0.2);
    border: none;
    padding: 10px;
    border-radius: 12px;
    color: white;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.wallet-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
}

/* Settings Sections */
.settings-section {
    margin-bottom: 32px;
}

.section-title {
    font-size: 16px;
    font-weight: 700;
    color: #6B7280;
    margin-bottom: 16px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Menu Items */
.menu-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.menu-item {
    background: white;
    border-radius: 16px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid #E5E7EB;
    text-decoration: none;
}

.menu-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border-color: #003B95;
}

.menu-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.menu-icon.blue { background: #EFF6FF; color: #003B95; }
.menu-icon.green { background: #ECFDF5; color: #10B981; }
.menu-icon.purple { background: #F5F3FF; color: #8B5CF6; }
.menu-icon.orange { background: #FFF7ED; color: #F97316; }
.menu-icon.red { background: #FEF2F2; color: #EF4444; }
.menu-icon.gray { background: #F3F4F6; color: #6B7280; }

.menu-icon i {
    font-size: 22px;
}

.menu-content {
    flex: 1;
}

.menu-title {
    font-size: 16px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 4px;
}

.menu-subtitle {
    font-size: 12px;
    color: #6B7280;
}

.menu-arrow {
    color: #9CA3AF;
    font-size: 14px;
}

/* Message Alert */
.message-alert {
    margin-bottom: 24px;
    padding: 14px 18px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.message-alert.success {
    background: #ECFDF5;
    color: #065F46;
    border: 1px solid #A7F3D0;
}

.message-alert.error {
    background: #FEF2F2;
    color: #991B1B;
    border: 1px solid #FECACA;
}

.message-alert i {
    font-size: 18px;
}

/* Mobile Wallet Card */
.mobile-wallet-card {
    background: linear-gradient(135deg, #003B95 0%, #0066CC 100%);
    border-radius: 20px;
    padding: 20px;
    margin: 16px;
    color: white;
    position: relative;
    overflow: hidden;
}

/* Modal */
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
    border-radius: 24px;
    max-width: 400px;
    width: 90%;
    padding: 24px;
    text-align: center;
    animation: slideUp 0.3s ease;
}

.modal-icon {
    width: 70px;
    height: 70px;
    background: #FEF2F2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.modal-icon i {
    font-size: 32px;
    color: #EF4444;
}

.modal-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 8px;
}

.modal-text {
    color: #6B7280;
    font-size: 14px;
    margin-bottom: 24px;
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
    <!-- Page Header -->
    <div class="page-header">
        <h1>Settings</h1>
        <p>Manage your account preferences</p>
    </div>

    <!-- Message Display -->
    <?php if($message): ?>
    <div class="message-alert <?php echo $message_type; ?>">
        <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
        <span><?php echo htmlspecialchars($message); ?></span>
    </div>
    <?php endif; ?>

    <!-- Wallet Card -->
    <div class="wallet-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="wallet-label">Wallet Balance</p>
                <p class="wallet-balance">₹<?php echo number_format($wallet_balance); ?></p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-wallet text-2xl"></i>
            </div>
        </div>
        <div class="wallet-actions">
            <button onclick="goPage('add-money')" class="wallet-btn">
                <i class="fas fa-plus"></i> Add Money
            </button>
            <button onclick="goPage('transactions')" class="wallet-btn">
                <i class="fas fa-history"></i> History
            </button>
        </div>
    </div>

    <!-- Account Settings -->
    <div class="settings-section">
        <div class="section-title">Account Settings</div>
        <div class="menu-grid">
            <div class="menu-item" onclick="goPage('change-password')">
                <div class="menu-icon blue">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="menu-content">
                    <div class="menu-title">Change Password</div>
                    <div class="menu-subtitle">Update your password regularly</div>
                </div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>

            <div class="menu-item" onclick="goPage('bank-cards')">
                <div class="menu-icon green">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="menu-content">
                    <div class="menu-title">Bank Cards</div>
                    <div class="menu-subtitle">Manage your saved cards</div>
                </div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>

            <div class="menu-item" onclick="goPage('withdraw')">
                <div class="menu-icon purple">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="menu-content">
                    <div class="menu-title">Withdraw Money</div>
                    <div class="menu-subtitle">Transfer to your bank account</div>
                </div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>
        </div>
    </div>

    <!-- Preferences -->
    <div class="settings-section">
        <div class="section-title">Preferences</div>
        <div class="menu-grid">
            <div class="menu-item" onclick="goPage('notifications')">
                <div class="menu-icon orange">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="menu-content">
                    <div class="menu-title">Notifications</div>
                    <div class="menu-subtitle">Manage your alerts</div>
                </div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>

            <div class="menu-item" onclick="goPage('privacy')">
                <div class="menu-icon gray">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="menu-content">
                    <div class="menu-title">Privacy & Security</div>
                    <div class="menu-subtitle">Control your data</div>
                </div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>
        </div>
    </div>

    <!-- Support & About -->
    <div class="settings-section">
        <div class="section-title">Support</div>
        <div class="menu-grid">
            <div class="menu-item" onclick="goPage('support')">
                <div class="menu-icon blue">
                    <i class="fas fa-headset"></i>
                </div>
                <div class="menu-content">
                    <div class="menu-title">Help & Support</div>
                    <div class="menu-subtitle">24/7 customer support</div>
                </div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>

            <div class="menu-item" onclick="goPage('about')">
                <div class="menu-icon purple">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="menu-content">
                    <div class="menu-title">About</div>
                    <div class="menu-subtitle">App version 1.0.0</div>
                </div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>

            <div class="menu-item" onclick="showLogoutModal()">
                <div class="menu-icon red">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="menu-content">
                    <div class="menu-title">Logout</div>
                    <div class="menu-subtitle">Sign out from your account</div>
                </div>
                <i class="fas fa-chevron-right menu-arrow"></i>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <!-- Mobile Wallet Card -->
    <div class="mobile-wallet-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/80 text-xs">Wallet Balance</p>
                <p class="text-white text-2xl font-bold mt-1">₹<?php echo number_format($wallet_balance); ?></p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-wallet text-xl text-white"></i>
            </div>
        </div>
        <div class="flex gap-2 mt-4">
            <button onclick="goPage('add-money')" class="flex-1 bg-white/20 py-2 rounded-xl text-sm font-semibold text-white">
                <i class="fas fa-plus mr-1"></i> Add Money
            </button>
            <button onclick="goPage('transactions')" class="flex-1 bg-white/20 py-2 rounded-xl text-sm font-semibold text-white">
                <i class="fas fa-history mr-1"></i> History
            </button>
        </div>
    </div>

    <!-- Message Display Mobile -->
    <?php if($message): ?>
    <div class="mx-4 mb-4">
        <div class="p-3 rounded-xl <?php echo $message_type == 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'; ?>">
            <div class="flex items-center gap-2 text-sm">
                <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Settings Menu Mobile -->
    <div class="px-4 py-2">
        <h3 class="font-bold text-gray-700 text-sm mb-3">ACCOUNT</h3>
        
        <div class="menu-item" onclick="goPage('change-password')">
            <div class="menu-icon blue">
                <i class="fas fa-lock"></i>
            </div>
            <div class="menu-content">
                <div class="menu-title">Change Password</div>
                <div class="menu-subtitle">Update your password</div>
            </div>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </div>

        <div class="menu-item" onclick="goPage('bank-cards')">
            <div class="menu-icon green">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="menu-content">
                <div class="menu-title">Bank Cards</div>
                <div class="menu-subtitle">Manage saved cards</div>
            </div>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </div>

        <div class="menu-item" onclick="goPage('withdraw')">
            <div class="menu-icon purple">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="menu-content">
                <div class="menu-title">Withdraw Money</div>
                <div class="menu-subtitle">Transfer to bank</div>
            </div>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </div>

        <h3 class="font-bold text-gray-700 text-sm mb-3 mt-4">PREFERENCES</h3>

        <div class="menu-item" onclick="goPage('notifications')">
            <div class="menu-icon orange">
                <i class="fas fa-bell"></i>
            </div>
            <div class="menu-content">
                <div class="menu-title">Notifications</div>
                <div class="menu-subtitle">Manage alerts</div>
            </div>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </div>

        <div class="menu-item" onclick="goPage('privacy')">
            <div class="menu-icon gray">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="menu-content">
                <div class="menu-title">Privacy & Security</div>
                <div class="menu-subtitle">Control your data</div>
            </div>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </div>

        <h3 class="font-bold text-gray-700 text-sm mb-3 mt-4">SUPPORT</h3>

        <div class="menu-item" onclick="goPage('support')">
            <div class="menu-icon blue">
                <i class="fas fa-headset"></i>
            </div>
            <div class="menu-content">
                <div class="menu-title">Help & Support</div>
                <div class="menu-subtitle">24/7 assistance</div>
            </div>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </div>

        <div class="menu-item" onclick="goPage('about')">
            <div class="menu-icon purple">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="menu-content">
                <div class="menu-title">About</div>
                <div class="menu-subtitle">Version 1.0.0</div>
            </div>
            <i class="fas fa-chevron-right menu-arrow"></i>
        </div>

        <div class="menu-item" onclick="showLogoutModal()">
            <div class="menu-icon red">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="menu-content">
                <div class="menu-title">Logout</div>
                <div class="menu-subtitle">Sign out</div>
            </div>
            <i class="fas fa-chevron-right menu-arrow"></i>
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
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        <h2 class="modal-title">Logout?</h2>
        <p class="modal-text">Are you sure you want to logout from your account?</p>
        <div class="flex gap-3">
            <button onclick="hideLogoutModal()" class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition">
                Cancel
            </button>
            <button onclick="logout()" class="flex-1 bg-red-500 text-white py-3 rounded-xl font-semibold hover:bg-red-600 transition">
                Logout
            </button>
        </div>
    </div>
</div>

<script>
function goPage(page) {
    window.location.href = '/' + page;
}

function goToPage(page) {
    if(page === 'home') window.location.href = '/home';
    else if(page === 'search') window.location.href = '/search';
    else if(page === 'bookings') window.location.href = '/bookings';
    else if(page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if(page === 'profile') window.location.href = '/profile';
    else window.location.href = '/' + page;
}

function goBack() {
    window.history.back();
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
                window.location.href = 'login';
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