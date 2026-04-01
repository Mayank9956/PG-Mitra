<?php
session_start();
require_once 'common/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$user_id = (int)$_SESSION['user_id'];
$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

if ($room_id <= 0) {
    header("Location: home.php");
    exit;
}

/* Fetch room */
$query = "SELECT r.id, r.title, ri.image_url, r.location, r.city FROM rooms r LEFT JOIN room_images ri ON r.id=ri.room_id AND ri.is_primary =1 WHERE r.id = ? LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

if (!$room) {
    header("Location: home.php");
    exit;
}




/* Existing review */
$check_query = "SELECT id, rating, comment, review_tags, review_images 
                FROM reviews 
                WHERE room_id = ? AND user_id = ? 
                LIMIT 1";
$stmt = $db->prepare($check_query);
$stmt->bind_param("ii", $room_id, $user_id);
$stmt->execute();
$existing_review = $stmt->get_result()->fetch_assoc();

$has_reviewed = !empty($existing_review);

$user_review = [
    'rating' => $existing_review['rating'] ?? '',
    'comment' => $existing_review['comment'] ?? '',
    'review_tags' => $existing_review['review_tags'] ?? '',
    'review_images' => $existing_review['review_images'] ?? ''
];

$selected_tags = [];
if (!empty($user_review['review_tags'])) {
    $selected_tags = array_map('trim', explode(',', $user_review['review_tags']));
}

// Get user info for display
$user_query = "SELECT full_name, profile_image FROM users WHERE id = ?";
$stmt = $db->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_info = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $has_reviewed ? 'Edit Review' : 'Write a Review'; ?> - PG Mitra</title>
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
    padding: 20px 16px 40px;
    background: white;
    min-height: 100vh;
}

/* Review Card */
.review-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    overflow: hidden;
    border: 1px solid #E5E7EB;
}

.review-header {
    padding: 32px;
    border-bottom: 1px solid #E5E7EB;
    background: linear-gradient(135deg, #F8FAFF 0%, #FFFFFF 100%);
}

.review-title {
    font-size: 28px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 8px;
}

.review-subtitle {
    font-size: 14px;
    color: #6B7280;
}

/* Room Info Section */
.room-info {
    padding: 24px 32px;
    background: #F9FAFB;
    border-bottom: 1px solid #E5E7EB;
    display: flex;
    gap: 20px;
    align-items: center;
}

.room-image {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.room-details h2 {
    font-size: 18px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 4px;
}

.room-location {
    font-size: 13px;
    color: #6B7280;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Form Section */
.form-section {
    padding: 32px;
}

/* Star Rating */
.star-rating-container {
    margin-bottom: 32px;
}

.star-label {
    font-size: 16px;
    font-weight: 600;
    color: #1E2A3A;
    margin-bottom: 12px;
    display: block;
}

.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-start;
    gap: 8px;
}

.star-rating input {
    display: none;
}

.star-rating label {
    font-size: 32px;
    color: #E5E7EB;
    cursor: pointer;
    transition: all 0.2s;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label {
    color: #FBBF24;
    transform: scale(1.05);
}

/* Comment Input */
.comment-input {
    margin-bottom: 32px;
}

.comment-input label {
    font-size: 16px;
    font-weight: 600;
    color: #1E2A3A;
    margin-bottom: 12px;
    display: block;
}

.comment-input textarea {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    resize: vertical;
    transition: all 0.2s;
}

.comment-input textarea:focus {
    outline: none;
    border-color: #003B95;
    box-shadow: 0 0 0 3px rgba(0,59,149,0.1);
}

/* Tags Section */
.tags-section {
    margin-bottom: 32px;
}

.tags-section label {
    font-size: 16px;
    font-weight: 600;
    color: #1E2A3A;
    margin-bottom: 12px;
    display: block;
}

.tags-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.tag-chip {
    padding: 8px 18px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 500;
    background: #F3F4F6;
    color: #4B5563;
    border: 1px solid #E5E7EB;
    cursor: pointer;
    transition: all 0.2s;
}

.tag-chip:hover {
    background: #E5E7EB;
    transform: translateY(-1px);
}

.tag-chip.active {
    background: #003B95;
    color: white;
    border-color: #003B95;
}

/* Photo Upload */
.photo-section {
    margin-bottom: 32px;
}

.photo-section label {
    font-size: 16px;
    font-weight: 600;
    color: #1E2A3A;
    margin-bottom: 12px;
    display: block;
}

.upload-area {
    border: 2px dashed #E5E7EB;
    border-radius: 16px;
    padding: 40px 20px;
    text-align: center;
    background: #F9FAFB;
    cursor: pointer;
    transition: all 0.2s;
}

.upload-area:hover {
    border-color: #003B95;
    background: #F3F4F6;
}

.upload-area i {
    font-size: 40px;
    color: #9CA3AF;
    margin-bottom: 12px;
}

.upload-area p {
    font-size: 14px;
    color: #6B7280;
    margin-bottom: 4px;
}

.upload-area small {
    font-size: 12px;
    color: #9CA3AF;
}

.upload-btn {
    margin-top: 16px;
    background: #003B95;
    color: white;
    border: none;
    padding: 10px 24px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.upload-btn:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

/* Photo Preview */
.photo-preview-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 16px;
}

.preview-item {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #E5E7EB;
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.remove-preview {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #EF4444;
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    transition: all 0.2s;
}

.remove-preview:hover {
    background: #DC2626;
    transform: scale(1.05);
}

.existing-photos {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #E5E7EB;
}

.existing-photos p {
    font-size: 13px;
    color: #6B7280;
    margin-bottom: 10px;
}

.existing-photos-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.existing-photo {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    border: 1px solid #E5E7EB;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    background: #003B95;
    color: white;
    border: none;
    padding: 16px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 24px;
}

.submit-btn:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
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
    z-index: 2000;
    animation: slideUp 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
    
    .review-header {
        padding: 20px;
    }
    
    .review-title {
        font-size: 22px;
    }
    
    .room-info {
        padding: 16px 20px;
    }
    
    .form-section {
        padding: 20px;
    }
    
    .star-rating label {
        font-size: 28px;
    }
    
    .tags-grid {
        gap: 8px;
    }
    
    .tag-chip {
        padding: 6px 14px;
        font-size: 12px;
    }
    
    .upload-area {
        padding: 30px 16px;
    }
    
    .upload-area i {
        font-size: 32px;
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
                <?php if (!empty($user_info['profile_image'])): ?>
                    <img src="<?php echo htmlspecialchars($user_info['profile_image']); ?>" alt="Profile">
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
        <?php if (!empty($user_info['profile_image'])): ?>
            <img src="<?php echo htmlspecialchars($user_info['profile_image']); ?>" alt="Profile">
        <?php else: ?>
            <i class="fas fa-user"></i>
        <?php endif; ?>
    </div>
</div>

<!-- Desktop Layout -->
<div class="main-container">
    <div class="review-card">
        <div class="review-header">
            <h1 class="review-title">
                <?php echo $has_reviewed ? 'Edit Your Review' : 'Write a Review'; ?>
            </h1>
            <p class="review-subtitle">
                Share your experience and help others make the right choice
            </p>
        </div>
        
        <div class="room-info">
            <img src="<?php echo htmlspecialchars($room['image_url'] ?: 'https://via.placeholder.com/80x80?text=PG'); ?>" 
                 alt="<?php echo htmlspecialchars($room['title']); ?>" 
                 class="room-image">
            <div class="room-details">
                <h2><?php echo htmlspecialchars($room['title']); ?></h2>
                <div class="room-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php //echo htmlspecialchars($room['location']); ?> <?php echo htmlspecialchars($room['city']); ?></span>
                </div>
            </div>
        </div>
        
        <form id="reviewForm" enctype="multipart/form-data" class="form-section">
            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
            <input type="hidden" name="review_tags" id="review_tags" value="<?php echo htmlspecialchars($user_review['review_tags']); ?>">
            
            <!-- Rating -->
            <div class="star-rating-container">
                <label class="star-label">Your Rating *</label>
                <div class="star-rating">
                    <input type="radio" name="rating" value="5" id="star5" <?php echo ((int)$user_review['rating'] === 5) ? 'checked' : ''; ?>>
                    <label for="star5" title="Excellent">★</label>
                    
                    <input type="radio" name="rating" value="4" id="star4" <?php echo ((int)$user_review['rating'] === 4) ? 'checked' : ''; ?>>
                    <label for="star4" title="Very Good">★</label>
                    
                    <input type="radio" name="rating" value="3" id="star3" <?php echo ((int)$user_review['rating'] === 3) ? 'checked' : ''; ?>>
                    <label for="star3" title="Average">★</label>
                    
                    <input type="radio" name="rating" value="2" id="star2" <?php echo ((int)$user_review['rating'] === 2) ? 'checked' : ''; ?>>
                    <label for="star2" title="Poor">★</label>
                    
                    <input type="radio" name="rating" value="1" id="star1" <?php echo ((int)$user_review['rating'] === 1) ? 'checked' : ''; ?>>
                    <label for="star1" title="Terrible">★</label>
                </div>
            </div>
            
            <!-- Comment -->
            <div class="comment-input">
                <label>Your Review *</label>
                <textarea name="comment" id="comment" rows="5" placeholder="Tell us about your experience at this PG. What did you like? What could be improved?" required><?php echo htmlspecialchars($user_review['comment']); ?></textarea>
            </div>
            
            <!-- Tags -->
            <div class="tags-section">
                <label>What stood out? (Optional)</label>
                <div class="tags-grid" id="tagsWrap">
                    <?php
                    $tags = ['Clean', 'Safe', 'Friendly Staff', 'Good Food', 'Value for Money', 'Peaceful', 'Near College', 'Spacious', 'Good WiFi', '24/7 Water'];
                    foreach ($tags as $tag):
                        $is_active = in_array($tag, $selected_tags);
                    ?>
                        <button type="button" class="tag-chip <?php echo $is_active ? 'active' : ''; ?>" data-tag="<?php echo htmlspecialchars($tag); ?>">
                            <?php echo htmlspecialchars($tag); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Photos -->
            <div class="photo-section">
                <label>Add Photos (Optional)</label>
                <div class="upload-area" onclick="document.getElementById('photoUpload').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p>Click or drag to upload photos</p>
                    <small>PNG, JPG, JPEG up to 5MB each</small>
                    <input type="file" name="review_photos[]" id="photoUpload" multiple accept="image/*" class="hidden">
                    <button type="button" class="upload-btn" onclick="event.stopPropagation(); document.getElementById('photoUpload').click()">
                        Choose Photos
                    </button>
                </div>
                <div id="photoPreview" class="photo-preview-grid"></div>
                
                <?php if (!empty($user_review['review_images'])): ?>
                    <div class="existing-photos">
                        <p><i class="fas fa-images"></i> Your existing photos</p>
                        <div class="existing-photos-grid">
                            <?php
                            $old_images = array_filter(array_map('trim', explode(',', $user_review['review_images'])));
                            foreach ($old_images as $img):
                            ?>
                                <img src="<?php echo htmlspecialchars($img); ?>" class="existing-photo" alt="Review image">
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" id="submitBtn" class="submit-btn">
                <?php echo $has_reviewed ? 'Update Review' : 'Submit Review'; ?>
            </button>
        </form>
    </div>
</div>

<!-- Mobile Layout -->
<div class="mobile-container">
    <div class="flex items-center gap-3 mb-5">
        <button onclick="goBack()" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </button>
        <div>
            <h1 class="text-xl font-bold text-gray-900">
                <?php echo $has_reviewed ? 'Edit Review' : 'Write a Review'; ?>
            </h1>
            <p class="text-xs text-gray-500">Share your experience</p>
        </div>
    </div>
    
    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl mb-5">
        <img src="<?php echo htmlspecialchars($room['image_url'] ?: 'https://via.placeholder.com/60x60?text=PG'); ?>" 
             class="w-14 h-14 rounded-lg object-cover" 
             alt="<?php echo htmlspecialchars($room['title']); ?>">
        <div>
            <h2 class="font-semibold text-gray-900"><?php echo htmlspecialchars($room['title']); ?></h2>
            <div class="flex items-center gap-1 text-xs text-gray-500">
                <i class="fas fa-map-marker-alt text-[#003B95]"></i>
                <span><?php echo htmlspecialchars($room['city']); ?></span>
            </div>
        </div>
    </div>
    
    <form id="mobileReviewForm" enctype="multipart/form-data">
        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
        <input type="hidden" name="review_tags" id="mobile_review_tags" value="<?php echo htmlspecialchars($user_review['review_tags']); ?>">
        
        <!-- Rating -->
        <div class="mb-5">
            <label class="font-semibold text-gray-900 mb-2 block">Your Rating *</label>
            <div class="star-rating">
                <input type="radio" name="rating" value="5" id="m_star5" <?php echo ((int)$user_review['rating'] === 5) ? 'checked' : ''; ?>>
                <label for="m_star5">★</label>
                <input type="radio" name="rating" value="4" id="m_star4" <?php echo ((int)$user_review['rating'] === 4) ? 'checked' : ''; ?>>
                <label for="m_star4">★</label>
                <input type="radio" name="rating" value="3" id="m_star3" <?php echo ((int)$user_review['rating'] === 3) ? 'checked' : ''; ?>>
                <label for="m_star3">★</label>
                <input type="radio" name="rating" value="2" id="m_star2" <?php echo ((int)$user_review['rating'] === 2) ? 'checked' : ''; ?>>
                <label for="m_star2">★</label>
                <input type="radio" name="rating" value="1" id="m_star1" <?php echo ((int)$user_review['rating'] === 1) ? 'checked' : ''; ?>>
                <label for="m_star1">★</label>
            </div>
        </div>
        
        <!-- Comment -->
        <div class="mb-5">
            <label class="font-semibold text-gray-900 mb-2 block">Your Review *</label>
            <textarea name="comment" id="mobile_comment" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#003B95] focus:border-transparent resize-none" placeholder="Share your experience..." required><?php echo htmlspecialchars($user_review['comment']); ?></textarea>
        </div>
        
        <!-- Tags -->
        <div class="mb-5">
            <label class="font-semibold text-gray-900 mb-2 block">What stood out? (Optional)</label>
            <div class="flex flex-wrap gap-2" id="mobile_tagsWrap">
                <?php
                $mobile_tags = ['Clean', 'Safe', 'Friendly Staff', 'Good Food', 'Value for Money', 'Peaceful', 'Near College'];
                foreach ($mobile_tags as $tag):
                    $is_active = in_array($tag, $selected_tags);
                ?>
                    <button type="button" class="tag-chip <?php echo $is_active ? 'active' : ''; ?>" data-tag="<?php echo htmlspecialchars($tag); ?>">
                        <?php echo htmlspecialchars($tag); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Photos -->
        <div class="mb-5">
            <label class="font-semibold text-gray-900 mb-2 block">Add Photos (Optional)</label>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center bg-gray-50" onclick="document.getElementById('mobile_photoUpload').click()">
                <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 mb-2"></i>
                <p class="text-xs text-gray-500">Click to upload photos</p>
                <input type="file" name="review_photos[]" id="mobile_photoUpload" multiple accept="image/*" class="hidden">
                <button type="button" class="mt-2 bg-[#003B95] text-white px-4 py-1.5 rounded-full text-xs" onclick="event.stopPropagation(); document.getElementById('mobile_photoUpload').click()">
                    Choose Photos
                </button>
            </div>
            <div id="mobile_photoPreview" class="flex flex-wrap gap-2 mt-3"></div>
            
            <?php if (!empty($user_review['review_images'])): ?>
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-xs text-gray-500 mb-2">Your existing photos</p>
                    <div class="flex flex-wrap gap-2">
                        <?php
                        $old_images = array_filter(array_map('trim', explode(',', $user_review['review_images'])));
                        foreach ($old_images as $img):
                        ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" class="w-14 h-14 rounded-lg object-cover border">
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <button type="submit" id="mobile_submitBtn" class="w-full bg-[#003B95] text-white py-4 rounded-xl font-semibold text-base">
            <?php echo $has_reviewed ? 'Update Review' : 'Submit Review'; ?>
        </button>
    </form>
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

<script>
let selectedTags = new Set();
let selectedFiles = [];

// Initialize tags
document.querySelectorAll('.tag-chip.active').forEach(btn => {
    selectedTags.add(btn.dataset.tag);
});
syncTags();

function syncTags() {
    document.getElementById('review_tags').value = Array.from(selectedTags).join(',');
    if (document.getElementById('mobile_review_tags')) {
        document.getElementById('mobile_review_tags').value = Array.from(selectedTags).join(',');
    }
}

// Tag click handlers
document.querySelectorAll('.tag-chip').forEach(btn => {
    btn.addEventListener('click', function() {
        const tag = this.dataset.tag;
        if (selectedTags.has(tag)) {
            selectedTags.delete(tag);
            this.classList.remove('active');
        } else {
            selectedTags.add(tag);
            this.classList.add('active');
        }
        syncTags();
    });
});

// Desktop photo upload
const photoUpload = document.getElementById('photoUpload');
const photoPreview = document.getElementById('photoPreview');

if (photoUpload) {
    photoUpload.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;
            if (file.size > 5 * 1024 * 1024) {
                showToast(file.name + ' is too large. Max 5MB.', 'error');
                return;
            }
            selectedFiles.push(file);
        });
        renderPhotoPreview();
        rebuildFileInput(photoUpload, selectedFiles);
    });
}

function renderPhotoPreview() {
    if (!photoPreview) return;
    photoPreview.innerHTML = '';
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrapper = document.createElement('div');
            wrapper.className = 'preview-item';
            wrapper.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-preview" onclick="removePreview(${index})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            photoPreview.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
}

function removePreview(index) {
    selectedFiles.splice(index, 1);
    renderPhotoPreview();
    if (photoUpload) rebuildFileInput(photoUpload, selectedFiles);
    if (mobilePhotoUpload) rebuildFileInput(mobilePhotoUpload, selectedFiles);
}

function rebuildFileInput(input, files) {
    const dt = new DataTransfer();
    files.forEach(file => dt.items.add(file));
    input.files = dt.files;
}

// Mobile photo upload
const mobilePhotoUpload = document.getElementById('mobile_photoUpload');
const mobilePhotoPreview = document.getElementById('mobile_photoPreview');
let mobileSelectedFiles = [];

if (mobilePhotoUpload) {
    mobilePhotoUpload.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;
            if (file.size > 5 * 1024 * 1024) {
                showToast(file.name + ' is too large. Max 5MB.', 'error');
                return;
            }
            mobileSelectedFiles.push(file);
        });
        renderMobilePhotoPreview();
        rebuildFileInput(mobilePhotoUpload, mobileSelectedFiles);
        if (photoUpload) rebuildFileInput(photoUpload, mobileSelectedFiles);
        selectedFiles = [...mobileSelectedFiles];
    });
}

function renderMobilePhotoPreview() {
    if (!mobilePhotoPreview) return;
    mobilePhotoPreview.innerHTML = '';
    mobileSelectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const wrapper = document.createElement('div');
            wrapper.className = 'preview-item';
            wrapper.style.width = '70px';
            wrapper.style.height = '70px';
            wrapper.innerHTML = `
                <img src="${e.target.result}" class="w-full h-full object-cover rounded-lg">
                <button type="button" class="remove-preview" style="width: 20px; height: 20px; font-size: 10px;" onclick="removeMobilePreview(${index})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            mobilePhotoPreview.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
}

function removeMobilePreview(index) {
    mobileSelectedFiles.splice(index, 1);
    renderMobilePhotoPreview();
    if (mobilePhotoUpload) rebuildFileInput(mobilePhotoUpload, mobileSelectedFiles);
    if (photoUpload) rebuildFileInput(photoUpload, mobileSelectedFiles);
    selectedFiles = [...mobileSelectedFiles];
}

function goBack() {
    window.history.back();
}

function goToPage(page) {
    if (page === 'home') window.location.href = '/';
    else if (page === 'search') window.location.href = '/search';
    else if (page === 'bookings') window.location.href = '/bookings';
    else if (page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if (page === 'profile') window.location.href = '/profile';
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2500);
}

// Form submission handlers
const reviewForm = document.getElementById('reviewForm');
const mobileReviewForm = document.getElementById('mobileReviewForm');

async function handleSubmit(form, isMobile = false) {
    const rating = form.querySelector('input[name="rating"]:checked');
    const comment = form.querySelector('[name="comment"]').value.trim();
    
    if (!rating) {
        showToast('Please select a rating', 'error');
        return false;
    }
    
    if (!comment) {
        showToast('Please write your review', 'error');
        return false;
    }
    
    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    
    const formData = new FormData(form);
    
    try {
        const response = await fetch('/api/save-review', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showToast(data.message || 'Review saved successfully!', 'success');
            setTimeout(() => {
                window.location.href = 'room-details?id=<?php echo $room_id; ?>';
            }, 1200);
        } else {
            showToast(data.message || 'Failed to save review', 'error');
        }
    } catch (error) {
        showToast('Something went wrong. Please try again.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
    
    return false;
}

if (reviewForm) {
    reviewForm.addEventListener('submit', (e) => {
        e.preventDefault();
        handleSubmit(reviewForm, false);
    });
}

if (mobileReviewForm) {
    mobileReviewForm.addEventListener('submit', (e) => {
        e.preventDefault();
        handleSubmit(mobileReviewForm, true);
    });
}
</script>

</body>
</html>