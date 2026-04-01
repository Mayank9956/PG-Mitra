<?php
date_default_timezone_set('Asia/Kolkata');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Cancellation Policy - StayEase</title>
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
                <h1 class="text-xl font-bold text-gray-900">Cancellation Policy</h1>
                <p class="text-xs text-gray-500 mt-0.5">Refund and cancellation rules</p>
            </div>
        </div>
    </div>

    <div class="p-5 space-y-4">
        <div class="highlight-box">
            Booking cancellation and refund depend on booking stage, payment type, and property rules. Please read carefully before confirming your stay.
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">1. Before Confirmation</h2>
            <p class="text-sm text-gray-600 leading-7">
                If a booking is still pending and has not been confirmed by the property or payment system, StayEase may cancel it automatically or manually without penalty.
            </p>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">2. After Confirmation</h2>
            <ul class="policy-list text-sm">
                <li>If the booking is confirmed, cancellation charges may apply.</li>
                <li>Platform fees, convenience charges, and contact unlock charges are generally non-refundable.</li>
                <li>Refund eligibility depends on the timing of cancellation and property-specific terms.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">3. Refund Rules</h2>
            <ul class="policy-list text-sm">
                <li>If cancellation is approved before check-in, eligible refund amount may be processed after deduction of applicable charges.</li>
                <li>If cancellation happens on or after check-in date, refund may be partial or not applicable.</li>
                <li>Coupon discounts used in a booking are not redeemable in cash.</li>
                <li>Wallet refunds, where applicable, may be credited back to the StayEase wallet.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">4. Security Deposit</h2>
            <ul class="policy-list text-sm">
                <li>Security deposit refund depends on the room listing and property owner rules.</li>
                <li>If the listing marks the deposit as non-refundable, it will not be returned after payment.</li>
                <li>If refundable, any damages, dues, or penalties may be deducted before refund.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">5. No-show Policy</h2>
            <p class="text-sm text-gray-600 leading-7">
                If the guest does not arrive on the booked date without prior notice, the booking may be treated as a no-show and refund may not be applicable.
            </p>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">6. Contact Unlock Charges</h2>
            <p class="text-sm text-gray-600 leading-7">
                Any amount paid to unlock owner or host contact details is treated as a service fee and is non-refundable once the number is unlocked.
            </p>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">7. Refund Processing Time</h2>
            <ul class="policy-list text-sm">
                <li>Approved refunds may take 5 to 10 working days depending on the payment method.</li>
                <li>Wallet refunds may reflect faster if processed internally.</li>
                <li>Banking delays, gateway issues, or holidays may increase refund time.</li>
            </ul>
        </div>

        <div class="section-card">
            <h2 class="text-lg font-bold text-gray-900 mb-3">8. Exceptional Cases</h2>
            <p class="text-sm text-gray-600 leading-7">
                StayEase may review special cancellation cases individually in situations like duplicate payment, technical failure, or verified property issue.
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