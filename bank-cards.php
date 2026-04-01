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

$email = $user['email'];
$phone = $user['phone'] ?? '';

// Get bank cards from database
$cards_query = "SELECT * FROM bank_cards WHERE user_id = ? ORDER BY is_default DESC, created_at DESC";
$stmt = $conn->prepare($cards_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cards_result = $stmt->get_result();
$cards = [];
while($row = $cards_result->fetch_assoc()) {
    // Mask account number
    $row['account_number_masked'] = 'XXXX' . substr($row['account_number'], -4);
    $cards[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Bank Cards - PG Mitra</title>
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

/* Desktop Header - Booking.com Style */
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

/* Add Card Button */
.add-card-btn {
    background: #F9FAFB;
    border: 2px dashed #D1D5DB;
    border-radius: 16px;
    padding: 32px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 32px;
}

.add-card-btn:hover {
    border-color: #003B95;
    background: #EFF6FF;
    transform: translateY(-2px);
}

.add-card-btn i {
    font-size: 48px;
    color: #9CA3AF;
    margin-bottom: 12px;
}

.add-card-btn h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1E2A3A;
    margin-bottom: 4px;
}

.add-card-btn p {
    font-size: 13px;
    color: #6B7280;
}

/* Bank Card */
.bank-card {
    background: linear-gradient(135deg, #003B95 0%, #0066CC 100%);
    border-radius: 20px;
    padding: 24px;
    color: white;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    transition: all 0.2s;
    box-shadow: 0 10px 30px rgba(0,59,149,0.2);
}

.bank-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(0,59,149,0.25);
}

.bank-card::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.bank-card::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -20%;
    width: 150px;
    height: 150px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    position: relative;
    z-index: 10;
}

.bank-icon {
    width: 48px;
    height: 48px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(5px);
}

.bank-icon i {
    font-size: 24px;
}

.default-badge {
    background: rgba(255,255,255,0.2);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    backdrop-filter: blur(5px);
    display: flex;
    align-items: center;
    gap: 6px;
}

.account-number {
    font-size: 20px;
    letter-spacing: 2px;
    font-family: monospace;
    margin: 16px 0 12px;
    position: relative;
    z-index: 10;
}

.bank-detail {
    font-size: 13px;
    opacity: 0.9;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
    position: relative;
    z-index: 10;
}

.bank-detail i {
    width: 20px;
    font-size: 12px;
}

.card-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 20px;
    position: relative;
    z-index: 10;
}

.card-action-btn {
    background: rgba(255,255,255,0.15);
    padding: 8px 16px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    color: white;
    display: flex;
    align-items: center;
    gap: 6px;
}

.card-action-btn:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-1px);
}

.card-action-btn.delete:hover {
    background: rgba(239,68,68,0.8);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #F9FAFB;
    border-radius: 16px;
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    background: #F3F4F6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-state-icon i {
    font-size: 40px;
    color: #9CA3AF;
}

.empty-state h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 14px;
    color: #6B7280;
    margin-bottom: 24px;
}

.empty-state-btn {
    background: #003B95;
    color: white;
    padding: 12px 24px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.empty-state-btn:hover {
    background: #002E7A;
    transform: translateY(-2px);
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
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #E5E7EB;
}

.modal-title {
    font-size: 22px;
    font-weight: 700;
    color: #1E2A3A;
}

.modal-close {
    width: 36px;
    height: 36px;
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

/* Form */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #D1D5DB;
    border-radius: 12px;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #003B95;
    box-shadow: 0 0 0 3px rgba(0,59,149,0.1);
}

.form-hint {
    font-size: 11px;
    color: #9CA3AF;
    margin-top: 4px;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 20px 0;
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #003B95;
    cursor: pointer;
}

.checkbox-group label {
    font-size: 14px;
    color: #374151;
    cursor: pointer;
}

/* Buttons */
.btn {
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.btn-primary {
    background: #003B95;
    color: white;
    width: 100%;
}

.btn-primary:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
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

/* Loading Spinner */
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
    .mobile-bank-card {
        background: white;
        border-radius: 16px;
        margin: 16px;
        padding: 20px;
        border: 1px solid #E5E7EB;
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
            <div class="profile-email"><?php echo htmlspecialchars($email); ?></div>
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
            <div class="sidebar-item active" onclick="goToPage('bank-cards')">
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
            <h1>Bank Cards</h1>
            <p>Manage your withdrawal accounts for seamless transactions</p>
        </div>

        <div class="info-alert">
            <i class="fas fa-shield-alt"></i>
            <p>Your bank details are securely stored and encrypted. We only use this information for withdrawals and refunds.</p>
        </div>

        <!-- Add Card Button -->
        <div class="add-card-btn" onclick="openAddCardModal()">
            <i class="fas fa-plus-circle"></i>
            <h3>Add New Bank Card</h3>
            <p>Add your bank account for withdrawals</p>
        </div>

        <!-- Cards List -->
        <div id="cardsContainer">
            <?php if(empty($cards)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3>No Bank Cards Added</h3>
                <p>Add your first bank card to start withdrawing money</p>
                <button class="empty-state-btn" onclick="openAddCardModal()">
                    <i class="fas fa-plus mr-2"></i> Add Bank Card
                </button>
            </div>
            <?php else: ?>
                <?php foreach($cards as $card): ?>
                <div class="bank-card" data-card-id="<?php echo $card['id']; ?>">
                    <div class="card-header">
                        <div class="bank-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <?php if($card['is_default']): ?>
                        <span class="default-badge">
                            <i class="fas fa-check-circle"></i> Default
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="account-number"><?php echo htmlspecialchars($card['account_number_masked']); ?></div>
                    <div class="bank-detail">
                        <i class="fas fa-user"></i>
                        <span><?php echo htmlspecialchars($card['full_name']); ?></span>
                    </div>
                    <div class="bank-detail">
                        <i class="fas fa-building"></i>
                        <span><?php echo htmlspecialchars($card['bank_name']); ?></span>
                    </div>
                    <div class="bank-detail">
                        <i class="fas fa-code"></i>
                        <span><?php echo htmlspecialchars($card['ifsc_code']); ?></span>
                    </div>
                    <?php if(!empty($card['upi_id'])): ?>
                    <div class="bank-detail">
                        <i class="fas fa-mobile-alt"></i>
                        <span><?php echo htmlspecialchars($card['upi_id']); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="card-actions">
                        <?php if(!$card['is_default']): ?>
                        <button class="card-action-btn" onclick="setDefault(<?php echo $card['id']; ?>)">
                            <i class="fas fa-star"></i> Set Default
                        </button>
                        <?php endif; ?>
                        <button class="card-action-btn delete" onclick="deleteCard(<?php echo $card['id']; ?>)">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <!-- Mobile Add Card Button -->
    <div class="add-card-btn" style="margin: 16px;" onclick="openAddCardModal()">
        <i class="fas fa-plus-circle"></i>
        <h3>Add New Bank Card</h3>
        <p>Add your bank account for withdrawals</p>
    </div>

    <!-- Mobile Cards List -->
    <div id="mobileCardsContainer">
        <?php if(empty($cards)): ?>
        <div class="empty-state" style="margin: 16px;">
            <div class="empty-state-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <h3>No Bank Cards Added</h3>
            <p>Add your first bank card to start withdrawing money</p>
            <button class="empty-state-btn" onclick="openAddCardModal()">
                <i class="fas fa-plus mr-2"></i> Add Bank Card
            </button>
        </div>
        <?php else: ?>
            <?php foreach($cards as $card): ?>
            <div class="bank-card" data-card-id="<?php echo $card['id']; ?>" style="margin: 0 16px 16px 16px;">
                <div class="card-header">
                    <div class="bank-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <?php if($card['is_default']): ?>
                    <span class="default-badge">
                        <i class="fas fa-check-circle"></i> Default
                    </span>
                    <?php endif; ?>
                </div>
                <div class="account-number"><?php echo htmlspecialchars($card['account_number_masked']); ?></div>
                <div class="bank-detail">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($card['full_name']); ?></span>
                </div>
                <div class="bank-detail">
                    <i class="fas fa-building"></i>
                    <span><?php echo htmlspecialchars($card['bank_name']); ?></span>
                </div>
                <div class="bank-detail">
                    <i class="fas fa-code"></i>
                    <span><?php echo htmlspecialchars($card['ifsc_code']); ?></span>
                </div>
                <?php if(!empty($card['upi_id'])): ?>
                <div class="bank-detail">
                    <i class="fas fa-mobile-alt"></i>
                    <span><?php echo htmlspecialchars($card['upi_id']); ?></span>
                </div>
                <?php endif; ?>
                <div class="card-actions">
                    <?php if(!$card['is_default']): ?>
                    <button class="card-action-btn" onclick="setDefault(<?php echo $card['id']; ?>)">
                        <i class="fas fa-star"></i> Set Default
                    </button>
                    <?php endif; ?>
                    <button class="card-action-btn delete" onclick="deleteCard(<?php echo $card['id']; ?>)">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
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

<!-- Add/Edit Card Modal -->
<div id="cardModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">Add Bank Card</h2>
            <div class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </div>
        </div>

        <form id="cardForm" onsubmit="saveCard(event)">
            <input type="hidden" id="cardId" value="">
            
            <div class="form-group">
                <label class="form-label">Full Name (as per bank)</label>
                <input type="text" id="fullName" class="form-input" placeholder="Enter your full name" value="<?php echo htmlspecialchars($display_name); ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" id="phone" class="form-input" placeholder="10-digit mobile number" maxlength="10" value="<?php echo htmlspecialchars($phone); ?>" required>
                <div class="form-hint">Enter 10-digit mobile number linked to bank</div>
            </div>

            <div class="form-group">
                <label class="form-label">UPI ID (Optional)</label>
                <input type="text" id="upiId" class="form-input" placeholder="e.g., name@okhdfcbank">
                <div class="form-hint">Your UPI ID for faster withdrawals</div>
            </div>

            <div class="form-group">
                <label class="form-label">Bank Name</label>
                <input type="text" id="bankName" class="form-input" placeholder="e.g., State Bank of India" required>
            </div>

            <div class="form-group">
                <label class="form-label">IFSC Code</label>
                <input type="text" id="ifscCode" class="form-input" placeholder="e.g., SBIN0001234" maxlength="11" style="text-transform:uppercase" required>
                <div class="form-hint">11-character IFSC code</div>
            </div>

            <div class="form-group">
                <label class="form-label">Account Number</label>
                <input type="text" id="accountNumber" class="form-input" placeholder="Enter account number" required>
                <div class="form-hint">9-18 digit account number</div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="isDefault">
                <label for="isDefault">Set as default withdrawal account</label>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                <span id="btnText">Save Card</span>
            </button>
        </form>
    </div>
</div>

<!-- Logout Modal -->
<div id="logoutModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 380px; text-align: center;">
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

// Open modal to add card
function openAddCardModal() {
    document.getElementById('modalTitle').textContent = 'Add Bank Card';
    document.getElementById('cardForm').reset();
    document.getElementById('cardId').value = '';
    document.getElementById('btnText').textContent = 'Save Card';
    document.getElementById('cardModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

// Close modal
function closeModal() {
    document.getElementById('cardModal').classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Save card (Add/Edit)
function saveCard(event) {
    event.preventDefault();
    
    const cardId = document.getElementById('cardId').value;
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    
    // Validate phone
    const phone = document.getElementById('phone').value;
    if(!/^\d{10}$/.test(phone)) {
        showToast('Please enter a valid 10-digit phone number', 'error');
        return;
    }
    
    // Validate IFSC
    const ifsc = document.getElementById('ifscCode').value.toUpperCase();
    if(!/^[A-Z]{4}0[A-Z0-9]{6}$/.test(ifsc)) {
        showToast('Please enter a valid IFSC code', 'error');
        return;
    }
    
    // Validate account number
    const account = document.getElementById('accountNumber').value.replace(/\s+/g, '');
    if(!/^\d{9,18}$/.test(account)) {
        showToast('Please enter a valid account number (9-18 digits)', 'error');
        return;
    }
    
    // Validate bank name
    if(!document.getElementById('bankName').value.trim()) {
        showToast('Please enter bank name', 'error');
        return;
    }
    
    // Validate full name
    if(!document.getElementById('fullName').value.trim()) {
        showToast('Please enter full name', 'error');
        return;
    }
    
    // Show loading
    submitBtn.disabled = true;
    btnText.innerHTML = '<span class="spinner"></span> Saving...';
    
    const data = {
        full_name: document.getElementById('fullName').value,
        phone: phone,
        upi_id: document.getElementById('upiId').value,
        bank_name: document.getElementById('bankName').value,
        ifsc_code: ifsc,
        account_number: account,
        is_default: document.getElementById('isDefault').checked
    };
    
    if(cardId) {
        data.card_id = parseInt(cardId);
    }
    
    const url = '/api/bank-card';
    const method = cardId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        submitBtn.disabled = false;
        btnText.textContent = cardId ? 'Update Card' : 'Save Card';
        
        if(result.success) {
            showToast(result.message, 'success');
            closeModal();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast(result.message || 'Failed to save card', 'error');
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        btnText.textContent = cardId ? 'Update Card' : 'Save Card';
        showToast('Connection error. Please try again.', 'error');
        console.error('Error:', error);
    });
}

// Set card as default
function setDefault(cardId) {
    if(!confirm('Set this as your default withdrawal account?')) return;
    
    fetch('/api/bank-card', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            card_id: cardId,
            is_default: true
        })
    })
    .then(response => response.json())
    .then(result => {
        if(result.success) {
            showToast('Default card updated', 'success');
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            showToast(result.message || 'Failed to update', 'error');
        }
    })
    .catch(error => {
        showToast('Connection error', 'error');
    });
}

// Delete card
function deleteCard(cardId) {
    if(!confirm('Are you sure you want to delete this card?')) return;
    
    fetch(`/api/bank-card?id=${cardId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(result => {
        if(result.success) {
            showToast('Card deleted successfully', 'success');
            setTimeout(() => {
                location.reload();
            }, 500);
        } else {
            showToast(result.message || 'Failed to delete', 'error');
        }
    })
    .catch(error => {
        showToast('Connection error', 'error');
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
    const modal = document.getElementById('cardModal');
    const logoutModal = document.getElementById('logoutModal');
    if (event.target == modal) {
        closeModal();
    }
    if (event.target == logoutModal) {
        hideLogoutModal();
    }
}

// Auto-uppercase IFSC
document.getElementById('ifscCode')?.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>

</body>
</html>
