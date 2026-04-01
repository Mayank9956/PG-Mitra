<?php
session_start();
require_once 'common/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Support Center - PG Mitra</title>
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

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #003B95 0%, #0066CC 100%);
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 32px;
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
    font-size: 28px;
    font-weight: 800;
    color: white;
    margin-bottom: 8px;
}

.hero-text p {
    font-size: 14px;
    color: rgba(255,255,255,0.9);
}

.hero-icon {
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-icon i {
    font-size: 32px;
    color: #FFB700;
}

/* Contact Grid */
.contact-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 32px;
}

.contact-card {
    background: white;
    border-radius: 16px;
    padding: 24px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.contact-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.contact-icon {
    width: 60px;
    height: 60px;
    background: #EFF6FF;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.contact-icon i {
    font-size: 28px;
    color: #003B95;
}

.contact-label {
    font-size: 14px;
    font-weight: 700;
    color: #1E2A3A;
    margin-bottom: 4px;
}

.contact-value {
    font-size: 12px;
    color: #6B7280;
}

/* Tabs */
.tabs-container {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    margin-bottom: 32px;
}

.tab-nav {
    display: flex;
    gap: 0;
    border-bottom: 1px solid #E5E7EB;
    background: #F9FAFB;
}

.tab-btn {
    flex: 1;
    padding: 16px;
    font-weight: 600;
    font-size: 14px;
    color: #6B7280;
    cursor: pointer;
    text-align: center;
    transition: all 0.2s;
    border-bottom: 3px solid transparent;
}

.tab-btn.active {
    color: #003B95;
    border-bottom-color: #003B95;
    background: white;
}

.tab-content {
    display: none;
    padding: 24px;
}

.tab-content.active {
    display: block;
}

/* FAQ Section */
.faq-item {
    border-bottom: 1px solid #F0F2F5;
}

.faq-question {
    padding: 16px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 500;
    color: #1E2A3A;
    transition: all 0.2s;
}

.faq-question:hover {
    background: #F9FAFB;
}

.faq-question i {
    transition: transform 0.2s;
    color: #9CA3AF;
}

.faq-question.active i {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 0 16px 16px 16px;
    color: #6B7280;
    font-size: 14px;
    line-height: 1.6;
    display: none;
}

.faq-answer.show {
    display: block;
}

/* Help Card */
.help-card {
    background: linear-gradient(135deg, #EFF6FF 0%, #FFFFFF 100%);
    border: 1px solid #E5E7EB;
    border-radius: 16px;
    padding: 20px;
    margin-top: 24px;
}

.help-card h4 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 8px;
}

.help-card p {
    font-size: 13px;
    color: #6B7280;
    margin-bottom: 16px;
}

.help-buttons {
    display: flex;
    gap: 12px;
}

.help-btn {
    flex: 1;
    padding: 10px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
}

.help-btn.primary {
    background: #003B95;
    color: white;
    border: none;
}

.help-btn.primary:hover {
    background: #002E7A;
}

.help-btn.secondary {
    background: white;
    color: #003B95;
    border: 1px solid #E5E7EB;
}

.help-btn.secondary:hover {
    background: #F9FAFB;
}

/* Ticket Card */
.ticket-card {
    background: #F9FAFB;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: all 0.2s;
    border-left: 4px solid;
}

.ticket-card:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.ticket-card.open { border-left-color: #10B981; }
.ticket-card.in-progress { border-left-color: #F59E0B; }
.ticket-card.resolved { border-left-color: #6B7280; }

.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.ticket-number {
    font-weight: 700;
    font-size: 13px;
    color: #1E2A3A;
}

.ticket-status {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
}

.status-open { background: #DCFCE7; color: #166534; }
.status-progress { background: #FEF3C7; color: #92400E; }
.status-resolved { background: #F3F4F6; color: #4B5563; }

.ticket-subject {
    font-weight: 600;
    margin-bottom: 6px;
    color: #1E2A3A;
}

.ticket-meta {
    display: flex;
    gap: 16px;
    font-size: 11px;
    color: #9CA3AF;
}

.ticket-meta i {
    margin-right: 4px;
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #1E2A3A;
    margin-bottom: 6px;
}

.form-label i {
    color: #003B95;
    margin-right: 6px;
}

.form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #E5E7EB;
    border-radius: 10px;
    font-size: 14px;
    font-family: 'Inter', sans-serif;
    transition: all 0.2s;
    background: white;
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: #003B95;
    box-shadow: 0 0 0 3px rgba(0,59,149,0.1);
}

/* Upload Box */
.upload-box {
    border: 2px dashed #E5E7EB;
    border-radius: 12px;
    padding: 24px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    background: #F9FAFB;
}

.upload-box:hover {
    border-color: #003B95;
    background: #EFF6FF;
}

.upload-box i {
    font-size: 32px;
    color: #9CA3AF;
    margin-bottom: 8px;
}

.file-preview-list {
    margin-top: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.file-preview-item {
    background: #F3F4F6;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 12px;
    color: #374151;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Submit Button */
.submit-btn {
    width: 100%;
    background: #003B95;
    color: white;
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    margin-top: 8px;
}

.submit-btn:hover {
    background: #002E7A;
    transform: translateY(-1px);
}

/* Alert */
.alert {
    padding: 16px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.alert-success {
    background: #DCFCE7;
    border: 1px solid #10B981;
    color: #166534;
}

.alert-error {
    background: #FEE2E2;
    border: 1px solid #EF4444;
    color: #991B1B;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    background: #F9FAFB;
    border-radius: 12px;
}

.empty-icon {
    width: 60px;
    height: 60px;
    background: #F3F4F6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.empty-icon i {
    font-size: 24px;
    color: #9CA3AF;
}

.empty-state h4 {
    font-weight: 600;
    margin-bottom: 4px;
}

.empty-state p {
    font-size: 13px;
    color: #6B7280;
    margin-bottom: 16px;
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
    
    .hero-section {
        margin: 16px;
        padding: 20px;
    }
    
    .hero-content {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }
    
    .hero-text h1 {
        font-size: 22px;
    }
    
    .contact-grid {
        gap: 12px;
        padding: 0 16px;
    }
    
    .contact-card {
        padding: 16px;
    }
    
    .contact-icon {
        width: 50px;
        height: 50px;
    }
    
    .contact-icon i {
        font-size: 24px;
    }
    
    .contact-label {
        font-size: 12px;
    }
    
    .tabs-container {
        margin: 0 16px 20px 16px;
    }
    
    .tab-btn {
        padding: 12px;
        font-size: 13px;
    }
    
    .tab-content {
        padding: 20px;
    }
    
    .help-buttons {
        flex-direction: column;
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
                <?php 
                $profile_image = isset($_SESSION['profile_image']) ? $_SESSION['profile_image'] : null;
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
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Support Center</h1>
                <p>We're here to help you 24/7</p>
            </div>
            <div class="hero-icon">
                <i class="fas fa-headset"></i>
            </div>
        </div>
    </div>

    <!-- Contact Cards -->
    <div class="contact-grid">
        <div class="contact-card" onclick="callSupport()">
            <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
            <div class="contact-label">Call Us</div>
            <div class="contact-value">24/7 Helpline</div>
        </div>
        <div class="contact-card" onclick="emailSupport()">
            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
            <div class="contact-label">Email</div>
            <div class="contact-value">support@pgmitra.com</div>
        </div>
        <div class="contact-card" onclick="chatSupport()">
            <div class="contact-icon"><i class="fas fa-comment-dots"></i></div>
            <div class="contact-label">Live Chat</div>
            <div class="contact-value">Reply in 5 min</div>
        </div>
    </div>

    <!-- Message Box -->
    <div id="messageBox" class="hidden"></div>

    <!-- Tabs -->
    <div class="tabs-container">
        <div class="tab-nav">
            <div class="tab-btn active" onclick="switchTab(event, 'faq')">FAQ</div>
            <div class="tab-btn" onclick="switchTab(event, 'tickets')">My Tickets</div>
            <div class="tab-btn" onclick="switchTab(event, 'new')">New Ticket</div>
        </div>

        <!-- FAQ Tab -->
        <div id="tab-faq" class="tab-content active">
            <h3 class="font-bold text-gray-800 mb-4">Frequently Asked Questions</h3>
            
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How do I book a room?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    You can book a room by searching for your desired location, selecting a room, and clicking the "Book Now" button. Follow the payment process to confirm your booking.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>What is the cancellation policy?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Free cancellation up to 7 days before check-in. Cancellation within 7 days may incur a fee equal to one month's rent. Security deposit is refundable as per terms.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How do I get my security deposit back?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Security deposit is refunded within 7-10 working days after check-out, subject to room inspection and no damages. The amount is credited to your original payment method.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Can I extend my stay?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    Yes, you can extend your stay by contacting the host directly or raising a support ticket. Extension is subject to availability.
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>What documents are required for booking?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    You'll need a valid government ID (Aadhar, Passport, Voter ID), and in some cases, student ID or work ID proof. All documents are securely stored.
                </div>
            </div>

            <div class="help-card">
                <h4>Still have questions?</h4>
                <p>Our support team is available 24/7 to help you</p>
                <div class="help-buttons">
                    <button class="help-btn primary" onclick="callSupport()">
                        <i class="fas fa-phone mr-1"></i> Call Now
                    </button>
                    <button class="help-btn secondary" onclick="switchToNewTab()">
                        <i class="fas fa-ticket mr-1"></i> Create Ticket
                    </button>
                </div>
            </div>
        </div>

        <!-- Tickets Tab -->
        <div id="tab-tickets" class="tab-content">
            <h3 class="font-bold text-gray-800 mb-4">Recent Tickets</h3>
            <div id="ticketsContainer"></div>
        </div>

        <!-- New Ticket Tab -->
        <div id="tab-new" class="tab-content">
            <h3 class="font-bold text-gray-800 mb-4">Create Support Ticket</h3>
            
            <form id="ticketForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-heading"></i> Subject</label>
                    <input type="text" name="subject" class="form-input" placeholder="Brief summary of your issue" required>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-tag"></i> Category</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select category</option>
                        <option value="booking">Booking Issue</option>
                        <option value="payment">Payment Problem</option>
                        <option value="refund">Refund Request</option>
                        <option value="cancellation">Cancellation</option>
                        <option value="host">Host Related</option>
                        <option value="technical">Technical Issue</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-flag"></i> Priority</label>
                    <select name="priority" class="form-select" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-calendar"></i> Related Booking (Optional)</label>
                    <select name="booking_id" id="bookingSelect" class="form-select">
                        <option value="">Select booking</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-align-left"></i> Description</label>
                    <textarea name="description" rows="5" class="form-textarea" placeholder="Please describe your issue in detail..." required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label"><i class="fas fa-paperclip"></i> Attachments (Optional)</label>
                    <div class="upload-box" onclick="document.getElementById('ticketImages').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p class="text-sm text-gray-600 mt-2">Click to upload images</p>
                        <p class="text-xs text-gray-400">PNG, JPG, JPEG, WEBP • Max 5MB each</p>
                    </div>
                    <input type="file" id="ticketImages" name="attachments[]" accept="image/png,image/jpeg,image/jpg,image/webp" multiple class="hidden">
                    <div id="filePreviewList" class="file-preview-list"></div>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane mr-2"></i> Submit Ticket
                </button>
            </form>

            <div class="text-center text-xs text-gray-400 mt-4">
                <i class="fas fa-clock mr-1"></i> Average response time: 2-4 hours
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
                <h1>Support Center</h1>
                <p>24/7 help available</p>
            </div>
            <div class="hero-icon">
                <i class="fas fa-headset"></i>
            </div>
        </div>
    </div>

    <!-- Contact Cards -->
    <div class="contact-grid" style="padding: 0 16px;">
        <div class="contact-card" onclick="callSupport()">
            <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
            <div class="contact-label">Call</div>
            <div class="contact-value">24/7</div>
        </div>
        <div class="contact-card" onclick="emailSupport()">
            <div class="contact-icon"><i class="fas fa-envelope"></i></div>
            <div class="contact-label">Email</div>
            <div class="contact-value">Support</div>
        </div>
        <div class="contact-card" onclick="chatSupport()">
            <div class="contact-icon"><i class="fas fa-comment-dots"></i></div>
            <div class="contact-label">Chat</div>
            <div class="contact-value">Live</div>
        </div>
    </div>

    <!-- Message Box -->
    <div id="mobileMessageBox" class="hidden" style="margin: 0 16px 16px 16px;"></div>

    <!-- Tabs -->
    <div class="tabs-container" style="margin: 0 16px 20px 16px;">
        <div class="tab-nav">
            <div class="tab-btn active" onclick="switchTabMobile(event, 'faq')">FAQ</div>
            <div class="tab-btn" onclick="switchTabMobile(event, 'tickets')">Tickets</div>
            <div class="tab-btn" onclick="switchTabMobile(event, 'new')">New</div>
        </div>

        <div id="mobile-tab-faq" class="tab-content active">
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>How to book?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">Search, select room, click "Book Now" and complete payment.</div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Cancellation policy?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">Free cancellation up to 7 days before check-in.</div>
            </div>
            <div class="faq-item">
                <div class="faq-question" onclick="toggleFaq(this)">
                    <span>Security deposit refund?</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">Refunded within 7-10 days after check-out.</div>
            </div>
            <div class="help-card" style="margin-top: 16px;">
                <h4>Need more help?</h4>
                <div class="help-buttons">
                    <button class="help-btn primary" onclick="callSupport()">Call Now</button>
                    <button class="help-btn secondary" onclick="switchToNewTabMobile()">Create Ticket</button>
                </div>
            </div>
        </div>

        <div id="mobile-tab-tickets" class="tab-content">
            <div id="mobileTicketsContainer"></div>
        </div>

        <div id="mobile-tab-new" class="tab-content">
            <form id="mobileTicketForm" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="text" name="subject" class="form-input" placeholder="Subject" required>
                </div>
                <div class="form-group">
                    <select name="category" class="form-select" required>
                        <option value="">Category</option>
                        <option value="booking">Booking Issue</option>
                        <option value="payment">Payment Problem</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <textarea name="description" rows="4" class="form-textarea" placeholder="Describe your issue..." required></textarea>
                </div>
                <div class="upload-box" onclick="document.getElementById('mobileTicketImages').click()">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <p class="text-xs mt-1">Add images (optional)</p>
                </div>
                <input type="file" id="mobileTicketImages" name="attachments[]" accept="image/*" multiple class="hidden">
                <button type="submit" class="submit-btn mt-4">Submit Ticket</button>
            </form>
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
// Tab switching for desktop
function switchTab(event, tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    event.target.classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
}

// Tab switching for mobile
function switchTabMobile(event, tab) {
    const tabsContainer = event.target.closest('.tabs-container');
    tabsContainer.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    tabsContainer.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    event.target.classList.add('active');
    tabsContainer.querySelector('#mobile-tab-' + tab).classList.add('active');
}

function switchToNewTab() {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    document.querySelectorAll('.tab-btn')[2].classList.add('active');
    document.getElementById('tab-new').classList.add('active');
}

function switchToNewTabMobile() {
    const tabsContainer = document.querySelector('.mobile-container .tabs-container');
    tabsContainer.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    tabsContainer.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    tabsContainer.querySelectorAll('.tab-btn')[2].classList.add('active');
    tabsContainer.querySelector('#mobile-tab-new').classList.add('active');
}

function toggleFaq(element) {
    element.classList.toggle('active');
    const answer = element.nextElementSibling;
    answer.classList.toggle('show');
}

function callSupport() {
    window.location.href = 'tel:+919876543210';
}

function emailSupport() {
    window.location.href = 'mailto:support@pgmitra.com?subject=Support Request';
}

function chatSupport() {
    window.location.href = '/chat';
}

function viewTicket(ticketId) {
    window.location.href = '/ticket-details?id=' + ticketId;
}

function goToPage(page) {
    if (page === 'home') window.location.href = '/home';
    else if (page === 'search') window.location.href = '/search';
    else if (page === 'bookings') window.location.href = '/bookings';
    else if (page === 'saved-rooms') window.location.href = '/saved-rooms';
    else if (page === 'profile') window.location.href = '/profile';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

function showMessage(message, type = 'success') {
    const box = document.getElementById('messageBox');
    const mobileBox = document.getElementById('mobileMessageBox');
    
    const html = `
        <div class="alert alert-${type}">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${escapeHtml(message)}</span>
        </div>
    `;
    
    if (box) {
        box.innerHTML = html;
        box.classList.remove('hidden');
        setTimeout(() => box.classList.add('hidden'), 5000);
    }
    
    if (mobileBox) {
        mobileBox.innerHTML = html;
        mobileBox.classList.remove('hidden');
        setTimeout(() => mobileBox.classList.add('hidden'), 5000);
    }
}

async function loadSupportData() {
    try {
        const response = await fetch('/api/support-api?action=get_support_data');
        const data = await response.json();

        if (!data.success) {
            showMessage(data.message || 'Failed to load support data', 'error');
            return;
        }

        renderTickets(data.tickets || []);
        renderBookings(data.bookings || []);
    } catch (error) {
        showMessage('Something went wrong while loading support data', 'error');
    }
}

function renderTickets(tickets) {
    const container = document.getElementById('ticketsContainer');
    const mobileContainer = document.getElementById('mobileTicketsContainer');

    if (!tickets.length) {
        const emptyHTML = `
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-ticket-alt"></i></div>
                <h4>No tickets yet</h4>
                <p>Create a support ticket and we'll help you</p>
                <button onclick="switchToNewTab()" class="help-btn primary" style="margin-top: 12px;">Create Ticket</button>
            </div>
        `;
        if (container) container.innerHTML = emptyHTML;
        if (mobileContainer) mobileContainer.innerHTML = emptyHTML.replace('switchToNewTab()', 'switchToNewTabMobile()');
        return;
    }

    const ticketsHTML = tickets.map(ticket => {
        let statusClass = 'status-open';
        if (ticket.status === 'in-progress') statusClass = 'status-progress';
        if (ticket.status === 'resolved') statusClass = 'status-resolved';
        
        let statusText = ticket.status.charAt(0).toUpperCase() + ticket.status.slice(1);
        if (ticket.status === 'in-progress') statusText = 'In Progress';

        return `
            <div class="ticket-card ${ticket.status}" onclick="viewTicket(${ticket.id})">
                <div class="ticket-header">
                    <span class="ticket-number">#${escapeHtml(ticket.ticket_number)}</span>
                    <span class="ticket-status ${statusClass}">${statusText}</span>
                </div>
                <div class="ticket-subject">${escapeHtml(ticket.subject)}</div>
                <div class="ticket-meta">
                    <span><i class="far fa-clock"></i> ${escapeHtml(ticket.created_at)}</span>
                    <span><i class="far fa-comment"></i> ${ticket.reply_count || 0} replies</span>
                </div>
            </div>
        `;
    }).join('');

    if (container) container.innerHTML = ticketsHTML;
    if (mobileContainer) mobileContainer.innerHTML = ticketsHTML;
}

function renderBookings(bookings) {
    const select = document.getElementById('bookingSelect');
    if (!select) return;
    
    select.innerHTML = `<option value="">Select booking</option>`;
    bookings.forEach(booking => {
        const option = document.createElement('option');
        option.value = booking.id;
        option.textContent = `${booking.room_name} (${booking.check_in})`;
        select.appendChild(option);
    });
}

// File upload handling
document.getElementById('ticketImages')?.addEventListener('change', function() {
    const preview = document.getElementById('filePreviewList');
    preview.innerHTML = '';
    const files = Array.from(this.files);
    files.forEach(file => {
        const item = document.createElement('div');
        item.className = 'file-preview-item';
        item.innerHTML = `
            <span><i class="fas fa-image mr-2 text-blue-500"></i>${escapeHtml(file.name)}</span>
            <span>${(file.size / 1024 / 1024).toFixed(2)} MB</span>
        `;
        preview.appendChild(item);
    });
});

document.getElementById('mobileTicketImages')?.addEventListener('change', function() {
    const preview = document.querySelector('#mobile-tab-new .file-preview-list');
    if (preview) {
        preview.innerHTML = '';
        const files = Array.from(this.files);
        files.forEach(file => {
            const item = document.createElement('div');
            item.className = 'file-preview-item';
            item.innerHTML = `
                <span><i class="fas fa-image mr-2 text-blue-500"></i>${escapeHtml(file.name)}</span>
                <span>${(file.size / 1024 / 1024).toFixed(2)} MB</span>
            `;
            preview.appendChild(item);
        });
    }
});

// Form submission
document.getElementById('ticketForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'create_ticket');
    
    try {
        const response = await fetch('/api/support-api', { method: 'POST', body: formData });
        const data = await response.json();
        if (data.success) {
            showMessage(data.message, 'success');
            this.reset();
            document.getElementById('filePreviewList').innerHTML = '';
            loadSupportData();
            switchToNewTab();
        } else {
            showMessage(data.message || 'Failed to create ticket', 'error');
        }
    } catch (error) {
        showMessage('Something went wrong while creating ticket', 'error');
    }
});

document.getElementById('mobileTicketForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'create_ticket');
    
    try {
        const response = await fetch('/api/support-api', { method: 'POST', body: formData });
        const data = await response.json();
        if (data.success) {
            showMessage(data.message, 'success');
            this.reset();
            const preview = this.querySelector('.file-preview-list');
            if (preview) preview.innerHTML = '';
            loadSupportData();
            switchToNewTabMobile();
        } else {
            showMessage(data.message || 'Failed to create ticket', 'error');
        }
    } catch (error) {
        showMessage('Something went wrong while creating ticket', 'error');
    }
});

loadSupportData();
</script>

</body>
</html>