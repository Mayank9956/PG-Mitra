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

// Fetch user details
$user_query = "SELECT username, full_name, profile_image, wallet_balance FROM users WHERE id = ?";
$stmt = $db->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$display_name = !empty($user['full_name']) ? $user['full_name'] : $user['username'];
$profile_image = !empty($user['profile_image']) ? $user['profile_image'] : 'https://ui-avatars.com/api/?name=' . urlencode($display_name) . '&background=003B95&color=fff&size=40';
$wallet_balance = $user['wallet_balance'] ?? 0;

// Fetch all bookings with related data
$query = "SELECT 
    b.*,
    r.title as room_name,
    r.location,
    r.city,
    r.pg_type,
    r.price as monthly_rent,
    r.rating,
    r.reviews_count,
    r.facilities,
    r.host_name,
    r.host_phone,
    r.host_email,

    ri.image_url as image_url,  -- primary image

    DATEDIFF(b.check_out, CURDATE()) as days_left,
    DATEDIFF(b.check_out, b.check_in) as total_days,
    DATEDIFF(CURDATE(), b.check_in) as days_completed

FROM bookings b 

LEFT JOIN rooms r 
    ON b.room_id = r.id 

LEFT JOIN room_images ri 
    ON r.id = ri.room_id AND ri.is_primary = 1

WHERE b.user_id = ? 

ORDER BY 
    CASE 
        WHEN b.status = 'pending' THEN 1
        WHEN b.status = 'confirmed' AND b.check_out >= CURDATE() THEN 2
        WHEN b.status = 'confirmed' AND b.check_out < CURDATE() THEN 3
        WHEN b.status = 'cancelled' THEN 4
        ELSE 5
    END,
    b.created_at DESC";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
$total_count = 0;
$active_count = 0;
$expired_count = 0;
$pending_count = 0;

while($row = $result->fetch_assoc()) {
    $bookings[] = $row;
    $total_count++;
    
    $today = date('Y-m-d');
    if($row['status'] == 'confirmed') {
        if($row['check_out'] >= $today) {
            $active_count++;
        } else {
            $expired_count++;
        }
    } elseif($row['status'] == 'pending') {
        $pending_count++;
        $active_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>My Bookings - PG Mitra</title>
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

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 32px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.stat-number {
    font-size: 28px;
    font-weight: 800;
    color: #003B95;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 13px;
    color: #6B7280;
}

/* Filters */
.filters-section {
    background: white;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.filter-buttons {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 8px 20px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 500;
    background: #F3F4F6;
    color: #4B5563;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-btn:hover {
    background: #E5E7EB;
}

.filter-btn.active {
    background: #003B95;
    color: white;
}

/* Bookings Grid */
.bookings-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Booking Card */
.booking-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    transition: all 0.2s;
    cursor: pointer;
}

.booking-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.booking-card-inner {
    display: flex;
    gap: 20px;
    padding: 20px;
}

.booking-image {
    width: 120px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    flex-shrink: 0;
}

.booking-info {
    flex: 1;
}

.booking-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}

.booking-title {
    font-size: 18px;
    font-weight: 700;
    color: #1E2A3A;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-confirmed {
    background: #DCFCE7;
    color: #166534;
}

.status-pending {
    background: #FEF3C7;
    color: #92400E;
}

.status-cancelled {
    background: #FEE2E2;
    color: #991B1B;
}

.status-expired {
    background: #F3F4F6;
    color: #4B5563;
}

.booking-location {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #6B7280;
    margin-bottom: 12px;
}

.booking-location i {
    color: #003B95;
    font-size: 12px;
}

.booking-dates {
    display: flex;
    gap: 20px;
    margin-bottom: 12px;
    font-size: 13px;
}

.date-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #4B5563;
}

.date-item i {
    color: #003B95;
}

.booking-price {
    font-size: 20px;
    font-weight: 800;
    color: #003B95;
    margin-top: 8px;
}

.price-period {
    font-size: 12px;
    font-weight: normal;
    color: #6B7280;
}

.booking-actions {
    display: flex;
    gap: 12px;
    margin-top: 12px;
}

.action-btn {
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.action-btn-primary {
    background: #003B95;
    color: white;
}

.action-btn-primary:hover {
    background: #002E7A;
}

.action-btn-secondary {
    background: #F3F4F6;
    color: #4B5563;
}

.action-btn-secondary:hover {
    background: #E5E7EB;
}

/* Progress Bar */
.progress-section {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #E5E7EB;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    margin-bottom: 6px;
    color: #6B7280;
}

.progress-bar {
    height: 6px;
    background: #E5E7EB;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #003B95;
    border-radius: 3px;
    transition: width 0.3s;
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
    border-radius: 16px;
    max-width: 500px;
    width: 90%;
    max-height: 85vh;
    overflow-y: auto;
    padding: 24px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #E5E7EB;
}

.modal-title {
    font-size: 20px;
    font-weight: 700;
}

.modal-close {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #F3F4F6;
    display: flex;
    align-items: center;
    justify-content: center;
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
    z-index: 3000;
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

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 16px;
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
    
    .stats-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        padding: 0 16px;
    }
    
    .stat-card {
        padding: 12px;
    }
    
    .stat-number {
        font-size: 22px;
    }
    
    .booking-card-inner {
        flex-direction: column;
        gap: 12px;
        padding: 16px;
    }
    
    .booking-image {
        width: 100%;
        height: 160px;
    }
    
    .booking-dates {
        flex-direction: column;
        gap: 8px;
    }
    
    .booking-actions {
        flex-wrap: wrap;
    }
    
    .filters-section {
        margin: 0 16px 16px 16px;
    }
    
    .bookings-grid {
        padding: 0 16px;
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
            <a href="/bookings" class="active"><i class="fas fa-ticket-alt"></i> Bookings</a>
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
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $total_count; ?></div>
            <div class="stat-label">Total Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $active_count; ?></div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $pending_count; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $expired_count; ?></div>
            <div class="stat-label">Completed</div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="filters-section">
        <div class="filter-buttons" id="desktopFilters">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="active">Active</button>
            <button class="filter-btn" data-filter="pending">Pending</button>
            <button class="filter-btn" data-filter="completed">Completed</button>
            <button class="filter-btn" data-filter="cancelled">Cancelled</button>
        </div>
    </div>
    
    <!-- Bookings Grid -->
    <div class="bookings-grid" id="desktopBookings">
        <!-- Bookings will be populated here -->
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <!-- Stats Cards -->
    <div class="stats-grid" style="margin-top: 16px;">
        <div class="stat-card">
            <div class="stat-number"><?php echo $total_count; ?></div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $active_count; ?></div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $pending_count; ?></div>
            <div class="stat-label">Pending</div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="filters-section">
        <div class="filter-buttons" id="mobileFilters">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="active">Active</button>
            <button class="filter-btn" data-filter="pending">Pending</button>
            <button class="filter-btn" data-filter="completed">Completed</button>
            <button class="filter-btn" data-filter="cancelled">Cancelled</button>
        </div>
    </div>
    
    <!-- Bookings List -->
    <div id="mobileBookings" class="space-x-3 space-y-3"></div>
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
            <i class="fas fa-ticket-alt" style="font-size: 22px; color: #003B95;"></i>
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

<!-- Booking Details Modal -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Booking Details</h2>
            <div class="modal-close" onclick="closeModal('bookingModal')">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

<!-- Contact Modal -->
<div id="contactModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Contact Host</h2>
            <div class="modal-close" onclick="closeModal('contactModal')">
                <i class="fas fa-times"></i>
            </div>
        </div>
        <div id="contactContent"></div>
    </div>
</div>

<script>
const bookings = <?php echo json_encode($bookings); ?>;
let currentFilter = 'all';

// Render bookings on page load
document.addEventListener('DOMContentLoaded', function() {
    renderBookings();
    setupFilters();
});

function setupFilters() {
    const desktopFilters = document.querySelectorAll('#desktopFilters .filter-btn');
    const mobileFilters = document.querySelectorAll('#mobileFilters .filter-btn');
    
    const allFilters = [...desktopFilters, ...mobileFilters];
    
    allFilters.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            currentFilter = filter;
            
            // Update active state
            allFilters.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            renderBookings();
        });
    });
}

function renderBookings() {
    let filtered = bookings;
    
    switch(currentFilter) {
        case 'active':
            filtered = bookings.filter(b => 
                b.status === 'confirmed' && b.check_out >= getTodayDate()
            );
            break;
        case 'pending':
            filtered = bookings.filter(b => b.status === 'pending');
            break;
        case 'completed':
            filtered = bookings.filter(b => 
                b.status === 'confirmed' && b.check_out < getTodayDate()
            );
            break;
        case 'cancelled':
            filtered = bookings.filter(b => b.status === 'cancelled');
            break;
        default:
            filtered = bookings;
    }
    
    renderDesktopBookings(filtered);
    renderMobileBookings(filtered);
}

function renderDesktopBookings(bookingsList) {
    const container = document.getElementById('desktopBookings');
    
    if (!container) return;
    
    if (bookingsList.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-bed"></i>
                </div>
                <h3 class="text-lg font-bold mb-2">No Bookings Found</h3>
                <p class="text-gray-500 mb-4">You don't have any bookings in this category</p>
                <a href="/search" class="inline-block bg-[#003B95] text-white px-6 py-3 rounded-lg font-semibold">
                    Find Accommodation
                </a>
            </div>
        `;
        return;
    }
    
    container.innerHTML = bookingsList.map(booking => createBookingCard(booking, 'desktop')).join('');
}

function renderMobileBookings(bookingsList) {
    const container = document.getElementById('mobileBookings');
    
    if (!container) return;
    
    if (bookingsList.length === 0) {
        container.innerHTML = `
            <div class="bg-white rounded-2xl p-8 text-center mx-4">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-bed text-2xl text-gray-400"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">No Bookings Found</h3>
                <p class="text-gray-500 text-sm">You don't have any bookings in this category</p>
                <a href="/search" class="inline-block mt-4 text-[#003B95] font-medium">Find Accommodation →</a>
            </div>
        `;
        return;
    }
    
    container.innerHTML = bookingsList.map(booking => createMobileBookingCard(booking)).join('');
}

function createBookingCard(booking, type) {
    const statusClass = getStatusClass(booking);
    const statusIcon = getStatusIcon(booking);
    const statusText = getStatusText(booking);
    const today = getTodayDate();
    const isActive = booking.status === 'confirmed' && booking.check_out >= today;
    
    let progressHTML = '';
    if (isActive) {
        const daysCompleted = Math.max(0, booking.days_completed || 0);
        const totalDays = Math.max(1, booking.total_days || 1);
        const progressPercent = Math.min(100, Math.round((daysCompleted / totalDays) * 100));
        const daysLeft = Math.max(0, booking.days_left || 0);
        
        progressHTML = `
            <div class="progress-section">
                <div class="progress-label">
                    <span>Stay Progress</span>
                    <span>${daysCompleted}/${totalDays} days</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: ${progressPercent}%"></div>
                </div>
                ${daysLeft <= 15 ? `<p class="text-xs text-orange-500 mt-2">${daysLeft} days remaining</p>` : ''}
            </div>
        `;
    }
    
    return `
        <div class="booking-card" onclick="viewBookingDetails(${booking.id})">
            <div class="booking-card-inner">
                <img src="${booking.image_url || 'https://via.placeholder.com/120x120?text=PG'}" 
                     class="booking-image" 
                     alt="${escapeHtml(booking.room_name)}">
                <div class="booking-info">
                    <div class="booking-header">
                        <h3 class="booking-title">${escapeHtml(booking.room_name || 'Room')}</h3>
                        <span class="status-badge ${statusClass}">
                            <i class="fas ${statusIcon} mr-1"></i>${statusText}
                        </span>
                    </div>
                    <div class="booking-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${escapeHtml(booking.location || 'Location')}, ${escapeHtml(booking.city || '')}</span>
                    </div>
                    <div class="booking-dates">
                        <div class="date-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Check-in: ${formatDate(booking.check_in)}</span>
                        </div>
                        <div class="date-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>Check-out: ${formatDate(booking.check_out)}</span>
                        </div>
                    </div>
                    <div class="booking-price">
                        ₹${numberFormat(booking.monthly_rent || 0)}<span class="price-period">/month</span>
                        ${booking.months > 1 ? `<span class="text-sm text-gray-500 ml-2">(${booking.months} months)</span>` : ''}
                    </div>
                    ${progressHTML}
                    <div class="booking-actions">
                        <button class="action-btn action-btn-primary" onclick="event.stopPropagation(); viewBookingDetails(${booking.id})">
                            <i class="fas fa-eye mr-1"></i> Details
                        </button>
                        ${isActive ? `
                        <button class="action-btn action-btn-secondary" onclick="event.stopPropagation(); contactHost(${booking.id})">
                            <i class="fas fa-phone mr-1"></i> Contact
                        </button>
                        ` : booking.status === 'confirmed' && booking.check_out < today ? `
                        <button class="action-btn action-btn-primary" onclick="event.stopPropagation(); rebook(${booking.room_id})">
                            <i class="fas fa-redo mr-1"></i> Rebook
                        </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
}

function createMobileBookingCard(booking) {
    const statusClass = getStatusClass(booking);
    const statusIcon = getStatusIcon(booking);
    const statusText = getStatusText(booking);
    const today = getTodayDate();
    const isActive = booking.status === 'confirmed' && booking.check_out >= today;
    
    return `
        <div class="bg-white rounded-xl overflow-hidden shadow-sm" onclick="viewBookingDetails(${booking.id})">
            <img src="${booking.image_url || 'https://via.placeholder.com/400x160?text=PG'}" 
                 class="w-full h-32 object-cover" 
                 alt="${escapeHtml(booking.room_name)}">
            <div class="p-4">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-gray-900">${escapeHtml(booking.room_name || 'Room')}</h3>
                    <span class="status-badge ${statusClass} text-xs">
                        <i class="fas ${statusIcon} mr-1"></i>${statusText}
                    </span>
                </div>
                <p class="text-gray-500 text-xs flex items-center gap-1 mb-2">
                    <i class="fas fa-map-marker-alt text-[#003B95]"></i>
                    ${escapeHtml(booking.location || 'Location')}
                </p>
                <div class="flex justify-between text-xs text-gray-600 mb-3">
                    <span><i class="fas fa-calendar-alt mr-1"></i>${formatDate(booking.check_in)}</span>
                    <span><i class="fas fa-calendar-check mr-1"></i>${formatDate(booking.check_out)}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                    <div>
                        <span class="text-[#003B95] font-bold">₹${numberFormat(booking.monthly_rent || 0)}</span>
                        <span class="text-xs text-gray-400">/month</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="bg-[#003B95] text-white px-3 py-1.5 rounded-lg text-xs" onclick="event.stopPropagation(); viewBookingDetails(${booking.id})">
                            Details
                        </button>
                        ${isActive ? `
                        <button class="bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg text-xs" onclick="event.stopPropagation(); contactHost(${booking.id})">
                            Contact
                        </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
}

function viewBookingDetails(id) {
    const booking = bookings.find(b => b.id == id);
    if (!booking) return;
    
    const modalContent = document.getElementById('modalContent');
    const today = getTodayDate();
    const isActive = booking.status === 'confirmed' && booking.check_out >= today;
    const daysCompleted = Math.max(0, booking.days_completed || 0);
    const totalDays = Math.max(1, booking.total_days || 1);
    const progressPercent = Math.min(100, Math.round((daysCompleted / totalDays) * 100));
    const statusClass = getStatusClass(booking);
    const statusIcon = getStatusIcon(booking);
    const statusText = getStatusText(booking);
    
    modalContent.innerHTML = `
        <div class="space-y-4">
            <div class="flex items-center gap-4">
                <img src="${booking.image_url || 'https://via.placeholder.com/80x80?text=PG'}" 
                     class="w-20 h-20 rounded-xl object-cover">
                <div class="flex-1">
                    <h3 class="font-bold text-lg">${escapeHtml(booking.room_name || 'Room')}</h3>
                    <p class="text-gray-500 text-sm flex items-center gap-1">
                        <i class="fas fa-map-marker-alt text-[#003B95]"></i>
                        ${escapeHtml(booking.location || 'Location')}, ${escapeHtml(booking.city || '')}
                    </p>
                    <span class="status-badge ${statusClass} text-xs mt-1 inline-block">
                        <i class="fas ${statusIcon} mr-1"></i>${statusText}
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 p-3 rounded-xl">
                    <p class="text-xs text-gray-500 mb-1">Booking ID</p>
                    <p class="font-semibold text-sm">#${escapeHtml(booking.booking_number)}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl">
                    <p class="text-xs text-gray-500 mb-1">Guests</p>
                    <p class="font-semibold text-sm">${booking.guests || 1} Guest${booking.guests > 1 ? 's' : ''}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl">
                    <p class="text-xs text-gray-500 mb-1">Check-in</p>
                    <p class="font-semibold text-sm">${formatDate(booking.check_in)}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl">
                    <p class="text-xs text-gray-500 mb-1">Check-out</p>
                    <p class="font-semibold text-sm">${formatDate(booking.check_out)}</p>
                </div>
            </div>
            
            ${isActive ? `
            <div class="bg-blue-50 p-4 rounded-xl">
                <div class="flex justify-between mb-2">
                    <span class="font-semibold">Stay Progress</span>
                    <span class="font-semibold text-blue-600">${progressPercent}%</span>
                </div>
                <div class="progress-bar mb-2">
                    <div class="progress-fill" style="width: ${progressPercent}%"></div>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>Days Completed: ${daysCompleted}</span>
                    <span>Total Days: ${totalDays}</span>
                </div>
            </div>
            ` : ''}
            
            <div class="bg-gray-50 p-4 rounded-xl">
                <p class="font-semibold mb-3">Payment Details</p>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Monthly Rent</span>
                        <span class="font-semibold">₹${numberFormat(booking.monthly_rent || 0)}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Duration</span>
                        <span class="font-semibold">${booking.months || 0} Month${booking.months > 1 ? 's' : ''}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Rent</span>
                        <span class="font-semibold">₹${numberFormat((booking.monthly_rent || 0) * (booking.months || 0))}</span>
                    </div>
                    <div class="border-t pt-2 mt-2">
                        <div class="flex justify-between font-bold">
                            <span>Total Amount</span>
                            <span class="text-[#003B95]">₹${numberFormat(booking.total_price || 0)}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            ${booking.special_requests ? `
            <div class="bg-yellow-50 p-4 rounded-xl">
                <p class="font-semibold mb-2"><i class="fas fa-clipboard-list mr-2"></i>Special Requests</p>
                <p class="text-sm text-gray-600">${escapeHtml(booking.special_requests)}</p>
            </div>
            ` : ''}
            
            <div class="bg-green-50 p-4 rounded-xl">
                <p class="font-semibold mb-3"><i class="fas fa-user-circle mr-2"></i>Host Information</p>
                <div class="space-y-2">
                    <p class="text-sm"><strong>Name:</strong> ${escapeHtml(booking.host_name || 'Host')}</p>
                    ${booking.host_phone ? `<p class="text-sm"><strong>Phone:</strong> ${escapeHtml(booking.host_phone)}</p>` : ''}
                    ${booking.host_email ? `<p class="text-sm"><strong>Email:</strong> ${escapeHtml(booking.host_email)}</p>` : ''}
                </div>
            </div>
            
            <button onclick="closeModal('bookingModal')" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold transition">
                Close
            </button>
        </div>
    `;
    
    openModal('bookingModal');
}

function contactHost(id) {
    const booking = bookings.find(b => b.id == id);
    if (!booking) return;
    
    const contactContent = document.getElementById('contactContent');
    
    contactContent.innerHTML = `
        <div class="text-center mb-6">
            <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-user text-3xl text-[#003B95]"></i>
            </div>
            <h3 class="font-bold text-lg">${escapeHtml(booking.host_name || 'Host')}</h3>
            <p class="text-gray-500 text-sm">${escapeHtml(booking.room_name)}</p>
        </div>
        
        <div class="space-y-3">
            ${booking.host_phone ? `
            <a href="tel:${booking.host_phone}" class="flex items-center gap-4 bg-green-50 hover:bg-green-100 p-4 rounded-xl transition">
                <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center text-white">
                    <i class="fas fa-phone"></i>
                </div>
                <div>
                    <p class="font-semibold">Call Host</p>
                    <p class="text-sm text-gray-500">${booking.host_phone}</p>
                </div>
            </a>
            ` : ''}
            
            ${booking.host_email ? `
            <a href="mailto:${booking.host_email}" class="flex items-center gap-4 bg-purple-50 hover:bg-purple-100 p-4 rounded-xl transition">
                <div class="w-12 h-12 rounded-full bg-purple-500 flex items-center justify-center text-white">
                    <i class="fas fa-envelope"></i>
                </div>
                <div>
                    <p class="font-semibold">Send Email</p>
                    <p class="text-sm text-gray-500">${booking.host_email}</p>
                </div>
            </a>
            ` : ''}
        </div>
        
        <button onclick="closeModal('contactModal')" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-xl font-semibold transition mt-6">
            Close
        </button>
    `;
    
    openModal('contactModal');
}

function rebook(roomId) {
    showToast('Redirecting to booking page...');
    setTimeout(() => {
        window.location.href = 'booking?room_id=' + roomId;
    }, 1000);
}

function openModal(modalId) {
    document.getElementById(modalId).classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
    document.body.style.overflow = 'auto';
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
}

function getStatusClass(booking) {
    const today = getTodayDate();
    if (booking.status === 'cancelled') return 'status-cancelled';
    if (booking.status === 'pending') return 'status-pending';
    if (booking.status === 'confirmed' && booking.check_out < today) return 'status-expired';
    return 'status-confirmed';
}

function getStatusIcon(booking) {
    const today = getTodayDate();
    if (booking.status === 'cancelled') return 'fa-times-circle';
    if (booking.status === 'pending') return 'fa-clock';
    if (booking.status === 'confirmed' && booking.check_out < today) return 'fa-calendar-times';
    return 'fa-check-circle';
}

function getStatusText(booking) {
    const today = getTodayDate();
    if (booking.status === 'cancelled') return 'Cancelled';
    if (booking.status === 'pending') return 'Pending';
    if (booking.status === 'confirmed' && booking.check_out < today) return 'Completed';
    return 'Active';
}

function getTodayDate() {
    return new Date().toISOString().split('T')[0];
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
}

function numberFormat(num) {
    return new Intl.NumberFormat('en-IN').format(num || 0);
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