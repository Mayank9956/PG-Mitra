<?php
require_once 'common/auth.php';

// Ensure user is logged in
$user = requireAuth($conn);

$user_id = $user['id'];

// ================= REMOVE FAVORITE =================
if (isset($_POST['remove_favorite'])) {
    $room_id = intval($_POST['room_id']);

    $stmt = $conn->prepare("DELETE FROM user_favorites WHERE user_id = ? AND room_id = ?");
    $stmt->bind_param("ii", $user_id, $room_id);
    $stmt->execute();

    header("Location: saved-rooms.php");
    exit;
}

// ================= FETCH FAVORITES =================
$query = "
    SELECT 
        r.*, 
        uf.created_at as saved_date,
        (
            SELECT image_url 
            FROM room_images 
            WHERE room_id = r.id AND is_primary = 1
            LIMIT 1
        ) AS primary_image
    FROM rooms r 
    JOIN user_favorites uf ON r.id = uf.room_id 
    WHERE uf.user_id = ? 
    ORDER BY uf.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$favorite_rooms = [];

while ($row = $result->fetch_assoc()) {
    $favorite_rooms[] = $row;
}

$total_favorites = count($favorite_rooms);

// ================= USER HEADER =================
$display_name = !empty($user['full_name']) 
    ? $user['full_name'] 
    : $user['username'];

$profile_image = !empty($user['profile_image']) 
    ? $user['profile_image'] 
    : 'https://ui-avatars.com/api/?name=' . urlencode($display_name) . '&background=003B95&color=fff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Saved Rooms - PG Mitra</title>
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

/* Stats Card */
.stats-card {
    background: linear-gradient(135deg, #003B95 0%, #0066CC 100%);
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 32px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.stats-left h2 {
    font-size: 14px;
    font-weight: 500;
    opacity: 0.9;
    margin-bottom: 8px;
}

.stats-left .number {
    font-size: 48px;
    font-weight: 800;
    line-height: 1;
}

.stats-left p {
    font-size: 14px;
    margin-top: 8px;
    opacity: 0.8;
}

.stats-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stats-icon i {
    font-size: 36px;
    color: #FFB700;
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

.remove-btn {
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
    border: none;
}

.remove-btn:hover {
    background: #EF4444;
    transform: scale(1.1);
}

.remove-btn:hover i {
    color: white;
}

.remove-btn i {
    font-size: 14px;
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

.features {
    display: flex;
    gap: 12px;
    margin: 12px 0;
    padding: 12px 0;
    border-top: 1px solid #F0F2F5;
    border-bottom: 1px solid #F0F2F5;
}

.feature {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #6B7280;
}

.feature i {
    color: #003B95;
    font-size: 12px;
}

.rating {
    display: flex;
    align-items: center;
    gap: 6px;
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

.saved-date {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #F0F2F5;
    font-size: 11px;
    color: #9CA3AF;
}

.saved-date i {
    font-size: 10px;
}

.book-btn {
    width: 100%;
    background: #003B95;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 12px;
}

.book-btn:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px;
    background: white;
    border-radius: 16px;
    grid-column: 1 / -1;
}

.empty-icon {
    width: 100px;
    height: 100px;
    background: #F3F4F6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.empty-icon i {
    font-size: 48px;
    color: #9CA3AF;
}

.empty-state h3 {
    font-size: 20px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 8px;
}

.empty-state p {
    color: #6B7280;
    margin-bottom: 24px;
}

.explore-btn {
    display: inline-block;
    background: #003B95;
    color: white;
    padding: 12px 32px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
}

.explore-btn:hover {
    background: #002E7A;
    transform: translateY(-1px);
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
    max-width: 400px;
    width: 90%;
    padding: 24px;
    text-align: center;
    animation: slideUp 0.3s ease;
}

.modal-icon {
    width: 70px;
    height: 70px;
    background: #FEF3C7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.modal-icon i {
    font-size: 32px;
    color: #F59E0B;
}

.modal-title {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 8px;
}

.modal-message {
    color: #6B7280;
    font-size: 14px;
    margin-bottom: 24px;
}

.modal-actions {
    display: flex;
    gap: 12px;
}

.modal-btn {
    flex: 1;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.modal-btn.cancel {
    background: #F3F4F6;
    color: #4B5563;
}

.modal-btn.cancel:hover {
    background: #E5E7EB;
}

.modal-btn.confirm {
    background: #EF4444;
    color: white;
}

.modal-btn.confirm:hover {
    background: #DC2626;
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
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
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
    
    .stats-card {
        margin: 16px;
        padding: 20px;
    }
    
    .stats-left .number {
        font-size: 36px;
    }
    
    .stats-icon {
        width: 60px;
        height: 60px;
    }
    
    .stats-icon i {
        font-size: 28px;
    }
    
    .results-grid {
        grid-template-columns: 1fr;
        gap: 16px;
        padding: 0 16px;
    }
    
    .empty-state {
        margin: 0 16px;
        padding: 40px 20px;
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
            <a href="/saved-rooms" class="active"><i class="fas fa-heart"></i> Saved</a>
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
    <!-- Stats Card -->
    <div class="stats-card">
        <div class="stats-left">
            <h2>Your Saved Rooms</h2>
            <div class="number"><?php echo $total_favorites; ?></div>
            <p>Favorites collection</p>
        </div>
        <div class="stats-icon">
            <i class="fas fa-heart"></i>
        </div>
    </div>
    
    <!-- Results Grid -->
    <div class="results-grid" id="desktopResults">
        <?php if($total_favorites > 0): ?>
            <?php foreach($favorite_rooms as $room): 
                $badge_class = $room['pg_type'] == 'men' ? 'badge-men' : ($room['pg_type'] == 'women' ? 'badge-women' : 'badge-unisex');
                $badge_text = $room['pg_type'] == 'men' ? 'Men Only' : ($room['pg_type'] == 'women' ? 'Women Only' : 'Unisex');
                $rating = $room['rating'] ? number_format($room['rating'], 1) : '4.5';
                $reviews = $room['reviews_count'] ?? 0;
                $image = $room['primary_image'] ?? 'https://via.placeholder.com/400x200?text=PG+Image';
            ?>
            <div class="room-card" data-room-id="<?php echo $room['id']; ?>">
                <div class="card-image" onclick="viewRoom(<?php echo $room['id']; ?>)">
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($room['title']); ?>">
                    <div class="pg-badge <?php echo $badge_class; ?>">
                        <?php echo $badge_text; ?>
                    </div>
                    <button class="remove-btn" onclick="event.stopPropagation(); showRemoveModal(<?php echo $room['id']; ?>, '<?php echo addslashes($room['title']); ?>')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="card-content" onclick="viewRoom(<?php echo $room['id']; ?>)">
                    <div class="card-header">
                        <h3 class="room-title"><?php echo htmlspecialchars($room['title']); ?></h3>
                        <div class="room-price">
                            <span class="price">₹<?php echo number_format($room['price']); ?></span>
                            <span class="price-period">/mo</span>
                        </div>
                    </div>
                    <div class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($room['location']); ?>, <?php echo htmlspecialchars($room['city']); ?></span>
                    </div>
                    <div class="features">
                        <div class="feature">
                            <i class="fas fa-bed"></i>
                            <span><?php echo $room['bedrooms']; ?> Bed</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-bath"></i>
                            <span><?php echo $room['bathrooms']; ?> Bath</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-users"></i>
                            <span><?php echo $room['max_guests']; ?> Guests</span>
                        </div>
                    </div>
                    <div class="rating">
                        <div class="rating-badge">
                            <i class="fas fa-star"></i>
                            <span><?php echo $rating; ?></span>
                        </div>
                        <span class="reviews"><?php echo $reviews; ?> reviews</span>
                    </div>
                    <div class="saved-date">
                        <i class="far fa-clock"></i>
                        <span>Saved on <?php echo date('d M Y', strtotime($room['saved_date'])); ?></span>
                    </div>
                    <button class="book-btn" onclick="event.stopPropagation(); bookNow(<?php echo $room['id']; ?>)">
                        <i class="fas fa-calendar-check mr-2"></i> Book Now
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="far fa-heart"></i>
                </div>
                <h3>No saved rooms yet</h3>
                <p>Start exploring and save your favorite rooms for later</p>
                <a href="/search" class="explore-btn">Explore Rooms</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <!-- Stats Card -->
    <div class="stats-card" style="margin: 16px;">
        <div class="stats-left">
            <h2>Your Saved Rooms</h2>
            <div class="number"><?php echo $total_favorites; ?></div>
            <p>Favorites collection</p>
        </div>
        <div class="stats-icon">
            <i class="fas fa-heart"></i>
        </div>
    </div>
    
    <!-- Results Grid -->
    <div class="results-grid" id="mobileResults">
        <?php if($total_favorites > 0): ?>
            <?php foreach($favorite_rooms as $room): 
                $badge_class = $room['pg_type'] == 'men' ? 'badge-men' : ($room['pg_type'] == 'women' ? 'badge-women' : 'badge-unisex');
                $badge_text = $room['pg_type'] == 'men' ? 'Men Only' : ($room['pg_type'] == 'women' ? 'Women Only' : 'Unisex');
                $rating = $room['rating'] ? number_format($room['rating'], 1) : '4.5';
                $reviews = $room['reviews_count'] ?? 0;
                $image = $room['primary_image'] ?? 'https://via.placeholder.com/400x200?text=PG+Image';
            ?>
            <div class="room-card" data-room-id="<?php echo $room['id']; ?>">
                <div class="card-image" onclick="viewRoom(<?php echo $room['id']; ?>)">
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($room['title']); ?>">
                    <div class="pg-badge <?php echo $badge_class; ?>">
                        <?php echo $badge_text; ?>
                    </div>
                    <button class="remove-btn" onclick="event.stopPropagation(); showRemoveModal(<?php echo $room['id']; ?>, '<?php echo addslashes($room['title']); ?>')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="card-content" onclick="viewRoom(<?php echo $room['id']; ?>)">
                    <div class="card-header">
                        <h3 class="room-title"><?php echo htmlspecialchars($room['title']); ?></h3>
                        <div class="room-price">
                            <span class="price">₹<?php echo number_format($room['price']); ?></span>
                            <span class="price-period">/mo</span>
                        </div>
                    </div>
                    <div class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($room['location']); ?>, <?php echo htmlspecialchars($room['city']); ?></span>
                    </div>
                    <div class="features">
                        <div class="feature">
                            <i class="fas fa-bed"></i>
                            <span><?php echo $room['bedrooms']; ?> Bed</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-bath"></i>
                            <span><?php echo $room['bathrooms']; ?> Bath</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-users"></i>
                            <span><?php echo $room['max_guests']; ?> Guests</span>
                        </div>
                    </div>
                    <div class="rating">
                        <div class="rating-badge">
                            <i class="fas fa-star"></i>
                            <span><?php echo $rating; ?></span>
                        </div>
                        <span class="reviews"><?php echo $reviews; ?> reviews</span>
                    </div>
                    <div class="saved-date">
                        <i class="far fa-clock"></i>
                        <span>Saved on <?php echo date('d M Y', strtotime($room['saved_date'])); ?></span>
                    </div>
                    <button class="book-btn" onclick="event.stopPropagation(); bookNow(<?php echo $room['id']; ?>)">
                        Book Now
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" style="margin: 0 16px;">
                <div class="empty-icon">
                    <i class="far fa-heart"></i>
                </div>
                <h3>No saved rooms yet</h3>
                <p>Start exploring and save your favorite rooms for later</p>
                <a href="/search" class="explore-btn">Explore Rooms</a>
            </div>
        <?php endif; ?>
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
            <i class="fas fa-heart" style="font-size: 22px; color: #003B95;"></i>
            <div style="font-size: 11px; margin-top: 4px;">Saved</div>
        </div>
        <div onclick="goToPage('profile')" style="text-align: center; cursor: pointer;">
            <i class="fas fa-user" style="font-size: 22px; color: #9CA3AF;"></i>
            <div style="font-size: 11px; margin-top: 4px;">Profile</div>
        </div>
    </div>
</div>

<!-- Remove Confirmation Modal -->
<div id="removeModal" class="modal">
    <div class="modal-content">
        <div class="modal-icon">
            <i class="fas fa-heart-broken"></i>
        </div>
        <h2 class="modal-title">Remove from Saved?</h2>
        <p class="modal-message" id="removeModalMessage">Are you sure you want to remove this room from your saved list?</p>
        <form method="POST" id="removeForm">
            <input type="hidden" name="room_id" id="removeRoomId">
            <input type="hidden" name="remove_favorite" value="1">
            <div class="modal-actions">
                <button type="button" class="modal-btn cancel" onclick="closeRemoveModal()">Cancel</button>
                <button type="submit" class="modal-btn confirm">Remove</button>
            </div>
        </form>
    </div>
</div>

<script>
let roomToRemove = null;

function viewRoom(roomId) {
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

function showRemoveModal(roomId, roomTitle) {
    roomToRemove = roomId;
    document.getElementById('removeRoomId').value = roomId;
    document.getElementById('removeModalMessage').textContent = `Are you sure you want to remove "${roomTitle}" from your saved list?`;
    document.getElementById('removeModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeRemoveModal() {
    document.getElementById('removeModal').classList.remove('show');
    document.body.style.overflow = 'auto';
    roomToRemove = null;
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i> ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
}

function goToPage(page) {
    if (page === 'home') window.location.href = '/home';
    else if (page === 'search') window.location.href = '/search';
    else if (page === 'bookings') window.location.href = '/bookings';
    else if (page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if (page === 'profile') window.location.href = '/profile';
}

// Close modal when clicking outside
document.getElementById('removeModal').addEventListener('click', function(e) {
    if (e.target === this) closeRemoveModal();
});

// Handle form submission
document.getElementById('removeForm').addEventListener('submit', function(e) {
    if (!roomToRemove) {
        e.preventDefault();
        return;
    }
});

// Check for success message from URL
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.has('removed')) {
        showToast('Room removed from saved', 'success');
    }
});
</script>

</body>
</html>