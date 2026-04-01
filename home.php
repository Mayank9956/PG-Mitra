<?php
require_once 'common/auth.php';

// OPTIONAL USER (login ho to milega, warna null)
$user_id = $user['id'] ?? null;


// ===== DEFAULT FILTER STATE =====
$selected_location = '';
$search_query = '';
$pg_type = '';
$min_price = 1000;
$max_price = 50000;


// ================= LOCATIONS =================
$locations_query = "SELECT DISTINCT city, COUNT(*) as room_count 
                    FROM rooms 
                    GROUP BY city 
                    ORDER BY room_count DESC";

$locations_result = $conn->query($locations_query);
$all_locations = [];

if ($locations_result && $locations_result->num_rows > 0) {
    while($row = $locations_result->fetch_assoc()) {
        $all_locations[] = $row;
    }
}


// ================= PG TYPE COUNTS =================
$type_counts = [
    'men' => 0,
    'women' => 0,
    'unisex' => 0
];

$count_query = "SELECT pg_type, COUNT(*) as count 
                FROM rooms 
                WHERE is_available = 1 AND status = 'approved'
                GROUP BY pg_type";

$count_result = $conn->query($count_query);

if ($count_result && $count_result->num_rows > 0) {
    while($row = $count_result->fetch_assoc()) {
        $type_counts[$row['pg_type']] = $row['count'];
    }
}


// ================= USER FAVORITES =================
$user_favorites = [];

if ($user_id) {
    $stmt = $conn->prepare("SELECT room_id FROM user_favorites WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    while ($fav = $result->fetch_assoc()) {
        $user_favorites[] = $fav['room_id'];
    }
}


// ================= PROFILE =================
$profile = null;

if ($user) {
    $profile = [
        'full_name' => $user['full_name'],
        'profile_image' => $user['profile_image'],
        'email' => $user['email']
    ];
}


// ================= USER NAME =================
$user_name = $user['full_name'] ?? $user['username'] ?? 'Guest User';

$profile_image = !empty($user['profile_image']) 
    ? $user['profile_image'] 
    : null;


// ================= SEARCH SUGGESTIONS =================

// Cities
$cities = [];

$cities_query = "SELECT city, COUNT(*) as total 
                 FROM rooms 
                 WHERE city IS NOT NULL AND city != ''
                 GROUP BY city 
                 ORDER BY total DESC 
                 LIMIT 8";

$cities_result = $conn->query($cities_query);

if ($cities_result && $cities_result->num_rows > 0) {
    while ($row = $cities_result->fetch_assoc()) {
        $cities[] = $row;
    }
}


// Areas
$areas = [];

$areas_query = "SELECT area, COUNT(*) as total 
                FROM rooms 
                WHERE area IS NOT NULL AND area != ''
                GROUP BY area 
                ORDER BY total DESC 
                LIMIT 8";

$areas_result = $conn->query($areas_query);

if ($areas_result && $areas_result->num_rows > 0) {
    while ($row = $areas_result->fetch_assoc()) {
        $areas[] = $row;
    }
}

// ================= COLLEGES FOR PROXIMITY =================
$colleges = [];

$colleges_query = "SELECT id, name, latitude, longitude 
                   FROM colleges 
                   WHERE latitude IS NOT NULL AND longitude IS NOT NULL
                   AND status = 'active'";

$colleges_result = $conn->query($colleges_query);

if ($colleges_result && $colleges_result->num_rows > 0) {
    while ($row = $colleges_result->fetch_assoc()) {
        $colleges[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>PG Mitra – Find Your Perfect PG Accommodation</title>
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

/* Hero Search Section - Booking.com Style */
.hero-section {
    background: linear-gradient(135deg, #003B95 0%, #0066CC 100%);
    padding: 48px 80px 64px;
}

.hero-title {
    color: white;
    font-size: 42px;
    font-weight: 800;
    margin-bottom: 16px;
    letter-spacing: -0.5px;
}

.hero-subtitle {
    color: rgba(255,255,255,0.9);
    font-size: 18px;
    margin-bottom: 40px;
}

/* Search Bar - Booking.com Style */
.search-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    padding: 20px 24px;
}

.search-grid {
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
}

.search-field {
    flex: 2;
    min-width: 200px;
    position: relative;
}

.search-field.location-field {
    flex: 2;
}

.search-field i {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #9CA3AF;
    font-size: 18px;
    z-index: 1;
}

.search-field input, .search-field select {
    width: 100%;
    padding: 16px 16px 16px 48px;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    font-size: 15px;
    font-family: 'Inter', sans-serif;
    background: #F9FAFB;
    transition: all 0.2s;
}

.search-field input:focus, .search-field select:focus {
    outline: none;
    border-color: #003B95;
    background: white;
    box-shadow: 0 0 0 3px rgba(0,59,149,0.1);
}

.price-fields {
    display: flex;
    gap: 12px;
    flex: 1.5;
    min-width: 240px;
}

.price-field {
    flex: 1;
    position: relative;
}

.price-field i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #9CA3AF;
    font-size: 14px;
}

.price-field input {
    width: 100%;
    padding: 16px 12px 16px 32px;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    font-size: 14px;
    background: #F9FAFB;
}

.search-btn {
    background: #003B95;
    color: white;
    padding: 16px 32px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

.search-btn:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

/* Main Container - Desktop Layout */
.main-container {
    max-width: 1440px;
    margin: 0 auto;
    padding: 32px 80px;
    display: flex;
    gap: 32px;
}

/* Sidebar Filters - Booking.com Style */
.filters-sidebar {
    width: 300px;
    flex-shrink: 0;
    background: white;
    border-radius: 12px;
    padding: 24px;
    position: sticky;
    top: 100px;
    height: fit-content;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.filter-section {
    margin-bottom: 28px;
    border-bottom: 1px solid #F0F2F5;
    padding-bottom: 24px;
}

.filter-section:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.filter-title {
    font-size: 18px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-title i {
    color: #003B95;
    font-size: 18px;
}

.filter-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.filter-option {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 8px;
    transition: all 0.2s;
}

.filter-option:hover {
    background: #F7F9FC;
}

.filter-option input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #003B95;
}

.filter-option label {
    flex: 1;
    font-size: 14px;
    font-weight: 500;
    color: #4B5563;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
}

.filter-option .count {
    color: #9CA3AF;
    font-size: 12px;
}

/* Budget Slider */
.budget-range {
    margin-top: 16px;
}

.budget-values {
    display: flex;
    justify-content: space-between;
    margin-bottom: 16px;
    font-size: 14px;
    font-weight: 600;
    color: #003B95;
}

.slider-container {
    position: relative;
    height: 40px;
}

.slider-track {
    position: absolute;
    width: 100%;
    height: 4px;
    background: #E5E7EB;
    border-radius: 4px;
    top: 50%;
    transform: translateY(-50%);
}

.slider-range {
    position: absolute;
    height: 4px;
    background: #003B95;
    border-radius: 4px;
    top: 50%;
    transform: translateY(-50%);
}

.slider-container input {
    position: absolute;
    width: 100%;
    height: 4px;
    background: none;
    pointer-events: none;
    -webkit-appearance: none;
    top: 50%;
    transform: translateY(-50%);
}

.slider-container input::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 20px;
    height: 20px;
    background: white;
    border: 2px solid #003B95;
    border-radius: 50%;
    cursor: pointer;
    pointer-events: auto;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* Location List in Sidebar */
.location-list-sidebar {
    max-height: 300px;
    overflow-y: auto;
}

.location-item {
    padding: 10px 12px;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.location-item:hover {
    background: #F7F9FC;
}

.location-item.active {
    background: #EFF6FF;
    border-left: 3px solid #003B95;
}

.location-name {
    font-size: 14px;
    font-weight: 500;
    color: #1E2A3A;
}

.location-count {
    font-size: 12px;
    color: #9CA3AF;
}

.current-location-btn {
    background: #F7F9FC;
    border: 1px solid #E5E7EB;
    padding: 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.current-location-btn:hover {
    background: #EFF6FF;
    border-color: #003B95;
}

.current-location-btn i {
    color: #003B95;
    font-size: 18px;
}

/* Content Area */
.content-area {
    flex: 1;
    min-width: 0;
}

/* Results Header */
.results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.results-title h2 {
    font-size: 24px;
    font-weight: 700;
    color: #1E2A3A;
}

.results-title p {
    font-size: 14px;
    color: #6B7280;
    margin-top: 4px;
}

.results-sort {
    display: flex;
    align-items: center;
    gap: 12px;
}

.results-sort label {
    font-size: 14px;
    color: #6B7280;
}

.results-sort select {
    padding: 10px 16px;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    cursor: pointer;
}

/* Rooms Grid */
.rooms-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
}

/* Room Card - Booking.com Style */
.room-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    cursor: pointer;
    position: relative;
}

.room-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.12);
}

.card-image {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.room-card:hover .card-image img {
    transform: scale(1.05);
}

.pg-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    z-index: 2;
}

.badge-men {
    background: #003B95;
    color: white;
}

.badge-women {
    background: #E61E4D;
    color: white;
}

.badge-unisex {
    background: #8B5CF6;
    color: white;
}

.wishlist-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 2;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.wishlist-btn:hover {
    transform: scale(1.1);
}

.wishlist-btn i {
    font-size: 18px;
    color: #EF4444;
}

.card-content {
    padding: 16px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}

.room-title {
    font-size: 18px;
    font-weight: 700;
    color: #1E2A3A;
    line-height: 1.3;
}

.room-price {
    text-align: right;
}

.price {
    font-size: 20px;
    font-weight: 800;
    color: #003B95;
}

.price-period {
    font-size: 12px;
    color: #9CA3AF;
}

.location {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
    color: #6B7280;
    font-size: 13px;
}

.location i {
    color: #003B95;
    font-size: 12px;
}

.distance {
    background: #F0F2F5;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    color: #4B5563;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 12px;
}

.amenities {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 12px 0;
    padding-top: 12px;
    border-top: 1px solid #F0F2F5;
}

.amenity {
    background: #F7F9FC;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    color: #4B5563;
    display: flex;
    align-items: center;
    gap: 4px;
}

.amenity i {
    font-size: 10px;
    color: #003B95;
}

.rating {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
}

.rating-badge {
    background: #003B95;
    color: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.reviews {
    font-size: 13px;
    color: #6B7280;
}

/* Mobile Bottom Navigation - Hidden on Desktop */
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
    box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
}

/* Loading & No Results */
.loading-spinner {
    text-align: center;
    padding: 60px;
    grid-column: 1 / -1;
}

.loading-spinner i {
    font-size: 48px;
    color: #003B95;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.no-results {
    text-align: center;
    padding: 60px;
    background: white;
    border-radius: 12px;
    grid-column: 1 / -1;
}

.no-results i {
    font-size: 64px;
    color: #E5E7EB;
    margin-bottom: 20px;
}

.clear-filters-btn {
    background: #003B95;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 16px;
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
@media (max-width: 1024px) {
    .desktop-header {
        padding: 0 32px;
    }
    .hero-section {
        padding: 32px 32px 48px;
    }
    .main-container {
        padding: 24px 32px;
        flex-direction: column;
    }
    .filters-sidebar {
        width: 100%;
        position: static;
        margin-bottom: 24px;
    }
    .filter-options {
        flex-direction: row;
        flex-wrap: wrap;
    }
    .filter-option {
        min-width: 100px;
    }
}

@media (max-width: 768px) {
    .desktop-header, .hero-section, .main-container {
        padding: 16px 20px;
    }
    .hero-title {
        font-size: 28px;
    }
    .hero-subtitle {
        font-size: 14px;
    }
    .search-grid {
        flex-direction: column;
    }
    .search-field, .price-fields, .search-btn {
        width: 100%;
    }
    .rooms-grid {
        grid-template-columns: 1fr;
    }
    .desktop-nav {
        display: none;
    }
    .mobile-bottom-nav {
        display: block;
    }
    .main-container {
        padding-bottom: 80px;
    }
    .user-menu {
        gap: 12px;
    }
}

/* Search Suggestions */
.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    z-index: 100;
    display: none;
    margin-top: 4px;
}

.search-suggestions.show {
    display: block;
}

.suggestion-item {
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    transition: background 0.2s;
}

.suggestion-item:hover {
    background: #F7F9FC;
}

.suggestion-item i {
    width: 20px;
    color: #9CA3AF;
}

.suggestion-item .item-title {
    font-weight: 500;
    font-size: 14px;
}
</style>
</head>
<body>

<!-- Desktop Header - Booking.com Style -->
<div class="desktop-header">
    <div class="header-top">
        <div class="logo">PG<span>Mitra</span></div>
        <div class="desktop-nav">
            <a href="/home" class="active"><i class="fas fa-home"></i> Home</a>
            <a href="/search"><i class="fas fa-search"></i> Search</a>
            <a href="/bookings"><i class="fas fa-ticket-alt"></i> Bookings</a>
            <a href="/saved-rooms"><i class="fas fa-heart"></i> Saved</a>
            <a href="/profile"><i class="fas fa-user"></i> Profile</a>
        </div>
        <div class="user-menu">
            <div class="user-avatar" onclick="window.location.href='/profile'">
                <?php if ($profile_image): ?>
                    <img src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Hero Search Section -->
<div class="hero-section">
    <div class="hero-title">Find your perfect PG</div>
    <div class="hero-subtitle">Search and compare thousands of PGs across India</div>
    
    <div class="search-card">
        <div class="search-grid">
            <div class="search-field location-field">
                <i class="fas fa-map-marker-alt"></i>
                <input type="text" id="desktopLocationInput" placeholder="Where are you living?" value="<?php echo htmlspecialchars($selected_location); ?>">
            </div>
            <div class="price-fields">
                <div class="price-field">
                    <i class="fas fa-rupee-sign"></i>
                    <input type="number" id="minPriceInput" placeholder="Min price" value="<?php echo $min_price; ?>">
                </div>
                <div class="price-field">
                    <i class="fas fa-rupee-sign"></i>
                    <input type="number" id="maxPriceInput" placeholder="Max price" value="<?php echo $max_price; ?>">
                </div>
            </div>
            <button class="search-btn" onclick="performDesktopSearch()">
                <i class="fas fa-search"></i> Search
            </button>
        </div>
    </div>
</div>

<!-- Main Container -->
<div class="main-container">
    <!-- Sidebar Filters -->
    <div class="filters-sidebar">
        <!-- PG Type Filter -->
        <div class="filter-section">
            <div class="filter-title">
                <i class="fas fa-users"></i>
                <span>PG Type</span>
            </div>
            <div class="filter-options">
                <div class="filter-option" onclick="filterByType('')">
                    <input type="radio" name="pgType" id="typeAll" <?php echo ($pg_type == '') ? 'checked' : ''; ?>>
                    <label for="typeAll">All <span class="count">(<?php echo $type_counts['men'] + $type_counts['women'] + $type_counts['unisex']; ?>)</span></label>
                </div>
                <div class="filter-option" onclick="filterByType('men')">
                    <input type="radio" name="pgType" id="typeMen" <?php echo ($pg_type == 'men') ? 'checked' : ''; ?>>
                    <label for="typeMen">Men Only <span class="count">(<?php echo $type_counts['men']; ?>)</span></label>
                </div>
                <div class="filter-option" onclick="filterByType('women')">
                    <input type="radio" name="pgType" id="typeWomen" <?php echo ($pg_type == 'women') ? 'checked' : ''; ?>>
                    <label for="typeWomen">Women Only <span class="count">(<?php echo $type_counts['women']; ?>)</span></label>
                </div>
                <div class="filter-option" onclick="filterByType('unisex')">
                    <input type="radio" name="pgType" id="typeUnisex" <?php echo ($pg_type == 'unisex') ? 'checked' : ''; ?>>
                    <label for="typeUnisex">Unisex <span class="count">(<?php echo $type_counts['unisex']; ?>)</span></label>
                </div>
            </div>
        </div>
        
        <!-- Budget Filter -->
        <div class="filter-section">
            <div class="filter-title">
                <i class="fas fa-wallet"></i>
                <span>Budget per month</span>
            </div>
            <div class="budget-range">
                <div class="budget-values">
                    <span>₹<span id="sidebarMinPrice"><?php echo $min_price; ?></span></span>
                    <span>₹<span id="sidebarMaxPrice"><?php echo $max_price; ?></span></span>
                </div>
                <div class="slider-container">
                    <div class="slider-track"></div>
                    <div class="slider-range" id="sliderRange" style="left: 0%; right: 0%;"></div>
                    <input type="range" min="1000" max="50000" value="<?php echo $min_price; ?>" id="sidebarMinSlider" oninput="updateSidebarBudget('min', this.value)">
                    <input type="range" min="1000" max="50000" value="<?php echo $max_price; ?>" id="sidebarMaxSlider" oninput="updateSidebarBudget('max', this.value)">
                </div>
            </div>
        </div>
        
        <!-- Popular Locations -->
        <div class="filter-section">
            <div class="filter-title">
                <i class="fas fa-city"></i>
                <span>Popular Cities</span>
            </div>
            <div class="current-location-btn" onclick="useCurrentLocation()">
                <i class="fas fa-location-dot"></i>
                <span>Use my current location</span>
            </div>
            <div class="location-list-sidebar">
                <?php foreach($all_locations as $location): ?>
                <div class="location-item" onclick="selectLocation('<?php echo $location['city']; ?>')">
                    <span class="location-name"><?php echo $location['city']; ?></span>
                    <span class="location-count"><?php echo $location['room_count']; ?> PGs</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Clear Filters -->
        <div class="filter-section">
            <button class="clear-filters-btn" style="width: 100%;" onclick="clearAllFilters()">
                <i class="fas fa-undo-alt"></i> Clear all filters
            </button>
        </div>
    </div>
    
    <!-- Content Area -->
    <div class="content-area">
        <div class="results-header">
            <div class="results-title">
                <h2>Available PGs</h2>
                <p id="resultsCountText">Loading properties...</p>
            </div>
            <div class="results-sort">
                <label>Sort by:</label>
                <select id="sortSelect" onchange="sortRooms(this.value)">
                    <option value="recommended">Recommended</option>
                    <option value="price_low">Price (Low to High)</option>
                    <option value="price_high">Price (High to Low)</option>
                    <option value="rating">Rating</option>
                </select>
            </div>
        </div>
        
        <div class="rooms-grid" id="roomsGrid">
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p style="margin-top: 12px; color: #6B7280;">Finding the best PGs for you...</p>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav">
    <div style="display: flex; justify-content: space-around; align-items: center;">
        <div onclick="goToPage('home')" style="text-align: center; cursor: pointer;">
            <i class="fas fa-home" style="font-size: 22px; color: #003B95;"></i>
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
            <i class="fas fa-user" style="font-size: 22px; color: #9CA3AF;"></i>
            <div style="font-size: 11px; margin-top: 4px;">Profile</div>
        </div>
    </div>
</div>
<script>
// ================= DATA =================
const colleges = <?php echo json_encode($colleges); ?>;
const userFavorites = <?php echo json_encode($user_favorites); ?>.map(Number);

// ================= STATE =================
let state = {
    search: '',
    location: '<?php echo $selected_location; ?>',
    type: '<?php echo $pg_type; ?>',
    min_price: <?php echo $min_price; ?>,
    max_price: <?php echo $max_price; ?>,
    lat: null,
    lng: null
};

let allRooms = [];
let currentSort = 'recommended';

// ================= HELPER: Geocode =================
async function geocodeLocation(locationName) {
    if (!locationName) return null;
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(locationName)}&format=json&limit=1`);
        const data = await response.json();
        if (data && data.length > 0) {
            return {
                lat: parseFloat(data[0].lat),
                lng: parseFloat(data[0].lon)
            };
        }
    } catch (e) {
        console.warn('Geocoding failed:', e);
    }
    return null;
}

// ================= INIT =================
document.addEventListener('DOMContentLoaded', () => {
    initLocation();
    
    const locationInput = document.getElementById('desktopLocationInput');
    if (locationInput) {
        locationInput.addEventListener('input', (e) => {
            showSearchSuggestions(e.target.value);
        });
    }
});

function initLocation() {
    const savedLat = localStorage.getItem('lat');
    const savedLng = localStorage.getItem('lng');
    const savedCity = localStorage.getItem('cityName');
    
    if (savedLat && savedLng && savedLat !== 'null') {
        state.lat = parseFloat(savedLat);
        state.lng = parseFloat(savedLng);
        
        if (savedCity && state.location === '') {
            state.location = savedCity;
            const locationInput = document.getElementById('desktopLocationInput');
            if (locationInput) locationInput.value = savedCity;
        }
        
        fetchRooms();
    } else {
        autoDetectLocation();
    }
}

function autoDetectLocation() {
    if (!navigator.geolocation) {
        fetchRooms();
        return;
    }
    
    navigator.geolocation.getCurrentPosition(
        async (position) => {
            state.lat = position.coords.latitude;
            state.lng = position.coords.longitude;
            
            localStorage.setItem('lat', state.lat);
            localStorage.setItem('lng', state.lng);
            
            try {
                const response = await fetch(`https://geocode.maps.co/reverse?lat=${state.lat}&lon=${state.lng}`);
                const data = await response.json();
                const cityName = data.address?.city || data.address?.town || data.address?.village || null;
                if (cityName) {
                    state.location = cityName;
                    localStorage.setItem('cityName', cityName);
                    const locationInput = document.getElementById('desktopLocationInput');
                    if (locationInput) locationInput.value = cityName;
                }
            } catch(e) {
                console.warn('Reverse geocoding failed, using coordinates only:', e);
            }
            
            fetchRooms();
        },
        (error) => {
            console.log('Geolocation error:', error);
            fetchRooms();
        },
        { enableHighAccuracy: true, timeout: 5000 }
    );
}

function fetchRooms() {
    const roomsGrid = document.getElementById('roomsGrid');
    if (roomsGrid) {
        roomsGrid.innerHTML = `
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                <p style="margin-top: 12px;">Finding the best PGs for you...</p>
            </div>
        `;
    }
    
    const params = new URLSearchParams(state);
    
    fetch('/api/room-filter?' + params.toString())
        .then(res => res.json())
        .then(rooms => {
            // Backend already provides distance and distance_text,
            // so no need to recalculate here.
            allRooms = rooms;
            sortAndRenderRooms();
        })
        .catch(error => {
            console.error('Fetch error:', error);
            if (roomsGrid) {
                roomsGrid.innerHTML = `
                    <div class="no-results">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Something went wrong</h3>
                        <p>Please check your connection and try again</p>
                        <button onclick="fetchRooms()" class="clear-filters-btn">Retry</button>
                    </div>
                `;
            }
        });
}

function sortAndRenderRooms() {
    let sortedRooms = [...allRooms];
    
    if (currentSort === 'price_low') {
        sortedRooms.sort((a, b) => a.price - b.price);
    } else if (currentSort === 'price_high') {
        sortedRooms.sort((a, b) => b.price - a.price);
    } else if (currentSort === 'rating') {
        sortedRooms.sort((a, b) => (b.rating || 0) - (a.rating || 0));
    } else {
        // recommended: sort by distance if available (backend already sent distance)
        sortedRooms.sort((a, b) => {
            if (a.distance && b.distance) return a.distance - b.distance;
            if (a.distance) return -1;
            if (b.distance) return 1;
            return 0;
        });
    }
    
    renderRooms(sortedRooms);
}

function sortRooms(sortType) {
    currentSort = sortType;
    sortAndRenderRooms();
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function renderRooms(rooms) {
    const roomsGrid = document.getElementById('roomsGrid');
    const resultsCountText = document.getElementById('resultsCountText');
    
    if (resultsCountText) {
        resultsCountText.textContent = `${rooms.length} properties found`;
    }
    
    if (!rooms.length) {
        roomsGrid.innerHTML = `
            <div class="no-results">
                <i class="fas fa-building"></i>
                <h3>No PGs found</h3>
                <p>Try adjusting your filters or select a different location</p>
                <button onclick="clearAllFilters()" class="clear-filters-btn">Clear all filters</button>
            </div>
        `;
        return;
    }
    
    let html = '';
    rooms.forEach(room => {
        const isFav = userFavorites.includes(Number(room.id));
        const heartIcon = isFav ? 'fas' : 'far';
        
        let amenitiesHTML = '';
        if (room.facilities) {
            const items = room.facilities.split(',');
            const topAmenities = items.slice(0, 3);
            topAmenities.forEach(item => {
                const [name, icon] = item.split('|');
                amenitiesHTML += `
                    <div class="amenity">
                        <i class="fa-solid ${icon || 'fa-circle-check'}"></i>
                        <span>${escapeHtml(name)}</span>
                    </div>
                `;
            });
        }
        
        let distanceHTML = '';
        if (room.distance_text) {
            distanceHTML = `
                <div class="distance">
                    <i class="fas fa-location-dot"></i>
                    <span>${escapeHtml(room.distance_text)}</span>
                </div>
            `;
        }
        
        html += `
            <div class="room-card" onclick="viewRoomDetails(${room.id})">
                <div class="card-image">
                    <img src="${room.primary_image || '/static/images/default-room.jpg'}" onerror="this.src='/static/images/default-room.jpg'">
                    <div class="pg-badge badge-${room.pg_type}">
                        ${room.pg_type === 'men' ? 'Men Only' : room.pg_type === 'women' ? 'Women Only' : 'Unisex'}
                    </div>
                    <div class="wishlist-btn" onclick="toggleFavorite(event, ${room.id})">
                        <i class="${heartIcon} fa-heart"></i>
                    </div>
                </div>
                <div class="card-content">
                    <div class="card-header">
                        <h3 class="room-title">${escapeHtml(room.title)}</h3>
                        <div class="room-price">
                            <span class="price">₹${room.price}</span>
                            <span class="price-period">/month</span>
                        </div>
                    </div>
                    <div class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${escapeHtml(room.location || room.area || '')}, ${escapeHtml(room.city || '')}</span>
                    </div>
                    ${distanceHTML}
                    <div class="amenities">
                        ${amenitiesHTML}
                    </div>
                    <div class="rating">
                        <div class="rating-badge">
                            <i class="fas fa-star"></i>
                       <span>${room.rating != null ? parseFloat(room.rating).toFixed(1) : '4.5'}</span>
                        </div>
                        <span class="reviews">${room.reviews_count || 0} reviews</span>
                    </div>
                </div>
            </div>
        `;
    });
    
    roomsGrid.innerHTML = html;
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function viewRoomDetails(roomId) {
    window.location.href = 'room-details?id=' + roomId;
}

function toggleFavorite(event, roomId) {
    event.stopPropagation();
    
    const heartBtn = event.currentTarget;
    const icon = heartBtn.querySelector('i');
    const isAdding = icon.classList.contains('far');
    
    fetch('/api/favorite-handler', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `room_id=${roomId}&action=${isAdding ? 'add' : 'remove'}`
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            showToast(data.message, true);
            if (data.message.toLowerCase().includes('login')) {
                setTimeout(() => window.location.href = '/login', 1000);
            }
            return;
        }
        
        if (isAdding) {
            icon.classList.replace('far', 'fas');
            if (!userFavorites.includes(Number(roomId))) {
                userFavorites.push(Number(roomId));
            }
        } else {
            icon.classList.replace('fas', 'far');
            const index = userFavorites.indexOf(Number(roomId));
            if (index > -1) userFavorites.splice(index, 1);
        }
        showToast(data.message);
    })
    .catch(() => showToast('Something went wrong', true));
}

function filterByType(type) {
    state.type = type;
    
    document.querySelectorAll('input[name="pgType"]').forEach(radio => {
        radio.checked = false;
    });
    
    if (type === '') document.getElementById('typeAll').checked = true;
    if (type === 'men') document.getElementById('typeMen').checked = true;
    if (type === 'women') document.getElementById('typeWomen').checked = true;
    if (type === 'unisex') document.getElementById('typeUnisex').checked = true;
    
    fetchRooms();
}

function updateSidebarBudget(type, value) {
    const minSlider = document.getElementById('sidebarMinSlider');
    const maxSlider = document.getElementById('sidebarMaxSlider');
    const sidebarMinPrice = document.getElementById('sidebarMinPrice');
    const sidebarMaxPrice = document.getElementById('sidebarMaxPrice');
    const sliderRange = document.getElementById('sliderRange');
    
    let minVal = parseInt(minSlider.value);
    let maxVal = parseInt(maxSlider.value);
    
    if (type === 'min') {
        minVal = Math.min(parseInt(value), maxVal - 100);
        minSlider.value = minVal;
    } else {
        maxVal = Math.max(parseInt(value), minVal + 100);
        maxSlider.value = maxVal;
    }
    
    sidebarMinPrice.textContent = minVal;
    sidebarMaxPrice.textContent = maxVal;
    
    const percentMin = ((minVal - 1000) / 49000) * 100;
    const percentMax = ((maxVal - 1000) / 49000) * 100;
    sliderRange.style.left = percentMin + '%';
    sliderRange.style.right = (100 - percentMax) + '%';
    
    state.min_price = minVal;
    state.max_price = maxVal;
    
    const minPriceInput = document.getElementById('minPriceInput');
    const maxPriceInput = document.getElementById('maxPriceInput');
    if (minPriceInput) minPriceInput.value = minVal;
    if (maxPriceInput) maxPriceInput.value = maxVal;
    
    fetchRooms();
}

async function selectLocation(city) {
    state.location = city;
    state.lat = null;
    state.lng = null;
    
    const locationInput = document.getElementById('desktopLocationInput');
    if (locationInput) locationInput.value = city;
    
    const coords = await geocodeLocation(city);
    if (coords) {
        state.lat = coords.lat;
        state.lng = coords.lng;
    }
    
    fetchRooms();
}

function useCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            async (position) => {
                state.lat = position.coords.latitude;
                state.lng = position.coords.longitude;
                state.location = '';
                
                localStorage.setItem('lat', state.lat);
                localStorage.setItem('lng', state.lng);
                
                try {
                    const response = await fetch(`https://geocode.maps.co/reverse?lat=${state.lat}&lon=${state.lng}`);
                    const data = await response.json();
                    const cityName = data.address?.city || data.address?.town || data.address?.village || null;
                    if (cityName) {
                        state.location = cityName;
                        localStorage.setItem('cityName', cityName);
                        const locationInput = document.getElementById('desktopLocationInput');
                        if (locationInput) locationInput.value = cityName;
                    }
                } catch(e) {
                    console.warn('Reverse geocoding failed, using coordinates only:', e);
                }
                
                fetchRooms();
                showToast('Location updated successfully');
            },
            (error) => {
                showToast('Unable to get location', true);
            }
        );
    } else {
        showToast('Geolocation not supported', true);
    }
}

async function performDesktopSearch() {
    const locationInput = document.getElementById('desktopLocationInput');
    const minPriceInput = document.getElementById('minPriceInput');
    const maxPriceInput = document.getElementById('maxPriceInput');
    
    const newLocation = locationInput ? locationInput.value : '';
    const newMinPrice = minPriceInput ? parseInt(minPriceInput.value) || 1000 : 1000;
    const newMaxPrice = maxPriceInput ? parseInt(maxPriceInput.value) || 50000 : 50000;
    
    // Geocode first, then update state
    let coords = null;
    if (newLocation) {
        coords = await geocodeLocation(newLocation);
    }
    
    state.location = newLocation;
    state.min_price = newMinPrice;
    state.max_price = newMaxPrice;
    
    if (coords) {
        state.lat = coords.lat;
        state.lng = coords.lng;
    } else {
        state.lat = null;
        state.lng = null;
    }
    
    // Update sidebar
    const sidebarMinSlider = document.getElementById('sidebarMinSlider');
    const sidebarMaxSlider = document.getElementById('sidebarMaxSlider');
    if (sidebarMinSlider) sidebarMinSlider.value = state.min_price;
    if (sidebarMaxSlider) sidebarMaxSlider.value = state.max_price;
    
    const sidebarMinPrice = document.getElementById('sidebarMinPrice');
    const sidebarMaxPrice = document.getElementById('sidebarMaxPrice');
    if (sidebarMinPrice) sidebarMinPrice.textContent = state.min_price;
    if (sidebarMaxPrice) sidebarMaxPrice.textContent = state.max_price;
    
    fetchRooms();
}

function clearAllFilters() {
    const savedLat = localStorage.getItem('lat');
    const savedLng = localStorage.getItem('lng');
    state = {
        search: '',
        location: '',
        type: '',
        min_price: 1000,
        max_price: 50000,
        lat: savedLat ? parseFloat(savedLat) : null,
        lng: savedLng ? parseFloat(savedLng) : null
    };
    
    document.getElementById('desktopLocationInput').value = '';
    document.getElementById('minPriceInput').value = 1000;
    document.getElementById('maxPriceInput').value = 50000;
    
    document.getElementById('sidebarMinSlider').value = 1000;
    document.getElementById('sidebarMaxSlider').value = 50000;
    document.getElementById('sidebarMinPrice').textContent = '1000';
    document.getElementById('sidebarMaxPrice').textContent = '50000';
    
    document.querySelectorAll('input[name="pgType"]').forEach(radio => {
        radio.checked = false;
    });
    document.getElementById('typeAll').checked = true;
    
    const sliderRange = document.getElementById('sliderRange');
    if (sliderRange) {
        sliderRange.style.left = '0%';
        sliderRange.style.right = '0%';
    }
    
    fetchRooms();
}

function goToPage(page) {
    if (page === 'home') window.location.href = '/home';
    else if (page === 'search') window.location.href = '/search';
    else if (page === 'bookings') window.location.href = '/bookings';
    else if (page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if (page === 'profile') window.location.href = '/profile';
}

function showToast(message, isError = false) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.style.background = isError ? '#EF4444' : '#1F2937';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Search Suggestions
let suggestionTimeout;

function showSearchSuggestions(value) {
    const suggestionsDiv = document.getElementById('searchSuggestions');
    if (!suggestionsDiv) {
        const searchField = document.querySelector('.search-field.location-field');
        if (searchField) {
            const div = document.createElement('div');
            div.id = 'searchSuggestions';
            div.className = 'search-suggestions';
            searchField.style.position = 'relative';
            searchField.appendChild(div);
        }
    }
    
    const suggestionsDiv2 = document.getElementById('searchSuggestions');
    if (!suggestionsDiv2) return;
    
    if (value.length < 2) {
        suggestionsDiv2.classList.remove('show');
        return;
    }
    
    clearTimeout(suggestionTimeout);
    suggestionTimeout = setTimeout(() => {
        const cities = <?php echo json_encode($cities); ?>;
        let html = '';
        cities.forEach(city => {
            html += `
                <div class="suggestion-item" onclick="selectSuggestion('${city.city}')">
                    <i class="fas fa-city"></i>
                    <div class="item-content">
                        <div class="item-title">${escapeHtml(city.city)}</div>
                        <div style="font-size: 12px; color: #9CA3AF;">${city.total}+ PGs available</div>
                    </div>
                </div>
            `;
        });
        suggestionsDiv2.innerHTML = html;
        suggestionsDiv2.classList.add('show');
    }, 300);
}

async function selectSuggestion(value) {
    const locationInput = document.getElementById('desktopLocationInput');
    if (locationInput) locationInput.value = value;
    state.location = value;
    
    const coords = await geocodeLocation(value);
    if (coords) {
        state.lat = coords.lat;
        state.lng = coords.lng;
    }
    
    const suggestionsDiv = document.getElementById('searchSuggestions');
    if (suggestionsDiv) suggestionsDiv.classList.remove('show');
    
    fetchRooms();
}

document.addEventListener('click', function(event) {
    const suggestions = document.getElementById('searchSuggestions');
    const searchField = document.querySelector('.search-field.location-field');
    if (suggestions && searchField && !searchField.contains(event.target)) {
        suggestions.classList.remove('show');
    }
});
</script>

</body>
</html>