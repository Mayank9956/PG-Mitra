<?php
require_once 'common/auth.php';

// Ensure user is logged in
$user = requireAuth($conn);

$user_id = $user['id'];
$completed_days = 0;

// Display name
$display_name = !empty($user['full_name']) 
    ? $user['full_name'] 
    : $user['username'];

// Profile image
$profile_image = !empty($user['profile_image']) 
    ? $user['profile_image'] 
    : 'https://ui-avatars.com/api/?name=' . urlencode($display_name);


// ==============================
// APP STATISTICS
// ==============================
$stats_query = "SELECT 
    (SELECT COUNT(*) FROM users) as total_users,
    (SELECT COUNT(*) FROM rooms WHERE is_available = 1) as total_rooms,
    (SELECT COUNT(*) FROM bookings WHERE status = 'confirmed') as total_bookings";

$stmt = $conn->prepare($stats_query);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>About StayEase - College Room Booking App</title>

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
    padding: 30px 20px 40px;
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

/* Logo */
.logo-container {
    width: 80px;
    height: 80px;
    background: white;
    border-radius: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.logo-container i {
    font-size: 40px;
    color: #667eea;
}

/* Feature Card */
.feature-card {
    background: white;
    border-radius: 24px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    border: 1px solid #F3F4F6;
    transition: all 0.3s;
}

.feature-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 30px -10px rgba(102,126,234,0.15);
    border-color: #667eea;
}

.feature-icon {
    width: 60px;
    height: 60px;
    background: #EFF6FF;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
}

.feature-icon i {
    font-size: 28px;
    color: #667eea;
}

.feature-title {
    font-size: 18px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 8px;
}

.feature-desc {
    font-size: 14px;
    color: #6B7280;
    line-height: 1.6;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin: 20px 0;
}

.stat-item {
    background: #F9FAFB;
    border-radius: 20px;
    padding: 20px;
    text-align: center;
}

.stat-number {
    font-size: 28px;
    font-weight: 800;
    color: #667eea;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 12px;
    color: #6B7280;
    font-weight: 500;
}

/* How It Works */
.step-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 24px;
}

.step-number {
    width: 40px;
    height: 40px;
    background: #667eea;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
    flex-shrink: 0;
}

.step-content h4 {
    font-size: 16px;
    font-weight: 700;
    color: #1F2937;
    margin-bottom: 4px;
}

.step-content p {
    font-size: 13px;
    color: #6B7280;
    line-height: 1.5;
}

/* Referral Highlight */
.referral-highlight {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 24px;
    padding: 24px;
    color: white;
    margin: 20px 0;
    position: relative;
    overflow: hidden;
}

.referral-highlight::before {
    content: '';
    position: absolute;
    top: -20px;
    right: -20px;
    width: 150px;
    height: 150px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.referral-highlight h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 10px;
}

.referral-highlight p {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 20px;
    line-height: 1.6;
}

.referral-badge {
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border-radius: 50px;
    padding: 12px 20px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: 1px solid rgba(255,255,255,0.3);
}

.referral-badge i {
    font-size: 20px;
}

.referral-badge span {
    font-size: 14px;
    font-weight: 600;
}

/* Team Section */
.team-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 20px;
}

.team-member {
    text-align: center;
}

.member-avatar {
    width: 80px;
    height: 80px;
    background: #EFF6FF;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
}

.member-avatar i {
    font-size: 32px;
    color: #667eea;
}

.member-name {
    font-size: 14px;
    font-weight: 600;
    color: #1F2937;
    margin-bottom: 2px;
}

.member-role {
    font-size: 11px;
    color: #9CA3AF;
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

/* Divider */
.divider {
    display: flex;
    align-items: center;
    text-align: center;
    margin: 30px 0 20px;
    color: #9CA3AF;
    font-size: 14px;
}

.divider::before,
.divider::after {
    content: '';
    flex: 1;
    border-bottom: 1px solid #E5E7EB;
}

.divider::before {
    margin-right: 15px;
}

.divider::after {
    margin-left: 15px;
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
            
            <div class="logo-container">
                <i class="fas fa-home"></i>
            </div>
            
            <h1 class="text-white text-3xl font-bold">StayEase</h1>
            <p class="text-white/80 text-sm mt-2">Your Perfect College Room Partner</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-5 pb-24">
        <!-- App Description -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-3">About StayEase</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                StayEase is India's first dedicated platform designed exclusively for college students to find, book, and manage affordable PG accommodations and hostels near their campuses. We understand the struggles of finding safe and budget-friendly stays during college years.
            </p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?php echo number_format($stats['total_users'] ?? 15000); ?>+</div>
                <div class="stat-label">Happy Students</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo number_format($stats['total_rooms'] ?? 5000); ?>+</div>
                <div class="stat-label">PG Rooms</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo number_format($stats['total_colleges'] ?? 200); ?>+</div>
                <div class="stat-label">Colleges</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo number_format($stats['total_bookings'] ?? 25000); ?>+</div>
                <div class="stat-label">Bookings Done</div>
            </div>
        </div>

        <!-- Features -->
        <h3 class="font-bold text-gray-800 text-lg mb-3">Why Students Love StayEase</h3>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h4 class="feature-title">College-Centric Listings</h4>
            <p class="feature-desc">
                All PG accommodations are verified and located within 5km radius of major colleges. Filter by your college name and find the perfect stay within walking distance or short commute.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h4 class="feature-title">Safe & Verified</h4>
            <p class="feature-desc">
                Every property is physically verified by our team. We ensure proper security, CCTV cameras, and women-safe accommodations with separate floors for girls and boys.
            </p>
        </div>

        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <h4 class="feature-title">Student Budget Friendly</h4>
            <p class="feature-desc">
                Starting from just ₹2,500/month. Special discounts for long-term stays and group bookings. No hidden charges, transparent pricing.
            </p>
        </div>

        <!-- Referral Program Highlight -->
        <div class="referral-highlight">
            <h3><i class="fas fa-gift mr-2"></i> Earn While You Save</h3>
            <p>
                Refer your friends and earn real money! Every successful referral gives you ₹100 in your wallet. 
                You can withdraw this money directly to your bank account or UPI, or use it to pay your room rent.
            </p>
            
            <div class="referral-badge">
                <i class="fas fa-rupee-sign"></i>
                <span>Refer & Earn ₹100 per friend</span>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="bg-white/10 rounded-xl p-3 text-center">
                    <i class="fas fa-wallet text-xl mb-1"></i>
                    <p class="text-xs">Withdraw to Bank</p>
                </div>
                <div class="bg-white/10 rounded-xl p-3 text-center">
                    <i class="fas fa-mobile-alt text-xl mb-1"></i>
                    <p class="text-xs">Pay Rent via Wallet</p>
                </div>
            </div>
        </div>

        <!-- How Wallet Works -->
        <div class="bg-green-50 border border-green-200 rounded-2xl p-5 mb-6">
            <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-wallet text-green-600"></i>
                How StayEase Wallet Works
            </h4>
            
            <div class="step-item">
                <div class="step-number" style="background: #10B981;">1</div>
                <div class="step-content">
                    <h4>Refer Friends</h4>
                    <p>Share your referral code with college friends. Get ₹100 for each friend who books through StayEase.</p>
                </div>
            </div>
            
            <div class="step-item">
                <div class="step-number" style="background: #10B981;">2</div>
                <div class="step-content">
                    <h4>Earn Money in Wallet</h4>
                    <p>Your referral earnings are credited directly to your StayEase wallet instantly.</p>
                </div>
            </div>
            
            <div class="step-item">
                <div class="step-number" style="background: #10B981;">3</div>
                <div class="step-content">
                    <h4>Withdraw or Pay Rent</h4>
                    <p>Transfer money to your bank account/UPI, or use wallet balance to pay your monthly room rent directly through the app.</p>
                </div>
            </div>
            
            <div class="flex gap-3 mt-4">
                <button onclick="goToPage('refer-earn.php')" class="flex-1 bg-green-600 text-white py-3 rounded-xl text-sm font-semibold">
                    <i class="fas fa-gift mr-1"></i> Refer Now
                </button>
                <button onclick="goToPage('withdraw.php')" class="flex-1 bg-white text-green-600 py-3 rounded-xl text-sm font-semibold border border-green-200">
                    <i class="fas fa-arrow-right mr-1"></i> Withdraw
                </button>
            </div>
        </div>

        <!-- How It Works -->
        <h3 class="font-bold text-gray-800 text-lg mb-3">How It Works</h3>
        
        <div class="step-item">
            <div class="step-number">1</div>
            <div class="step-content">
                <h4>Search by College</h4>
                <p>Enter your college name or city. Browse through verified PGs and hostels near your campus.</p>
            </div>
        </div>
        
        <div class="step-item">
            <div class="step-number">2</div>
            <div class="step-content">
                <h4>Book Online</h4>
                <p>Select your preferred room, pay a small booking amount, and secure your stay instantly.</p>
            </div>
        </div>
        
        <div class="step-item">
            <div class="step-number">3</div>
            <div class="step-content">
                <h4>Move In & Enjoy</h4>
                <p>Complete your documents, pay remaining rent via app, and start your hassle-free stay.</p>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider">Our Impact</div>

        <!-- Testimonials -->
        <div class="bg-gray-50 rounded-2xl p-5 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-quote-right text-xl text-blue-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Rahul, DTU</p>
                    <p class="text-xs text-gray-500">Computer Science, 3rd Year</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 italic">
                "StayEase helped me find affordable PG near DTU. I've already referred 5 friends and earned ₹500 which I used to pay my rent!"
            </p>
        </div>

        <div class="bg-gray-50 rounded-2xl p-5 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center">
                    <i class="fas fa-quote-right text-xl text-pink-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Priya, Lady Shri Ram College</p>
                    <p class="text-xs text-gray-500">Psychology, 2nd Year</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 italic">
                "As a girl, safety was my priority. StayEase's verified women-only PGs gave me peace of mind. The wallet feature is a bonus!"
            </p>
        </div>

        <!-- Trust Badges -->
        <div class="flex flex-wrap gap-3 justify-center my-6">
            <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-full text-xs">
                <i class="fas fa-check-circle text-green-600 mr-1"></i> 5000+ Verified PGs
            </span>
            <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-full text-xs">
                <i class="fas fa-shield-alt text-blue-600 mr-1"></i> Safe Stay Guarantee
            </span>
            <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-full text-xs">
                <i class="fas fa-rupee-sign text-green-600 mr-1"></i> Lowest Prices
            </span>
            <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-full text-xs">
                <i class="fas fa-clock text-orange-600 mr-1"></i> 24/7 Support
            </span>
        </div>

        <!-- CTA Buttons -->
        <div class="flex gap-3 mt-8 mb-4">
            <button onclick="goToPage('refer-earn.php')" class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white py-4 rounded-2xl font-semibold text-sm shadow-lg">
                <i class="fas fa-gift mr-1"></i> Refer & Earn
            </button>
            <button onclick="goToPage('search.php')" class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-500 text-white py-4 rounded-2xl font-semibold text-sm shadow-lg">
                <i class="fas fa-search mr-1"></i> Find PG
            </button>
        </div>

        <!-- Version -->
        <p class="text-center text-xs text-gray-400 mt-6">
            StayEase v1.0.0 | Made with <i class="fas fa-heart text-red-500"></i> for College Students
        </p>
    </div>

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
function goBack() {
    window.history.back();
}

function goToPage(page) {
    window.location.href = page;
}
</script>

</body>
</html>