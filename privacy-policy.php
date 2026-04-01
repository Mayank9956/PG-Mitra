<?php
date_default_timezone_set('Asia/Kolkata');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Privacy Policy - StayEase</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* {
    font-family: 'Sora', sans-serif;
}
body {
    background: #f4f6fb;
    color: #111827;
}
.app-container {
    max-width: 414px;
    margin: 0 auto;
    background: #ffffff;
    min-height: 100vh;
    box-shadow: 0 0 30px rgba(15, 23, 42, 0.06);
}
.sticky-header {
    position: sticky;
    top: 0;
    z-index: 20;
    background: rgba(255,255,255,0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid #eef2f7;
}
.section-card {
    background: #ffffff;
    border: 1px solid #eef2f7;
    border-radius: 20px;
    padding: 18px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
}
.policy-list {
    padding-left: 18px;
}
.policy-list li {
    margin-bottom: 10px;
    color: #4b5563;
    line-height: 1.7;
}
.highlight-box {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    color: #1d4ed8;
    border-radius: 16px;
    padding: 14px 16px;
    font-size: 14px;
    line-height: 1.7;
}
</style>
</head>
<body>

<div class="app-container">
    <div class="sticky-header px-5 py-4">
        <div class="flex items-center gap-4">
            <button onclick="goBack()" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </button>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Privacy Policy</h1>
                <p class="text-xs text-gray-500 mt-0.5">How we collect, use, and protect your data</p>
            </div>
        </div>
    </div>

    <div class="p-5 space-y-4">
        <div class="highlight-box">
            Your privacy is important to StayEase. This Privacy Policy explains how we collect, use, and protect your personal information while using our services.
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">1. Information We Collect</h2>
            <ul class="policy-list text-sm">
                <li>Personal details like name, email, phone number, and payment information.</li>
                <li>Booking history and preferences to provide personalized services.</li>
                <li>Device information, IP address, and usage data to improve app performance.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">2. How We Use Information</h2>
            <ul class="policy-list text-sm">
                <li>To process bookings and payments securely.</li>
                <li>To communicate important updates, offers, or changes.</li>
                <li>To improve our app, services, and user experience.</li>
                <li>To detect and prevent fraud or unauthorized activity.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">3. Sharing Your Information</h2>
            <ul class="policy-list text-sm">
                <li>We do not sell your personal information to third parties.</li>
                <li>Information may be shared with property owners for booking purposes.</li>
                <li>We may share information with service providers for app functionality or analytics.</li>
                <li>We may disclose information if required by law or legal process.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">4. Cookies and Tracking</h2>
            <p class="text-sm text-gray-600 leading-7">
                StayEase uses cookies and similar technologies to enhance your experience, remember preferences, and analyze traffic. You can manage cookie preferences in your browser.
            </p>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">5. Data Security</h2>
            <p class="text-sm text-gray-600 leading-7">
                We implement appropriate technical and organizational measures to protect your data from unauthorized access, loss, or misuse. However, no method is 100% secure.
            </p>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">6. Your Rights</h2>
            <ul class="policy-list text-sm">
                <li>You can request access to your personal information or correction of inaccuracies.</li>
                <li>You may request deletion of your data where applicable.</li>
                <li>You can opt-out of marketing communications at any time.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">7. Third-party Services</h2>
            <p class="text-sm text-gray-600 leading-7">
                Our app may use third-party services for analytics, payments, or communication. These services have their own privacy policies, and we are not responsible for their practices.
            </p>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">8. Policy Updates</h2>
            <p class="text-sm text-gray-600 leading-7">
                We may update this Privacy Policy from time to time. Any changes will be posted on this page with an updated "Last updated" date.
            </p>
        </div>

        <div class="pb-6 text-center text-xs text-gray-400">
            Last updated: <?php echo date('d M Y'); ?>
        </div>
    </div>
</div>

<script>
function goBack() {
    window.history.back();
}
</script>

</body>
</html>