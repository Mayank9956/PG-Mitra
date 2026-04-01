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
$profile_image = !empty($user['profile_image']) ? $user['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($display_name) . '&background=3B82F6&color=fff&size=40';
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
<title>Withdraw Money - StayEase</title>
<script src="/static/js/script.js"></script>
<link rel="stylesheet" href="/static/css/style.css">
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

/* Withdraw Form */
.withdraw-form {
    padding: 0 16px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-input, .form-select {
    width: 100%;
    padding: 16px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s;
    background: #F9FAFB;
}

.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #3B82F6;
    background: white;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.form-input.error {
    border-color: #EF4444;
    background: #FEF2F2;
}

/* Method Toggle */
.method-toggle {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}

.method-option {
    flex: 1;
    padding: 16px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}

.method-option.active {
    border-color: #3B82F6;
    background: #EFF6FF;
}

.method-option i {
    font-size: 24px;
    color: #9CA3AF;
    margin-bottom: 8px;
}

.method-option.active i {
    color: #3B82F6;
}

.method-option span {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #6B7280;
}

.method-option.active span {
    color: #3B82F6;
}

/* Bank Card Option */
.bank-card-option {
    background: white;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.bank-card-option:hover {
    border-color: #3B82F6;
}

.bank-card-option.selected {
    border-color: #3B82F6;
    background: #EFF6FF;
}

.bank-card-option .bank-name {
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 4px;
}

.bank-card-option .account-details {
    font-size: 13px;
    color: #6B7280;
}

.bank-card-option .default-badge {
    background: #10B981;
    color: white;
    font-size: 10px;
    padding: 2px 8px;
    border-radius: 30px;
    margin-left: 8px;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    background: linear-gradient(135deg, #3B82F6, #2563EB);
    color: white;
    padding: 18px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 20px;
    box-shadow: 0 10px 25px -5px rgba(59,130,246,0.5);
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px -5px rgba(59,130,246,0.6);
}

.submit-btn:disabled {
    opacity: 0.5;
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
#historyContainer{
     margin-bottom: 20%;
}

/* Withdrawal History */
.history-section {
    padding: 10px;
    margin-bottom: 20px;
}

.history-item {
    background: #F9FAFB;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
    border-left: 4px solid;
}

.history-item.pending { border-left-color: #F59E0B; }
.history-item.processing { border-left-color: #3B82F6; }
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
    color: #1F2937;
}

.history-status {
    padding: 4px 12px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
}

.status-pending { background: #FEF3C7; color: #B45309; }
.status-processing { background: #DBEAFE; color: #1E40AF; }
.status-completed { background: #D1FAE5; color: #047857; }
.status-failed { background: #FEE2E2; color: #B91C1C; }
.status-cancelled { background: #E5E7EB; color: #4B5563; }

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
    display: inline-block;
}

.cancel-btn:hover {
    text-decoration: underline;
}

/* Toast */
.toast {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    padding: 14px 28px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    z-index: 9999;
    animation: slideDown 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.toast.success {
    background: #10B981;
    color: white;
}

.toast.error {
    background: #EF4444;
    color: white;
}

.toast.info {
    background: #3B82F6;
    color: white;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translate(-50%, -20px);
    }
    to {
        opacity: 1;
        transform: translate(-50%, 0);
    }
}

/* Quick Amount */
.quick-amounts {
    display: flex;
    gap: 10px;
    margin: 15px 0;
    flex-wrap: wrap;
}

.quick-amount {
    background: #F3F4F6;
    padding: 10px 16px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 600;
    color: #4B5563;
    cursor: pointer;
    transition: all 0.2s;
}

.quick-amount:hover {
    background: #3B82F6;
    color: white;
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
            <h1 class="text-white text-2xl font-bold">Withdraw Money</h1>
            <p class="text-white/80 text-sm mt-1">Transfer funds to your bank account</p>
        </div>
    </div>

    <!-- Wallet Card -->
    <div class="wallet-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="wallet-label">Available Balance</p>
                <p class="wallet-balance" id="walletBalance">₹<?php echo number_format($wallet_balance); ?></p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <i class="fas fa-wallet text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Withdraw Form -->
    <div class="withdraw-form">
        <h3 class="font-bold text-gray-800 mb-4">Withdraw Funds</h3>

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
                    <label class="form-label">Select Bank Account</label>
                    <?php if(empty($saved_cards)): ?>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-3">
                            <p class="text-sm text-yellow-700">No bank cards added yet.</p>
                            <a href="bank-cards.php" class="text-sm text-blue-600 font-semibold mt-2 inline-block">Add Bank Card →</a>
                        </div>
                    <?php else: ?>
                        <?php foreach($saved_cards as $card): ?>
                        <div class="bank-card-option" onclick="selectCard(<?php echo $card['id']; ?>)">
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
                                <i class="fas fa-check-circle text-2xl <?php echo $card['is_default'] ? 'text-blue-600' : 'text-gray-300'; ?>"></i>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- UPI Method Fields -->
            <div id="upiFields" style="display: none;">
                <div class="form-group">
                    <label class="form-label">UPI ID</label>
                    <input type="text" id="upiId" class="form-input" placeholder="e.g., name@okhdfcbank">
                    <div class="text-xs text-gray-400 mt-1">Enter your UPI ID</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Account Holder Name</label>
                    <input type="text" id="accountHolder" class="form-input" placeholder="Enter name as per bank">
                </div>
            </div>

            <!-- Amount -->
            <div class="form-group">
                <label class="form-label">Amount (₹)</label>
                <input type="number" id="amount" class="form-input" placeholder="Enter amount" min="100" max="50000" step="100" required>
                
                <!-- Quick Amounts -->
                <div class="quick-amounts">
                    <span class="quick-amount" onclick="setAmount(500)">₹500</span>
                    <span class="quick-amount" onclick="setAmount(1000)">₹1,000</span>
                    <span class="quick-amount" onclick="setAmount(2000)">₹2,000</span>
                    <span class="quick-amount" onclick="setAmount(5000)">₹5,000</span>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fas fa-arrow-right mr-2"></i>
                Withdraw Now
            </button>
        </form>
    </div>

    <!-- Withdrawal History -->
    <div class="history-section">
        <h3 class="font-bold text-gray-800 mb-4">Withdrawal History</h3>
        <div id="historyContainer">
            <!-- History will be loaded here -->
            <div class="text-center py-8 text-gray-400" id="loadingHistory">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p class="text-sm">Loading history...</p>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div class="nav-items">
            <div class="nav-item " onclick="goToPage('home')">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </div>
            <div class="nav-item" onclick="goToPage('search')">
                <i class="fas fa-search"></i>
                <span>Search</span>
            </div>
            <div class="nav-item" onclick="goToPage('bookings')">
                <i class="fas fa-ticket-alt"></i>
                <span>Bookings</span>
            </div>
            <div class="nav-item" onclick="goToPage('saved-rooms')">
                <i class="fas fa-heart"></i>
                <span>Saved</span>
            </div>
            <div class="nav-item active" onclick="goToPage('profile')">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </div>
        </div>
    </div>
    
</div>

<script>
let currentMethod = 'bank';
let selectedCardId = null;

// Load withdrawal history on page load
document.addEventListener('DOMContentLoaded', function() {
    loadWithdrawalHistory();
});

// Set withdrawal method
function setMethod(method) {
    currentMethod = method;
    
    // Update UI
    document.getElementById('methodBank').classList.toggle('active', method === 'bank');
    document.getElementById('methodUpi').classList.toggle('active', method === 'upi');
    
    // Show/hide fields
    document.getElementById('bankFields').style.display = method === 'bank' ? 'block' : 'none';
    document.getElementById('upiFields').style.display = method === 'upi' ? 'block' : 'none';
}

// Select bank card
function selectCard(cardId) {
    selectedCardId = cardId;
    
    // Update UI
    document.querySelectorAll('.bank-card-option').forEach(option => {
        option.classList.remove('selected');
        option.querySelector('i').className = 'fas fa-check-circle text-2xl text-gray-300';
    });
    
    const selectedOption = event.currentTarget;
    selectedOption.classList.add('selected');
    selectedOption.querySelector('i').className = 'fas fa-check-circle text-2xl text-blue-600';
    
    // Set radio
    const radio = selectedOption.querySelector('input[type="radio"]');
    if(radio) radio.checked = true;
}

// Set amount from quick buttons
function setAmount(amount) {
    document.getElementById('amount').value = amount;
}

// Submit withdrawal
function submitWithdraw(event) {
    event.preventDefault();
    
    const amount = parseFloat(document.getElementById('amount').value);
    const submitBtn = document.getElementById('submitBtn');
    
    // Validation
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
    
    // Show loading
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    
    // Send request
    fetch('/api/withdraw', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        
        if(result.success) {
            showToast(result.message, 'success');
            
            // Update balance
            document.getElementById('walletBalance').innerHTML = '₹' + result.new_balance.toLocaleString('en-IN');
            
            // Clear form
            document.getElementById('amount').value = '';
            if(currentMethod === 'upi') {
                document.getElementById('upiId').value = '';
                document.getElementById('accountHolder').value = '';
            }
            
            // Reload history
            loadWithdrawalHistory();
        } else {
            showToast(result.message, 'error');
        }
    })
    .catch(error => {
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        showToast('Connection error. Please try again.', 'error');
        console.error('Error:', error);
    });
}

// Load withdrawal history
function loadWithdrawalHistory() {
    fetch('/api/withdraw')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('historyContainer');
            
            if(data.success && data.data.length > 0) {
                let html = '';
                data.data.forEach(withdrawal => {
                    const date = new Date(withdrawal.created_at);
                    const formattedDate = date.toLocaleDateString('en-IN', { 
                        day: '2-digit', 
                        month: 'short', 
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    const statusClass = withdrawal.status;
                    const methodIcon = withdrawal.withdrawal_method === 'bank' ? 'fa-university' : 'fa-mobile-alt';
                    
                    html += `
                        <div class="history-item ${withdrawal.status}">
                            <div class="history-header">
                                <span class="history-amount">₹${withdrawal.amount.toLocaleString('en-IN')}</span>
                                <span class="history-status status-${withdrawal.status}">${withdrawal.status.toUpperCase()}</span>
                            </div>
                            <div class="history-method">
                                <i class="fas ${methodIcon} mr-1"></i>
                                ${withdrawal.withdrawal_method === 'bank' ? withdrawal.bank_name : withdrawal.upi_id}
                            </div>
                            <div class="history-date">
                                <i class="far fa-clock mr-1"></i>${formattedDate}
                                ${withdrawal.transaction_id ? `<br><span class="text-xs">TXN: ${withdrawal.transaction_id}</span>` : ''}
                            </div>
                            ${withdrawal.status === 'pending' ? `
                                <button class="cancel-btn" onclick="cancelWithdrawal(${withdrawal.id})">
                                    <i class="fas fa-times-circle mr-1"></i>Cancel Request
                                </button>
                            ` : ''}
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = `
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-history text-4xl mb-3"></i>
                        <p class="text-sm">No withdrawal history yet</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading history:', error);
            document.getElementById('historyContainer').innerHTML = `
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                    <p class="text-sm">Failed to load history</p>
                </div>
            `;
        });
}

// Cancel withdrawal
function cancelWithdrawal(withdrawalId) {
    if(!confirm('Are you sure you want to cancel this withdrawal request?')) return;
    
    fetch('/api/withdraw', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            withdrawal_id: withdrawalId
        })
    })
    .then(response => response.json())
    .then(result => {
        if(result.success) {
            showToast('Withdrawal cancelled successfully', 'success');
            
            // Update balance
            document.getElementById('walletBalance').innerHTML = '₹' + result.new_balance.toLocaleString('en-IN');
            
            // Reload history
            loadWithdrawalHistory();
        } else {
            showToast(result.message, 'error');
        }
    });
}

// Navigation
function goBack() {
    window.history.back();
}

function goToPage(page) {
    window.location.href = page;
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

// Initialize default card selection
document.addEventListener('DOMContentLoaded', function() {
    // Auto-select default card
    const defaultCard = document.querySelector('.bank-card-option input[checked]');
    if(defaultCard) {
        const cardDiv = defaultCard.closest('.bank-card-option');
        if(cardDiv) {
            cardDiv.classList.add('selected');
            cardDiv.querySelector('i').className = 'fas fa-check-circle text-2xl text-blue-600';
            selectedCardId = defaultCard.value;
        }
    }
});
</script>

</body>
</html>