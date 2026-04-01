<?php
require_once 'common/auth.php';

// Optional user
$user_id = $user['id'] ?? null;

// ================= ROOM ID =================
$room_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($room_id == 0) {
    header("Location: home");
    exit;
}

// ================= ROOM DETAILS =================
$query = "SELECT r.*, 
          (SELECT AVG(rating) FROM reviews WHERE room_id = r.id) as avg_rating,
          (SELECT COUNT(*) FROM reviews WHERE room_id = r.id) as total_reviews
          FROM rooms r 
          WHERE r.id = ? AND r.is_available = 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: home");
    exit;
}

$room = $result->fetch_assoc();

// ================= ROOM IMAGES =================
$images_query = "SELECT image_url FROM room_images 
                 WHERE room_id = ? 
                 ORDER BY is_primary DESC";

$stmt = $conn->prepare($images_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$images_result = $stmt->get_result();

$room_images = [];

if ($images_result->num_rows > 0) {
    while ($img = $images_result->fetch_assoc()) {
        $room_images[] = $img['image_url'];
    }
} else {
    $room_images[] = $room['image_url'] ?? '';
}

// ================= REVIEWS =================
$reviews_query = "SELECT r.*, u.full_name as user_name, u.profile_image 
                  FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.room_id = ? 
                  ORDER BY r.created_at DESC 
                  LIMIT 5";

$stmt = $conn->prepare($reviews_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$reviews_result = $stmt->get_result();

$reviews = [];

while ($rev = $reviews_result->fetch_assoc()) {
    $reviews[] = $rev;
}

// ================= SIMILAR ROOMS =================
$similar_query = "
    SELECT r.id, r.title, r.price, r.rating, r.location, ri.image_url
    FROM rooms r
    LEFT JOIN room_images ri 
        ON r.id = ri.room_id AND ri.is_primary = 1
    WHERE r.city = ? 
      AND r.id != ? 
      AND r.is_available = 1 
      AND r.status = 'approved'
    LIMIT 3
";

$stmt = $conn->prepare($similar_query);
$stmt->bind_param("si", $room['city'], $room_id);
$stmt->execute();
$similar_result = $stmt->get_result();

$similar_rooms = [];

while ($sim = $similar_result->fetch_assoc()) {
    $similar_rooms[] = $sim;
}

// ================= FAVORITE CHECK =================
$is_favorite = false;

if ($user_id) {
    $stmt = $conn->prepare("SELECT id FROM user_favorites WHERE user_id = ? AND room_id = ?");
    $stmt->bind_param("ii", $user_id, $room_id);
    $stmt->execute();

    $fav_result = $stmt->get_result();
    $is_favorite = $fav_result->num_rows > 0;
}

// ================= FACILITIES =================
$stmt = $conn->prepare("
    SELECT f.f_name, f.icon 
    FROM facilities f
    INNER JOIN room_facilities rf ON f.id = rf.facility_id
    WHERE rf.room_id = ?
");

$stmt->bind_param("i", $room_id);
$stmt->execute();

$facilities_result = $stmt->get_result();

$facilities = [];

while ($row = $facilities_result->fetch_assoc()) {
    $facilities[] = $row;
}

// ================= FETCH ROOM RULES =================
function getRoomRules($conn, $room_id) {
    $rules = [
        'allowed' => [],
        'not_allowed' => []
    ];
    
    $stmt = $conn->prepare("SELECT rule_type, rule_text, icon FROM room_rules WHERE room_id = ? ORDER BY rule_type, sort_order");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $rules[$row['rule_type']][] = [
            'text' => $row['rule_text'],
            'icon' => $row['icon']
        ];
    }
    
    return $rules;
}

// ================= FETCH ROOM INSTRUCTIONS =================
function getRoomInstructions($conn, $room_id) {
    $instructions = [];
    
    $stmt = $conn->prepare("SELECT instruction_text, icon FROM room_instructions WHERE room_id = ? ORDER BY sort_order");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $instructions[] = [
            'text' => $row['instruction_text'],
            'icon' => $row['icon']
        ];
    }
    
    return $instructions;
}

$roomRules = getRoomRules($conn, $room_id);
$roomInstructions = getRoomInstructions($conn, $room_id);

$has_active_subscription = false;

if (isset($_SESSION['user_id'])) {
    $today = date('Y-m-d');
    $stmt = $conn->prepare("
        SELECT id 
        FROM user_subscriptions 
        WHERE user_id = ?
          AND status = 'active'
          AND start_date <= ?
          AND end_date >= ?
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->bind_param("iss", $_SESSION['user_id'], $today, $today);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $has_active_subscription = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $room['title']; ?> - PG Mitra</title>
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

.desktop-nav a:hover {
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
}

/* Main Container */
.main-container {
    max-width: 1440px;
    margin: 0 auto;
    padding: 32px 80px;
}

/* Mobile Container */
.mobile-container {
    display: none;
    padding: 0 0 80px 0;
    background: white;
}

/* Image Slider */
.image-slider-container {
    position: relative;
    width: 100%;
    height: 320px;
    overflow: hidden;
    background: #e5e7eb;
}

.slider-wrapper {
    display: flex;
    transition: transform 0.3s ease;
    height: 100%;
}

.slide {
    flex: 0 0 100%;
    height: 100%;
}

.slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.slider-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.slider-btn.left {
    left: 12px;
}

.slider-btn.right {
    right: 12px;
}

.slider-dots {
    position: absolute;
    bottom: 12px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
    z-index: 10;
}

.dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    transition: all 0.2s;
}

.dot.active {
    width: 24px;
    border-radius: 4px;
    background: white;
}

.back-btn {
    position: absolute;
    top: 12px;
    left: 12px;
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.favorite-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.favorite-btn i {
    color: #EF4444;
}

.pg-badge-mobile {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 10;
    background: rgba(0,0,0,0.7);
    color: white;
}

/* Price Card */
.price-card-mobile {
    margin: -20px 16px 20px 16px;
    background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
    border-radius: 16px;
    padding: 16px;
    color: white;
    position: relative;
    z-index: 20;
    box-shadow: 0 8px 24px rgba(37,99,235,0.2);
}

.price-card-mobile .price {
    font-size: 28px;
    font-weight: 800;
}

/* Content Sections */
.mobile-section {
    padding: 20px 16px;
    border-bottom: 8px solid #F3F4F6;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 16px;
}

/* Location Card */
.location-card {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 16px;
    margin-top: 8px;
}

.location-locked {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    background: #FEF3C7;
    border-radius: 10px;
    margin-top: 12px;
}

.location-locked i {
    color: #F59E0B;
    font-size: 20px;
}

.location-locked .lock-info {
    flex: 1;
    margin-left: 12px;
}

.location-locked .lock-title {
    font-weight: 600;
    color: #92400E;
    font-size: 14px;
}

.location-locked .lock-subtitle {
    font-size: 12px;
    color: #B45309;
    margin-top: 2px;
}

.unlock-btn {
    background: #003B95;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.unlock-btn:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

/* Exact Location Card */
.exact-location-card {
    margin-top: 12px;
    padding: 12px;
    background: #ECFDF5;
    border: 1px solid #10B981;
    border-radius: 10px;
}

.exact-location-card i {
    color: #10B981;
}

/* Facilities Grid */
.facilities-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.facility-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #F9FAFB;
    border-radius: 10px;
    border: 1px solid #E5E7EB;
}

.facility-item i {
    width: 20px;
    color: #003B95;
}

.facility-item span {
    font-size: 14px;
    font-weight: 500;
}

/* Info Grid */
.info-grid-mobile {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 16px;
}

.info-card-mobile {
    text-align: center;
    padding: 12px;
    background: #F9FAFB;
    border-radius: 12px;
    border: 1px solid #E5E7EB;
}

.info-card-mobile i {
    font-size: 20px;
    color: #003B95;
    margin-bottom: 6px;
}

.info-card-mobile .value {
    font-size: 18px;
    font-weight: 700;
}

.info-card-mobile .label {
    font-size: 11px;
    color: #6B7280;
}

/* Rules Cards */
.rules-card-mobile {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 16px;
}

.rules-title-mobile {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.rules-list-mobile {
    list-style: none;
    padding: 0;
}

.rules-list-mobile li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    font-size: 13px;
    color: #4B5563;
    border-bottom: 1px solid #E5E7EB;
}

.rules-list-mobile li:last-child {
    border-bottom: none;
}

/* Review Card */
.review-card-mobile {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
}

.review-header-mobile {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.reviewer-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.reviewer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #003B95;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
}

.review-rating-mobile {
    background: #FEF3C7;
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #92400E;
}

/* Similar Rooms */
.similar-list-mobile {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.similar-card-mobile {
    display: flex;
    gap: 12px;
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.similar-card-mobile:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.similar-image-mobile {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    overflow: hidden;
}

.similar-image-mobile img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Booking Bar */
.booking-bar-mobile {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    border-top: 1px solid #E5E7EB;
    padding: 12px 16px;
    display: flex;
    gap: 12px;
    align-items: center;
    z-index: 100;
}

.booking-price-mobile {
    flex: 1;
}

.booking-price-mobile .price {
    font-size: 24px;
    font-weight: 800;
    color: #003B95;
}

.booking-price-mobile .period {
    font-size: 12px;
    color: #6B7280;
}

.book-now-btn-mobile {
    background: #003B95;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.book-now-btn-mobile:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

/* Desktop Layout */
.desktop-layout {
    display: block;
}

/* Desktop Image Gallery */
.image-gallery {
    display: flex;
    gap: 12px;
    margin-bottom: 32px;
}

.main-image {
    flex: 2;
    height: 450px;
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
}

.main-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.thumbnail-grid {
    flex: 1;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.thumbnail {
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    height: 219px;
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Desktop Content Layout */
.content-layout {
    display: flex;
    gap: 48px;
}

.left-column {
    flex: 2;
}

.right-column {
    flex: 1;
    position: sticky;
    top: 100px;
    height: fit-content;
}

/* Desktop Booking Card */
.booking-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border: 1px solid #E5E7EB;
}

.price-section {
    border-bottom: 1px solid #E5E7EB;
    padding-bottom: 20px;
    margin-bottom: 20px;
}
.price-section .price {
    font-size: 32px;
    font-weight: 800;
    color: #003B95;
}


.price {
    font-size: 32px;
    font-weight: 800;
    color: #fff;
}

.price-period {
    font-size: 14px;
    color: #6B7280;
}

.deposit {
    font-size: 14px;
    color: #6B7280;
    margin-top: 8px;
}

.rating-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #003B95;
    color: white;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
}

.book-btn {
    width: 100%;
    background: #003B95;
    color: white;
    border: none;
    padding: 14px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    margin-bottom: 12px;
    transition: all 0.2s;
}

.book-btn:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    font-size: 14px;
}

.contact-item i {
    width: 20px;
    color: #003B95;
}

/* Desktop Location Card */
.desktop-location-card {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 24px;
}

.desktop-location-locked {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    background: #FEF3C7;
    border-radius: 10px;
    margin-top: 12px;
}

/* Exact Location Desktop */
.exact-location-desktop {
    margin-top: 12px;
    padding: 12px;
    background: #ECFDF5;
    border: 1px solid #10B981;
    border-radius: 10px;
}

/* Rules Grid Desktop */
.rules-grid-desktop {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

.rules-card-desktop {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 20px;
}

.rules-title-desktop {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.rules-list-desktop {
    list-style: none;
    padding: 0;
}

.rules-list-desktop li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    font-size: 14px;
    color: #4B5563;
    border-bottom: 1px solid #E5E7EB;
}

.rules-list-desktop li:last-child {
    border-bottom: none;
}

/* Facilities Grid Desktop */
.facilities-grid-desktop {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 32px;
}

/* Toast */
.toast {
    position: fixed;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%);
    background: #1F2937;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    z-index: 2000;
    animation: slideUp 0.3s ease;
}

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
            <a href="/profile"><i class="fas fa-user"></i> Profile</a>
        </div>
        <div class="user-menu">
            <div class="user-avatar" onclick="window.location.href='/profile'">
                <?php 
                $profile_image = !empty($user['profile_image']) ? $user['profile_image'] : null;
                if ($profile_image): ?>
                    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Header -->
<div class="mobile-header">
    <div class="logo">PG<span>Mitra</span></div>
    <div class="user-avatar" onclick="window.location.href='/profile'">
        <?php if ($profile_image): ?>
            <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile">
        <?php else: ?>
            <i class="fas fa-user"></i>
        <?php endif; ?>
    </div>
</div>

<!-- Desktop Layout -->
<div class="main-container">
    <!-- Image Gallery Desktop -->
    <div class="image-gallery">
        <div class="main-image" onclick="openLightbox(0)">
            <img src="<?php echo $room_images[0]; ?>" alt="Main Image">
        </div>
        <?php if(count($room_images) > 1): ?>
        <div class="thumbnail-grid">
            <?php for($i = 1; $i < min(4, count($room_images)); $i++): ?>
            <div class="thumbnail" onclick="openLightbox(<?php echo $i; ?>)">
                <img src="<?php echo $room_images[$i]; ?>" alt="Thumbnail">
            </div>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="content-layout">
        <div class="left-column">
            <h1 class="text-3xl font-bold mb-3"><?php echo htmlspecialchars($room['title']); ?></h1>
            
            <!-- Desktop Location Card with Lock Feature -->
            <div class="desktop-location-card">
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-map-marker-alt text-[#003B95] text-lg"></i>
                    <span><?php // echo htmlspecialchars($room['location']); ?> <?php echo htmlspecialchars($room['city']); ?></span>
                </div>
                
                <?php if(!$has_active_subscription): ?>
                <div class="desktop-location-locked">
                    <i class="fas fa-lock text-yellow-600 text-xl"></i>
                    <div class="flex-1 ml-3">
                        <div class="font-semibold text-yellow-800 text-sm">Exact location & contact details locked</div>
                        <div class="text-xs text-yellow-700">Subscribe to see exact location, phone number & email</div>
                    </div>
                    <button class="unlock-btn" onclick="window.location.href='/subscription'">
                        <i class="fas fa-crown mr-1"></i> Subscribe Now
                    </button>
                </div>
                <?php else: ?>
                <div class="exact-location-desktop">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-map-marker-alt text-green-600"></i>
                        <span class="text-sm font-medium text-green-700">Exact Location</span>
                    </div>
                    <p class="text-sm text-gray-700 ml-6"><?php echo htmlspecialchars($room['address'] ?: $room['location']); ?>, <?php echo htmlspecialchars($room['city']); ?></p>
                    <div class="mt-3 pt-2 border-t border-green-200">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-phone text-green-600"></i>
                            <span class="text-sm text-gray-700"><?php echo htmlspecialchars($room['host_phone']); ?></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-envelope text-green-600"></i>
                            <span class="text-sm text-gray-700"><?php echo htmlspecialchars($room['host_email']); ?></span>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="flex items-center gap-3 mb-6">
                <div class="rating-badge">
                    <i class="fas fa-star"></i>
                    <span><?php echo number_format($room['avg_rating'] ?? $room['rating'], 1); ?></span>
                </div>
                <span class="text-gray-500"><?php echo $room['total_reviews'] ?? $room['reviews_count']; ?> reviews</span>
                <span class="px-3 py-1 rounded-full text-sm font-semibold 
                    <?php echo $room['pg_type'] == 'men' ? 'bg-blue-100 text-blue-700' : ($room['pg_type'] == 'women' ? 'bg-pink-100 text-pink-700' : 'bg-purple-100 text-purple-700'); ?>">
                    <?php echo $room['pg_type'] == 'men' ? 'Men Only' : ($room['pg_type'] == 'women' ? 'Women Only' : 'Unisex'); ?>
                </span>
            </div>
            
            <!-- Quick Info Desktop -->
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <i class="fas fa-bed text-2xl text-[#003B95] mb-2"></i>
                    <div class="text-xl font-bold"><?php echo $room['bedrooms']; ?></div>
                    <div class="text-xs text-gray-500">Bedrooms</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <i class="fas fa-bath text-2xl text-[#003B95] mb-2"></i>
                    <div class="text-xl font-bold"><?php echo $room['bathrooms']; ?></div>
                    <div class="text-xs text-gray-500">Bathrooms</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <i class="fas fa-users text-2xl text-[#003B95] mb-2"></i>
                    <div class="text-xl font-bold"><?php echo $room['max_guests']; ?></div>
                    <div class="text-xs text-gray-500">Max Guests</div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-3">About this PG</h2>
                <div class="text-gray-600 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($room['description'])); ?>
                </div>
            </div>
            
            <!-- Facilities Desktop -->
            <?php if(!empty($facilities)): ?>
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-3">Features & Facilities</h2>
                <div class="facilities-grid-desktop">
                    <?php foreach($facilities as $facility): ?>
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                        <i class="fas <?php echo $facility['icon']; ?> text-[#003B95]"></i>
                        <span><?php echo htmlspecialchars($facility['f_name']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Rules & Policies Desktop -->
            <?php if(!empty($roomRules['allowed']) || !empty($roomRules['not_allowed'])): ?>
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-4">Rules & Policies</h2>
                <div class="rules-grid-desktop">
                    <?php if(!empty($roomRules['allowed'])): ?>
                    <div class="rules-card-desktop">
                        <div class="rules-title-desktop">
                            <i class="fas fa-check-circle text-green-500"></i>
                            <span>Allowed</span>
                        </div>
                        <ul class="rules-list-desktop">
                            <?php foreach($roomRules['allowed'] as $rule): ?>
                            <li>
                                <i class="fas <?php echo $rule['icon']; ?> text-green-500 w-5"></i>
                                <span><?php echo htmlspecialchars($rule['text']); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($roomRules['not_allowed'])): ?>
                    <div class="rules-card-desktop">
                        <div class="rules-title-desktop">
                            <i class="fas fa-times-circle text-red-500"></i>
                            <span>Not Allowed</span>
                        </div>
                        <ul class="rules-list-desktop">
                            <?php foreach($roomRules['not_allowed'] as $rule): ?>
                            <li>
                                <i class="fas <?php echo $rule['icon']; ?> text-red-500 w-5"></i>
                                <span><?php echo htmlspecialchars($rule['text']); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Instructions Desktop -->
            <?php if(!empty($roomInstructions)): ?>
            <div class="mb-8">
                <h2 class="text-xl font-bold mb-3">Important Instructions</h2>
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <ul class="space-y-2">
                        <?php foreach($roomInstructions as $instruction): ?>
                        <li class="flex gap-3 text-sm">
                            <i class="fas <?php echo $instruction['icon']; ?> text-yellow-600 mt-1"></i>
                            <span><?php echo htmlspecialchars($instruction['text']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    
    
        <div class="right-column">
            <div class="booking-card">
                <div class="price-section">
                    <div class="price">₹<?php echo number_format($room['price']); ?><span class="price-period"> /month</span></div>
                    <?php if($room['security_deposit'] > 0): ?>
                    <div class="deposit">+ ₹<?php echo number_format($room['security_deposit']); ?> refundable deposit</div>
                    <?php endif; ?>
                </div>
                <button class="book-btn" onclick="bookNow(<?php echo $room_id; ?>)">Book Now</button>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>
                            <?php if($has_active_subscription): ?>
                                <?php echo htmlspecialchars($room['host_phone']); ?>
                            <?php else: ?>
                                <span class="text-gray-400">••••••••••</span>
                            <?php endif; ?>
                        </span>
                        <?php if(!$has_active_subscription): ?>
                        <button onclick="window.location.href='/subscription'" class="text-xs bg-gray-100 px-3 py-1 rounded hover:bg-gray-200 transition">Subscribe to view</button>
                        <?php endif; ?>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>
                            <?php if($has_active_subscription): ?>
                                <?php echo htmlspecialchars($room['host_email']); ?>
                            <?php else: ?>
                                <span class="text-gray-400">••••••••••</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="text-xs text-gray-500 text-center mt-4">
                    <i class="fas fa-shield-alt"></i> Secure booking & payment protection
                </div>
            </div>
        </div>
        
        
    </div>
    
          <!-- Reviews -->
    <div class="mobile-section">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title mb-0">Reviews</h2><span onclick="showAllReviews()" class="text-[#003B95]">View All Reviews</span>
            <button onclick="writeReview()" class="text-[#003B95] text-sm font-medium">Write a review</button>
        </div>
        <?php if(count($reviews) > 0): ?>
            <?php foreach(array_slice($reviews, 0, 3) as $review): ?>
            <div class="review-card-mobile">
                <div class="review-header-mobile">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">
                            <?php echo strtoupper(substr($review['user_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="font-semibold text-sm"><?php echo htmlspecialchars($review['user_name']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo date('d M Y', strtotime($review['created_at'])); ?></div>
                        </div>
                    </div>
                    <div class="review-rating-mobile">
                        <i class="fas fa-star"></i>
                        <span><?php echo $review['rating']; ?></span>
                    </div>
                </div>
                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($review['comment']); ?></p>
            </div>
            <?php endforeach; ?>
            <?php if(count($reviews) > 3): ?>
            <button onclick="showAllReviews()" class="w-full text-center text-[#003B95] text-sm py-2">See all reviews</button>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-center text-gray-500 py-4">No reviews yet</p>
        <?php endif; ?>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <!-- Image Slider -->
    <div class="image-slider-container">
        <div class="slider-wrapper" id="sliderWrapper">
            <?php foreach($room_images as $index => $image): ?>
            <div class="slide">
                <img src="<?php echo $image; ?>" alt="Image <?php echo $index + 1; ?>">
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if(count($room_images) > 1): ?>
        <div class="slider-btn left" onclick="slideLeft()">
            <i class="fas fa-chevron-left"></i>
        </div>
        <div class="slider-btn right" onclick="slideRight()">
            <i class="fas fa-chevron-right"></i>
        </div>
        <div class="slider-dots" id="sliderDots">
            <?php foreach($room_images as $index => $image): ?>
            <div class="dot <?php echo $index == 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $index; ?>)"></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!--<div class="back-btn" onclick="goBack()">-->
        <!--    <i class="fas fa-arrow-left"></i>-->
        <!--</div>-->
        
        <div class="favorite-btn" onclick="toggleFavoriteMobile(<?php echo $room_id; ?>)">
            <i class="<?php echo $is_favorite ? 'fas' : 'far'; ?> fa-heart"></i>
        </div>
        
        <div class="pg-badge-mobile">
            <?php echo $room['pg_type'] == 'men' ? 'Men Only' : ($room['pg_type'] == 'women' ? 'Women Only' : 'Unisex'); ?>
        </div>
    </div>
    
    <!-- Price Card -->
    <div class="price-card-mobile">
        <div class="flex justify-between items-center">
            <div>
                <div class="text-white/80 text-sm">Starting from</div>
                <div class="price">₹<?php echo number_format($room['price']); ?></div>
                <div class="text-xs text-white/70">per month</div>
            </div>
            <div class="bg-white/20 px-3 py-2 rounded-lg">
                <i class="fas fa-star text-yellow-400"></i>
                <span class="ml-1 font-semibold"><?php echo number_format($room['avg_rating'] ?? $room['rating'], 1); ?></span>
            </div>
        </div>
    </div>
    
    <!-- Title & Location -->
    <div class="mobile-section">
        <h1 class="text-2xl font-bold mb-2"><?php echo htmlspecialchars($room['title']); ?></h1>
        
        <!-- Location with Lock Feature -->
        <div class="location-card">
            <div class="flex items-center gap-2 text-gray-600">
                <i class="fas fa-map-marker-alt text-[#003B95] text-lg"></i>
                <span class="text-sm"><?php //echo htmlspecialchars($room['location']); ?> <?php echo htmlspecialchars($room['city']); ?></span>
            </div>
            
            <?php if(!$has_active_subscription): ?>
            <div class="location-locked">
                <i class="fas fa-lock"></i>
                <div class="lock-info">
                    <div class="lock-title">Exact location & contact details locked</div>
                    <div class="lock-subtitle">Subscribe to see exact location, phone & email</div>
                </div>
                <button class="unlock-btn" onclick="window.location.href='/subscription'">
                    <i class="fas fa-crown mr-1"></i> Subscribe
                </button>
            </div>
            <?php else: ?>
            <div class="exact-location-card">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-map-marker-alt text-green-600"></i>
                    <span class="text-sm font-medium text-green-700">📍 Exact Location</span>
                </div>
                <p class="text-sm text-gray-700 ml-6"><?php echo htmlspecialchars($room['address'] ?: $room['location']); ?>, <?php echo htmlspecialchars($room['city']); ?></p>
                <div class="mt-3 pt-2 border-t border-green-200">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-phone text-green-600"></i>
                        <span class="text-sm text-gray-700"><?php echo htmlspecialchars($room['host_phone']); ?></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-envelope text-green-600"></i>
                        <span class="text-sm text-gray-700"><?php echo htmlspecialchars($room['host_email']); ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Quick Info -->
        <div class="info-grid-mobile">
            <div class="info-card-mobile">
                <i class="fas fa-bed"></i>
                <div class="value"><?php echo $room['bedrooms']; ?></div>
                <div class="label">Bedrooms</div>
            </div>
            <div class="info-card-mobile">
                <i class="fas fa-bath"></i>
                <div class="value"><?php echo $room['bathrooms']; ?></div>
                <div class="label">Bathrooms</div>
            </div>
            <div class="info-card-mobile">
                <i class="fas fa-users"></i>
                <div class="value"><?php echo $room['max_guests']; ?></div>
                <div class="label">Max Guests</div>
            </div>
        </div>
    </div>
    
    <!-- Description -->
    <div class="mobile-section">
        <h2 class="section-title">About this PG</h2>
        <div class="text-gray-600 leading-relaxed text-sm">
            <?php echo nl2br(htmlspecialchars($room['description'])); ?>
        </div>
    </div>
    
    <!-- Facilities -->
    <?php if(!empty($facilities)): ?>
    <div class="mobile-section">
        <h2 class="section-title">Features & Facilities</h2>
        <div class="facilities-grid">
            <?php foreach($facilities as $facility): ?>
            <div class="facility-item">
                <i class="fas <?php echo $facility['icon']; ?>"></i>
                <span><?php echo htmlspecialchars($facility['f_name']); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Rules & Policies Mobile -->
    <?php if(!empty($roomRules['allowed']) || !empty($roomRules['not_allowed'])): ?>
    <div class="mobile-section">
        <h2 class="section-title">Rules & Policies</h2>
        
        <?php if(!empty($roomRules['allowed'])): ?>
        <div class="rules-card-mobile">
            <div class="rules-title-mobile">
                <i class="fas fa-check-circle text-green-500"></i>
                <span>Allowed</span>
            </div>
            <ul class="rules-list-mobile">
                <?php foreach($roomRules['allowed'] as $rule): ?>
                <li>
                    <i class="fas <?php echo $rule['icon']; ?> text-green-500 w-5"></i>
                    <span><?php echo htmlspecialchars($rule['text']); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($roomRules['not_allowed'])): ?>
        <div class="rules-card-mobile">
            <div class="rules-title-mobile">
                <i class="fas fa-times-circle text-red-500"></i>
                <span>Not Allowed</span>
            </div>
            <ul class="rules-list-mobile">
                <?php foreach($roomRules['not_allowed'] as $rule): ?>
                <li>
                    <i class="fas <?php echo $rule['icon']; ?> text-red-500 w-5"></i>
                    <span><?php echo htmlspecialchars($rule['text']); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Instructions Mobile -->
    <?php if(!empty($roomInstructions)): ?>
    <div class="mobile-section">
        <h2 class="section-title">Important Instructions</h2>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <ul class="space-y-2">
                <?php foreach($roomInstructions as $instruction): ?>
                <li class="flex gap-3 text-sm">
                    <i class="fas <?php echo $instruction['icon']; ?> text-yellow-600 mt-1"></i>
                    <span><?php echo htmlspecialchars($instruction['text']); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Reviews -->
    <div class="mobile-section">
        <div class="flex justify-between items-center mb-4">
            <h2 class="section-title mb-0">Reviews</h2>
            <button onclick="writeReview()" class="text-[#003B95] text-sm font-medium">Write a review</button>
        </div>
        <?php if(count($reviews) > 0): ?>
            <?php foreach(array_slice($reviews, 0, 3) as $review): ?>
            <div class="review-card-mobile">
                <div class="review-header-mobile">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">
                            <?php echo strtoupper(substr($review['user_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="font-semibold text-sm"><?php echo htmlspecialchars($review['user_name']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo date('d M Y', strtotime($review['created_at'])); ?></div>
                        </div>
                    </div>
                    <div class="review-rating-mobile">
                        <i class="fas fa-star"></i>
                        <span><?php echo $review['rating']; ?></span>
                    </div>
                </div>
                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($review['comment']); ?></p>
            </div>
            <?php endforeach; ?>
            <?php if(count($reviews) > 3): ?>
            <button onclick="showAllReviews()" class="w-full text-center text-[#003B95] text-sm py-2">See all reviews</button>
            <?php endif; ?>
        <?php else: ?>
            <p class="text-center text-gray-500 py-4">No reviews yet</p>
        <?php endif; ?>
    </div>
    
    <!-- Similar Rooms -->
    <?php if(count($similar_rooms) > 0): ?>
    <div class="mobile-section">
        <h2 class="section-title">Similar PGs</h2>
        <div class="similar-list-mobile">
            <?php foreach($similar_rooms as $similar): ?>
            <div class="similar-card-mobile" onclick="viewRoom(<?php echo $similar['id']; ?>)">
                <div class="similar-image-mobile">
                    <img src="<?php echo $similar['image_url']; ?>" alt="<?php echo $similar['title']; ?>">
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold text-sm mb-1"><?php echo htmlspecialchars($similar['title']); ?></h4>
                    <div class="text-[#003B95] font-bold">₹<?php echo number_format($similar['price']); ?>/mo</div>
                    <div class="flex items-center gap-1 mt-1">
                        <i class="fas fa-star text-yellow-500 text-xs"></i>
                        <span class="text-xs"><?php echo $similar['rating']; ?></span>
                        <span class="text-xs text-gray-400">• <?php echo $similar['location']; ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Booking Bar -->
    <div class="booking-bar-mobile">
        <div class="booking-price-mobile">
            <div class="price">₹<?php echo number_format($room['price']); ?></div>
            <div class="period">per month</div>
        </div>
        <button class="book-now-btn-mobile" onclick="bookNow(<?php echo $room_id; ?>)">
            Book Now
        </button>
    </div>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 bg-black bg-opacity-90 hidden z-50 items-center justify-center" onclick="closeLightbox()">
    <span class="absolute top-4 right-4 text-white text-3xl cursor-pointer">&times;</span>
    <img id="lightboxImage" class="max-w-[90%] max-h-[90%] object-contain">
</div>

<script>
// Mobile Slider
let currentSlide = 0;
const slides = document.querySelectorAll('#sliderWrapper .slide');
const totalSlides = slides.length;

function slideLeft() {
    if(currentSlide > 0) {
        currentSlide--;
    } else {
        currentSlide = totalSlides - 1;
    }
    updateSlider();
}

function slideRight() {
    if(currentSlide < totalSlides - 1) {
        currentSlide++;
    } else {
        currentSlide = 0;
    }
    updateSlider();
}

function goToSlide(index) {
    currentSlide = index;
    updateSlider();
}

function updateSlider() {
    const wrapper = document.getElementById('sliderWrapper');
    if(wrapper) {
        wrapper.style.transform = `translateX(-${currentSlide * 100}%)`;
        
        const dots = document.querySelectorAll('.dot');
        dots.forEach((dot, index) => {
            if(index === currentSlide) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }
}

function goBack() {
    window.history.back();
}

function viewRoom(roomId) {
    window.location.href = 'room-details?id=' + roomId;
}

function bookNow(roomId) {
    <?php if(!isset($_SESSION['user_id'])): ?>
        showToast('Please login to book');
        setTimeout(() => {
            window.location.href = 'login';
        }, 1500);
        return;
    <?php endif; ?>
    window.location.href = 'booking?room_id=' + roomId;
}

function writeReview() {
    <?php if(!isset($_SESSION['user_id'])): ?>
        showToast('Please login to write a review');
        setTimeout(() => {
            window.location.href = 'login';
        }, 1500);
        return;
    <?php endif; ?>
    window.location.href = 'write-review?room_id=<?php echo $room_id; ?>';
}

function showAllReviews() {
    window.location.href = 'reviews?room_id=<?php echo $room_id; ?>';
}

function toggleFavoriteMobile(roomId) {
    <?php if(!isset($_SESSION['user_id'])): ?>
        showToast('Please login to save favorites');
        setTimeout(() => {
            window.location.href = 'login';
        }, 1500);
        return;
    <?php endif; ?>
    
    const favBtn = document.querySelector('.favorite-btn');
    const icon = favBtn.querySelector('i');
    const isAdding = icon.classList.contains('far');
    
    fetch('/api/favorite-handler', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `room_id=${roomId}&action=${isAdding ? 'add' : 'remove'}`
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            if(isAdding) {
                icon.classList.replace('far', 'fas');
                showToast('Added to favorites');
            } else {
                icon.classList.replace('fas', 'far');
                showToast('Removed from favorites');
            }
        } else {
            showToast(data.message);
        }
    })
    .catch(() => showToast('Something went wrong'));
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
}

function openLightbox(index) {
    const lightbox = document.getElementById('lightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const images = <?php echo json_encode($room_images); ?>;
    lightboxImage.src = images[index];
    lightbox.classList.remove('hidden');
    lightbox.classList.add('flex');
}

function closeLightbox() {
    const lightbox = document.getElementById('lightbox');
    lightbox.classList.add('hidden');
    lightbox.classList.remove('flex');
}
</script>

</body>
</html>