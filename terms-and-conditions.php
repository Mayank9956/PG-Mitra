<?php
date_default_timezone_set('Asia/Kolkata');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Terms & Conditions - StayEase</title>
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
                <h1 class="text-xl font-bold text-gray-900">Terms & Conditions</h1>
                <p class="text-xs text-gray-500 mt-0.5">Please read before booking</p>
            </div>
        </div>
    </div>

    <div class="p-5 space-y-4">
        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">1. Acceptance of Terms</h2>
            <p class="text-sm text-gray-600 leading-7">
                By using StayEase, browsing listings, unlocking contact details, or making a booking, you agree to follow these Terms & Conditions.
            </p>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">2. Booking Rules</h2>
            <ul class="policy-list text-sm">
                <li>All bookings are subject to room availability and owner approval where applicable.</li>
                <li>You must provide correct personal and booking details at the time of reservation.</li>
                <li>Wrong information may lead to booking rejection or cancellation.</li>
                <li>Minimum stay duration, security deposit, and monthly rent may vary from property to property.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">3. Payments</h2>
            <ul class="policy-list text-sm">
                <li>Users must pay the applicable booking amount, rent, service charges, and deposit shown at checkout.</li>
                <li>Any wallet balance, discount, or coupon benefit will be adjusted as per platform rules.</li>
                <li>Security deposit terms depend on the property and may be refundable or non-refundable as mentioned on the listing.</li>
                <li>StayEase reserves the right to cancel invalid or suspicious transactions.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">4. Coupon & Offer Policy</h2>
            <ul class="policy-list text-sm">
                <li>Coupons are valid only for eligible users, properties, and booking amounts.</li>
                <li>Only one coupon may be used per booking unless otherwise stated.</li>
                <li>Coupons cannot be exchanged for cash.</li>
                <li>StayEase may withdraw or modify any offer without prior notice.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">5. User Responsibilities</h2>
            <ul class="policy-list text-sm">
                <li>You must maintain proper conduct at the property.</li>
                <li>Any damage caused to room, furniture, or property items may be charged separately.</li>
                <li>You must follow PG/hostel rules including visitor policy, timing rules, and property guidelines.</li>
                <li>Illegal activity, nuisance, or misconduct may result in immediate cancellation without refund.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">6. Contact Unlock Charges</h2>
            <ul class="policy-list text-sm">
                <li>If you pay to unlock an owner or host contact number, that charge is treated as a platform service charge.</li>
                <li>Unlocked contact charges are generally non-refundable once access is granted.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">7. Cancellations & Refunds</h2>
            <p class="text-sm text-gray-600 leading-7">
                Cancellation and refund requests are handled according to the applicable cancellation policy shown on the platform and property details page.
            </p>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">8. Platform Rights</h2>
            <ul class="policy-list text-sm">
                <li>StayEase may suspend accounts, reject bookings, or remove listings in case of fraud, misuse, or policy violations.</li>
                <li>We may update these terms from time to time without individual notice.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">9. Limitation of Liability</h2>
            <p class="text-sm text-gray-600 leading-7">
                StayEase acts as a booking facilitation platform. Property stay experience, room condition, and on-ground services are primarily managed by the property owner or operator.
            </p>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">10. Contact</h2>
            <p class="text-sm text-gray-600 leading-7">
                For any support, dispute, or policy-related issue, please contact the StayEase support team through the app or official support channel.
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