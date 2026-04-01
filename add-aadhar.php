<?php
require_once 'common/auth.php';

// Ensure user is logged in
$user = requireAuth($conn);

// Direct values
$user_id = $user['id'];
$username = $user['username'];
$full_name = $user['full_name'];
$email = $user['email'];
$phone = $user['phone'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Aadhar Verification - PG Mitra</title>
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
    background: linear-gradient(135deg, #003B95 0%, #0066CC 100%);
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.page-header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -20%;
    width: 250px;
    height: 250px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.page-header-content {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    gap: 20px;
}

.page-header-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.page-header-icon i {
    font-size: 32px;
    color: white;
}

.page-header-text h1 {
    font-size: 28px;
    font-weight: 700;
    color: white;
    margin-bottom: 4px;
}

.page-header-text p {
    font-size: 14px;
    color: rgba(255,255,255,0.8);
}

/* Status Card */
.status-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    gap: 20px;
}

.status-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.status-icon.pending { background: #FEF3C7; color: #F59E0B; }
.status-icon.verified { background: #DCFCE7; color: #10B981; }
.status-icon.rejected { background: #FEE2E2; color: #EF4444; }
.status-icon.not-verified { background: #F3F4F6; color: #6B7280; }

.status-icon i {
    font-size: 32px;
}

.status-info h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 4px;
}

.status-info p {
    font-size: 13px;
    color: #6B7280;
}

/* Form Section */
.form-section {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

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

.form-input.error {
    border-color: #EF4444;
}

.form-input.success {
    border-color: #10B981;
}

.form-hint {
    font-size: 12px;
    color: #9CA3AF;
    margin-top: 6px;
}

/* Upload Box */
.upload-box {
    border: 2px dashed #E5E7EB;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #F9FAFB;
}

.upload-box:hover {
    border-color: #003B95;
    background: #EFF6FF;
}

.upload-box.has-image {
    border-color: #10B981;
    background: #F0FDF4;
    padding: 16px;
}

.upload-icon {
    width: 50px;
    height: 50px;
    background: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.upload-icon i {
    font-size: 24px;
    color: #003B95;
}

.upload-title {
    font-weight: 600;
    color: #1E2A3A;
    margin-bottom: 4px;
}

.upload-subtitle {
    font-size: 11px;
    color: #9CA3AF;
}

/* Preview Container */
.preview-container {
    display: flex;
    align-items: center;
    gap: 16px;
}

.preview-image {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.preview-info {
    flex: 1;
    text-align: left;
}

.preview-filename {
    font-weight: 600;
    font-size: 13px;
    color: #1E2A3A;
    margin-bottom: 4px;
    word-break: break-all;
}

.preview-filesize {
    font-size: 11px;
    color: #6B7280;
}

.remove-file {
    color: #EF4444;
    font-size: 11px;
    cursor: pointer;
    margin-top: 6px;
    display: inline-block;
}

.remove-file:hover {
    text-decoration: underline;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    background: #003B95;
    color: white;
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 16px;
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

/* Document Preview */
.doc-preview {
    background: #F9FAFB;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
}

.doc-preview-title {
    font-size: 14px;
    font-weight: 600;
    color: #1E2A3A;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.doc-preview-title i {
    color: #003B95;
}

.doc-images {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.doc-image {
    flex: 1;
    min-width: 120px;
    text-align: center;
}

.doc-image img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 10px;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.doc-image-label {
    font-size: 11px;
    color: #6B7280;
    margin-top: 8px;
}

/* Rejection Card */
.rejection-card {
    background: #FEF2F2;
    border: 1px solid #FECACA;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.rejection-title {
    color: #DC2626;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.rejection-reason {
    background: white;
    border-radius: 10px;
    padding: 12px;
    font-size: 13px;
    color: #374151;
    border-left: 3px solid #DC2626;
}

.rejection-date {
    font-size: 11px;
    color: #9CA3AF;
    margin-top: 8px;
    text-align: right;
}

/* Benefits Card */
.benefits-card {
    background: linear-gradient(135deg, #003B95 0%, #0066CC 100%);
    border-radius: 16px;
    padding: 24px;
    margin-top: 24px;
    color: white;
}

.benefits-card h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.benefits-list {
    list-style: none;
    padding: 0;
}

.benefits-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    font-size: 13px;
}

.benefits-list li i {
    color: #FFB700;
}

/* Loading Overlay */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 2000;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid rgba(255,255,255,0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
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
    
    .page-header {
        margin: 16px;
        padding: 20px;
    }
    
    .page-header-content {
        gap: 12px;
    }
    
    .page-header-icon {
        width: 50px;
        height: 50px;
    }
    
    .page-header-icon i {
        font-size: 24px;
    }
    
    .page-header-text h1 {
        font-size: 22px;
    }
    
    .status-card {
        margin: 0 16px 20px 16px;
        padding: 16px;
    }
    
    .status-icon {
        width: 60px;
        height: 60px;
    }
    
    .status-icon i {
        font-size: 28px;
    }
    
    .form-section {
        margin: 0 16px 20px 16px;
        padding: 20px;
    }
    
    .doc-images {
        flex-direction: column;
    }
    
    .doc-image {
        width: 100%;
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
                <?php 
                $profile_image = !empty($user['profile_image']) ? $user['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($full_name ?? $username) . '&background=003B95&color=fff';
                ?>
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
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="page-header-text">
                <h1>Aadhar Verification</h1>
                <p>Verify your identity to unlock all features</p>
            </div>
        </div>
    </div>

    <!-- Status Card (will be populated by JS) -->
    <div id="desktopStatusCard" class="status-card">
        <div class="flex items-center justify-center w-full py-4">
            <div class="w-8 h-8 border-4 border-gray-200 border-t-[#003B95] rounded-full animate-spin"></div>
        </div>
    </div>

    <!-- Main Content (will be populated by JS) -->
    <div id="desktopMainContent"></div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <!-- Page Header -->
    <div class="page-header" style="margin: 16px;">
        <div class="page-header-content">
            <div class="page-header-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="page-header-text">
                <h1>Aadhar Verification</h1>
                <p>Verify your identity</p>
            </div>
        </div>
    </div>

    <!-- Status Card (will be populated by JS) -->
    <div id="mobileStatusCard" class="status-card" style="margin: 0 16px 20px 16px;">
        <div class="flex items-center justify-center w-full py-4">
            <div class="w-8 h-8 border-4 border-gray-200 border-t-[#003B95] rounded-full animate-spin"></div>
        </div>
    </div>

    <!-- Main Content (will be populated by JS) -->
    <div id="mobileMainContent"></div>
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

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>

<script>
// Global variables
let frontFile = null;
let backFile = null;

// Load aadhar status on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAadharStatus();
});

// Load aadhar status from API
function loadAadharStatus() {
    showLoading();
    
    fetch('/api/get-aadhar-status')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                if(data.has_aadhar) {
                    if(data.data.verified === 1) {
                        showVerifiedView(data.data);
                    } else if(data.data.verified === 2) {
                        showRejectedView(data.data);
                    } else {
                        showPendingView(data.data);
                    }
                } else {
                    showFormView();
                }
            } else {
                showFormView();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showFormView();
        })
        .finally(() => {
            hideLoading();
        });
}

// Show form view
function showFormView() {
    const statusHTML = `
        <div class="status-icon not-verified">
            <i class="fas fa-id-card"></i>
        </div>
        <div class="status-info">
            <h3>Not Verified</h3>
            <p>Please submit your Aadhar details for verification</p>
            <p class="text-xs text-gray-400 mt-1">Verification takes 24-48 hours</p>
        </div>
    `;
    
    const formHTML = `
        <div class="form-section">
            <form onsubmit="submitAadhar(event)">
                <!-- Aadhar Number -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-id-card"></i>
                        Aadhar Number
                    </label>
                    <input type="text" 
                           name="aadhar_number" 
                           id="aadhar_number"
                           class="form-input" 
                           placeholder="XXXX XXXX XXXX"
                           maxlength="14"
                           required>
                    <div class="form-hint" id="aadhar_hint">Enter 12-digit Aadhar number</div>
                </div>

                <!-- Full Name -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i>
                        Full Name (as per Aadhar)
                    </label>
                    <input type="text" 
                           name="full_name" 
                           id="full_name"
                           class="form-input" 
                           placeholder="Enter your full name"
                           value="<?php echo htmlspecialchars($full_name ?? $username); ?>"
                           required>
                    <div class="form-hint">Name should exactly match your Aadhar card</div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Address (as per Aadhar)
                    </label>
                    <textarea name="address" 
                              id="address"
                              rows="3" 
                              class="form-input" 
                              placeholder="Enter your complete address"
                              required></textarea>
                    <div class="form-hint">Include house no, street, city, state, pincode</div>
                </div>

                <!-- Aadhar Front -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-image"></i>
                        Aadhar Card (Front)
                    </label>
                    <div class="upload-box" id="frontUploadBox" onclick="document.getElementById('aadhar_front').click()">
                        <input type="file" 
                               name="aadhar_front" 
                               id="aadhar_front" 
                               accept="image/jpeg,image/jpg,image/png,image/webp" 
                               style="display: none;" 
                               onchange="handleFileSelect(this, 'front')">
                        <div id="frontPreview">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="upload-title">Click to upload front side</div>
                            <div class="upload-subtitle">PNG, JPG up to 5MB</div>
                        </div>
                    </div>
                </div>

                <!-- Aadhar Back -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-image"></i>
                        Aadhar Card (Back)
                    </label>
                    <div class="upload-box" id="backUploadBox" onclick="document.getElementById('aadhar_back').click()">
                        <input type="file" 
                               name="aadhar_back" 
                               id="aadhar_back" 
                               accept="image/jpeg,image/jpg,image/png,image/webp" 
                               style="display: none;" 
                               onchange="handleFileSelect(this, 'back')">
                        <div id="backPreview">
                            <div class="upload-icon">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="upload-title">Click to upload back side</div>
                            <div class="upload-subtitle">PNG, JPG up to 5MB</div>
                        </div>
                    </div>
                </div>

                <button type="submit" id="submitBtn" class="submit-btn">
                    <i class="fas fa-file-alt"></i>
                    Submit for Verification
                </button>

                <p class="text-xs text-gray-400 text-center mt-4">
                    <i class="fas fa-lock text-green-500 mr-1"></i>
                    Your information is secure and encrypted
                </p>
            </form>
        </div>
    `;
    
    updateUI(statusHTML, formHTML);
    
    // Add Aadhar number formatting
    document.getElementById('aadhar_number')?.addEventListener('input', formatAadhar);
}

// Show pending view
function showPendingView(data) {
    const statusHTML = `
        <div class="status-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="status-info">
            <h3>Pending Verification</h3>
            <p>Your documents are being reviewed</p>
            <p class="text-xs text-gray-400 mt-1">Submitted on ${formatDate(data.submitted_at)}</p>
        </div>
    `;
    
    const contentHTML = `
        <div class="form-section">
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-5 mb-4">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fas fa-hourglass-half text-2xl text-yellow-600"></i>
                    <div>
                        <h3 class="font-bold text-gray-800">Verification in Progress</h3>
                        <p class="text-xs text-gray-500">We'll notify you once verified</p>
                    </div>
                </div>
                
                <div class="doc-preview">
                    <div class="doc-preview-title">
                        <i class="fas fa-id-card"></i>
                        Submitted Documents
                    </div>
                    <div class="doc-images">
                        <div class="doc-image">
                            <img src="${data.front_image}" alt="Aadhar Front" onerror="this.src='https://via.placeholder.com/200x150?text=No+Image'">
                            <div class="doc-image-label">Front Side</div>
                        </div>
                        <div class="doc-image">
                            <img src="${data.back_image}" alt="Aadhar Back" onerror="this.src='https://via.placeholder.com/200x150?text=No+Image'">
                            <div class="doc-image-label">Back Side</div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        Verification typically takes 24-48 hours. You'll receive a notification once completed.
                    </p>
                </div>
            </div>
        </div>
    `;
    
    updateUI(statusHTML, contentHTML);
}

// Show rejected view
function showRejectedView(data) {
    const statusHTML = `
        <div class="status-icon rejected">
            <i class="fas fa-times-circle"></i>
        </div>
        <div class="status-info">
            <h3 class="text-red-600">Verification Failed</h3>
            <p>Your Aadhar was not verified</p>
            <p class="text-xs text-gray-400 mt-1">Please check the reason and resubmit</p>
        </div>
    `;
    
    const contentHTML = `
        <div class="form-section">
            <div class="rejection-card">
                <div class="rejection-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Rejection Reason
                </div>
                <div class="rejection-reason">
                    ${escapeHtml(data.remarks || 'No specific reason provided. Please contact support.')}
                </div>
                ${data.verified_at ? `
                <div class="rejection-date">
                    <i class="far fa-calendar-alt mr-1"></i>
                    Rejected on ${formatDate(data.verified_at)}
                </div>
                ` : ''}
            </div>
            
            <div class="doc-preview">
                <div class="doc-preview-title">
                    <i class="fas fa-id-card"></i>
                    Submitted Documents
                </div>
                <div class="doc-images">
                    <div class="doc-image">
                        <img src="${data.front_image}" alt="Aadhar Front" onerror="this.src='https://via.placeholder.com/200x150?text=No+Image'">
                        <div class="doc-image-label">Front Side</div>
                    </div>
                    <div class="doc-image">
                        <img src="${data.back_image}" alt="Aadhar Back" onerror="this.src='https://via.placeholder.com/200x150?text=No+Image'">
                        <div class="doc-image-label">Back Side</div>
                    </div>
                </div>
            </div>
            
            <button onclick="resubmitAadhar()" class="submit-btn" style="background: #EF4444; margin-top: 20px;">
                <i class="fas fa-redo-alt"></i>
                Resubmit for Verification
            </button>
            
            <p class="text-xs text-gray-400 text-center mt-4">
                <i class="fas fa-info-circle mr-1"></i>
                Please correct the issues mentioned above and resubmit your Aadhar details.
            </p>
        </div>
    `;
    
    updateUI(statusHTML, contentHTML);
}

// Show verified view
function showVerifiedView(data) {
    const statusHTML = `
        <div class="status-icon verified">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="status-info">
            <h3 class="text-green-600">Verified</h3>
            <p>Your Aadhar has been verified</p>
            <p class="text-xs text-gray-400 mt-1">Verified on ${formatDate(data.verified_at)}</p>
        </div>
    `;
    
    const contentHTML = `
        <div class="form-section">
            <div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-4">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fas fa-shield-alt text-2xl text-green-600"></i>
                    <div>
                        <h3 class="font-bold text-gray-800">Verified Aadhar Details</h3>
                        <p class="text-xs text-gray-500">Your identity has been verified</p>
                    </div>
                </div>
                
                <div class="space-y-2 text-sm mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Aadhar Number:</span>
                        <span class="font-semibold">${data.aadhar_number}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Name:</span>
                        <span class="font-semibold">${escapeHtml(data.full_name)}</span>
                    </div>
                </div>
                
                <div class="doc-preview">
                    <div class="doc-preview-title">
                        <i class="fas fa-id-card"></i>
                        Verified Documents
                    </div>
                    <div class="doc-images">
                        <div class="doc-image">
                            <img src="${data.front_image}" alt="Aadhar Front">
                            <div class="doc-image-label">Front Side</div>
                        </div>
                        <div class="doc-image">
                            <img src="${data.back_image}" alt="Aadhar Back">
                            <div class="doc-image-label">Back Side</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="benefits-card">
                <h3>
                    <i class="fas fa-crown"></i>
                    Verified Benefits
                </h3>
                <ul class="benefits-list">
                    <li><i class="fas fa-check-circle"></i> Book without security deposit</li>
                    <li><i class="fas fa-check-circle"></i> Get verified tenant badge</li>
                    <li><i class="fas fa-check-circle"></i> Priority support</li>
                    <li><i class="fas fa-check-circle"></i> Special discounts on long stays</li>
                </ul>
            </div>
        </div>
    `;
    
    updateUI(statusHTML, contentHTML);
}

// Update UI for both desktop and mobile
function updateUI(statusHTML, contentHTML) {
    const desktopStatus = document.getElementById('desktopStatusCard');
    const desktopContent = document.getElementById('desktopMainContent');
    const mobileStatus = document.getElementById('mobileStatusCard');
    const mobileContent = document.getElementById('mobileMainContent');
    
    if (desktopStatus) desktopStatus.innerHTML = statusHTML;
    if (desktopContent) desktopContent.innerHTML = contentHTML;
    if (mobileStatus) mobileStatus.innerHTML = statusHTML;
    if (mobileContent) mobileContent.innerHTML = contentHTML;
}

// Resubmit function
function resubmitAadhar() {
    frontFile = null;
    backFile = null;
    showFormView();
    showToast('Please submit your Aadhar details again', 'info');
}

// Handle file selection
function handleFileSelect(input, type) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            showToast('Please select a valid image file (JPG, PNG, WEBP)', 'error');
            input.value = '';
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            showToast('File size must be less than 5MB', 'error');
            input.value = '';
            return;
        }
        
        if (type === 'front') {
            frontFile = file;
        } else {
            backFile = file;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewDiv = document.getElementById(type + 'Preview');
            const uploadBox = document.getElementById(type + 'UploadBox');
            const fileSize = (file.size / 1024).toFixed(2) + ' KB';
            
            previewDiv.innerHTML = `
                <div class="preview-container">
                    <img src="${e.target.result}" class="preview-image">
                    <div class="preview-info">
                        <div class="preview-filename">${file.name.substring(0, 25)}${file.name.length > 25 ? '...' : ''}</div>
                        <div class="preview-filesize">${fileSize}</div>
                        <span class="remove-file" onclick="removeFile('${type}')">
                            <i class="fas fa-times-circle"></i> Remove
                        </span>
                    </div>
                </div>
            `;
            uploadBox.classList.add('has-image');
        };
        reader.readAsDataURL(file);
    }
}

// Remove file
function removeFile(type) {
    const input = document.getElementById('aadhar_' + type);
    input.value = '';
    
    if (type === 'front') {
        frontFile = null;
    } else {
        backFile = null;
    }
    
    const previewDiv = document.getElementById(type + 'Preview');
    const uploadBox = document.getElementById(type + 'UploadBox');
    
    previewDiv.innerHTML = `
        <div class="upload-icon">
            <i class="fas fa-cloud-upload-alt"></i>
        </div>
        <div class="upload-title">Click to upload ${type === 'front' ? 'front' : 'back'} side</div>
        <div class="upload-subtitle">PNG, JPG up to 5MB</div>
    `;
    uploadBox.classList.remove('has-image');
}

// Format Aadhar number
function formatAadhar() {
    let value = this.value.replace(/\s+/g, '');
    if (value.length > 12) value = value.slice(0, 12);
    
    let formatted = '';
    for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 4 === 0) {
            formatted += ' ';
        }
        formatted += value[i];
    }
    this.value = formatted;
    
    const hint = document.getElementById('aadhar_hint');
    if (value.length === 12) {
        if (/^[2-9]{1}[0-9]{11}$/.test(value)) {
            this.classList.remove('error');
            this.classList.add('success');
            hint.innerHTML = '✓ Valid Aadhar number';
            hint.style.color = '#10B981';
        } else {
            this.classList.add('error');
            this.classList.remove('success');
            hint.innerHTML = '✗ Invalid Aadhar number format';
            hint.style.color = '#EF4444';
        }
    } else {
        this.classList.remove('success', 'error');
        hint.innerHTML = 'Enter 12-digit Aadhar number';
        hint.style.color = '#9CA3AF';
    }
}

// Submit form
function submitAadhar(event) {
    event.preventDefault();
    
    if (!frontFile) {
        showToast('Please upload front side of Aadhar', 'error');
        return;
    }
    
    if (!backFile) {
        showToast('Please upload back side of Aadhar', 'error');
        return;
    }
    
    const aadhar = document.getElementById('aadhar_number').value.replace(/\s+/g, '');
    if (aadhar.length !== 12 || !/^[2-9]{1}[0-9]{11}$/.test(aadhar)) {
        showToast('Please enter a valid 12-digit Aadhar number', 'error');
        document.getElementById('aadhar_number').focus();
        return;
    }
    
    const fullName = document.getElementById('full_name').value.trim();
    if (fullName.length < 3) {
        showToast('Please enter your full name', 'error');
        document.getElementById('full_name').focus();
        return;
    }
    
    const address = document.getElementById('address').value.trim();
    if (address.length < 10) {
        showToast('Please enter your complete address', 'error');
        document.getElementById('address').focus();
        return;
    }
    
    const btn = document.getElementById('submitBtn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    btn.disabled = true;
    showLoading();
    
    const formData = new FormData();
    formData.append('aadhar_number', aadhar);
    formData.append('full_name', fullName);
    formData.append('address', address);
    formData.append('aadhar_front', frontFile);
    formData.append('aadhar_back', backFile);
    
    fetch('/api/upload-aadhar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => {
                loadAadharStatus();
            }, 2000);
        } else {
            showToast(data.message, 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Network error. Please try again.', 'error');
        btn.innerHTML = originalText;
        btn.disabled = false;
    })
    .finally(() => {
        hideLoading();
    });
}

// Helper functions
function goToPage(page) {
    if (page === 'home') window.location.href = '/home';
    else if (page === 'search') window.location.href = '/search';
    else if (page === 'bookings') window.location.href = '/bookings';
    else if (page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if (page === 'profile') window.location.href = '/profile';
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    return date.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
}

function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    let icon = 'fa-info-circle';
    if(type === 'success') icon = 'fa-check-circle';
    if(type === 'error') icon = 'fa-exclamation-circle';
    
    toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}
</script>

</body>
</html>