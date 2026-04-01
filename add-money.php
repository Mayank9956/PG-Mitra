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

$wallet_balance = $user['wallet_balance'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Add Money - PG Mitra</title>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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

/* Wallet Card */
.wallet-card {
    background: linear-gradient(135deg, #003B95 0%, #4F46E5 100%);
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

/* Amount Presets */
.amount-presets {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

.preset-btn {
    background: white;
    border: 2px solid #E5E7EB;
    border-radius: 16px;
    padding: 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}

.preset-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    border-color: #003B95;
}

.preset-btn.active {
    border-color: #003B95;
    background: #EFF6FF;
}

.preset-amount {
    font-size: 20px;
    font-weight: 700;
    color: #1E2A3A;
}

.preset-label {
    font-size: 11px;
    color: #6B7280;
    margin-top: 4px;
}

/* Custom Amount */
.custom-amount {
    margin-bottom: 32px;
}

.amount-input-group {
    position: relative;
    display: flex;
    align-items: center;
}

.amount-symbol {
    position: absolute;
    left: 16px;
    font-size: 20px;
    font-weight: 600;
    color: #6B7280;
}

.amount-input {
    width: 100%;
    padding: 16px 16px 16px 45px;
    border: 2px solid #E5E7EB;
    border-radius: 16px;
    font-size: 18px;
    font-weight: 500;
    transition: all 0.2s;
}

.amount-input:focus {
    outline: none;
    border-color: #003B95;
    box-shadow: 0 0 0 3px rgba(0,59,149,0.1);
}

/* Submit Button */
.submit-btn {
    width: 100%;
    background: #003B95;
    color: white;
    padding: 16px;
    border: none;
    border-radius: 16px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 32px;
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

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Recent Transactions */
.recent-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-title {
    font-size: 18px;
    font-weight: 700;
    color: #1E2A3A;
}

.view-all {
    font-size: 13px;
    color: #003B95;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
}

.view-all:hover {
    text-decoration: underline;
}

.recent-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #F0F2F5;
}

.recent-item:last-child {
    border-bottom: none;
}

.recent-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.recent-icon i {
    font-size: 20px;
}

.recent-icon.success { background: #DCFCE7; color: #166534; }
.recent-icon.pending { background: #FEF3C7; color: #92400E; }
.recent-icon.failed { background: #FEE2E2; color: #991B1B; }

.recent-details {
    flex: 1;
}

.recent-amount {
    font-size: 16px;
    font-weight: 700;
    color: #1E2A3A;
}

.recent-amount.credit { color: #10B981; }
.recent-amount.debit { color: #EF4444; }

.recent-meta {
    font-size: 11px;
    color: #9CA3AF;
    margin-top: 2px;
}

.recent-status {
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.recent-status.success { background: #DCFCE7; color: #166534; }
.recent-status.pending { background: #FEF3C7; color: #92400E; }
.recent-status.failed { background: #FEE2E2; color: #991B1B; }

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
    
    .wallet-card {
        margin: 0 16px 20px 16px;
        padding: 20px;
    }
    
    .wallet-balance {
        font-size: 28px;
    }
    
    .amount-presets {
        gap: 12px;
        padding: 0 16px;
    }
    
    .preset-btn {
        padding: 12px;
    }
    
    .preset-amount {
        font-size: 16px;
    }
    
    .custom-amount {
        padding: 0 16px;
    }
    
    .submit-btn {
        width: calc(100% - 32px);
        margin: 0 16px 20px 16px;
    }
    
    .recent-section {
        margin: 0 16px 20px 16px;
        padding: 20px;
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
            <a href="/"><i class="fas fa-home"></i> Home</a>
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
                <h1>Add Money to Wallet</h1>
                <p>Add funds for quick and easy payments</p>
            </div>
            <div class="hero-icon">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>

    <!-- Wallet Card -->
    <div class="wallet-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="wallet-label">Current Balance</p>
                <p class="wallet-balance" id="walletBalance">₹<?php echo number_format($wallet_balance); ?></p>
            </div>
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-wallet text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Amount Presets -->
    <div class="amount-presets">
        <div class="preset-btn" onclick="selectAmount(100, this)">
            <div class="preset-amount">₹100</div>
            <div class="preset-label">Quick add</div>
        </div>
        <div class="preset-btn" onclick="selectAmount(500, this)">
            <div class="preset-amount">₹500</div>
            <div class="preset-label">Popular</div>
        </div>
        <div class="preset-btn" onclick="selectAmount(1000, this)">
            <div class="preset-amount">₹1,000</div>
            <div class="preset-label">Best value</div>
        </div>
        <div class="preset-btn" onclick="selectAmount(2000, this)">
            <div class="preset-amount">₹2,000</div>
            <div class="preset-label">Monthly rent</div>
        </div>
        <div class="preset-btn" onclick="selectAmount(5000, this)">
            <div class="preset-amount">₹5,000</div>
            <div class="preset-label">+ Bonus</div>
        </div>
        <div class="preset-btn" onclick="selectAmount(10000, this)">
            <div class="preset-amount">₹10,000</div>
            <div class="preset-label">Save more</div>
        </div>
    </div>

    <!-- Custom Amount -->
    <div class="custom-amount">
        <div class="amount-input-group">
            <span class="amount-symbol">₹</span>
            <input type="number" id="customAmount" class="amount-input" placeholder="Enter amount" min="100" max="50000" step="1">
        </div>
        <p class="text-xs text-gray-400 mt-2">Min: ₹100 | Max: ₹50,000 per transaction</p>
    </div>

    <!-- Submit Button -->
    <button class="submit-btn" id="addMoneyBtn" onclick="addMoney()">
        <i class="fas fa-wallet"></i>
        Add Money
    </button>

    <!-- Recent Transactions -->
    <div class="recent-section">
        <div class="section-header">
            <h3 class="section-title">Recent Transactions</h3>
            <a href="/transactions" class="view-all">View all <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div id="recentTransactions">
            <div class="text-center py-8" id="loadingRecent">
                <div class="empty-icon mx-auto">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <p class="text-sm text-gray-500">Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <!-- Hero Section -->
    <div class="hero-section" style="margin: 16px;">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Add Money</h1>
                <p>Quick wallet recharge</p>
            </div>
            <div class="hero-icon">
                <i class="fas fa-wallet"></i>
            </div>
        </div>
    </div>

    <!-- Wallet Card -->
    <div class="wallet-card" style="margin: 0 16px 20px 16px;">
        <div class="flex items-center justify-between">
            <div>
                <p class="wallet-label">Current Balance</p>
                <p class="wallet-balance" id="mobileWalletBalance">₹<?php echo number_format($wallet_balance); ?></p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-wallet text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Amount Presets -->
    <div class="amount-presets" style="padding: 0 16px;">
        <div class="preset-btn" onclick="selectAmountMobile(100, this)">
            <div class="preset-amount">₹100</div>
            <div class="preset-label">Quick</div>
        </div>
        <div class="preset-btn" onclick="selectAmountMobile(500, this)">
            <div class="preset-amount">₹500</div>
            <div class="preset-label">Popular</div>
        </div>
        <div class="preset-btn" onclick="selectAmountMobile(1000, this)">
            <div class="preset-amount">₹1,000</div>
            <div class="preset-label">Best</div>
        </div>
        <div class="preset-btn" onclick="selectAmountMobile(2000, this)">
            <div class="preset-amount">₹2,000</div>
            <div class="preset-label">Monthly</div>
        </div>
        <div class="preset-btn" onclick="selectAmountMobile(5000, this)">
            <div class="preset-amount">₹5,000</div>
            <div class="preset-label">+Bonus</div>
        </div>
        <div class="preset-btn" onclick="selectAmountMobile(10000, this)">
            <div class="preset-amount">₹10,000</div>
            <div class="preset-label">Save</div>
        </div>
    </div>

    <!-- Custom Amount -->
    <div class="custom-amount" style="padding: 0 16px;">
        <div class="amount-input-group">
            <span class="amount-symbol">₹</span>
            <input type="number" id="mobileCustomAmount" class="amount-input" placeholder="Enter amount" min="100" max="50000" step="1">
        </div>
        <p class="text-xs text-gray-400 mt-2">Min: ₹100 | Max: ₹50,000</p>
    </div>

    <!-- Submit Button -->
    <button class="submit-btn" style="width: calc(100% - 32px); margin: 0 16px 20px 16px;" id="mobileAddMoneyBtn" onclick="addMoneyMobile()">
        <i class="fas fa-wallet"></i>
        Add Money
    </button>

    <!-- Recent Transactions -->
    <div class="recent-section" style="margin: 0 16px 20px 16px;">
        <div class="section-header">
            <h3 class="section-title">Recent</h3>
            <a href="/transactions" class="view-all">View all <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div id="mobileRecentTransactions">
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

<script>
let selectedAmount = 0;
let isMobile = window.innerWidth <= 768;

// Load recent transactions on page load
document.addEventListener('DOMContentLoaded', function() {
    loadRecentTransactions();
});

// Load recent transactions
function loadRecentTransactions() {
    fetch('/api/recent-transaction?type=recent&limit=10')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                renderRecentTransactions(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading transactions:', error);
        });
}

// Render recent transactions
function renderRecentTransactions(transactions) {
    const container = document.getElementById('recentTransactions');
    const mobileContainer = document.getElementById('mobileRecentTransactions');
    
    if(!transactions || transactions.length === 0) {
        const emptyHTML = `
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-history"></i>
                </div>
                <p>No recent transactions</p>
            </div>
        `;
        if(container) container.innerHTML = emptyHTML;
        if(mobileContainer) mobileContainer.innerHTML = emptyHTML;
        return;
    }
    
    let html = '';
    transactions.forEach(t => {
        const date = new Date(t.created_at);
        const formattedDateTime = date.toLocaleString('en-IN', { 
            timeZone: 'Asia/Kolkata',
            day: '2-digit',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
        
        let statusClass = '';
        let statusIcon = '';
        let statusText = '';
        
        switch(t.payment_status) {
            case 'success':
                statusClass = 'success';
                statusIcon = 'fa-check-circle';
                statusText = 'Success';
                break;
            case 'pending':
                statusClass = 'pending';
                statusIcon = 'fa-clock';
                statusText = 'Pending';
                break;
            case 'failed':
                statusClass = 'failed';
                statusIcon = 'fa-times-circle';
                statusText = 'Failed';
                break;
            default:
                statusClass = 'pending';
                statusIcon = 'fa-question-circle';
                statusText = t.payment_status || 'Unknown';
        }
        
        const amountClass = t.transaction_type === 'credit' ? 'credit' : 'debit';
        const amountSign = t.transaction_type === 'credit' ? '+' : '-';
        
        html += `
            <div class="recent-item">
                <div class="recent-icon ${statusClass}">
                    <i class="fas ${statusIcon}"></i>
                </div>
                <div class="recent-details">
                    <div class="recent-amount ${amountClass}">
                        ${amountSign} ₹${parseFloat(t.amount).toLocaleString('en-IN')}
                    </div>
                    <div class="recent-meta">${formattedDateTime}</div>
                    ${t.payment_method ? `<div class="recent-method text-xs text-gray-400 mt-1">${t.payment_method}</div>` : ''}
                </div>
                <span class="recent-status ${statusClass}">
                    <i class="fas ${statusIcon}"></i> ${statusText}
                </span>
            </div>
        `;
    });
    
    if(container) container.innerHTML = html;
    if(mobileContainer) mobileContainer.innerHTML = html;
}

// Select preset amount - Desktop
function selectAmount(amount, element) {
    selectedAmount = amount;
    document.getElementById('customAmount').value = amount;
    
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    element.classList.add('active');
}

// Select preset amount - Mobile
function selectAmountMobile(amount, element) {
    selectedAmount = amount;
    document.getElementById('mobileCustomAmount').value = amount;
    
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    element.classList.add('active');
}

// Add money - Desktop
function addMoney() {
    const amount = document.getElementById('customAmount').value || selectedAmount;
    const submitBtn = document.getElementById('addMoneyBtn');
    
    if(!amount || amount <= 0){
        showToast('Please enter an amount', 'error');
        return;
    }
    
    if(amount < 100){
        showToast('Minimum amount is ₹100', 'error');
        return;
    }
    
    if(amount > 50000){
        showToast('Maximum amount is ₹50,000', 'error');
        return;
    }
    
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    
    fetch('/api/add-money', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({amount: parseFloat(amount)})
    })
    .then(res => res.json())
    .then(data => {
        if(!data.success){
            showToast(data.message, 'error');
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            return;
        }
        
        const options = {
            key: data.data.key,
            amount: data.data.amount * 100,
            currency: "INR",
            name: "PG Mitra",
            description: "Wallet Recharge",
            order_id: data.data.razorpay_order_id,
            handler: function (response) {
                verifyPayment(response, amount);
            },
            prefill: {
                name: "<?php echo $display_name; ?>",
                email: "<?php echo $user['email']; ?>",
                contact: "<?php echo $user['phone'] ?? ''; ?>"
            },
            theme: {
                color: "#003B95"
            },
            modal: {
                ondismiss: function() {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                }
            }
        };
        
        const rzp = new Razorpay(options);
        rzp.open();
        
        setTimeout(() => {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }, 1000);
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Connection error. Please try again.', 'error');
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
    });
}

// Add money - Mobile
function addMoneyMobile() {
    const amount = document.getElementById('mobileCustomAmount').value || selectedAmount;
    const submitBtn = document.getElementById('mobileAddMoneyBtn');
    
    if(!amount || amount <= 0){
        showToast('Please enter an amount', 'error');
        return;
    }
    
    if(amount < 100){
        showToast('Minimum amount is ₹100', 'error');
        return;
    }
    
    if(amount > 50000){
        showToast('Maximum amount is ₹50,000', 'error');
        return;
    }
    
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    
    fetch('/api/add-money', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({amount: parseFloat(amount)})
    })
    .then(res => res.json())
    .then(data => {
        if(!data.success){
            showToast(data.message, 'error');
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
            return;
        }
        
        const options = {
            key: data.data.key,
            amount: data.data.amount * 100,
            currency: "INR",
            name: "PG Mitra",
            description: "Wallet Recharge",
            order_id: data.data.razorpay_order_id,
            handler: function (response) {
                verifyPaymentMobile(response, amount);
            },
            prefill: {
                name: "<?php echo $display_name; ?>",
                email: "<?php echo $user['email']; ?>",
                contact: "<?php echo $user['phone'] ?? ''; ?>"
            },
            theme: {
                color: "#003B95"
            },
            modal: {
                ondismiss: function() {
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
                }
            }
        };
        
        const rzp = new Razorpay(options);
        rzp.open();
        
        setTimeout(() => {
            submitBtn.classList.remove('loading');
            submitBtn.disabled = false;
        }, 1000);
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Connection error. Please try again.', 'error');
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
    });
}

// Verify payment - Desktop
function verifyPayment(paymentData, amount) {
    const submitBtn = document.getElementById('addMoneyBtn');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    
    fetch('/api/verify-payment', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(paymentData)
    })
    .then(res => res.json())
    .then(result => {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        
        if(result.success) {
            showToast('₹' + amount + ' added successfully!', 'success');
            
            if(result.data && result.data.new_balance) {
                document.getElementById('walletBalance').innerHTML = '₹' + result.data.new_balance.toLocaleString('en-IN');
                if(document.getElementById('mobileWalletBalance')) {
                    document.getElementById('mobileWalletBalance').innerHTML = '₹' + result.data.new_balance.toLocaleString('en-IN');
                }
            } else {
                location.reload();
            }
            
            document.getElementById('customAmount').value = '';
            selectedAmount = 0;
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            loadRecentTransactions();
        } else {
            showToast(result.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Payment verification failed', 'error');
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
    });
}

// Verify payment - Mobile
function verifyPaymentMobile(paymentData, amount) {
    const submitBtn = document.getElementById('mobileAddMoneyBtn');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    
    fetch('/api/verify-payment', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(paymentData)
    })
    .then(res => res.json())
    .then(result => {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        
        if(result.success) {
            showToast('₹' + amount + ' added successfully!', 'success');
            
            if(result.data && result.data.new_balance) {
                document.getElementById('mobileWalletBalance').innerHTML = '₹' + result.data.new_balance.toLocaleString('en-IN');
                if(document.getElementById('walletBalance')) {
                    document.getElementById('walletBalance').innerHTML = '₹' + result.data.new_balance.toLocaleString('en-IN');
                }
            } else {
                location.reload();
            }
            
            document.getElementById('mobileCustomAmount').value = '';
            selectedAmount = 0;
            document.querySelectorAll('.preset-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            loadRecentTransactions();
        } else {
            showToast(result.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Payment verification failed', 'error');
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
    });
}

// Navigation functions
function goToPage(page) {
    if (page === 'home') window.location.href = '/';
    else if (page === 'search') window.location.href = '/search';
    else if (page === 'bookings') window.location.href = '/bookings';
    else if (page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if (page === 'profile') window.location.href = '/profile';
}

// Toast notification
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    
    let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    if(type === 'info') icon = 'fa-info-circle';
    
    toast.innerHTML = `<i class="fas ${icon}"></i> ${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Handle custom amount input - Desktop
document.getElementById('customAmount')?.addEventListener('input', function() {
    selectedAmount = parseFloat(this.value) || 0;
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.classList.remove('active');
    });
});

// Handle custom amount input - Mobile
document.getElementById('mobileCustomAmount')?.addEventListener('input', function() {
    selectedAmount = parseFloat(this.value) || 0;
    document.querySelectorAll('.preset-btn').forEach(btn => {
        btn.classList.remove('active');
    });
});
</script>

</body>
</html>