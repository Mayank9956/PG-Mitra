<?php
require_once 'common/auth.php';
require_once 'common/razorpay_config.php';
// Ensure user is logged in
$user = requireAuth($conn);

date_default_timezone_set('Asia/Kolkata');

$user_id = $user['id'];
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

// Invalid room → redirect
if ($room_id <= 0) {
    header("Location: home.php");
    exit;
}

// ==============================
// FETCH ROOM
// ==============================
$query = "
    SELECT r.*, ri.image_url
    FROM rooms r
    LEFT JOIN room_images ri 
        ON r.id = ri.room_id AND ri.is_primary = 1
    WHERE r.id = ? AND r.is_available = 1
    LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();

// Room not found
if (!$room) {
    header("Location: home.php");
    exit;
}

// ==============================
// USER DATA (FROM AUTH)
// ==============================
$full_name = $user['full_name'];
$email = $user['email'];
$phone = $user['phone'];
$wallet_balance = (float)($user['wallet_balance'] ?? 0);
$profile_image = !empty($user['profile_image']) ? $user['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($full_name) . '&background=003B95&color=fff';

// ==============================
// ROOM DATA
// ==============================
$security_deposit = !empty($room['security_deposit']) 
    ? (float)$room['security_deposit'] 
    : 5000;

$monthly_price = (float)($room['price'] ?? 0);
$max_guests = (int)($room['max_guests'] ?? 1);
$max_months = 12;

// Razorpay Key ID (Replace with your actual key)
$razorpay_key_id = "rzp_test_SUF3k8IAr1EBTh";

// Generate CSRF token for security
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Prepare safe JSON data for JavaScript
$safe_data = [
    'monthly_price' => $monthly_price,
    'security_deposit' => $security_deposit,
    'wallet_balance' => $wallet_balance,
    'room_id' => $room_id,
    'user_id' => $user_id,
    'razorpay_key' => $razorpay_key_id,
    'csrf_token' => $csrf_token,
    'full_name' => $full_name,
    'email' => $email,
    'phone' => $phone,
    'room_title' => $room['title'],
    'room_location' => $room['location'] ?? 'Location available',
    'room_image' => $room['image_url'] ?? 'https://placehold.co/100x100/003B95/white?text=ROOM',
    'room_rating' => $room['rating'] ?? '4.5'
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Complete Booking - PG Mitra</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
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

        /* Booking Card */
        .booking-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid #E5E7EB;
            overflow: hidden;
        }

        /* Step Indicator */
        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .step-dot {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
        }

        .step-active {
            background: #003B95;
            color: white;
        }

        .step-inactive {
            background: #E5E7EB;
            color: #6B7280;
        }

        .step-label {
            font-size: 13px;
            font-weight: 500;
            color: #6B7280;
        }

        .step-active .step-label {
            color: #003B95;
        }

        .step-line {
            width: 40px;
            height: 2px;
            background: #E5E7EB;
        }

        /* Form Elements */
        .input-field, .select-field, .textarea-field {
            width: 100%;
            border: 1px solid #E5E7EB;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            outline: none;
            background: white;
            transition: all 0.2s;
        }

        .input-field:focus, .select-field:focus, .textarea-field:focus {
            border-color: #003B95;
            box-shadow: 0 0 0 3px rgba(0,59,149,0.1);
        }

        .label-text {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            display: block;
        }

        /* Payment Method */
        .payment-method-box {
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            padding: 16px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .payment-method-box:hover {
            border-color: #003B95;
            background: #F9FAFB;
        }

        .payment-method-box.selected {
            border-color: #003B95;
            background: #EFF6FF;
        }

        /* Buttons */
        .primary-btn {
            background: #003B95;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .primary-btn:hover {
            background: #002E7A;
            transform: translateY(-1px);
        }

        .secondary-btn {
            background: #10B981;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .secondary-btn:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Price Lines */
        .price-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 14px;
            color: #4B5563;
        }

        /* Sticky Bottom Bar */
        .sticky-bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #E5E7EB;
            padding: 12px 20px;
            z-index: 100;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }

        /* Toast */
        .toast-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideDown 0.3s ease;
        }

        .toast-info { background: #1F2937; }
        .toast-success { background: #10B981; }
        .toast-error { background: #EF4444; }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        .loading-spinner {
            display: inline-block;
            width: 18px;
            height: 18px;
            border: 2px solid white;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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

        /* Mobile Bottom Bar */
        .mobile-bottom-bar {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #E5E7EB;
            padding: 12px 16px;
            z-index: 100;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .desktop-header {
                display: none;
            }
            .mobile-bottom-bar .gap-3 {     gap: 7.25rem!important}
            .mobile-header {
                display: flex;
            }
            
            .main-container {
                display: none;
            }
            
            .mobile-container {
                display: block;
                padding-bottom: 80px;
                     margin-bottom: 20%;
            }
            
            .mobile-bottom-nav {
                display: block;
            }
            
            .mobile-bottom-bar {
                display: flex;
                     margin-bottom: 17%;
            }
            
            .sticky-bottom-bar {
                display: none;
            }
            
            .step-label {
                display: none;
            }
            
            .step-line {
                width: 30px;
            }
            
            .booking-card {
                margin: 0 16px 16px 16px;
            }
            
            .payment-method-box {
                padding: 12px;
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
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <button onclick="history.back()" class="text-[#003B95] mb-4 inline-flex items-center gap-2 hover:underline">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <h1 class="text-2xl font-bold text-gray-900">Secure Checkout</h1>
            <p class="text-gray-500 text-sm">Fast, safe and clean booking experience</p>
        </div>

        <!-- Step Indicator -->
        <div class="step-indicator mb-6">
            <div class="step">
                <span id="stepDot1" class="step-dot step-active">1</span>
                <span class="step-label">Details</span>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <span id="stepDot2" class="step-dot step-inactive">2</span>
                <span class="step-label">Payment</span>
            </div>
        </div>

        <!-- STEP 1: Booking Details -->
        <div id="step1Container" class="space-y-4">
            <!-- Room Summary -->
            <div class="booking-card p-5">
                <h2 class="font-bold text-gray-800 text-base mb-4">
                    <i class="fas fa-bed text-[#003B95] mr-2"></i>Booking Summary
                </h2>
                <div class="flex gap-4">
                    <img src="<?php echo htmlspecialchars($room['image_url'] ?? 'https://placehold.co/100x100/003B95/white?text=ROOM'); ?>" class="w-20 h-20 rounded-xl object-cover border border-gray-200" alt="room">
                    <div class="flex-1">
                        <h3 class="font-bold text-gray-900"><?php echo htmlspecialchars($room['title']); ?></h3>
                        <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                            <i class="fas fa-map-marker-alt text-gray-400 text-xs"></i>
                            <?php echo htmlspecialchars($room['location'] ?? 'Location not available'); ?>
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="bg-yellow-50 text-yellow-700 text-xs font-bold px-2 py-1 rounded-full">
                                <i class="fas fa-star text-xs mr-1"></i><?php echo htmlspecialchars($room['rating'] ?? '4.5'); ?>
                            </span>
                            <span class="bg-blue-50 text-[#003B95] text-xs font-bold px-2 py-1 rounded-full">
                                ₹<?php echo number_format($monthly_price); ?>/month
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="booking-card p-5">
                <h3 class="font-bold text-gray-800 text-sm mb-4">
                    <i class="fas fa-calendar-check text-[#003B95] mr-2"></i>Booking Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label-text">Check-in Date</label>
                        <input type="date" id="check_in_step1" class="input-field" min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div>
                        <label class="label-text">Duration</label>
                        <select id="months_step1" class="select-field">
                            <?php for ($i = 1; $i <= $max_months; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> month<?php echo $i > 1 ? 's' : ''; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="label-text">Number of Guests</label>
                        <select id="guests_step1" class="select-field">
                            <?php for ($i = 1; $i <= $max_guests; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> guest<?php echo $i > 1 ? 's' : ''; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="label-text">Special Requests</label>
                        <textarea id="requests_step1" rows="2" class="textarea-field" placeholder="Example: early move-in, quiet room..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Wallet & Coupon -->
            <div class="booking-card p-5">
                <div class="flex justify-between items-center flex-wrap gap-3 mb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-wallet text-[#003B95]"></i>
                            <span class="font-semibold text-gray-800">Wallet Balance</span>
                        </div>
                        <p class="text-2xl font-bold text-gray-900 mt-1">₹<?php echo number_format($wallet_balance); ?></p>
                    </div>
                    <?php if ($wallet_balance > 0): ?>
                        <label class="flex items-center gap-2 text-sm bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-full cursor-pointer transition">
                            <input type="checkbox" id="useWalletCheckbox">
                            <span class="font-medium">Use wallet</span>
                        </label>
                    <?php endif; ?>
                </div>

                <div class="flex gap-2">
                    <input type="text" id="couponCodeInput" placeholder="Enter coupon code" class="flex-1 input-field">
                    <button id="applyCouponBtnUI" type="button" class="bg-[#003B95] text-white px-4 rounded-xl text-sm font-semibold hover:bg-[#002E7A] transition">
                        Apply
                    </button>
                </div>

                <div id="couponAppliedMsg" class="text-xs text-green-700 mt-3 hidden bg-green-50 border border-green-100 rounded-xl p-3"></div>
                <div id="walletInsufficientMsg" class="text-xs text-amber-800 bg-amber-50 border border-amber-100 p-3 rounded-xl mt-3 hidden"></div>
            </div>

            <!-- Price Summary -->
            <div class="booking-card p-5 bg-gradient-to-r from-blue-50 to-white">
                <div class="price-line">
                    <span>Monthly Rent</span>
                    <span class="font-semibold" id="step1MonthlyPrice">₹0</span>
                </div>
                <div class="price-line">
                    <span>Duration</span>
                    <span class="font-semibold" id="displayMonths">1 month</span>
                </div>
                <div class="price-line">
                    <span>Total Rent</span>
                    <span class="font-semibold" id="totalRentVal">₹0</span>
                </div>
                <div class="price-line">
                    <span>Security Deposit</span>
                    <span class="font-semibold" id="step1SecurityDeposit">₹0</span>
                </div>
                <div id="step1CouponRow" class="price-line text-green-700 hidden">
                    <span>Coupon Discount</span>
                    <span class="font-semibold" id="step1CouponVal">-₹0</span>
                </div>
                <div id="step1WalletRow" class="price-line text-green-700 hidden">
                    <span>Wallet Used</span>
                    <span class="font-semibold" id="step1WalletVal">-₹0</span>
                </div>
                <hr class="my-3 border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500">Total payable now</p>
                        <h4 class="text-2xl font-bold text-gray-900" id="totalPayableStep1">₹0</h4>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">You save</p>
                        <h4 class="text-lg font-bold text-green-600" id="dynamicSavings">₹0</h4>
                    </div>
                </div>
            </div>

            <!-- Terms -->
            <div class="booking-card p-5">
                <label class="flex items-start gap-2 cursor-pointer">
                    <input type="checkbox" id="termsCheckbox" class="mt-1">
                    <span class="text-sm text-gray-600">
                        By continuing, you confirm that you are above 18 years of age and agree to PG Mitra's
                        <a href="terms-and-conditions" class="text-[#003B95] font-semibold">Terms & Conditions</a>
                        and
                        <a href="privacy-policy" class="text-[#003B95] font-semibold">Privacy Policy</a>.
                    </span>
                </label>
            </div>
        </div>

        <!-- STEP 2: Payment Details -->
        <div id="step2Container" class="space-y-4 hidden">
            <!-- Back Button -->
            <div class="booking-card p-4 flex items-center gap-3">
                <button id="backToStep1" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                    <i class="fas fa-arrow-left"></i>
                </button>
                <div>
                    <div class="font-bold text-gray-900">Payment Details</div>
                    <div class="text-xs text-gray-500">Review discounts and complete booking</div>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="booking-card p-5">
                <div class="space-y-1">
                    <div class="price-line">
                        <span>Monthly Rent</span>
                        <span class="font-semibold" id="breakMonthlyPrice">₹0</span>
                    </div>
                    <div class="price-line">
                        <span>Number of Months</span>
                        <span class="font-semibold" id="breakMonthsCount">0</span>
                    </div>
                    <div class="price-line">
                        <span>Total Rent</span>
                        <span class="font-semibold" id="breakTotalRent">₹0</span>
                    </div>
                    <div class="price-line">
                        <span>Security Deposit</span>
                        <span class="font-semibold" id="breakSecurityDeposit">₹0</span>
                    </div>
                    <div id="breakCouponRow" class="price-line text-green-700 hidden">
                        <span><i class="fas fa-tag text-green-500 mr-1"></i>Coupon Discount</span>
                        <span class="font-semibold" id="breakCouponAmount">-₹0</span>
                    </div>
                    <div id="breakWalletRow" class="price-line text-green-700 hidden">
                        <span><i class="fas fa-wallet text-green-500 mr-1"></i>Wallet Applied</span>
                        <span class="font-semibold" id="breakWalletAmount">-₹0</span>
                    </div>
                    <hr class="my-3 border-gray-200">
                    <div class="flex justify-between items-center pt-1">
                        <div>
                            <div class="text-gray-500 text-xs">Final amount</div>
                            <div class="text-gray-900 text-lg font-bold">Total to Pay</div>
                        </div>
                        <span class="text-[#003B95] text-2xl font-bold" id="paymentPageTotal">₹0</span>
                    </div>
                </div>
                <div class="mt-4 text-xs bg-green-50 border border-green-100 p-3 rounded-xl text-green-700 flex items-center gap-2">
                    <i class="fas fa-percent"></i>
                    <span>You save <strong id="totalSavingsValue">₹0</strong> on this booking</span>
                </div>
            </div>

            <!-- Payment Options -->
            <div id="paymentMethodsSection" class="booking-card p-5">
                <h3 class="font-bold text-gray-800 text-sm mb-4">Select Payment Option</h3>
                <div class="space-y-3">
                    <div data-method="online" class="payment-method-box flex items-center justify-between selected" id="payOnlineMethod">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="finalPaymentMethod" value="online" class="w-4 h-4" checked>
                            <div>
                                <div class="font-semibold">Online Payment (Razorpay)</div>
                                <div class="text-xs text-gray-500">Card, UPI, Net Banking</div>
                            </div>
                        </div>
                        <i class="fas fa-credit-card text-[#003B95] text-xl"></i>
                    </div>

                    <div data-method="later" class="payment-method-box flex items-center justify-between" id="payLaterMethod">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="finalPaymentMethod" value="later" class="w-4 h-4">
                            <div>
                                <div class="font-semibold">Pay Later</div>
                                <div class="text-xs text-gray-500">Pay at property on arrival</div>
                            </div>
                        </div>
                        <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Zero Payment Box -->
            <div id="zeroPaymentBox" class="booking-card p-5 hidden bg-green-50 border border-green-100">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-green-100 text-green-700 flex items-center justify-center">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-green-800">No payment required</h4>
                        <p class="text-xs text-green-700">Your wallet and discounts fully cover this booking amount.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <!-- Step Indicator -->
    <div class="step-indicator mt-4 mb-4">
        <div class="step">
            <span id="mobileStepDot1" class="step-dot step-active">1</span>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <span id="mobileStepDot2" class="step-dot step-inactive">2</span>
        </div>
    </div>

    <!-- Mobile Step 1 -->
    <div id="mobileStep1Container">
        <!-- Room Summary -->
        <div class="bg-white rounded-xl p-4 mx-4 mb-3 shadow-sm border border-gray-100">
            <div class="flex gap-3">
                <img src="<?php echo htmlspecialchars($room['image_url'] ?? 'https://placehold.co/80x80/003B95/white?text=ROOM'); ?>" class="w-16 h-16 rounded-xl object-cover">
                <div class="flex-1">
                    <h3 class="font-bold text-gray-900 text-sm"><?php echo htmlspecialchars($room['title']); ?></h3>
                    <p class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($room['location'] ?? 'Location not available'); ?></p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="bg-yellow-50 text-yellow-700 text-xs px-2 py-0.5 rounded-full">★ <?php echo htmlspecialchars($room['rating'] ?? '4.5'); ?></span>
                        <span class="text-[#003B95] font-bold text-sm">₹<?php echo number_format($monthly_price); ?>/mo</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="bg-white rounded-xl p-4 mx-4 mb-3 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 text-sm mb-3">Booking Details</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-semibold text-gray-600 mb-1 block">Check-in Date</label>
                    <input type="date" id="mobile_check_in" class="input-field text-sm w-full" min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-600 mb-1 block">Duration</label>
                        <select id="mobile_months" class="select-field text-sm w-full">
                            <?php for ($i = 1; $i <= $max_months; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> month<?php echo $i > 1 ? 's' : ''; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-600 mb-1 block">Guests</label>
                        <select id="mobile_guests" class="select-field text-sm w-full">
                            <?php for ($i = 1; $i <= $max_guests; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> guest<?php echo $i > 1 ? 's' : ''; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-600 mb-1 block">Special Requests</label>
                    <textarea id="mobile_requests" rows="2" class="textarea-field text-sm w-full" placeholder="Any special requests?"></textarea>
                </div>
            </div>
        </div>

        <!-- Wallet & Coupon -->
        <div class="bg-white rounded-xl p-4 mx-4 mb-3 shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-3">
                <div>
                    <p class="text-xs text-gray-500">Wallet Balance</p>
                    <p class="text-xl font-bold text-gray-900">₹<?php echo number_format($wallet_balance); ?></p>
                </div>
                <?php if ($wallet_balance > 0): ?>
                    <label class="flex items-center gap-2 text-xs bg-gray-100 px-3 py-2 rounded-full">
                        <input type="checkbox" id="mobile_useWallet">
                        <span>Use wallet</span>
                    </label>
                <?php endif; ?>
            </div>
            <div class="flex gap-2">
                <input type="text" id="mobile_couponCode" placeholder="Coupon code" class="flex-1 input-field text-sm">
                <button id="mobile_applyCoupon" class="bg-[#003B95] text-white px-4 rounded-xl text-xs font-semibold">Apply</button>
            </div>
            <div id="mobile_couponMsg" class="text-xs text-green-700 mt-2 hidden bg-green-50 border border-green-100 rounded-lg p-2"></div>
        </div>

        <!-- Price Summary -->
        <div class="bg-gradient-to-r from-blue-50 to-white rounded-xl p-4 mx-4 mb-3 shadow-sm">
            <div class="flex justify-between text-sm py-1">
                <span class="text-gray-600">Monthly Rent</span>
                <span class="font-semibold" id="mobile_monthlyPrice">₹0</span>
            </div>
            <div class="flex justify-between text-sm py-1">
                <span class="text-gray-600">Duration</span>
                <span class="font-semibold" id="mobile_displayMonths">1 month</span>
            </div>
            <div class="flex justify-between text-sm py-1">
                <span class="text-gray-600">Total Rent</span>
                <span class="font-semibold" id="mobile_totalRent">₹0</span>
            </div>
            <div class="flex justify-between text-sm py-1">
                <span class="text-gray-600">Security Deposit</span>
                <span class="font-semibold" id="mobile_securityDeposit">₹0</span>
            </div>
            <div id="mobile_couponRow" class="flex justify-between text-sm py-1 text-green-700 hidden">
                <span>Coupon Discount</span>
                <span id="mobile_couponAmount">-₹0</span>
            </div>
            <div id="mobile_walletRow" class="flex justify-between text-sm py-1 text-green-700 hidden">
                <span>Wallet Used</span>
                <span id="mobile_walletAmount">-₹0</span>
            </div>
            <hr class="my-2 border-gray-200">
            <div class="flex justify-between items-center">
                <span class="text-gray-900 font-bold">Total Payable</span>
                <span class="text-[#003B95] font-bold text-xl" id="mobile_totalPayable">₹0</span>
            </div>
            <div class="text-right mt-1">
                <p class="text-xs text-green-600">You save: <span id="mobile_savings">₹0</span></p>
            </div>
        </div>

        <!-- Terms -->
        <div class="mx-4 mb-4">
            <label class="flex items-start gap-2 text-xs text-gray-500">
                <input type="checkbox" id="mobile_terms" class="mt-0.5">
               <span class="text-sm text-gray-600">
                        By continuing, you confirm that you are above 18 years of age and agree to PG Mitra's
                        <a href="terms-and-conditions" class="text-[#003B95] font-semibold">Terms & Conditions</a>
                        and
                        <a href="privacy-policy" class="text-[#003B95] font-semibold">Privacy Policy</a>.
                    </span>
            </label>
        </div>
    </div>

    <!-- Mobile Step 2 -->
    <div id="mobileStep2Container" class="hidden">
        <!-- Price Breakdown -->
        <div class="bg-white rounded-xl p-4 mx-4 mb-3 shadow-sm border border-gray-100">
            <div class="flex justify-between text-sm py-1">
                <span class="text-gray-600">Total Rent</span>
                <span class="font-semibold" id="mobile2_totalRent">₹0</span>
            </div>
            <div class="flex justify-between text-sm py-1">
                <span class="text-gray-600">Security Deposit</span>
                <span class="font-semibold" id="mobile2_securityDeposit">₹0</span>
            </div>
            <div id="mobile2_couponRow" class="flex justify-between text-sm py-1 text-green-700 hidden">
                <span>Coupon Discount</span>
                <span id="mobile2_couponAmount">-₹0</span>
            </div>
            <div id="mobile2_walletRow" class="flex justify-between text-sm py-1 text-green-700 hidden">
                <span>Wallet Used</span>
                <span id="mobile2_walletAmount">-₹0</span>
            </div>
            <hr class="my-2 border-gray-200">
            <div class="flex justify-between items-center">
                <span class="font-bold text-gray-900">Total to Pay</span>
                <span class="text-[#003B95] font-bold text-xl" id="mobile2_totalPayable">₹0</span>
            </div>
            <div class="mt-2 text-xs text-green-600 bg-green-50 p-2 rounded-lg">
                <i class="fas fa-tag mr-1"></i> You save: <span id="mobile2_savings">₹0</span>
            </div>
        </div>

        <!-- Payment Options -->
        <div class="bg-white rounded-xl p-4 mx-4 mb-3 shadow-sm border border-gray-100">
            <h3 class="font-semibold text-gray-800 text-sm mb-3">Payment Option</h3>
            <div class="space-y-2">
                <div id="mobile_payOnline" class="payment-method-box p-3 selected">
                    <div class="flex items-center gap-2">
                        <input type="radio" name="mobile_payment" value="online" checked class="w-4 h-4">
                        <div class="flex-1">
                            <span class="font-semibold text-sm">Online Payment</span>
                            <span class="text-xs text-gray-500 ml-2">Card, UPI, NetBanking</span>
                        </div>
                        <i class="fas fa-credit-card text-[#003B95] text-lg"></i>
                    </div>
                </div>
                <div id="mobile_payLater" class="payment-method-box p-3">
                    <div class="flex items-center gap-2">
                        <input type="radio" name="mobile_payment" value="later" class="w-4 h-4">
                        <div class="flex-1">
                            <span class="font-semibold text-sm">Pay Later</span>
                            <span class="text-xs text-gray-500 ml-2">At property on arrival</span>
                        </div>
                        <i class="fas fa-money-bill-wave text-green-600 text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Zero Payment Box -->
        <div id="mobile_zeroPaymentBox" class="bg-green-50 rounded-xl p-4 mx-4 border border-green-100 hidden">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                <div>
                    <p class="font-semibold text-green-800 text-sm">No payment required</p>
                    <p class="text-xs text-green-700">Wallet & discounts cover this booking</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sticky Bottom Bar (Desktop) -->
<div class="sticky-bottom-bar">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs text-gray-500">Payable amount</p>
                <h3 id="stickyBottomAmount" class="text-xl font-bold text-gray-900">₹0</h3>
                <p class="text-xs text-green-600 font-semibold">Savings: <span id="stickyBottomSavings">₹0</span></p>
            </div>

            <button id="continueToPaymentBtn" class="primary-btn flex items-center gap-2">
                <i class="fas fa-chevron-right"></i> Proceed to Payment
            </button>

            <button id="finalPayNowBtn" class="secondary-btn hidden items-center gap-2">
                <i class="fas fa-lock"></i> Pay Now
            </button>
        </div>
    </div>
</div>

<!-- Mobile Bottom Bar -->
<div class="mobile-bottom-bar">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs text-gray-500">Payable amount</p>
            <h3 id="mobileBottomAmount" class="text-xl font-bold text-[#003B95]">₹0</h3>
            <p class="text-xs text-green-600">Save: <span id="mobileBottomSavings">₹0</span></p>
        </div>

        <button id="mobileContinueBtn" class="bg-[#003B95] text-white px-6 py-3 rounded-xl font-semibold text-sm">
            Continue <i class="fas fa-arrow-right ml-1"></i>
        </button>

        <button id="mobilePayNowBtn" class="bg-[#10B981] text-white px-6 py-3 rounded-xl font-semibold text-sm hidden">
            <i class="fas fa-lock mr-1"></i> Pay Now
        </button>
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
// Safe JSON data from PHP
const AppConfig = <?php echo json_encode($safe_data); ?>;

// Extract values from config
const monthlyPrice = AppConfig.monthly_price;
const securityDeposit = AppConfig.security_deposit;
let walletBalance = AppConfig.wallet_balance;
const roomId = AppConfig.room_id;
const userId = AppConfig.user_id;
const RAZORPAY_KEY_ID = AppConfig.razorpay_key;
const CSRF_TOKEN = AppConfig.csrf_token;
const userFullName = AppConfig.full_name;
const userEmail = AppConfig.email;
const userPhone = AppConfig.phone;

// State Variables
let selectedMonths = 1;
let guests = 1;
let checkInDate = "";
let specialRequests = "";
let useWallet = false;
let couponDiscountAmount = 0;
let appliedCouponCode = null;
let appliedCouponId = null;
let walletDeduction = 0;
let totalBaseAmount = 0;
let finalPayable = 0;
let isProcessing = false;
let isMobile = window.innerWidth <= 768;

// DOM Elements - Desktop
const monthsSelect = document.getElementById('months_step1');
const guestsSelect = document.getElementById('guests_step1');
const checkInInput = document.getElementById('check_in_step1');
const requestsText = document.getElementById('requests_step1');
const useWalletCheck = document.getElementById('useWalletCheckbox');
const applyCouponBtn = document.getElementById('applyCouponBtnUI');
const couponCodeInput = document.getElementById('couponCodeInput');
const termsCheckbox = document.getElementById('termsCheckbox');
const continueBtn = document.getElementById('continueToPaymentBtn');
const finalPayBtn = document.getElementById('finalPayNowBtn');
const stickyBottomAmount = document.getElementById('stickyBottomAmount');
const stickyBottomSavings = document.getElementById('stickyBottomSavings');
const step1Div = document.getElementById('step1Container');
const step2Div = document.getElementById('step2Container');
const backBtn = document.getElementById('backToStep1');
const stepDot1 = document.getElementById('stepDot1');
const stepDot2 = document.getElementById('stepDot2');

// DOM Elements - Mobile
const mobileCheckIn = document.getElementById('mobile_check_in');
const mobileMonths = document.getElementById('mobile_months');
const mobileGuests = document.getElementById('mobile_guests');
const mobileRequests = document.getElementById('mobile_requests');
const mobileUseWallet = document.getElementById('mobile_useWallet');
const mobileCouponCode = document.getElementById('mobile_couponCode');
const mobileApplyCoupon = document.getElementById('mobile_applyCoupon');
const mobileTerms = document.getElementById('mobile_terms');
const mobileContinueBtn = document.getElementById('mobileContinueBtn');
const mobilePayNowBtn = document.getElementById('mobilePayNowBtn');
const mobileStep1Container = document.getElementById('mobileStep1Container');
const mobileStep2Container = document.getElementById('mobileStep2Container');
const mobileStepDot1 = document.getElementById('mobileStepDot1');
const mobileStepDot2 = document.getElementById('mobileStepDot2');
const mobilePayOnline = document.getElementById('mobile_payOnline');
const mobilePayLater = document.getElementById('mobile_payLater');
const mobileBottomAmount = document.getElementById('mobileBottomAmount');
const mobileBottomSavings = document.getElementById('mobileBottomSavings');

// Utility Functions
function formatMoney(amount) {
    return '₹' + Number(amount || 0).toLocaleString('en-IN');
}

function formatMoneyNum(amount) {
    return Number(amount || 0).toLocaleString('en-IN');
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    let toastClass = 'toast-info';
    if (type === 'success') toastClass = 'toast-success';
    if (type === 'error') toastClass = 'toast-error';
    toast.className = `toast-message ${toastClass}`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function setButtonLoading(button, text) {
    button.disabled = true;
    button.innerHTML = `<span class="loading-spinner"></span><span>${text}</span>`;
}

function resetButton(button, originalText, iconHtml = '<i class="fas fa-lock"></i>') {
    button.disabled = false;
    button.innerHTML = `${iconHtml}<span>${originalText}</span>`;
}

function updateStickyBar() {
    const savings = couponDiscountAmount + walletDeduction;
    
    if (!isMobile) {
        stickyBottomAmount.innerText = formatMoney(finalPayable);
        stickyBottomSavings.innerText = formatMoney(savings);
        
        if (!step2Div.classList.contains('hidden')) {
            continueBtn.classList.add('hidden');
            finalPayBtn.classList.remove('hidden');
        } else {
            finalPayBtn.classList.add('hidden');
            continueBtn.classList.remove('hidden');
        }
        updateFinalButtonText();
    } else {
        if (mobileBottomAmount) mobileBottomAmount.innerText = formatMoney(finalPayable);
        if (mobileBottomSavings) mobileBottomSavings.innerText = formatMoney(savings);
        
        if (!mobileStep2Container.classList.contains('hidden')) {
            mobileContinueBtn.classList.add('hidden');
            mobilePayNowBtn.classList.remove('hidden');
        } else {
            mobilePayNowBtn.classList.add('hidden');
            mobileContinueBtn.classList.remove('hidden');
        }
        updateMobileFinalButtonText();
    }
}

function updateFinalButtonText() {
    const selectedMethod = document.querySelector('input[name="finalPaymentMethod"]:checked')?.value;

    if (finalPayable === 0) {
        finalPayBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Booking';
        return;
    }

    if (selectedMethod === 'later') {
        finalPayBtn.innerHTML = '<i class="fas fa-calendar-check"></i> Confirm Booking';
        return;
    }

    finalPayBtn.innerHTML = `<i class="fas fa-lock"></i> Pay ${formatMoney(finalPayable)}`;
}

function updateMobileFinalButtonText() {
    const selectedMethod = document.querySelector('input[name="mobile_payment"]:checked')?.value;

    if (finalPayable === 0) {
        mobilePayNowBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirm Booking';
        return;
    }

    if (selectedMethod === 'later') {
        mobilePayNowBtn.innerHTML = '<i class="fas fa-calendar-check"></i> Confirm Booking';
        return;
    }

    mobilePayNowBtn.innerHTML = `<i class="fas fa-lock"></i> Pay ${formatMoney(finalPayable)}`;
}

function toggleStep(step) {
    if (step === 1) {
        step1Div.classList.remove('hidden');
        step2Div.classList.add('hidden');
        stepDot1.classList.remove('step-inactive');
        stepDot1.classList.add('step-active');
        stepDot2.classList.remove('step-active');
        stepDot2.classList.add('step-inactive');
        
        if (isMobile) {
            mobileStep1Container.classList.remove('hidden');
            mobileStep2Container.classList.add('hidden');
            mobileStepDot1.classList.remove('step-inactive');
            mobileStepDot1.classList.add('step-active');
            mobileStepDot2.classList.remove('step-active');
            mobileStepDot2.classList.add('step-inactive');
        }
    } else {
        step1Div.classList.add('hidden');
        step2Div.classList.remove('hidden');
        stepDot2.classList.remove('step-inactive');
        stepDot2.classList.add('step-active');
        stepDot1.classList.remove('step-active');
        stepDot1.classList.add('step-inactive');
        
        if (isMobile) {
            mobileStep1Container.classList.add('hidden');
            mobileStep2Container.classList.remove('hidden');
            mobileStepDot2.classList.remove('step-inactive');
            mobileStepDot2.classList.add('step-active');
            mobileStepDot1.classList.remove('step-active');
            mobileStepDot1.classList.add('step-inactive');
        }
    }

    updateStickyBar();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function recalcTotals() {
    const totalRent = monthlyPrice * selectedMonths;
    totalBaseAmount = totalRent + securityDeposit;

    let afterCoupon = totalBaseAmount - couponDiscountAmount;
    if (afterCoupon < 0) afterCoupon = 0;

    if (useWallet && walletBalance > 0) {
        walletDeduction = Math.min(walletBalance, afterCoupon);
    } else {
        walletDeduction = 0;
    }

    finalPayable = afterCoupon - walletDeduction;
    if (finalPayable < 0) finalPayable = 0;

    const totalSaved = couponDiscountAmount + walletDeduction;

    // Update Desktop elements
    const step1MonthlyPrice = document.getElementById('step1MonthlyPrice');
    const displayMonths = document.getElementById('displayMonths');
    const totalRentVal = document.getElementById('totalRentVal');
    const step1SecurityDeposit = document.getElementById('step1SecurityDeposit');
    const totalPayableStep1 = document.getElementById('totalPayableStep1');
    const dynamicSavings = document.getElementById('dynamicSavings');
    const step1CouponRow = document.getElementById('step1CouponRow');
    const step1CouponVal = document.getElementById('step1CouponVal');
    const step1WalletRow = document.getElementById('step1WalletRow');
    const step1WalletVal = document.getElementById('step1WalletVal');
    const breakMonthlyPrice = document.getElementById('breakMonthlyPrice');
    const breakMonthsCount = document.getElementById('breakMonthsCount');
    const breakTotalRent = document.getElementById('breakTotalRent');
    const breakSecurityDeposit = document.getElementById('breakSecurityDeposit');
    const breakCouponRow = document.getElementById('breakCouponRow');
    const breakCouponAmount = document.getElementById('breakCouponAmount');
    const breakWalletRow = document.getElementById('breakWalletRow');
    const breakWalletAmount = document.getElementById('breakWalletAmount');
    const paymentPageTotal = document.getElementById('paymentPageTotal');
    const totalSavingsValueSpan = document.getElementById('totalSavingsValue');
    const walletInsufficientMsgDiv = document.getElementById('walletInsufficientMsg');

    if (step1MonthlyPrice) step1MonthlyPrice.innerText = formatMoney(monthlyPrice);
    if (displayMonths) displayMonths.innerText = `${selectedMonths} month${selectedMonths > 1 ? 's' : ''}`;
    if (totalRentVal) totalRentVal.innerText = formatMoney(totalRent);
    if (step1SecurityDeposit) step1SecurityDeposit.innerText = formatMoney(securityDeposit);
    if (totalPayableStep1) totalPayableStep1.innerText = formatMoney(finalPayable);
    if (dynamicSavings) dynamicSavings.innerText = formatMoney(totalSaved);

    if (couponDiscountAmount > 0) {
        if (step1CouponRow) step1CouponRow.classList.remove('hidden');
        if (step1CouponVal) step1CouponVal.innerText = '-₹' + formatMoneyNum(couponDiscountAmount);
    } else {
        if (step1CouponRow) step1CouponRow.classList.add('hidden');
    }

    if (walletDeduction > 0) {
        if (step1WalletRow) step1WalletRow.classList.remove('hidden');
        if (step1WalletVal) step1WalletVal.innerText = '-₹' + formatMoneyNum(walletDeduction);
    } else {
        if (step1WalletRow) step1WalletRow.classList.add('hidden');
    }

    if (useWallet && walletBalance > 0 && walletBalance < afterCoupon && afterCoupon > 0) {
        if (walletInsufficientMsgDiv) {
            walletInsufficientMsgDiv.innerHTML = `<i class="fas fa-info-circle mr-1"></i> Wallet partially used. Remaining ₹${formatMoneyNum(afterCoupon - walletDeduction)} to be paid.`;
            walletInsufficientMsgDiv.classList.remove('hidden');
        }
    } else {
        if (walletInsufficientMsgDiv) walletInsufficientMsgDiv.classList.add('hidden');
    }

    if (breakMonthlyPrice) breakMonthlyPrice.innerText = formatMoney(monthlyPrice);
    if (breakMonthsCount) breakMonthsCount.innerText = selectedMonths;
    if (breakTotalRent) breakTotalRent.innerText = formatMoney(totalRent);
    if (breakSecurityDeposit) breakSecurityDeposit.innerText = formatMoney(securityDeposit);

    if (couponDiscountAmount > 0) {
        if (breakCouponRow) breakCouponRow.classList.remove('hidden');
        if (breakCouponAmount) breakCouponAmount.innerText = '-₹' + formatMoneyNum(couponDiscountAmount);
    } else {
        if (breakCouponRow) breakCouponRow.classList.add('hidden');
    }

    if (walletDeduction > 0) {
        if (breakWalletRow) breakWalletRow.classList.remove('hidden');
        if (breakWalletAmount) breakWalletAmount.innerText = '-₹' + formatMoneyNum(walletDeduction);
    } else {
        if (breakWalletRow) breakWalletRow.classList.add('hidden');
    }

    if (paymentPageTotal) paymentPageTotal.innerText = formatMoney(finalPayable);
    if (totalSavingsValueSpan) totalSavingsValueSpan.innerText = formatMoney(totalSaved);

    const paymentMethodsSection = document.getElementById('paymentMethodsSection');
    const zeroPaymentBox = document.getElementById('zeroPaymentBox');

    if (finalPayable === 0) {
        if (paymentMethodsSection) paymentMethodsSection.classList.add('hidden');
        if (zeroPaymentBox) zeroPaymentBox.classList.remove('hidden');
    } else {
        if (paymentMethodsSection) paymentMethodsSection.classList.remove('hidden');
        if (zeroPaymentBox) zeroPaymentBox.classList.add('hidden');
    }

    // Update Mobile elements
    const mobileMonthlyPrice = document.getElementById('mobile_monthlyPrice');
    const mobileDisplayMonths = document.getElementById('mobile_displayMonths');
    const mobileTotalRent = document.getElementById('mobile_totalRent');
    const mobileSecurityDeposit = document.getElementById('mobile_securityDeposit');
    const mobileTotalPayable = document.getElementById('mobile_totalPayable');
    const mobileSavings = document.getElementById('mobile_savings');
    const mobileCouponRow = document.getElementById('mobile_couponRow');
    const mobileCouponAmount = document.getElementById('mobile_couponAmount');
    const mobileWalletRow = document.getElementById('mobile_walletRow');
    const mobileWalletAmount = document.getElementById('mobile_walletAmount');
    const mobile2TotalRent = document.getElementById('mobile2_totalRent');
    const mobile2SecurityDeposit = document.getElementById('mobile2_securityDeposit');
    const mobile2TotalPayable = document.getElementById('mobile2_totalPayable');
    const mobile2Savings = document.getElementById('mobile2_savings');
    const mobile2CouponRow = document.getElementById('mobile2_couponRow');
    const mobile2CouponAmount = document.getElementById('mobile2_couponAmount');
    const mobile2WalletRow = document.getElementById('mobile2_walletRow');
    const mobile2WalletAmount = document.getElementById('mobile2_walletAmount');
    const mobileZeroPaymentBox = document.getElementById('mobile_zeroPaymentBox');

    if (mobileMonthlyPrice) mobileMonthlyPrice.innerText = formatMoney(monthlyPrice);
    if (mobileDisplayMonths) mobileDisplayMonths.innerText = `${selectedMonths} month${selectedMonths > 1 ? 's' : ''}`;
    if (mobileTotalRent) mobileTotalRent.innerText = formatMoney(totalRent);
    if (mobileSecurityDeposit) mobileSecurityDeposit.innerText = formatMoney(securityDeposit);
    if (mobileTotalPayable) mobileTotalPayable.innerText = formatMoney(finalPayable);
    if (mobileSavings) mobileSavings.innerText = formatMoney(totalSaved);
    if (mobile2TotalRent) mobile2TotalRent.innerText = formatMoney(totalRent);
    if (mobile2SecurityDeposit) mobile2SecurityDeposit.innerText = formatMoney(securityDeposit);
    if (mobile2TotalPayable) mobile2TotalPayable.innerText = formatMoney(finalPayable);
    if (mobile2Savings) mobile2Savings.innerText = formatMoney(totalSaved);

    if (couponDiscountAmount > 0) {
        if (mobileCouponRow) mobileCouponRow.classList.remove('hidden');
        if (mobileCouponAmount) mobileCouponAmount.innerText = '-₹' + formatMoneyNum(couponDiscountAmount);
        if (mobile2CouponRow) mobile2CouponRow.classList.remove('hidden');
        if (mobile2CouponAmount) mobile2CouponAmount.innerText = '-₹' + formatMoneyNum(couponDiscountAmount);
    } else {
        if (mobileCouponRow) mobileCouponRow.classList.add('hidden');
        if (mobile2CouponRow) mobile2CouponRow.classList.add('hidden');
    }

    if (walletDeduction > 0) {
        if (mobileWalletRow) mobileWalletRow.classList.remove('hidden');
        if (mobileWalletAmount) mobileWalletAmount.innerText = '-₹' + formatMoneyNum(walletDeduction);
        if (mobile2WalletRow) mobile2WalletRow.classList.remove('hidden');
        if (mobile2WalletAmount) mobile2WalletAmount.innerText = '-₹' + formatMoneyNum(walletDeduction);
    } else {
        if (mobileWalletRow) mobileWalletRow.classList.add('hidden');
        if (mobile2WalletRow) mobile2WalletRow.classList.add('hidden');
    }

    if (finalPayable === 0) {
        if (mobileZeroPaymentBox) mobileZeroPaymentBox.classList.remove('hidden');
    } else {
        if (mobileZeroPaymentBox) mobileZeroPaymentBox.classList.add('hidden');
    }

    updateStickyBar();
}

// Coupon Functions
async function applyCoupon() {
    const code = isMobile ? mobileCouponCode.value.trim().toUpperCase() : couponCodeInput.value.trim().toUpperCase();

    if (!code) {
        showToast("Please enter a coupon code", "error");
        return;
    }

    if (totalBaseAmount <= 0) {
        showToast("Please select valid booking details", "error");
        return;
    }

    try {
        const response = await fetch('/api/validate-coupon', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                code: code,
                amount: totalBaseAmount,
                csrf_token: CSRF_TOKEN
            })
        });

        const data = await response.json();

        if (!data.valid) {
            throw new Error(data.message || "Invalid coupon code");
        }

        couponDiscountAmount = Number(data.discount || 0);
        appliedCouponCode = code;
        appliedCouponId = data.coupon_id || null;

        const couponAppliedMsgDiv = document.getElementById('couponAppliedMsg');
        if (couponAppliedMsgDiv) {
            couponAppliedMsgDiv.innerHTML = `
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <div class="font-semibold">
                            <i class="fas fa-check-circle mr-1"></i>${data.message}
                        </div>
                        <div class="text-[11px] mt-1">
                            Coupon <strong>${code}</strong> applied successfully.
                        </div>
                    </div>
                    <button type="button" onclick="removeCoupon()" class="text-blue-600 font-semibold whitespace-nowrap">
                        Remove
                    </button>
                </div>
            `;
            couponAppliedMsgDiv.classList.remove('hidden');
        }
        
        const mobileCouponMsgDiv = document.getElementById('mobile_couponMsg');
        if (mobileCouponMsgDiv) {
            mobileCouponMsgDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <div>
                        <i class="fas fa-check-circle mr-1"></i>${data.message}
                    </div>
                    <button type="button" onclick="removeCoupon()" class="text-blue-600 text-xs">Remove</button>
                </div>
            `;
            mobileCouponMsgDiv.classList.remove('hidden');
        }
        
        if (isMobile && mobileCouponCode) mobileCouponCode.value = '';
        if (!isMobile && couponCodeInput) couponCodeInput.value = '';
        
        recalcTotals();
        showToast(data.message, "success");

    } catch (error) {
        couponDiscountAmount = 0;
        appliedCouponCode = null;
        appliedCouponId = null;
        showToast(error.message || "Error validating coupon", "error");
    }
}

window.removeCoupon = function() {
    couponDiscountAmount = 0;
    appliedCouponCode = null;
    appliedCouponId = null;
    const couponAppliedMsgDiv = document.getElementById('couponAppliedMsg');
    if (couponAppliedMsgDiv) couponAppliedMsgDiv.classList.add('hidden');
    const mobileCouponMsgDiv = document.getElementById('mobile_couponMsg');
    if (mobileCouponMsgDiv) mobileCouponMsgDiv.classList.add('hidden');
    recalcTotals();
    showToast("Coupon removed", 'info');
};

function setPaymentMethod(method) {
    if (method === 'online') {
        const payOnlineMethod = document.getElementById('payOnlineMethod');
        const payLaterMethod = document.getElementById('payLaterMethod');
        const onlineRadio = document.querySelector('input[name="finalPaymentMethod"][value="online"]');
        const laterRadio = document.querySelector('input[name="finalPaymentMethod"][value="later"]');
        
        if (payOnlineMethod) payOnlineMethod.classList.add('selected');
        if (payLaterMethod) payLaterMethod.classList.remove('selected');
        if (onlineRadio) onlineRadio.checked = true;
        if (laterRadio) laterRadio.checked = false;
    } else {
        const payOnlineMethod = document.getElementById('payOnlineMethod');
        const payLaterMethod = document.getElementById('payLaterMethod');
        const onlineRadio = document.querySelector('input[name="finalPaymentMethod"][value="online"]');
        const laterRadio = document.querySelector('input[name="finalPaymentMethod"][value="later"]');
        
        if (payLaterMethod) payLaterMethod.classList.add('selected');
        if (payOnlineMethod) payOnlineMethod.classList.remove('selected');
        if (laterRadio) laterRadio.checked = true;
        if (onlineRadio) onlineRadio.checked = false;
    }
    updateFinalButtonText();
}

function setMobilePaymentMethod(method) {
    if (method === 'online') {
        mobilePayOnline.classList.add('selected');
        mobilePayLater.classList.remove('selected');
        const radio = document.querySelector('input[name="mobile_payment"][value="online"]');
        if (radio) radio.checked = true;
    } else {
        mobilePayLater.classList.add('selected');
        mobilePayOnline.classList.remove('selected');
        const radio = document.querySelector('input[name="mobile_payment"][value="later"]');
        if (radio) radio.checked = true;
    }
    updateMobileFinalButtonText();
}

// Booking Submission
async function submitBooking(paymentMethod, paymentData = null) {
    if (isProcessing) return;
    isProcessing = true;

    const bookingData = {
        room_id: roomId,
        check_in: checkInDate,
        months: selectedMonths,
        guests: guests,
        special_requests: specialRequests,
        use_wallet: useWallet ? 1 : 0,
        coupon_id: appliedCouponId,
        payment_method: paymentMethod,
        csrf_token: CSRF_TOKEN
    };

    if (paymentData) {
        bookingData.razorpay_payment_id = paymentData.razorpay_payment_id;
        bookingData.razorpay_signature = paymentData.razorpay_signature;
        bookingData.razorpay_order_id = paymentData.razorpay_order_id;
    }

    try {
        const response = await fetch('/api/book_room_now', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookingData)
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message || 'Booking successful!', 'success');
            setTimeout(() => {
                window.location.href = 'booking_confirmation?id=' + data.booking_id;
            }, 1500);
        } else {
            throw new Error(data.message || 'Booking failed');
        }
    } catch (error) {
        showToast(error.message, 'error');
        isProcessing = false;
    }
}

// Razorpay Payment
async function initiateRazorpayPayment() {
    if (isProcessing) return;
    
    const payBtn = isMobile ? mobilePayNowBtn : finalPayBtn;
    setButtonLoading(payBtn, 'Creating order...');
    isProcessing = true;

    const orderData = {
        room_id: roomId,
        check_in: checkInDate,
        months: selectedMonths,
        guests: guests,
        use_wallet: useWallet ? 1 : 0,
        coupon_id: appliedCouponId,
        amount: finalPayable,
        csrf_token: CSRF_TOKEN
    };

    try {
        const response = await fetch('/api/create-razorpay-order', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
        });

        const order = await response.json();

        if (!order.success) {
            throw new Error(order.message || "Order creation failed");
        }

        const options = {
            key: RAZORPAY_KEY_ID,
            amount: order.amount,
            currency: "INR",
            name: "PG Mitra",
            description: "Room Booking",
            order_id: order.order_id,
            prefill: {
                name: userFullName,
                email: userEmail,
                contact: userPhone
            },
            theme: {
                color: "#003B95"
            },
            handler: function(response) {
                submitBooking('online', {
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_signature: response.razorpay_signature,
                    razorpay_order_id: order.order_id
                });
            }
        };

        const rzp = new Razorpay(options);
        
        rzp.on('payment.failed', function(response) {
            showToast(response.error.description || "Payment failed. Please try again.", 'error');
            resetButton(payBtn, isMobile ? 'Pay Now' : 'Pay Now', '<i class="fas fa-lock"></i>');
            isProcessing = false;
        });

        rzp.open();
        
        setTimeout(() => {
            resetButton(payBtn, isMobile ? 'Pay Now' : 'Pay Now', '<i class="fas fa-lock"></i>');
        }, 500);

    } catch (error) {
        showToast(error.message, 'error');
        resetButton(payBtn, isMobile ? 'Pay Now' : 'Pay Now', '<i class="fas fa-lock"></i>');
        isProcessing = false;
    }
}

// Event Listeners - Desktop
if (monthsSelect) monthsSelect.addEventListener('change', (e) => {
    selectedMonths = parseInt(e.target.value) || 1;
    recalcTotals();
});

if (guestsSelect) guestsSelect.addEventListener('change', (e) => {
    guests = parseInt(e.target.value) || 1;
});

if (checkInInput) checkInInput.addEventListener('change', (e) => {
    if (e.target.value) {
        checkInDate = e.target.value;
        recalcTotals();
    }
});

if (useWalletCheck) useWalletCheck.addEventListener('change', (e) => {
    useWallet = e.target.checked;
    recalcTotals();
});

if (applyCouponBtn) applyCouponBtn.addEventListener('click', applyCoupon);

if (continueBtn) continueBtn.addEventListener('click', () => {
    if (!termsCheckbox.checked) {
        showToast("Please accept Terms & Conditions", 'error');
        return;
    }

    if (!checkInDate) {
        showToast("Please select check-in date", 'error');
        return;
    }

    specialRequests = requestsText.value.trim();
    recalcTotals();
    toggleStep(2);
});

if (backBtn) backBtn.addEventListener('click', () => {
    toggleStep(1);
});

if (document.getElementById('payOnlineMethod')) {
    document.getElementById('payOnlineMethod').addEventListener('click', () => setPaymentMethod('online'));
}
if (document.getElementById('payLaterMethod')) {
    document.getElementById('payLaterMethod').addEventListener('click', () => setPaymentMethod('later'));
}

if (finalPayBtn) finalPayBtn.addEventListener('click', async () => {
    if (isProcessing) return;
    
    const paymentMethod = document.querySelector('input[name="finalPaymentMethod"]:checked')?.value || 'online';

    if (paymentMethod === 'later') {
        setButtonLoading(finalPayBtn, 'Confirming...');
        await submitBooking('later');
        return;
    }

    if (finalPayable === 0) {
        setButtonLoading(finalPayBtn, 'Confirming...');
        await submitBooking('wallet_only');
        return;
    }

    if (paymentMethod === 'online' && finalPayable > 0) {
        await initiateRazorpayPayment();
    }
});

// Event Listeners - Mobile
if (mobileMonths) mobileMonths.addEventListener('change', (e) => {
    selectedMonths = parseInt(e.target.value) || 1;
    recalcTotals();
});

if (mobileGuests) mobileGuests.addEventListener('change', (e) => {
    guests = parseInt(e.target.value) || 1;
});

if (mobileCheckIn) mobileCheckIn.addEventListener('change', (e) => {
    if (e.target.value) {
        checkInDate = e.target.value;
        recalcTotals();
    }
});

if (mobileUseWallet) mobileUseWallet.addEventListener('change', (e) => {
    useWallet = e.target.checked;
    recalcTotals();
});

if (mobileApplyCoupon) mobileApplyCoupon.addEventListener('click', applyCoupon);

if (mobileContinueBtn) mobileContinueBtn.addEventListener('click', () => {
    if (!mobileTerms.checked) {
        showToast("Please accept Terms & Conditions", 'error');
        return;
    }

    if (!checkInDate) {
        showToast("Please select check-in date", 'error');
        return;
    }

    specialRequests = mobileRequests.value.trim();
    recalcTotals();
    toggleStep(2);
});

if (mobilePayOnline) mobilePayOnline.addEventListener('click', () => setMobilePaymentMethod('online'));
if (mobilePayLater) mobilePayLater.addEventListener('click', () => setMobilePaymentMethod('later'));

if (mobilePayNowBtn) mobilePayNowBtn.addEventListener('click', async () => {
    if (isProcessing) return;
    
    const paymentMethod = document.querySelector('input[name="mobile_payment"]:checked')?.value || 'online';

    if (paymentMethod === 'later') {
        setButtonLoading(mobilePayNowBtn, 'Confirming...');
        await submitBooking('later');
        return;
    }

    if (finalPayable === 0) {
        setButtonLoading(mobilePayNowBtn, 'Confirming...');
        await submitBooking('wallet_only');
        return;
    }

    if (paymentMethod === 'online' && finalPayable > 0) {
        await initiateRazorpayPayment();
    }
});

// Initialize
const today = new Date().toISOString().slice(0, 10);
if (checkInInput) {
    checkInInput.value = today;
    checkInInput.min = today;
    checkInDate = today;
}
if (mobileCheckIn) {
    mobileCheckIn.value = today;
    mobileCheckIn.min = today;
    if (!checkInDate) checkInDate = today;
}

recalcTotals();
toggleStep(1);
setPaymentMethod('online');
setMobilePaymentMethod('online');

function goToPage(page) {
    if (page === 'home') window.location.href = '/';
    else if (page === 'search') window.location.href = '/search';
    else if (page === 'bookings') window.location.href = '/bookings';
    else if (page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if (page === 'profile') window.location.href = '/profile';
}
</script>

</body>
</html>