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
$user_query = "SELECT username, full_name, profile_image, wallet_balance FROM users WHERE id = ?";
$stmt = $db->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$display_name = !empty($user['full_name']) ? $user['full_name'] : $user['username'];
$profile_image = !empty($user['profile_image']) ? $user['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($display_name) . '&background=3B82F6&color=fff&size=40';
$wallet_balance = $user['wallet_balance'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Transaction History - StayEase</title>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

* {
    font-family: 'Inter', sans-serif;
}

body {
    background: #f3f4f6;
}

.app-container {
    max-width: 414px;
    margin: auto;
    background: #ffffff;
    min-height: 100vh;
    position: relative;
    box-shadow: 0 0 30px rgba(0,0,0,0.05);
}

/* Header */
.header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 30px 20px 30px;
    border-bottom-left-radius: 40px;
    border-bottom-right-radius: 40px;
    position: relative;
    overflow: hidden;
}

.header::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -20%;
    width: 300px;
    height: 300px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -20%;
    width: 250px;
    height: 250px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

/* Wallet Card */
.wallet-card {
    background: linear-gradient(135deg, #3B82F6, #8B5CF6);
    border-radius: 24px;
    padding: 20px;
    margin: -20px 16px 20px;
    color: white;
    position: relative;
    z-index: 10;
    box-shadow: 0 20px 40px -10px rgba(59,130,246,0.3);
}

.wallet-balance {
    font-size: 36px;
    font-weight: 800;
    margin-top: 5px;
}

.wallet-label {
    font-size: 13px;
    opacity: 0.9;
}

/* Summary Cards */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    padding: 0 16px;
    margin-bottom: 20px;
}

.summary-card {
    background: #F9FAFB;
    border-radius: 16px;
    padding: 12px 4px;
    text-align: center;
}

.summary-icon {
    width: 32px;
    height: 32px;
    background: #EFF6FF;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 6px;
}

.summary-icon i {
    font-size: 16px;
    color: #3B82F6;
}

.summary-value {
    font-size: 16px;
    font-weight: 700;
    color: #1F2937;
}

.summary-label {
    font-size: 9px;
    color: #6B7280;
    margin-top: 2px;
}

/* Filter Tabs */
.filter-tabs {
    display: flex;
    gap: 8px;
    padding: 0 16px;
    margin-bottom: 16px;
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.filter-tabs::-webkit-scrollbar {
    display: none;
}

.filter-btn {
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 600;
    background: #F3F4F6;
    color: #6B7280;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-btn.active {
    background: #3B82F6;
    color: white;
}

/* Date Filter */
.date-filter {
    display: flex;
    gap: 10px;
    padding: 0 16px;
    margin-bottom: 16px;
}

.date-input {
    flex: 1;
    padding: 10px 12px;
    border: 2px solid #E5E7EB;
    border-radius: 30px;
    font-size: 12px;
    background: #F9FAFB;
}

.date-input:focus {
    outline: none;
    border-color: #3B82F6;
}

/* Transaction List */
.transactions-list {
    padding: 0 16px;
    margin-bottom: 80px;
}

.transaction-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 0;
    border-bottom: 1px solid #F3F4F6;
}

.transaction-icon {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.icon-credit { background: #D1FAE5; color: #10B981; }
.icon-debit { background: #FEE2E2; color: #EF4444; }
.icon-referral { background: #EFF6FF; color: #3B82F6; }
.icon-rent { background: #FEF3C7; color: #F59E0B; }
.icon-withdraw { background: #F3E8FF; color: #8B5CF6; }

.transaction-details {
    flex: 1;
}

.transaction-title {
    font-size: 15px;
    font-weight: 600;
    color: #1F2937;
    margin-bottom: 4px;
}

.transaction-meta {
    font-size: 11px;
    color: #9CA3AF;
    display: flex;
    gap: 12px;
}

.transaction-amount {
    font-size: 16px;
    font-weight: 700;
}

.amount-credit { color: #10B981; }
.amount-debit { color: #EF4444; }

.transaction-status {
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 30px;
    margin-left: 8px;
}

.status-completed { background: #D1FAE5; color: #047857; }
.status-pending { background: #FEF3C7; color: #B45309; }
.status-failed { background: #FEE2E2; color: #B91C1C; }

/* Loading Spinner */
.loading-spinner {
    text-align: center;
    padding: 40px;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #F3F4F6;
    border-top-color: #3B82F6;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: #F3F4F6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-icon i {
    font-size: 32px;
    color: #9CA3AF;
}

.empty-state h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 8px;
}

.empty-state p {
    color: #6B7280;
    font-size: 14px;
    margin-bottom: 20px;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin: 20px 0;
}

.page-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.page-btn.active {
    background: #3B82F6;
    color: white;
}

.page-btn:not(.active) {
    background: #F3F4F6;
    color: #6B7280;
}

.page-btn:hover:not(.active) {
    background: #E5E7EB;
}

/* Bottom Navigation */
.bottom-nav {
    background: white;
    border-top: 1px solid #F3F4F6;
    padding: 12px 20px;
    position: fixed;
    bottom: 0;
    width: 100%;
    max-width: 414px;
    z-index: 100;
}

.nav-items {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    cursor: pointer;
    color: #9CA3AF;
    transition: all 0.2s;
}

.nav-item.active {
    color: #3B82F6;
}

.nav-item i {
    font-size: 20px;
}

.nav-item span {
    font-size: 10px;
    font-weight: 500;
}
</style>
</head>
<body>

<div class="app-container">
    <!-- Header -->
    <div class="header">
        <div class="relative z-10">
            <button onclick="goBack()" class="text-white text-xl mb-4">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1 class="text-white text-2xl font-bold">Transactions</h1>
            <p class="text-white/80 text-sm mt-1">Your wallet activity history</p>
        </div>
    </div>

    <!-- Wallet Card -->
    <div class="wallet-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="wallet-label">Current Balance</p>
                <p class="wallet-balance" id="walletBalance">₹<?php echo number_format($wallet_balance); ?></p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-wallet text-2xl"></i>
            </div>
        </div>
        <div class="flex gap-2 mt-4">
            <button onclick="goToPage('add-money.php')" class="flex-1 bg-white/20 py-2.5 rounded-xl text-sm font-semibold hover:bg-white/30 transition">
                <i class="fas fa-plus mr-1"></i> Add Money
            </button>
            <button onclick="goToPage('withdraw.php')" class="flex-1 bg-white/20 py-2.5 rounded-xl text-sm font-semibold hover:bg-white/30 transition">
                <i class="fas fa-arrow-up mr-1"></i> Withdraw
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-grid" id="summaryContainer">
        <!-- Will be populated by JS -->
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <button class="filter-btn active" onclick="filterTransactions('all')" data-filter="all">All</button>
        <button class="filter-btn" onclick="filterTransactions('credit')" data-filter="credit">Credits</button>
        <button class="filter-btn" onclick="filterTransactions('debit')" data-filter="debit">Debits</button>
        <button class="filter-btn" onclick="filterTransactions('referral_earning')" data-filter="referral_earning">Referrals</button>
        <button class="filter-btn" onclick="filterTransactions('rent_payment')" data-filter="rent_payment">Rent</button>
        <button class="filter-btn" onclick="filterTransactions('wallet_add')" data-filter="wallet_add">Add Money</button>
        <button class="filter-btn" onclick="filterTransactions('withdrawal')" data-filter="withdrawal">Withdrawals</button>
    </div>

    <!-- Date Filter -->
    <div class="date-filter">
        <input type="date" id="dateFrom" class="date-input" placeholder="From" onchange="applyDateFilter()">
        <input type="date" id="dateTo" class="date-input" placeholder="To" onchange="applyDateFilter()">
        <button onclick="clearDateFilter()" class="px-4 py-2 bg-gray-100 rounded-xl text-sm text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Transactions List -->
    <div class="transactions-list" id="transactionsContainer">
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner"></div>
            <p class="text-sm text-gray-500">Loading transactions...</p>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination" id="pagination"></div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div class="nav-items">
            <div class="nav-item" onclick="goToPage('index.php')">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </div>
            <div class="nav-item" onclick="goToPage('search.php')">
                <i class="fas fa-search"></i>
                <span>Search</span>
            </div>
            <div class="nav-item" onclick="goToPage('bookings.php')">
                <i class="fas fa-ticket-alt"></i>
                <span>Bookings</span>
            </div>
            <div class="nav-item" onclick="goToPage('support.php')">
                <i class="fas fa-headset"></i>
                <span>Support</span>
            </div>
            <div class="nav-item active" onclick="goToPage('profile.php')">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </div>
        </div>
    </div>
</div>

<script>
let currentFilter = 'all';
let currentPage = 1;
let totalPages = 1;

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSummary();
    loadTransactions();
});

// Load transaction summary
function loadSummary() {
    fetch('api/transactions.php?summary=true')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                renderSummary(data.data);
            }
        });
}

// Render summary cards
function renderSummary(summary) {
    const container = document.getElementById('summaryContainer');
    container.innerHTML = `
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-gift"></i>
            </div>
            <div class="summary-value">${summary.total_referrals || 0}</div>
            <div class="summary-label">Referrals</div>
        </div>
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-home"></i>
            </div>
            <div class="summary-value">${summary.total_rent_payments || 0}</div>
            <div class="summary-label">Rent Paid</div>
        </div>
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="summary-value">${summary.total_adds || 0}</div>
            <div class="summary-label">Adds</div>
        </div>
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="summary-value">${summary.total_withdrawals || 0}</div>
            <div class="summary-label">Withdrawals</div>
        </div>
    `;
}

// Load transactions with filters
function loadTransactions() {
    showLoading();
    
    let url = `api/transactions.php?page=${currentPage}&limit=20`;
    
    if(currentFilter !== 'all') {
        url += `&category=${currentFilter}`;
    }
    
    const fromDate = document.getElementById('dateFrom').value;
    const toDate = document.getElementById('dateTo').value;
    
    if(fromDate) url += `&from=${fromDate}`;
    if(toDate) url += `&to=${toDate}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                renderTransactions(data.data);
                renderPagination(data.pagination);
            } else {
                showError('Failed to load transactions');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Connection error');
        });
}

// Render transactions list
function renderTransactions(transactions) {
    const container = document.getElementById('transactionsContainer');
    
    if(transactions.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <h3>No Transactions Yet</h3>
                <p>Your transaction history will appear here</p>
                <button onclick="goToPage('refer-earn.php')" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-sm font-semibold mt-2">
                    Start Earning
                </button>
            </div>
        `;
        return;
    }
    
    let html = '';
    transactions.forEach(t => {
        const date = new Date(t.created_at);
        const formattedDate = date.toLocaleDateString('en-IN', { 
            day: '2-digit', 
            month: 'short', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        
        // Determine icon and color based on category
        let iconClass = '';
        let iconBg = '';
        let title = '';
        
        switch(t.category) {
            case 'referral_earning':
                iconClass = 'fa-gift';
                iconBg = 'icon-referral';
                title = 'Referral Bonus';
                break;
            case 'rent_payment':
                iconClass = 'fa-home';
                iconBg = 'icon-rent';
                title = 'Rent Payment';
                break;
            case 'wallet_add':
                iconClass = 'fa-plus-circle';
                iconBg = 'icon-credit';
                title = 'Money Added';
                break;
            case 'withdrawal':
                iconClass = 'fa-arrow-up';
                iconBg = 'icon-withdraw';
                title = 'Withdrawal';
                break;
            default:
                iconClass = t.type === 'credit' ? 'fa-arrow-down' : 'fa-arrow-up';
                iconBg = t.type === 'credit' ? 'icon-credit' : 'icon-debit';
                title = t.description || (t.type === 'credit' ? 'Money Received' : 'Money Sent');
        }
        
        const amountClass = t.type === 'credit' ? 'amount-credit' : 'amount-debit';
        const amountSymbol = t.type === 'credit' ? '+' : '-';
        
        html += `
            <div class="transaction-item">
                <div class="transaction-icon ${iconBg}">
                    <i class="fas ${iconClass}"></i>
                </div>
                <div class="transaction-details">
                    <div class="transaction-title">
                        ${title}
                        ${t.status !== 'completed' ? `<span class="transaction-status status-${t.status}">${t.status}</span>` : ''}
                    </div>
                    <div class="transaction-meta">
                        <span><i class="far fa-clock mr-1"></i>${formattedDate}</span>
                        <span><i class="fas fa-hashtag mr-1"></i>${t.transaction_id.slice(-8)}</span>
                    </div>
                </div>
                <div class="transaction-amount ${amountClass}">
                    ${amountSymbol}₹${Math.abs(t.amount).toLocaleString('en-IN')}
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Render pagination
function renderPagination(pagination) {
    totalPages = pagination.total_pages;
    const container = document.getElementById('pagination');
    
    if(totalPages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let html = '';
    
    if(currentPage > 1) {
        html += `<div class="page-btn" onclick="changePage(${currentPage - 1})"><i class="fas fa-chevron-left"></i></div>`;
    }
    
    for(let i = 1; i <= totalPages; i++) {
        if(i === currentPage) {
            html += `<div class="page-btn active">${i}</div>`;
        } else if(i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
            html += `<div class="page-btn" onclick="changePage(${i})">${i}</div>`;
        } else if(i === currentPage - 3 || i === currentPage + 3) {
            html += `<div class="page-btn">...</div>`;
        }
    }
    
    if(currentPage < totalPages) {
        html += `<div class="page-btn" onclick="changePage(${currentPage + 1})"><i class="fas fa-chevron-right"></i></div>`;
    }
    
    container.innerHTML = html;
}

// Change page
function changePage(page) {
    currentPage = page;
    loadTransactions();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Filter transactions
function filterTransactions(filter) {
    currentFilter = filter;
    currentPage = 1;
    
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.filter === filter);
    });
    
    loadTransactions();
}

// Apply date filter
function applyDateFilter() {
    currentPage = 1;
    loadTransactions();
}

// Clear date filter
function clearDateFilter() {
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    applyDateFilter();
}

// Show loading state
function showLoading() {
    document.getElementById('transactionsContainer').innerHTML = `
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p class="text-sm text-gray-500">Loading transactions...</p>
        </div>
    `;
}

// Show error
function showError(message) {
    document.getElementById('transactionsContainer').innerHTML = `
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3>Oops!</h3>
            <p>${message}</p>
            <button onclick="loadTransactions()" class="bg-blue-600 text-white px-6 py-3 rounded-xl text-sm font-semibold mt-2">
                Try Again
            </button>
        </div>
    `;
}

// Navigation
function goBack() {
    window.history.back();
}

function goToPage(page) {
    window.location.href = page;
}
</script>

</body>
</html>