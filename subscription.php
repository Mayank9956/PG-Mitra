<?php
session_start();
require_once 'common/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$user_id = (int) $_SESSION['user_id'];

// Get user details
$user_query = "SELECT username, full_name, profile_image FROM users WHERE id = ?";
$stmt = $db->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$display_name = !empty($user['full_name']) ? $user['full_name'] : $user['username'];
$profile_image = !empty($user['profile_image']) ? $user['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($display_name) . '&background=003B95&color=fff';

/*
|--------------------------------------------------------------------------
| Active subscription
|--------------------------------------------------------------------------
*/
$active_subscription = null;

$stmt = $db->prepare("
    SELECT 
        us.*,
        sp.name AS plan_name,
        sp.slug AS plan_slug,
        sp.discount_percent
    FROM user_subscriptions us
    INNER JOIN subscription_plans sp ON sp.id = us.plan_id
    WHERE us.user_id = ?
      AND us.status = 'active'
      AND (us.end_date IS NULL OR us.end_date >= NOW())
    ORDER BY us.end_date DESC, us.id DESC
    LIMIT 1
");

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $active_subscription = $result ? $result->fetch_assoc() : null;

    if ($active_subscription) {
        $remaining_days = 0;

        if (!empty($active_subscription['end_date'])) {
            $end = new DateTime($active_subscription['end_date']);
            $now = new DateTime();

            if ($end > $now) {
                $diff = $now->diff($end);
                $remaining_days = (int) $diff->days;
                if ($diff->h > 0 || $diff->i > 0 || $diff->s > 0) {
                    $remaining_days += 1;
                }
            }
        }

        $active_subscription['remaining_days'] = $remaining_days;
    }
}

/*
|--------------------------------------------------------------------------
| Fetch only active plans
|--------------------------------------------------------------------------
*/
$plans = [];

$plan_query = "
    SELECT *
    FROM subscription_plans
    WHERE is_active = '1'
    ORDER BY 
        CASE 
            WHEN billing_type = 'monthly' THEN 1
            WHEN billing_type = 'yearly' THEN 2
            ELSE 3
        END,
        price ASC,
        id ASC
";

$plan_stmt = $db->prepare($plan_query);
if ($plan_stmt) {
    $plan_stmt->execute();
    $plan_result = $plan_stmt->get_result();

    while ($row = $plan_result->fetch_assoc()) {
        $plans[] = $row;
    }
}

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/
if (!function_exists('normalizePlanKey')) {
    function normalizePlanKey($value) {
        $value = strtolower(trim((string)$value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value);
        return trim($value, '-');
    }
}

/*
|--------------------------------------------------------------------------
| Group monthly/yearly by family
|--------------------------------------------------------------------------
*/
$plans_by_family = [];

foreach ($plans as $plan) {
    $slug = strtolower($plan['slug'] ?? '');
    $name = $plan['name'] ?? 'Plan';
    $billing_type = strtolower($plan['billing_type'] ?? 'monthly');

    $family_key = preg_replace('/-(monthly|yearly)$/', '', $slug);
    if (empty($family_key)) {
        $family_key = normalizePlanKey($name);
    }

    if (!isset($plans_by_family[$family_key])) {
        $plans_by_family[$family_key] = [
            'family_key' => $family_key,
            'name' => $name,
            'monthly' => null,
            'yearly' => null,
            'description' => $plan['description'] ?? '',
            'features' => [],
            'discount_percent' => $plan['discount_percent'] ?? 0
        ];
    }

    if ($billing_type === 'monthly') {
        $plans_by_family[$family_key]['monthly'] = $plan;
    } elseif ($billing_type === 'yearly') {
        $plans_by_family[$family_key]['yearly'] = $plan;
    } else {
        if (!$plans_by_family[$family_key]['monthly']) {
            $plans_by_family[$family_key]['monthly'] = $plan;
        }
    }

    if (!empty($plan['description']) && empty($plans_by_family[$family_key]['description'])) {
        $plans_by_family[$family_key]['description'] = $plan['description'];
    }

    if (!empty($plan['discount_percent']) && empty($plans_by_family[$family_key]['discount_percent'])) {
        $plans_by_family[$family_key]['discount_percent'] = $plan['discount_percent'];
    }

    if (!empty($plan['features'])) {
        $decoded_features = json_decode($plan['features'], true);
        if (is_array($decoded_features) && !empty($decoded_features)) {
            $plans_by_family[$family_key]['features'] = $decoded_features;
        }
    }
}

// Active plan family
$current_plan_family = '';
if ($active_subscription && !empty($active_subscription['plan_slug'])) {
    $current_plan_family = preg_replace('/-(monthly|yearly)$/', '', strtolower($active_subscription['plan_slug']));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Subscription Plans - PG Mitra</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
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
    padding: 40px;
    margin-bottom: 40px;
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
    font-size: 32px;
    font-weight: 800;
    color: white;
    margin-bottom: 12px;
}

.hero-text p {
    font-size: 16px;
    color: rgba(255,255,255,0.9);
    max-width: 500px;
}

.hero-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-icon i {
    font-size: 40px;
    color: #FFB700;
}

/* Billing Toggle */
.billing-toggle {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 32px;
}

.toggle-wrap {
    background: white;
    border-radius: 12px;
    padding: 4px;
    display: inline-flex;
    gap: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.toggle-btn {
    border: none;
    background: transparent;
    color: #6B7280;
    font-size: 14px;
    font-weight: 600;
    padding: 10px 24px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
}

.toggle-btn.active {
    background: #003B95;
    color: white;
    box-shadow: 0 2px 8px rgba(0,59,149,0.2);
}

/* Plans Grid */
.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

/* Plan Card */
.plan-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    position: relative;
}

.plan-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.12);
}

.plan-card.active {
    border: 2px solid #003B95;
    box-shadow: 0 8px 24px rgba(0,59,149,0.15);
}

.plan-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    background: #FEF3C7;
    color: #F59E0B;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 20px;
    z-index: 10;
}

.plan-header {
    padding: 24px;
    border-bottom: 1px solid #F0F2F5;
    background: #F9FAFB;
}

.plan-name {
    font-size: 20px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 8px;
}

.plan-price {
    font-size: 36px;
    font-weight: 800;
    color: #003B95;
}

.plan-price-period {
    font-size: 14px;
    font-weight: normal;
    color: #6B7280;
}

.plan-description {
    font-size: 13px;
    color: #6B7280;
    margin-top: 8px;
}

.discount-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 12px;
    padding: 6px 12px;
    border-radius: 20px;
    background: #ECFDF5;
    color: #10B981;
    font-size: 12px;
    font-weight: 600;
}

.plan-features {
    padding: 24px;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    font-size: 14px;
    color: #4B5563;
    border-bottom: 1px solid #F0F2F5;
}

.feature-item:last-child {
    border-bottom: none;
}

.feature-item i {
    width: 20px;
    color: #10B981;
    font-size: 14px;
}

.subscribe-btn {
    width: calc(100% - 48px);
    margin: 0 24px 24px 24px;
    background: #003B95;
    color: white;
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.subscribe-btn:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

.subscribe-btn.current-plan {
    background: #E5E7EB;
    color: #6B7280;
    cursor: not-allowed;
}

.subscribe-btn.current-plan:hover {
    transform: none;
}

.subscribe-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Status Card */
.status-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.status-left h3 {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 8px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #EFF6FF;
    color: #003B95;
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 600;
    margin-top: 8px;
}

.status-right {
    width: 48px;
    height: 48px;
    background: #EFF6FF;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.status-right i {
    font-size: 24px;
    color: #003B95;
}

/* Benefits Section */
.benefits-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-top: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.benefits-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.benefits-header h3 {
    font-size: 18px;
    font-weight: 700;
}

.view-all-btn {
    background: none;
    border: none;
    color: #003B95;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
}

.benefits-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.benefit-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.benefit-icon {
    width: 40px;
    height: 40px;
    background: #EFF6FF;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.benefit-icon i {
    font-size: 18px;
    color: #003B95;
}

.benefit-text h4 {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 4px;
}

.benefit-text p {
    font-size: 12px;
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
        padding: 24px;
    }
    
    .hero-content {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    .hero-text h1 {
        font-size: 24px;
    }
    
    .hero-text p {
        font-size: 13px;
    }
    
    .hero-icon {
        width: 60px;
        height: 60px;
    }
    
    .hero-icon i {
        font-size: 28px;
    }
    
    .billing-toggle {
        justify-content: center;
        margin: 0 16px 20px 16px;
    }
    
    .plans-grid {
        grid-template-columns: 1fr;
        gap: 16px;
        padding: 0 16px;
    }
    
    .status-card {
        margin: 0 16px 20px 16px;
        padding: 20px;
    }
    
    .benefits-section {
        margin: 0 16px 20px 16px;
        padding: 20px;
    }
    
    .benefits-grid {
        grid-template-columns: 1fr;
        gap: 16px;
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
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Unlock Premium Benefits</h1>
                <p>Get exclusive discounts, priority support, and special perks on every booking</p>
            </div>
            <div class="hero-icon">
                <i class="fas fa-crown"></i>
            </div>
        </div>
    </div>

    <!-- Billing Toggle -->
    <div class="billing-toggle">
        <div class="toggle-wrap">
            <button class="toggle-btn active" onclick="switchBilling('monthly', this)">Monthly</button>
            <button class="toggle-btn" onclick="switchBilling('yearly', this)">Yearly</button>
        </div>
    </div>

    <!-- Plans Grid -->
    <div class="plans-grid" id="desktopPlans">
        <?php if (!empty($plans_by_family)): ?>
            <?php foreach ($plans_by_family as $family_key => $group): ?>
                <?php
                $monthlyPlan = $group['monthly'];
                $yearlyPlan  = $group['yearly'];

                $defaultPlan = $monthlyPlan ?: $yearlyPlan;
                if (!$defaultPlan) continue;

                $planName = $defaultPlan['name'] ?? 'Plan';
                $planDesc = !empty($group['description']) ? $group['description'] : 'Choose this subscription to unlock premium benefits.';

                $monthlyAmount = isset($monthlyPlan['price']) ? (int)$monthlyPlan['price'] : (int)($defaultPlan['price'] ?? 0);
                $yearlyAmount  = isset($yearlyPlan['price']) ? (int)$yearlyPlan['price'] : (int)($defaultPlan['price'] ?? 0);

                $monthlySlug = $monthlyPlan['slug'] ?? '';
                $yearlySlug  = $yearlyPlan['slug'] ?? '';

                $isCurrentPlan = ($current_plan_family === $family_key);

                $badgeText = 'Popular';
                if (stripos($planName, 'lite') !== false) $badgeText = 'Starter';
                if (stripos($planName, 'plus') !== false) $badgeText = 'Most Popular';
                if (stripos($planName, 'elite') !== false) $badgeText = 'Premium';

                $featureList = $group['features'];
                if (empty($featureList)) {
                    if (stripos($planName, 'lite') !== false) {
                        $featureList = ['Up to 5% discount on room bookings', 'Faster booking confirmation', 'Basic support access'];
                    } elseif (stripos($planName, 'plus') !== false) {
                        $featureList = ['Up to 12% discount on all stays', 'Free rescheduling on eligible bookings', 'Priority chat support', 'Exclusive member-only room deals'];
                    } elseif (stripos($planName, 'elite') !== false) {
                        $featureList = ['Up to 20% discount on selected properties', 'Early check-in / late checkout benefits', 'Premium concierge support', 'Highest cashback and loyalty rewards'];
                    } else {
                        $featureList = ['Exclusive member discounts', 'Faster support access', 'Better value on room bookings'];
                    }
                }

                $discountPercent = (int)($group['discount_percent'] ?? 0);
                ?>
                <div class="plan-card <?php echo $isCurrentPlan ? 'active' : ''; ?>">
                    <div class="plan-badge"><?php echo htmlspecialchars($badgeText); ?></div>
                    <div class="plan-header">
                        <h3 class="plan-name"><?php echo htmlspecialchars($planName); ?></h3>
                        <div>
                            <span class="plan-price price" data-monthly="<?php echo $monthlyAmount; ?>" data-yearly="<?php echo $yearlyAmount; ?>">
                                ₹<?php echo $monthlyAmount; ?>
                            </span>
                            <span class="plan-price-period billing-label">/month</span>
                        </div>
                        <p class="plan-description"><?php echo htmlspecialchars($planDesc); ?></p>
                        <?php if ($discountPercent > 0): ?>
                            <div class="discount-chip">
                                <i class="fas fa-bolt"></i> Save up to <?php echo $discountPercent; ?>%
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="plan-features">
                        <?php foreach ($featureList as $feature): ?>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span><?php echo htmlspecialchars($feature); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="subscribe-btn <?php echo $isCurrentPlan ? 'current-plan' : ''; ?>"
                            onclick="<?php echo $isCurrentPlan ? 'return false;' : 'choosePlanBySlug(this)'; ?>"
                            <?php echo $isCurrentPlan ? 'disabled' : ''; ?>
                            data-monthly-slug="<?php echo htmlspecialchars($monthlySlug); ?>"
                            data-yearly-slug="<?php echo htmlspecialchars($yearlySlug); ?>">
                        <?php echo $isCurrentPlan ? 'Current Plan' : 'Choose Plan'; ?>
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-12 bg-white rounded-xl">
                <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-bold">No active plans available</h3>
                <p class="text-gray-500 mt-2">Please check back later for subscription plans</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Status Card -->
    <div class="status-card">
        <div class="status-left">
            <h3><?php echo $active_subscription ? htmlspecialchars($active_subscription['plan_name']) : 'No Active Subscription'; ?></h3>
            <?php if ($active_subscription): ?>
                <div class="status-badge">
                    <i class="fas fa-bolt"></i>
                    Active Plan
                    <?php if (!empty($active_subscription['end_date'])): ?>
                        · <?php echo (int)($active_subscription['remaining_days'] ?? 0); ?> days remaining
                    <?php endif; ?>
                </div>
                <?php if (!empty($active_subscription['end_date'])): ?>
                    <p class="text-xs text-gray-500 mt-2">Valid till <?php echo date('d M Y', strtotime($active_subscription['end_date'])); ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-sm text-gray-500 mt-1">Choose a plan and unlock room booking benefits today</p>
            <?php endif; ?>
        </div>
        <div class="status-right">
            <i class="fas fa-id-badge"></i>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="benefits-section">
        <div class="benefits-header">
            <h3>✨ Member Benefits</h3>
            <button class="view-all-btn" onclick="showBenefits()">View all <i class="fas fa-arrow-right ml-1"></i></button>
        </div>
        <div class="benefits-grid">
            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-tags"></i></div>
                <div class="benefit-text">
                    <h4>Exclusive Pricing</h4>
                    <p>Special room prices for members</p>
                </div>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-headset"></i></div>
                <div class="benefit-text">
                    <h4>Priority Support</h4>
                    <p>Get faster help 24/7</p>
                </div>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-wallet"></i></div>
                <div class="benefit-text">
                    <h4>Better Savings</h4>
                    <p>Save more on every stay</p>
                </div>
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
                <h1>Unlock Benefits</h1>
                <p>Exclusive discounts & priority support</p>
            </div>
            <div class="hero-icon">
                <i class="fas fa-crown"></i>
            </div>
        </div>
    </div>

    <!-- Billing Toggle -->
    <div class="billing-toggle" style="justify-content: center; margin: 0 16px 20px 16px;">
        <div class="toggle-wrap">
            <button class="toggle-btn active" onclick="switchBilling('monthly', this)">Monthly</button>
            <button class="toggle-btn" onclick="switchBilling('yearly', this)">Yearly</button>
        </div>
    </div>

    <!-- Plans Grid -->
    <div class="plans-grid" id="mobilePlans" style="padding: 0 16px;">
        <!-- Plans will be populated via JS from desktop plans -->
    </div>

    <!-- Status Card -->
    <div class="status-card" style="margin: 0 16px 20px 16px;">
        <div class="status-left">
            <h3><?php echo $active_subscription ? htmlspecialchars($active_subscription['plan_name']) : 'No Active Subscription'; ?></h3>
            <?php if ($active_subscription): ?>
                <div class="status-badge">
                    <i class="fas fa-bolt"></i>
                    Active · <?php echo (int)($active_subscription['remaining_days'] ?? 0); ?> days left
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-500 mt-1">Choose a plan to unlock benefits</p>
            <?php endif; ?>
        </div>
        <div class="status-right">
            <i class="fas fa-id-badge"></i>
        </div>
    </div>

    <!-- Benefits Section -->
    <div class="benefits-section" style="margin: 0 16px 20px 16px;">
        <div class="benefits-header">
            <h3>✨ Member Benefits</h3>
            <button class="view-all-btn" onclick="showBenefits()">View all</button>
        </div>
        <div class="benefits-grid">
            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-tags"></i></div>
                <div class="benefit-text">
                    <h4>Exclusive Pricing</h4>
                    <p>Special member rates</p>
                </div>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-headset"></i></div>
                <div class="benefit-text">
                    <h4>Priority Support</h4>
                    <p>24/7 faster help</p>
                </div>
            </div>
            <div class="benefit-item">
                <div class="benefit-icon"><i class="fas fa-wallet"></i></div>
                <div class="benefit-text">
                    <h4>Better Savings</h4>
                    <p>Save on every stay</p>
                </div>
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
let currentBilling = 'monthly';

// Copy desktop plans to mobile
document.addEventListener('DOMContentLoaded', function() {
    const desktopPlans = document.getElementById('desktopPlans');
    const mobilePlans = document.getElementById('mobilePlans');
    
    if (desktopPlans && mobilePlans) {
        mobilePlans.innerHTML = desktopPlans.innerHTML;
    }
});

function switchBilling(type, el) {
    currentBilling = type;
    
    document.querySelectorAll('.toggle-btn').forEach(btn => btn.classList.remove('active'));
    el.classList.add('active');
    
    document.querySelectorAll('.price').forEach(priceEl => {
        const amount = type === 'monthly' ? priceEl.dataset.monthly : priceEl.dataset.yearly;
        priceEl.textContent = '₹' + amount;
    });
    
    document.querySelectorAll('.billing-label').forEach(label => {
        label.textContent = type === 'monthly' ? '/month' : '/year';
    });
}

function choosePlanBySlug(buttonEl) {
    const monthlySlug = buttonEl.dataset.monthlySlug || '';
    const yearlySlug = buttonEl.dataset.yearlySlug || '';
    
    const planSlug = currentBilling === 'monthly' ? monthlySlug : yearlySlug;
    
    if (!planSlug) {
        showToast('Selected billing plan is not available', 'error');
        return;
    }
    
    setSubscribeButtonsDisabled(true);
    showToast('Creating payment order...');
    
    fetch('/api/create-order', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ plan_slug: planSlug })
    })
    .then(async response => {
        const raw = await response.text();
        try {
            return JSON.parse(raw);
        } catch (e) {
            console.error(raw);
            throw new Error('Invalid server response');
        }
    })
    .then(data => {
        if (!data.success) {
            showToast(data.message || 'Unable to create order', 'error');
            setSubscribeButtonsDisabled(false);
            return;
        }
        openRazorpayCheckout(data.data);
    })
    .catch(error => {
        console.error(error);
        showToast('Something went wrong', 'error');
        setSubscribeButtonsDisabled(false);
    });
}

function openRazorpayCheckout(orderData) {
    const options = {
        key: orderData.key,
        amount: orderData.amount,
        currency: orderData.currency,
        name: 'PG Mitra',
        description: orderData.plan_name,
        order_id: orderData.order_id,
        handler: function(response) {
            verifyPayment(response);
        },
        modal: {
            ondismiss: function() {
                setSubscribeButtonsDisabled(false);
                showToast('Payment cancelled', 'error');
            }
        },
        theme: { color: '#003B95' }
    };
    
    const rzp = new Razorpay(options);
    rzp.open();
}

function verifyPayment(paymentResponse) {
    showToast('Verifying payment...');
    
    fetch('/api/verify-payment', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(paymentResponse)
    })
    .then(async response => {
        const raw = await response.text();
        try {
            return JSON.parse(raw);
        } catch (e) {
            console.error(raw);
            throw new Error('Invalid server response');
        }
    })
    .then(data => {
        if (data.success) {
            showToast('Subscription activated successfully', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 900);
        } else {
            showToast(data.message || 'Payment verification failed', 'error');
            setSubscribeButtonsDisabled(false);
        }
    })
    .catch(error => {
        console.error(error);
        showToast('Payment verification failed', 'error');
        setSubscribeButtonsDisabled(false);
    });
}

function setSubscribeButtonsDisabled(disabled) {
    document.querySelectorAll('.subscribe-btn').forEach(btn => {
        if (!btn.classList.contains('current-plan')) {
            btn.disabled = disabled;
        }
    });
}

function showBenefits() {
    showToast('Member benefits: extra discounts, faster support, flexible booking perks, exclusive room deals.', 'info');
}

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 2500);
}

function goToPage(page) {
    if (page === 'home') window.location.href = '/home';
    else if (page === 'search') window.location.href = '/search';
    else if (page === 'bookings') window.location.href = '/bookings';
    else if (page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if (page === 'profile') window.location.href = '/profile';
}
</script>

</body>
</html>