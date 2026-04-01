<?php
require_once 'common/auth.php';

// Optional user
$user_id = $user['id'] ?? null;

// ================= ROOM ID =================
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

if ($room_id <= 0) {
    header("Location: index.php");
    exit;
}

// ================= ROOM DETAILS =================
$room_query = "
    SELECT r.id, r.title, r.rating, r.reviews_count, ri.image_url, r.location, r.city
    FROM rooms r
    LEFT JOIN room_images ri 
        ON r.id = ri.room_id AND ri.is_primary = 1
    WHERE r.id = ? 
    LIMIT 1
";

$stmt = $conn->prepare($room_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room_result = $stmt->get_result();
$room = $room_result->fetch_assoc();

if (!$room) {
    header("Location: home.php");
    exit;
}

// ================= PAGINATION =================
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// ================= REVIEWS =================
$reviews_query = "
    SELECT r.*, u.full_name AS user_name, u.profile_image
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.room_id = ?
    ORDER BY r.created_at DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($reviews_query);
$stmt->bind_param("iii", $room_id, $limit, $offset);
$stmt->execute();
$reviews = $stmt->get_result();

// ================= TOTAL COUNT =================
$count_query = "SELECT COUNT(*) AS total FROM reviews WHERE room_id = ?";
$stmt = $conn->prepare($count_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$count_result = $stmt->get_result();
$total = (int)($count_result->fetch_assoc()['total'] ?? 0);
$total_pages = max(1, (int)ceil($total / $limit));

// ================= RATING DISTRIBUTION =================
$distribution_query = "
    SELECT
        SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) AS five_star,
        SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) AS four_star,
        SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) AS three_star,
        SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) AS two_star,
        SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) AS one_star
    FROM reviews
    WHERE room_id = ?
";

$stmt = $conn->prepare($distribution_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$distribution_result = $stmt->get_result();
$distribution = $distribution_result->fetch_assoc() ?: [
    'five_star' => 0,
    'four_star' => 0,
    'three_star' => 0,
    'two_star' => 0,
    'one_star' => 0
];

// Calculate average rating
$avg_rating = $room['rating'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Reviews - <?php echo htmlspecialchars($room['title']); ?> | PG Mitra</title>
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
    color: white;
}

/* Main Container */
.main-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 80px;
}

/* Mobile Container */
.mobile-container {
    display: none;
    padding: 0 0 80px 0;
    background: white;
    min-height: 100vh;
}

/* Breadcrumb */
.breadcrumb {
    margin-bottom: 24px;
}

.breadcrumb a {
    color: #003B95;
    text-decoration: none;
    font-size: 14px;
}

.breadcrumb span {
    color: #6B7280;
    font-size: 14px;
}

/* Reviews Container */
.reviews-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    overflow: hidden;
}

/* Room Info Card */
.room-info-card {
    padding: 24px;
    border-bottom: 1px solid #E5E7EB;
    display: flex;
    gap: 20px;
    align-items: center;
}

.room-image {
    width: 100px;
    height: 100px;
    border-radius: 12px;
    object-fit: cover;
}

.room-details h1 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 8px;
}

.room-location {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #6B7280;
    margin-bottom: 12px;
}

.rating-summary {
    display: flex;
    align-items: center;
    gap: 12px;
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

.rating-badge i {
    color: #FFB700;
}

/* Rating Distribution */
.rating-distribution {
    padding: 24px;
    border-bottom: 1px solid #E5E7EB;
}

.distribution-title {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 20px;
}

.distribution-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.distribution-label {
    width: 80px;
    font-size: 14px;
    color: #4B5563;
}

.progress-bar {
    flex: 1;
    height: 8px;
    background: #E5E7EB;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #F59E0B;
    border-radius: 4px;
}

.distribution-count {
    width: 40px;
    text-align: right;
    font-size: 14px;
    color: #6B7280;
}

/* Reviews List */
.reviews-list {
    padding: 24px;
}

.review-card {
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    transition: all 0.2s;
}

.review-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.reviewer-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.reviewer-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #003B95, #0066CC);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 18px;
    object-fit: cover;
}

.reviewer-name {
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 4px;
}

.review-date {
    font-size: 12px;
    color: #9CA3AF;
}

.review-rating {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #FEF3C7;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: #92400E;
}

.review-rating i {
    color: #FBBF24;
}

.review-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 12px;
}

.tag {
    padding: 4px 12px;
    background: #EFF6FF;
    color: #003B95;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.review-comment {
    color: #4B5563;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 16px;
}

.review-images {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}

.review-image {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    cursor: pointer;
    border: 1px solid #E5E7EB;
    transition: transform 0.2s;
}

.review-image:hover {
    transform: scale(1.05);
}

.review-actions {
    display: flex;
    gap: 20px;
    padding-top: 12px;
    border-top: 1px solid #E5E7EB;
}

.edit-btn, .delete-btn {
    background: none;
    border: none;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}

.edit-btn {
    color: #003B95;
}

.edit-btn:hover {
    color: #002E7A;
}

.delete-btn {
    color: #EF4444;
}

.delete-btn:hover {
    color: #DC2626;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 24px;
    flex-wrap: wrap;
}

.page-link {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    background: #F3F4F6;
    color: #4B5563;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
}

.page-link:hover {
    background: #E5E7EB;
}

.page-link.active {
    background: #003B95;
    color: white;
}

.page-link.disabled {
    opacity: 0.5;
    cursor: not-allowed;
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

.write-review-btn {
    display: inline-block;
    background: #003B95;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    margin-top: 20px;
    transition: all 0.2s;
}

.write-review-btn:hover {
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
    animation: slideUp 0.3s ease;
}

.modal-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 16px;
}

.modal-icon {
    width: 48px;
    height: 48px;
    background: #FEF3C7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-icon i {
    font-size: 24px;
    color: #F59E0B;
}

.modal-title {
    font-size: 18px;
    font-weight: 700;
}

.modal-text {
    color: #6B7280;
    font-size: 14px;
    line-height: 1.5;
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
    cursor: pointer;
    border: none;
    transition: all 0.2s;
}

.modal-btn.cancel {
    background: #F3F4F6;
    color: #4B5563;
}

.modal-btn.confirm {
    background: #EF4444;
    color: white;
}

.modal-btn.confirm:hover {
    background: #DC2626;
}

/* Image Viewer */
.image-viewer {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.95);
    z-index: 3000;
    flex-direction: column;
}

.image-viewer.show {
    display: flex;
}

.image-viewer-header {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    z-index: 10;
}

.image-viewer-close {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
}

.image-viewer-counter {
    color: white;
    font-size: 14px;
    background: rgba(0,0,0,0.5);
    padding: 8px 16px;
    border-radius: 20px;
}

.image-viewer-content {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.image-viewer-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.2);
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
}

.image-viewer-nav.prev {
    left: 20px;
}

.image-viewer-nav.next {
    right: 20px;
}

.image-viewer-image {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
}

.image-viewer-dots {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
}

.image-viewer-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255,255,255,0.5);
    cursor: pointer;
}

.image-viewer-dot.active {
    background: white;
    width: 20px;
    border-radius: 4px;
}

/* Toast */
.toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    z-index: 4000;
    animation: slideUp 0.3s ease;
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
    background: #1F2937;
    color: white;
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
    
    .room-info-card {
        flex-direction: column;
        text-align: center;
    }
    
    .room-image {
        width: 80px;
        height: 80px;
    }
    
    .room-location {
        justify-content: center;
    }
    
    .rating-summary {
        justify-content: center;
    }
    
    .review-header {
        flex-direction: column;
        gap: 12px;
    }
    
    .review-rating {
        align-self: flex-start;
    }
    
    .mobile-bottom-nav {
        display: block;
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
    <div class="breadcrumb">
        <a href="room-details?id=<?php echo $room_id; ?>"><i class="fas fa-arrow-left mr-2"></i> Back to Room</a>
        <span class="mx-2">/</span>
        <span>Reviews</span>
    </div>
    
    <div class="reviews-container">
        <!-- Room Info -->
        <div class="room-info-card">
            <img src="<?php echo htmlspecialchars($room['image_url'] ?: 'https://via.placeholder.com/100x100?text=PG'); ?>" 
                 alt="<?php echo htmlspecialchars($room['title']); ?>" 
                 class="room-image">
            <div class="room-details">
                <h1><?php echo htmlspecialchars($room['title']); ?></h1>
                <div class="room-location">
                    <i class="fas fa-map-marker-alt text-[#003B95]"></i>
                    <span><?php // echo htmlspecialchars($room['location']); ?> <?php echo htmlspecialchars($room['city']); ?></span>
                </div>
                <div class="rating-summary">
                    <div class="rating-badge">
                        <i class="fas fa-star"></i>
                        <span><?php echo number_format($avg_rating, 1); ?></span>
                    </div>
                    <span class="text-gray-500"><?php echo $total; ?> reviews</span>
                </div>
            </div>
        </div>
        
        <!-- Rating Distribution -->
        <div class="rating-distribution">
            <h3 class="distribution-title">Rating Distribution</h3>
            <?php
            $rating_labels = [5 => 'Excellent', 4 => 'Good', 3 => 'Average', 2 => 'Poor', 1 => 'Terrible'];
            for ($i = 5; $i >= 1; $i--):
                $key = $i == 5 ? 'five_star' : ($i == 4 ? 'four_star' : ($i == 3 ? 'three_star' : ($i == 2 ? 'two_star' : 'one_star')));
                $count = (int)($distribution[$key] ?? 0);
                $percentage = $total > 0 ? ($count / $total) * 100 : 0;
            ?>
            <div class="distribution-item">
                <span class="distribution-label"><?php echo $rating_labels[$i]; ?></span>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                </div>
                <span class="distribution-count"><?php echo $count; ?></span>
            </div>
            <?php endfor; ?>
        </div>
        
        <!-- Reviews List -->
        <div class="reviews-list">
            <?php if ($reviews && $reviews->num_rows > 0): ?>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <?php if (!empty($review['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($review['profile_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($review['user_name']); ?>" 
                                     class="reviewer-avatar" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="reviewer-avatar">
                                    <?php echo strtoupper(substr($review['user_name'] ?: 'U', 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="reviewer-name"><?php echo htmlspecialchars($review['user_name']); ?></div>
                                <div class="review-date">
                                    <?php echo date('d M Y', strtotime($review['created_at'])); ?>
                                    <?php if (!empty($review['updated_at']) && $review['updated_at'] !== $review['created_at']): ?>
                                        <span class="text-gray-400">(Edited)</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="review-rating">
                            <i class="fas fa-star"></i>
                            <span><?php echo (int)$review['rating']; ?></span>
                        </div>
                    </div>
                    
                    <?php if (!empty($review['review_tags'])): ?>
                        <div class="review-tags">
                            <?php
                            $tags = array_filter(array_map('trim', explode(',', $review['review_tags'])));
                            foreach ($tags as $tag):
                            ?>
                                <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="review-comment">
                        <?php echo nl2br(htmlspecialchars($review['comment'] ?? '')); ?>
                    </div>
                    
                    <?php if (!empty($review['review_images'])): ?>
                        <div class="review-images">
                            <?php
                            $images = array_filter(array_map('trim', explode(',', $review['review_images'])));
                            foreach ($images as $index => $img):
                            ?>
                                <img src="<?php echo htmlspecialchars($img); ?>" 
                                     alt="Review image" 
                                     class="review-image"
                                     onclick="openImageViewer(<?php echo htmlspecialchars(json_encode(array_values($images)), ENT_QUOTES, 'UTF-8'); ?>, <?php echo $index; ?>)">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$review['user_id']): ?>
                        <div class="review-actions">
                            <button onclick="editReview(<?php echo (int)$review['id']; ?>)" class="edit-btn">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="deleteReview(<?php echo (int)$review['id']; ?>)" class="delete-btn">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?room_id=<?php echo $room_id; ?>&page=<?php echo $page - 1; ?>" class="page-link">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?room_id=<?php echo $room_id; ?>&page=<?php echo $i; ?>" 
                           class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                        <a href="?room_id=<?php echo $room_id; ?>&page=<?php echo $page + 1; ?>" class="page-link">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="font-semibold text-lg mb-2">No Reviews Yet</h3>
                    <p class="text-gray-500 mb-4">Be the first to share your experience</p>
                    <a href="write-review.php?room_id=<?php echo $room_id; ?>" class="write-review-btn">
                        <i class="fas fa-pen mr-2"></i> Write a Review
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <div class="sticky top-0 bg-white z-20 px-4 py-3 border-b border-gray-100 flex items-center gap-3">
        <button onclick="goBack()" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </button>
        <div>
            <h1 class="font-bold text-gray-900">Reviews</h1>
            <p class="text-xs text-gray-500"><?php echo $total; ?> guest reviews</p>
        </div>
    </div>
    
    <div class="p-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <img src="<?php echo htmlspecialchars($room['image_url'] ?: 'https://via.placeholder.com/60x60?text=PG'); ?>" 
                 class="w-14 h-14 rounded-xl object-cover" 
                 alt="<?php echo htmlspecialchars($room['title']); ?>">
            <div>
                <h2 class="font-semibold text-gray-900"><?php echo htmlspecialchars($room['title']); ?></h2>
                <div class="flex items-center gap-2 mt-1">
                    <div class="flex items-center gap-1 bg-yellow-100 px-2 py-0.5 rounded-full">
                        <i class="fas fa-star text-yellow-500 text-xs"></i>
                        <span class="text-xs font-semibold"><?php echo number_format($avg_rating, 1); ?></span>
                    </div>
                    <span class="text-xs text-gray-500"><?php echo $total; ?> reviews</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="p-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-900 mb-3">Rating Distribution</h3>
        <?php for ($i = 5; $i >= 1; $i--):
            $key = $i == 5 ? 'five_star' : ($i == 4 ? 'four_star' : ($i == 3 ? 'three_star' : ($i == 2 ? 'two_star' : 'one_star')));
            $count = (int)($distribution[$key] ?? 0);
            $percentage = $total > 0 ? ($count / $total) * 100 : 0;
        ?>
        <div class="flex items-center gap-2 mb-2">
            <span class="text-xs text-gray-600 w-14"><?php echo $rating_labels[$i]; ?></span>
            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-yellow-500 rounded-full" style="width: <?php echo $percentage; ?>%"></div>
            </div>
            <span class="text-xs text-gray-500 w-8 text-right"><?php echo $count; ?></span>
        </div>
        <?php endfor; ?>
    </div>
    
    <div class="p-4">
        <?php if ($reviews && $reviews->num_rows > 0): ?>
            <?php while ($review = $reviews->fetch_assoc()): ?>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-3">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-2">
                        <?php if (!empty($review['profile_image'])): ?>
                            <img src="<?php echo htmlspecialchars($review['profile_image']); ?>" class="w-10 h-10 rounded-full object-cover">
                        <?php else: ?>
                            <div class="w-10 h-10 rounded-full bg-[#003B95] flex items-center justify-center text-white font-bold">
                                <?php echo strtoupper(substr($review['user_name'] ?: 'U', 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="font-semibold text-sm"><?php echo htmlspecialchars($review['user_name']); ?></div>
                            <div class="text-xs text-gray-500"><?php echo date('d M Y', strtotime($review['created_at'])); ?></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 bg-yellow-100 px-2 py-1 rounded-full">
                        <i class="fas fa-star text-yellow-500 text-xs"></i>
                        <span class="text-xs font-semibold"><?php echo (int)$review['rating']; ?></span>
                    </div>
                </div>
                
                <?php if (!empty($review['review_tags'])): ?>
                    <div class="flex flex-wrap gap-1 mb-2">
                        <?php
                        $tags = array_filter(array_map('trim', explode(',', $review['review_tags'])));
                        foreach (array_slice($tags, 0, 3) as $tag):
                        ?>
                            <span class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full"><?php echo htmlspecialchars($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($review['comment'] ?? ''); ?></p>
                
                <?php if (!empty($review['review_images'])): ?>
                    <div class="flex gap-2 mt-3">
                        <?php
                        $images = array_filter(array_map('trim', explode(',', $review['review_images'])));
                        foreach (array_slice($images, 0, 3) as $index => $img):
                        ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" 
                                 class="w-14 h-14 rounded-lg object-cover cursor-pointer border"
                                 onclick="openImageViewer(<?php echo htmlspecialchars(json_encode(array_values($images)), ENT_QUOTES, 'UTF-8'); ?>, <?php echo $index; ?>)">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$review['user_id']): ?>
                    <div class="flex gap-4 mt-3 pt-2 border-t border-gray-200">
                        <button onclick="editReview(<?php echo (int)$review['id']; ?>)" class="text-xs text-blue-600">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button onclick="deleteReview(<?php echo (int)$review['id']; ?>)" class="text-xs text-red-600">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
            
            <?php if ($total_pages > 1): ?>
            <div class="flex justify-center gap-2 mt-4">
                <?php if ($page > 1): ?>
                    <a href="?room_id=<?php echo $room_id; ?>&page=<?php echo $page - 1; ?>" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-chevron-left text-sm"></i>
                    </a>
                <?php endif; ?>
                <span class="px-4 py-2 text-sm">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
                <?php if ($page < $total_pages): ?>
                    <a href="?room_id=<?php echo $room_id; ?>&page=<?php echo $page + 1; ?>" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-10">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-star text-2xl text-gray-300"></i>
                </div>
                <p class="text-gray-500 text-sm">No reviews yet</p>
                <a href="write-review.php?room_id=<?php echo $room_id; ?>" class="inline-block mt-3 text-[#003B95] text-sm font-medium">
                    <i class="fas fa-pen mr-1"></i> Write first review
                </a>
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
            <i class="fas fa-heart" style="font-size: 22px; color: #9CA3AF;"></i>
            <div style="font-size: 11px; margin-top: 4px;">Saved</div>
        </div>
        <div onclick="goToPage('profile')" style="text-align: center; cursor: pointer;">
            <i class="fas fa-user" style="font-size: 22px; color: #9CA3AF;"></i>
            <div style="font-size: 11px; margin-top: 4px;">Profile</div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-trash-alt"></i>
            </div>
            <h3 class="modal-title">Delete Review?</h3>
        </div>
        <p class="modal-text">This action cannot be undone. Your review will be permanently removed.</p>
        <div class="modal-actions">
            <button onclick="closeDeleteModal()" class="modal-btn cancel">Cancel</button>
            <button onclick="confirmDelete()" class="modal-btn confirm">Delete</button>
        </div>
    </div>
</div>

<!-- Image Viewer -->
<div id="imageViewer" class="image-viewer">
    <div class="image-viewer-header">
        <div class="image-viewer-counter" id="viewerCounter">1 / 1</div>
        <button onclick="closeImageViewer()" class="image-viewer-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="image-viewer-content">
        <button onclick="prevImage()" class="image-viewer-nav prev">
            <i class="fas fa-chevron-left"></i>
        </button>
        <img id="viewerImage" class="image-viewer-image" src="" alt="Review image">
        <button onclick="nextImage()" class="image-viewer-nav next">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    <div id="viewerDots" class="image-viewer-dots"></div>
</div>

<script>
let reviewToDelete = null;
let viewerImages = [];
let viewerIndex = 0;

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

function editReview(reviewId) {
    window.location.href = 'write-review?room_id=<?php echo $room_id; ?>&edit=' + reviewId;
}

function deleteReview(reviewId) {
    reviewToDelete = reviewId;
    document.getElementById('deleteModal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    reviewToDelete = null;
    document.getElementById('deleteModal').classList.remove('show');
    document.body.style.overflow = 'auto';
}

async function confirmDelete() {
    if (!reviewToDelete) return;
    
    const confirmBtn = document.querySelector('#deleteModal .confirm');
    confirmBtn.disabled = true;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
    
    try {
        const response = await fetch('/api/delete-review', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'review_id=' + reviewToDelete
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast('Review deleted successfully', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Failed to delete review', 'error');
        }
    } catch (error) {
        showToast('Something went wrong. Please try again.', 'error');
    } finally {
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = 'Delete';
        closeDeleteModal();
    }
}

function openImageViewer(images, index) {
    viewerImages = images;
    viewerIndex = index;
    updateImageViewer();
    document.getElementById('imageViewer').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeImageViewer() {
    document.getElementById('imageViewer').classList.remove('show');
    document.body.style.overflow = 'auto';
}

function updateImageViewer() {
    const img = document.getElementById('viewerImage');
    const counter = document.getElementById('viewerCounter');
    const dotsContainer = document.getElementById('viewerDots');
    
    img.src = viewerImages[viewerIndex];
    counter.textContent = `${viewerIndex + 1} / ${viewerImages.length}`;
    
    dotsContainer.innerHTML = '';
    viewerImages.forEach((_, i) => {
        const dot = document.createElement('div');
        dot.className = 'image-viewer-dot' + (i === viewerIndex ? ' active' : '');
        dot.onclick = () => {
            viewerIndex = i;
            updateImageViewer();
        };
        dotsContainer.appendChild(dot);
    });
}

function prevImage() {
    viewerIndex = viewerIndex > 0 ? viewerIndex - 1 : viewerImages.length - 1;
    updateImageViewer();
}

function nextImage() {
    viewerIndex = viewerIndex < viewerImages.length - 1 ? viewerIndex + 1 : 0;
    updateImageViewer();
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2500);
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});

// Keyboard navigation for image viewer
document.addEventListener('keydown', function(e) {
    const viewer = document.getElementById('imageViewer');
    if (viewer.classList.contains('show')) {
        if (e.key === 'Escape') closeImageViewer();
        if (e.key === 'ArrowLeft') prevImage();
        if (e.key === 'ArrowRight') nextImage();
    }
});
</script>

</body>
</html>