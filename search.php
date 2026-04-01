<?php
session_start();
require_once 'common/db_connect.php';

$database = new Database();
$db = $database->getConnection();

// Get search query from URL
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Fetch all rooms with primary image
$query = "
    SELECT r.*, ri.image_url
    FROM rooms r
    LEFT JOIN room_images ri 
        ON r.id = ri.room_id AND ri.is_primary = 1
    WHERE r.is_available = 1 AND status = 'approved'
    ORDER BY r.rating DESC, r.reviews_count DESC
";

$result = $db->query($query);

$rooms = [];
while($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

// Get user info if logged in
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest User';
$profile_image = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Search Rooms - PG Mitra</title>
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
    max-width: 1440px;
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

/* Search Section */
.search-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    margin-bottom: 32px;
    overflow: hidden;
}

.search-header {
    padding: 24px;
    border-bottom: 1px solid #E5E7EB;
}

.search-title {
    font-size: 28px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 8px;
}

.search-subtitle {
    font-size: 14px;
    color: #6B7280;
}

.search-box {
    padding: 24px;
}

.search-input-wrapper {
    display: flex;
    gap: 16px;
    align-items: center;
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 12px 20px;
    transition: all 0.2s;
}

.search-input-wrapper:focus-within {
    border-color: #003B95;
    box-shadow: 0 0 0 3px rgba(0,59,149,0.1);
    background: white;
}

.search-input-wrapper i {
    color: #9CA3AF;
    font-size: 18px;
}

.search-input-wrapper input {
    flex: 1;
    background: transparent;
    border: none;
    font-size: 16px;
    outline: none;
    font-family: 'Inter', sans-serif;
}

.search-input-wrapper input::placeholder {
    color: #9CA3AF;
}

.clear-search-btn {
    background: none;
    border: none;
    color: #9CA3AF;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
}

.clear-search-btn:hover {
    color: #EF4444;
}

.search-stats {
    margin-top: 12px;
    font-size: 13px;
    color: #6B7280;
}

.search-stats span {
    font-weight: 600;
    color: #003B95;
}

/* Results Grid */
.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
}

/* Room Card */
.room-card {
    background: white;
    border-radius: 16px;
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
    height: 200px;
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

.card-content {
    padding: 16px;
}

.room-title {
    font-size: 18px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 6px;
    line-height: 1.3;
}

.room-location {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #6B7280;
    font-size: 13px;
    margin-bottom: 12px;
}

.room-location i {
    color: #003B95;
    font-size: 12px;
}

.rating {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
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

.rating-badge i {
    color: #FFB700;
    font-size: 11px;
}

.reviews {
    font-size: 13px;
    color: #6B7280;
}

.price-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #F0F2F5;
}

.price {
    font-size: 20px;
    font-weight: 800;
    color: #003B95;
}

.price-period {
    font-size: 12px;
    color: #9CA3AF;
    font-weight: normal;
}

.book-btn {
    background: #003B95;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.book-btn:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

/* Loading Spinner */
.loading-spinner {
    display: none;
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

/* No Results */
.no-results {
    text-align: center;
    padding: 60px;
    background: white;
    border-radius: 16px;
    grid-column: 1 / -1;
}

.no-results i {
    font-size: 64px;
    color: #E5E7EB;
    margin-bottom: 20px;
}

.no-results h3 {
    font-size: 20px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 8px;
}

.no-results p {
    color: #6B7280;
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

/* Mobile Bottom Navigation */
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

/* Responsive */
@media (max-width: 1024px) {
    .desktop-header {
        padding: 0 32px;
    }
    .main-container {
        padding: 24px 32px;
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
    
    .results-grid {
        grid-template-columns: 1fr;
        gap: 16px;
        padding: 16px;
    }
    
    .search-box {
        padding: 16px;
    }
    
    .search-title {
        font-size: 22px;
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
            <a href="/search" class="active"><i class="fas fa-search"></i> Search</a>
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
    <div class="search-section">
        <div class="search-header">
            <h1 class="search-title">Find Your Perfect PG</h1>
            <p class="search-subtitle">Search and compare thousands of PGs across India</p>
        </div>
        
        <div class="search-box">
            <div class="search-input-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="desktopSearchInput" placeholder="Search by city, location, or room name..." value="<?php echo htmlspecialchars($search_query); ?>" autofocus>
                <?php if(!empty($search_query)): ?>
                <button class="clear-search-btn" onclick="clearSearchDesktop()">
                    <i class="fas fa-times"></i>
                </button>
                <?php endif; ?>
            </div>
            <div class="search-stats">
                <span id="desktopResultCount"><?php echo count($rooms); ?></span> properties found
            </div>
        </div>
    </div>
    
    <div id="desktopResultsGrid" class="results-grid">
        <!-- Rooms will be populated here by JavaScript -->
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <div class="bg-white px-4 pt-4 pb-3 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <button onclick="goBack()" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </button>
            <h1 class="text-lg font-bold text-gray-900">Search Rooms</h1>
        </div>
        
        <div class="bg-gray-100 rounded-xl flex items-center gap-3 px-4 py-3 focus-within:ring-2 focus-within:ring-[#003B95] transition">
            <i class="fas fa-search text-gray-400"></i>
            <input type="text" id="mobileSearchInput" placeholder="Search by city, location, or room name..." 
                   class="flex-1 bg-transparent text-sm focus:outline-none" 
                   value="<?php echo htmlspecialchars($search_query); ?>">
            <?php if(!empty($search_query)): ?>
            <button onclick="clearSearchMobile()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
            <?php endif; ?>
        </div>
        
        <div class="text-xs text-gray-500 mt-2">
            <span id="mobileResultCount"><?php echo count($rooms); ?></span> properties found
        </div>
    </div>
    
    <div id="mobileResultsGrid" class="p-4 space-y-3">
        <!-- Rooms will be populated here by JavaScript -->
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
            <i class="fas fa-search" style="font-size: 22px; color: #003B95;"></i>
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
// All rooms data from PHP
const allRooms = <?php echo json_encode($rooms); ?>;
let currentSearchQuery = '<?php echo $search_query; ?>';

// DOM elements
let desktopInput, mobileInput, desktopGrid, mobileGrid, desktopCount, mobileCount;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    desktopInput = document.getElementById('desktopSearchInput');
    mobileInput = document.getElementById('mobileSearchInput');
    desktopGrid = document.getElementById('desktopResultsGrid');
    mobileGrid = document.getElementById('mobileResultsGrid');
    desktopCount = document.getElementById('desktopResultCount');
    mobileCount = document.getElementById('mobileResultCount');
    
    if (currentSearchQuery) {
        performSearch(currentSearchQuery);
    } else {
        renderRooms(allRooms);
    }
    
    // Add input event listeners
    if (desktopInput) {
        desktopInput.addEventListener('input', (e) => handleSearch(e.target.value));
    }
    if (mobileInput) {
        mobileInput.addEventListener('input', (e) => handleSearch(e.target.value));
    }
});

// Handle search with debounce
let searchTimeout;
function handleSearch(query) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch(query);
        
        // Update URL without reloading
        const url = new URL(window.location);
        if (query) {
            url.searchParams.set('q', query);
        } else {
            url.searchParams.delete('q');
        }
        window.history.pushState({}, '', url);
    }, 300);
}

// Perform search
function performSearch(query) {
    const searchTerm = query.toLowerCase().trim();
    
    let filtered;
    
    if (searchTerm === '') {
        filtered = allRooms;
    } else {
        filtered = allRooms.filter(room => 
            (room.title && room.title.toLowerCase().includes(searchTerm)) ||
            (room.location && room.location.toLowerCase().includes(searchTerm)) ||
            (room.city && room.city.toLowerCase().includes(searchTerm)) ||
            (room.area && room.area && room.area.toLowerCase().includes(searchTerm))
        );
    }
    
    renderRooms(filtered);
    
    // Update result counts
    if (desktopCount) desktopCount.textContent = filtered.length;
    if (mobileCount) mobileCount.textContent = filtered.length;
}

// Render rooms for both desktop and mobile
function renderRooms(rooms) {
    renderDesktopRooms(rooms);
    renderMobileRooms(rooms);
}

function renderDesktopRooms(rooms) {
    if (!desktopGrid) return;
    
    desktopGrid.innerHTML = '';
    
    if (!rooms || rooms.length === 0) {
        desktopGrid.innerHTML = `
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>No Rooms Found</h3>
                <p>We couldn't find any rooms matching your search</p>
                <button onclick="clearSearchDesktop()" class="clear-filters-btn">
                    <i class="fas fa-times mr-2"></i>Clear Search
                </button>
            </div>
        `;
        return;
    }
    
    rooms.forEach(room => {
        desktopGrid.appendChild(createRoomCard(room));
    });
}

function renderMobileRooms(rooms) {
    if (!mobileGrid) return;
    
    mobileGrid.innerHTML = '';
    
    if (!rooms || rooms.length === 0) {
        mobileGrid.innerHTML = `
            <div class="bg-white rounded-2xl p-8 text-center">
                <i class="fas fa-search text-5xl text-gray-300 mb-3"></i>
                <h3 class="font-semibold text-gray-900 mb-1">No Rooms Found</h3>
                <p class="text-gray-500 text-sm mb-4">We couldn't find any rooms matching your search</p>
                <button onclick="clearSearchMobile()" class="bg-[#003B95] text-white px-5 py-2 rounded-full text-sm font-medium">
                    Clear Search
                </button>
            </div>
        `;
        return;
    }
    
    rooms.forEach(room => {
        mobileGrid.appendChild(createMobileRoomCard(room));
    });
}

function createRoomCard(room) {
    const div = document.createElement('div');
    div.className = "room-card";
    div.onclick = () => viewRoomDetails(room.id);
    
    const badgeClass = room.pg_type === 'men' ? 'badge-men' : (room.pg_type === 'women' ? 'badge-women' : 'badge-unisex');
    const badgeText = room.pg_type === 'men' ? 'Men Only' : (room.pg_type === 'women' ? 'Women Only' : 'Unisex');
    const rating = room.rating ? parseFloat(room.rating).toFixed(1) : '4.5';
    const reviewsCount = room.reviews_count || 0;
    const price = room.price ? Number(room.price).toLocaleString('en-IN') : '0';
    const imageUrl = room.image_url || 'https://via.placeholder.com/400x200?text=PG+Image';
    const location = room.location || room.city || 'Location not specified';
    
    div.innerHTML = `
        <div class="card-image">
            <img src="${escapeHtml(imageUrl)}" alt="${escapeHtml(room.title)}">
            <div class="pg-badge ${badgeClass}">${badgeText}</div>
        </div>
        <div class="card-content">
            <h3 class="room-title">${escapeHtml(room.title || 'Room')}</h3>
            <div class="room-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>${escapeHtml(location)}</span>
            </div>
            <div class="rating">
                <div class="rating-badge">
                    <i class="fas fa-star"></i>
                    <span>${rating}</span>
                </div>
                <span class="reviews">${reviewsCount} reviews</span>
            </div>
            <div class="price-section">
                <div>
                    <span class="price">₹${price}</span>
                    <span class="price-period">/month</span>
                </div>
                <button class="book-btn" onclick="event.stopPropagation(); bookNow(${room.id})">
                    <i class="fas fa-calendar-check"></i> Book
                </button>
            </div>
        </div>
    `;
    
    return div;
}

function createMobileRoomCard(room) {
    const div = document.createElement('div');
    div.className = "bg-white rounded-xl overflow-hidden room-card shadow-sm";
    div.onclick = () => viewRoomDetails(room.id);
    
    const badgeClass = room.pg_type === 'men' ? 'badge-men' : (room.pg_type === 'women' ? 'badge-women' : 'badge-unisex');
    const badgeText = room.pg_type === 'men' ? 'Men Only' : (room.pg_type === 'women' ? 'Women Only' : 'Unisex');
    const rating = room.rating ? parseFloat(room.rating).toFixed(1) : '4.5';
    const reviewsCount = room.reviews_count || 0;
    const price = room.price ? Number(room.price).toLocaleString('en-IN') : '0';
    const imageUrl = room.image_url || 'https://via.placeholder.com/400x200?text=PG+Image';
    const location = room.location || room.city || 'Location not specified';
    
    div.innerHTML = `
        <div class="relative">
            <img src="${escapeHtml(imageUrl)}" class="w-full h-40 object-cover" alt="${escapeHtml(room.title)}">
            <div class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs font-semibold ${badgeClass}">${badgeText}</div>
        </div>
        <div class="p-4">
            <h3 class="font-bold text-gray-900 mb-1">${escapeHtml(room.title || 'Room')}</h3>
            <p class="text-gray-500 text-xs flex items-center gap-1 mb-2">
                <i class="fas fa-map-marker-alt text-[#003B95] text-xs"></i>
                <span>${escapeHtml(location)}</span>
            </p>
            <div class="flex items-center gap-2 mb-3">
                <div class="flex items-center gap-1 bg-yellow-50 px-2 py-1 rounded-full">
                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                    <span class="text-xs font-semibold">${rating}</span>
                </div>
                <span class="text-xs text-gray-400">(${reviewsCount} reviews)</span>
            </div>
            <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                <div>
                    <span class="text-[#003B95] font-bold">₹${price}</span>
                    <span class="text-xs text-gray-400">/month</span>
                </div>
                <button class="bg-[#003B95] text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-1" onclick="event.stopPropagation(); bookNow(${room.id})">
                    <i class="fas fa-calendar-check text-xs"></i> Book
                </button>
            </div>
        </div>
    `;
    
    return div;
}

// Clear search functions
function clearSearchDesktop() {
    if (desktopInput) {
        desktopInput.value = '';
        performSearch('');
        const url = new URL(window.location);
        url.searchParams.delete('q');
        window.history.pushState({}, '', url);
    }
}

function clearSearchMobile() {
    if (mobileInput) {
        mobileInput.value = '';
        performSearch('');
        const url = new URL(window.location);
        url.searchParams.delete('q');
        window.history.pushState({}, '', url);
    }
}

// Navigation functions
function goBack() {
    window.history.back();
}

function goToPage(page) {
    if (page === 'home') window.location.href = '/home';
    else if (page === 'search') window.location.href = '/search';
    else if (page === 'bookings') window.location.href = '/bookings';
    else if (page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if (page === 'profile') window.location.href = '/profile';
}

function viewRoomDetails(roomId) {
    window.location.href = 'room-details?id=' + roomId;
}

function bookNow(roomId) {
    <?php if(!isset($_SESSION['user_id'])): ?>
        showToast('Please login to book');
        setTimeout(() => {
            window.location.href = 'login';
        }, 1000);
    <?php else: ?>
        window.location.href = 'booking?room_id=' + roomId;
    <?php endif; ?>
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
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
</script>

</body>
</html>