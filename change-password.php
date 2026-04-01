<?php
require_once 'common/auth.php';

// Ensure user is logged in
$user = requireAuth($conn);

// Direct values
$user_id = $user['id'];

$display_name = !empty($user['full_name']) 
    ? $user['full_name'] 
    : $user['username'];

$profile_image = !empty($user['profile_image']) 
    ? $user['profile_image'] 
    : 'https://ui-avatars.com/api/?name=' . urlencode($display_name) . '&background=003B95&color=fff';

// Handle messages from session
$message = '';
$message_type = '';
if(isset($_SESSION['password_message'])) {
    $message = $_SESSION['password_message'];
    $message_type = $_SESSION['password_message_type'];
    unset($_SESSION['password_message']);
    unset($_SESSION['password_message_type']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Change Password - PG Mitra</title>
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

/* Form Styles */
.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-label i {
    margin-right: 6px;
    color: #003B95;
}

.password-input-wrapper {
    position: relative;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    padding-right: 48px;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    transition: all 0.2s;
    background: white;
}

.form-input:focus {
    outline: none;
    border-color: #003B95;
    box-shadow: 0 0 0 3px rgba(0,59,149,0.1);
}

.form-input.error {
    border-color: #EF4444;
}

.toggle-password {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #9CA3AF;
    transition: color 0.2s;
    background: white;
    padding: 4px;
}

.toggle-password:hover {
    color: #003B95;
}

/* Password Strength */
.password-strength {
    margin-top: 8px;
}

.strength-bar {
    height: 4px;
    background: #E5E7EB;
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 6px;
}

.strength-progress {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.strength-text {
    font-size: 11px;
    color: #6B7280;
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

/* Validation List */
.validation-list {
    background: #F9FAFB;
    border-radius: 12px;
    padding: 16px;
    margin-top: 16px;
    border: 1px solid #E5E7EB;
}

.validation-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: #6B7280;
    margin-bottom: 10px;
}

.validation-item:last-child {
    margin-bottom: 0;
}

.validation-item i {
    width: 18px;
    font-size: 12px;
}

.validation-item.valid {
    color: #10B981;
}

.validation-item.valid i {
    color: #10B981;
}

.match-text {
    font-size: 11px;
    margin-top: 6px;
    display: block;
}

/* Message Alert */
.message-alert {
    margin-bottom: 24px;
    padding: 14px 18px;
    border-radius: 8px;
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

/* Buttons */
.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    font-family: 'Inter', sans-serif;
}

.btn-primary {
    background: #003B95;
    color: white;
    width: 100%;
}

.btn-primary:hover {
    background: #002E7A;
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Spinner */
.spinner {
    display: inline-block;
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 0.8s linear infinite;
    margin-right: 8px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
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

.toast.success {
    background: #10B981;
}

.toast.error {
    background: #EF4444;
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
    
    /* Mobile Card Styles */
    .mobile-password-card {
        background: white;
        border-radius: 16px;
        margin: 16px;
        padding: 24px;
        border: 1px solid #E5E7EB;
    }
    
    .mobile-password-icon {
        width: 60px;
        height: 60px;
        background: #EFF6FF;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }
    
    .mobile-password-icon i {
        font-size: 28px;
        color: #003B95;
    }
    
    .mobile-password-title {
        text-align: center;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 8px;
    }
    
    .mobile-password-subtitle {
        text-align: center;
        font-size: 13px;
        color: #6B7280;
        margin-bottom: 24px;
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
            <div class="sidebar-item active" onclick="goToPage('change-password')">
                <i class="fas fa-lock"></i>
                <span>Change Password</span>
            </div>
            <div class="sidebar-item" onclick="goToPage('bank-cards')">
                <i class="fas fa-credit-card"></i>
                <span>Bank Cards</span>
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
            <h1>Change Password</h1>
            <p>Update your password to keep your account secure</p>
        </div>

        <!-- Message Display -->
        <?php if($message): ?>
        <div class="message-alert <?php echo $message_type; ?>">
            <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
        <?php endif; ?>

        <div class="info-alert">
            <i class="fas fa-shield-alt"></i>
            <p>Choose a strong password that you don't use elsewhere. A strong password contains at least 8 characters with uppercase, lowercase, and numbers.</p>
        </div>

        <form id="passwordForm" onsubmit="changePassword(event)">
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-key"></i> Current Password
                </label>
                <div class="password-input-wrapper">
                    <input type="password" id="currentPassword" class="form-input" placeholder="Enter your current password" required>
                    <span class="toggle-password" onclick="togglePassword('currentPassword')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-lock"></i> New Password
                </label>
                <div class="password-input-wrapper">
                    <input type="password" id="newPassword" class="form-input" placeholder="Enter new password" onkeyup="checkPasswordStrength()" required>
                    <span class="toggle-password" onclick="togglePassword('newPassword')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="password-strength">
                    <div class="strength-bar">
                        <div class="strength-progress" id="strengthProgress"></div>
                    </div>
                    <div class="strength-text" id="strengthText"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-check-circle"></i> Confirm New Password
                </label>
                <div class="password-input-wrapper">
                    <input type="password" id="confirmPassword" class="form-input" placeholder="Confirm your new password" onkeyup="checkPasswordMatch()" required>
                    <span class="toggle-password" onclick="togglePassword('confirmPassword')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <span class="match-text" id="matchText"></span>
            </div>

            <!-- Password Requirements -->
            <div class="validation-list">
                <div class="validation-item" id="reqLength">
                    <i class="far fa-circle"></i>
                    <span>At least 6 characters</span>
                </div>
                <div class="validation-item" id="reqUppercase">
                    <i class="far fa-circle"></i>
                    <span>At least one uppercase letter (A-Z)</span>
                </div>
                <div class="validation-item" id="reqLowercase">
                    <i class="far fa-circle"></i>
                    <span>At least one lowercase letter (a-z)</span>
                </div>
                <div class="validation-item" id="reqNumber">
                    <i class="far fa-circle"></i>
                    <span>At least one number (0-9)</span>
                </div>
                <div class="validation-item" id="reqMatch">
                    <i class="far fa-circle"></i>
                    <span>Passwords match</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                <span id="btnText">Update Password</span>
            </button>
        </form>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <div class="mobile-password-card">
        <div class="mobile-password-icon">
            <i class="fas fa-lock"></i>
        </div>
        <div class="mobile-password-title">Change Password</div>
        <div class="mobile-password-subtitle">Keep your account secure</div>

        <?php if($message): ?>
        <div class="message-alert <?php echo $message_type; ?>" style="margin-bottom: 20px;">
            <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
        <?php endif; ?>

        <div class="info-alert" style="margin-bottom: 20px;">
            <i class="fas fa-shield-alt"></i>
            <p>Choose a strong password with uppercase, lowercase, and numbers</p>
        </div>

        <form id="mobilePasswordForm" onsubmit="changePassword(event)">
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <div class="password-input-wrapper">
                    <input type="password" id="mobileCurrentPassword" class="form-input" placeholder="Enter current password" required>
                    <span class="toggle-password" onclick="togglePassword('mobileCurrentPassword')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">New Password</label>
                <div class="password-input-wrapper">
                    <input type="password" id="mobileNewPassword" class="form-input" placeholder="Enter new password" onkeyup="checkMobilePasswordStrength()" required>
                    <span class="toggle-password" onclick="togglePassword('mobileNewPassword')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div class="password-strength">
                    <div class="strength-bar">
                        <div class="strength-progress" id="mobileStrengthProgress"></div>
                    </div>
                    <div class="strength-text" id="mobileStrengthText"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <div class="password-input-wrapper">
                    <input type="password" id="mobileConfirmPassword" class="form-input" placeholder="Confirm new password" onkeyup="checkMobilePasswordMatch()" required>
                    <span class="toggle-password" onclick="togglePassword('mobileConfirmPassword')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <span class="match-text" id="mobileMatchText"></span>
            </div>

            <div class="validation-list">
                <div class="validation-item" id="mobileReqLength">
                    <i class="far fa-circle"></i>
                    <span>At least 6 characters</span>
                </div>
                <div class="validation-item" id="mobileReqUppercase">
                    <i class="far fa-circle"></i>
                    <span>At least one uppercase letter</span>
                </div>
                <div class="validation-item" id="mobileReqLowercase">
                    <i class="far fa-circle"></i>
                    <span>At least one lowercase letter</span>
                </div>
                <div class="validation-item" id="mobileReqNumber">
                    <i class="far fa-circle"></i>
                    <span>At least one number</span>
                </div>
                <div class="validation-item" id="mobileReqMatch">
                    <i class="far fa-circle"></i>
                    <span>Passwords match</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="mobileSubmitBtn">
                <span id="mobileBtnText">Update Password</span>
            </button>
        </form>
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
    else if(page === 'settings') window.location.href = '/settings';
    else window.location.href = '/' + page;
}

// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.parentElement.querySelector('.toggle-password i');
    
    if(input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Set validation state
function setValidationState(id, isValid, text) {
    const el = document.getElementById(id);
    if(el) {
        if(isValid) {
            el.classList.add('valid');
            el.innerHTML = `<i class="fas fa-check-circle"></i><span>${text}</span>`;
        } else {
            el.classList.remove('valid');
            el.innerHTML = `<i class="far fa-circle"></i><span>${text}</span>`;
        }
    }
}

// Check password strength (Desktop)
function checkPasswordStrength() {
    const password = document.getElementById('newPassword').value;
    updatePasswordStrength(password, 'strengthProgress', 'strengthText');
    updateRequirements(password);
    checkPasswordMatch();
}

// Check password strength (Mobile)
function checkMobilePasswordStrength() {
    const password = document.getElementById('mobileNewPassword').value;
    updatePasswordStrength(password, 'mobileStrengthProgress', 'mobileStrengthText');
    updateMobileRequirements(password);
    checkMobilePasswordMatch();
}

// Update password strength UI
function updatePasswordStrength(password, progressId, textId) {
    const strength = calculatePasswordStrength(password);
    const progressBar = document.getElementById(progressId);
    const strengthText = document.getElementById(textId);
    
    if(password.length === 0) {
        if(progressBar) progressBar.style.width = '0%';
        if(strengthText) strengthText.textContent = '';
        return;
    }
    
    let width, color, text;
    if(strength < 2) {
        width = '25%';
        color = '#EF4444';
        text = 'Weak';
    } else if(strength < 4) {
        width = '50%';
        color = '#F97316';
        text = 'Fair';
    } else if(strength < 5) {
        width = '75%';
        color = '#FFB700';
        text = 'Good';
    } else {
        width = '100%';
        color = '#10B981';
        text = 'Strong';
    }
    
    if(progressBar) {
        progressBar.style.width = width;
        progressBar.style.background = color;
    }
    if(strengthText) {
        strengthText.textContent = text;
        strengthText.style.color = color;
    }
}

// Calculate password strength
function calculatePasswordStrength(password) {
    let strength = 0;
    if(password.length >= 6) strength++;
    if(password.length >= 8) strength++;
    if(/[A-Z]/.test(password)) strength++;
    if(/[a-z]/.test(password)) strength++;
    if(/[0-9]/.test(password)) strength++;
    if(/[!@#$%^&*]/.test(password)) strength++;
    return strength;
}

// Update password requirements (Desktop)
function updateRequirements(password) {
    const requirements = {
        reqLength: password.length >= 6,
        reqUppercase: /[A-Z]/.test(password),
        reqLowercase: /[a-z]/.test(password),
        reqNumber: /[0-9]/.test(password)
    };
    
    for(const [id, isValid] of Object.entries(requirements)) {
        let text = '';
        if(id === 'reqLength') text = 'At least 6 characters';
        if(id === 'reqUppercase') text = 'At least one uppercase letter (A-Z)';
        if(id === 'reqLowercase') text = 'At least one lowercase letter (a-z)';
        if(id === 'reqNumber') text = 'At least one number (0-9)';
        setValidationState(id, isValid, text);
    }
}

// Update mobile requirements
function updateMobileRequirements(password) {
    const requirements = {
        mobileReqLength: password.length >= 6,
        mobileReqUppercase: /[A-Z]/.test(password),
        mobileReqLowercase: /[a-z]/.test(password),
        mobileReqNumber: /[0-9]/.test(password)
    };
    
    for(const [id, isValid] of Object.entries(requirements)) {
        let text = '';
        if(id === 'mobileReqLength') text = 'At least 6 characters';
        if(id === 'mobileReqUppercase') text = 'At least one uppercase letter';
        if(id === 'mobileReqLowercase') text = 'At least one lowercase letter';
        if(id === 'mobileReqNumber') text = 'At least one number';
        setValidationState(id, isValid, text);
    }
}

// Check password match (Desktop)
function checkPasswordMatch() {
    const newPass = document.getElementById('newPassword').value;
    const confirmPass = document.getElementById('confirmPassword').value;
    const matchText = document.getElementById('matchText');
    const isValid = confirmPass.length > 0 && newPass === confirmPass;
    
    if(confirmPass.length === 0) {
        if(matchText) matchText.textContent = '';
        setValidationState('reqMatch', false, 'Passwords match');
        return;
    }
    
    if(newPass === confirmPass) {
        if(matchText) {
            matchText.textContent = '✓ Passwords match';
            matchText.style.color = '#10B981';
        }
        setValidationState('reqMatch', true, 'Passwords match');
    } else {
        if(matchText) {
            matchText.textContent = '✗ Passwords do not match';
            matchText.style.color = '#EF4444';
        }
        setValidationState('reqMatch', false, 'Passwords match');
    }
}

// Check password match (Mobile)
function checkMobilePasswordMatch() {
    const newPass = document.getElementById('mobileNewPassword').value;
    const confirmPass = document.getElementById('mobileConfirmPassword').value;
    const matchText = document.getElementById('mobileMatchText');
    const isValid = confirmPass.length > 0 && newPass === confirmPass;
    
    if(confirmPass.length === 0) {
        if(matchText) matchText.textContent = '';
        setValidationState('mobileReqMatch', false, 'Passwords match');
        return;
    }
    
    if(newPass === confirmPass) {
        if(matchText) {
            matchText.textContent = '✓ Passwords match';
            matchText.style.color = '#10B981';
        }
        setValidationState('mobileReqMatch', true, 'Passwords match');
    } else {
        if(matchText) {
            matchText.textContent = '✗ Passwords do not match';
            matchText.style.color = '#EF4444';
        }
        setValidationState('mobileReqMatch', false, 'Passwords match');
    }
}

// Validate password
function validatePassword(password) {
    if(password.length < 6) {
        return { valid: false, message: 'Password must be at least 6 characters long' };
    }
    if(!/[A-Z]/.test(password)) {
        return { valid: false, message: 'Password must contain at least one uppercase letter' };
    }
    if(!/[a-z]/.test(password)) {
        return { valid: false, message: 'Password must contain at least one lowercase letter' };
    }
    if(!/[0-9]/.test(password)) {
        return { valid: false, message: 'Password must contain at least one number' };
    }
    return { valid: true, message: '' };
}

// Change password API call
function changePassword(event) {
    event.preventDefault();
    
    // Get values from active form (desktop or mobile)
    const isMobile = window.innerWidth <= 768;
    const currentPassword = isMobile ? document.getElementById('mobileCurrentPassword').value : document.getElementById('currentPassword').value;
    const newPassword = isMobile ? document.getElementById('mobileNewPassword').value : document.getElementById('newPassword').value;
    const confirmPassword = isMobile ? document.getElementById('mobileConfirmPassword').value : document.getElementById('confirmPassword').value;
    
    const submitBtn = isMobile ? document.getElementById('mobileSubmitBtn') : document.getElementById('submitBtn');
    const btnText = isMobile ? document.getElementById('mobileBtnText') : document.getElementById('btnText');
    
    // Validate
    if(!currentPassword) {
        showToast('Please enter current password', 'error');
        return;
    }
    
    if(!newPassword) {
        showToast('Please enter new password', 'error');
        return;
    }
    
    if(!confirmPassword) {
        showToast('Please confirm your new password', 'error');
        return;
    }
    
    if(newPassword !== confirmPassword) {
        showToast('New passwords do not match', 'error');
        return;
    }
    
    const validation = validatePassword(newPassword);
    if(!validation.valid) {
        showToast(validation.message, 'error');
        return;
    }
    
    if(newPassword === currentPassword) {
        showToast('New password must be different from current password', 'error');
        return;
    }
    
    // Show loading
    submitBtn.disabled = true;
    btnText.innerHTML = '<span class="spinner"></span> Updating...';
    
    // Make API call
    fetch('/api/change-password', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            current_password: currentPassword,
            new_password: newPassword,
            confirm_password: confirmPassword
        })
    })
    .then(response => response.json())
    .then(result => {
        submitBtn.disabled = false;
        btnText.innerHTML = 'Update Password';
        
        if(result.success) {
            showToast(result.message, 'success');
            // Clear form
            if(isMobile) {
                document.getElementById('mobileCurrentPassword').value = '';
                document.getElementById('mobileNewPassword').value = '';
                document.getElementById('mobileConfirmPassword').value = '';
                document.getElementById('mobileStrengthProgress').style.width = '0%';
                document.getElementById('mobileStrengthText').textContent = '';
                document.getElementById('mobileMatchText').textContent = '';
                updateMobileRequirements('');
            } else {
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmPassword').value = '';
                document.getElementById('strengthProgress').style.width = '0%';
                document.getElementById('strengthText').textContent = '';
                document.getElementById('matchText').textContent = '';
                updateRequirements('');
            }
            
            // Reset validation states
            const reqIds = isMobile ? 
                ['mobileReqLength', 'mobileReqUppercase', 'mobileReqLowercase', 'mobileReqNumber', 'mobileReqMatch'] :
                ['reqLength', 'reqUppercase', 'reqLowercase', 'reqNumber', 'reqMatch'];
            
            reqIds.forEach(id => {
                let text = '';
                if(id.includes('Length')) text = 'At least 6 characters';
                else if(id.includes('Uppercase')) text = 'At least one uppercase letter';
                else if(id.includes('Lowercase')) text = 'At least one lowercase letter';
                else if(id.includes('Number')) text = 'At least one number';
                else if(id.includes('Match')) text = 'Passwords match';
                setValidationState(id, false, text);
            });
            
            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = '/settings';
            }, 2000);
        } else {
            showToast(result.message, 'error');
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        btnText.innerHTML = 'Update Password';
        showToast('Connection error. Please try again.', 'error');
        console.error('Error:', error);
    });
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
    toast.className = 'toast ' + type;
    
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