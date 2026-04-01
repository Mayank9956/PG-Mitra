<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
require_once 'common/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$database = new Database();
$db = $database->getConnection();

$user_id = (int)$_SESSION['user_id'];
$booking_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($booking_id <= 0) {
    header("Location: my-bookings.php");
    exit;
}

/* FETCH BOOKING + ROOM */
$query = "SELECT 
            b.id,
            b.booking_number,
            b.room_id,
            b.user_id,
            b.check_in,
            b.check_out,
            b.total_days,
            b.completed_days,
            b.months,
            b.total_rent,
            b.security_deposit,
            b.final_amount,
            b.status,
            r.title AS room_title,
            r.image_url,
            r.price AS room_price,
            r.location,
            r.city
          FROM bookings b
          INNER JOIN rooms r ON r.id = b.room_id
          WHERE b.id = ? AND b.user_id = ?
          LIMIT 1";

$stmt = $db->prepare($query);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    header("Location: my-bookings.php");
    exit;
}

$message = '';
$message_type = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Extend Booking - StayEase</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{font-family:'Sora',sans-serif;box-sizing:border-box}
body{background:#f4f6fb;margin:0;padding:0}
.app-container{
    max-width:414px;
    margin:0 auto;
    background:#fff;
    min-height:100vh;
    box-shadow:0 0 30px rgba(0,0,0,.06);
}
.toast{
    position:fixed;
    top:20px;
    left:50%;
    transform:translateX(-50%);
    padding:12px 18px;
    border-radius:12px;
    color:#fff;
    font-size:14px;
    font-weight:600;
    z-index:9999;
    box-shadow:0 12px 28px rgba(0,0,0,.18);
}
.toast.success{background:#16a34a}
.toast.error{background:#dc2626}
.toast.info{background:#111827}
</style>
</head>
<body>

<div class="app-container">
    <!-- Header -->
    <div class="sticky top-0 bg-white z-10 border-b border-gray-100 px-5 py-4">
        <div class="flex items-center gap-4">
            <button onclick="goBack()" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </button>
            <div>
                <h1 class="text-lg font-bold text-gray-900">Extend Booking</h1>
                <p class="text-xs text-gray-500">Update your stay duration</p>
            </div>
        </div>
    </div>

    <!-- Room Info -->
    <div class="p-5 border-b border-gray-100">
        <div class="flex gap-4">
            <img src="<?php echo htmlspecialchars($booking['image_url']); ?>" 
                 alt="<?php echo htmlspecialchars($booking['room_title']); ?>"
                 class="w-24 h-24 rounded-2xl object-cover border border-gray-100">

            <div class="flex-1 min-w-0">
                <h2 class="font-bold text-gray-900 text-lg leading-6">
                    <?php echo htmlspecialchars($booking['room_title']); ?>
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    <?php echo htmlspecialchars($booking['location'] . ', ' . $booking['city']); ?>
                </p>

                <div class="mt-3 flex items-center gap-2 flex-wrap">
                    <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                        Booking #<?php echo htmlspecialchars($booking['booking_number']); ?>
                    </span>
                    <span class="px-3 py-1 rounded-full bg-green-50 text-green-700 text-xs font-semibold capitalize">
                        <?php echo htmlspecialchars($booking['status']); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Booking Summary -->
    <div class="p-5 border-b border-gray-100">
        <h3 class="text-base font-bold text-gray-900 mb-4">Current Booking Details</h3>

        <div class="grid grid-cols-2 gap-3">
            <div class="bg-gray-50 rounded-2xl p-4">
                <div class="text-xs text-gray-500 mb-1">Check-in</div>
                <div class="text-sm font-semibold text-gray-900">
                    <?php echo date('d M Y', strtotime($booking['check_in'])); ?>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-4">
                <div class="text-xs text-gray-500 mb-1">Current Check-out</div>
                <div class="text-sm font-semibold text-gray-900">
                    <?php echo date('d M Y', strtotime($booking['check_out'])); ?>
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-4">
                <div class="text-xs text-gray-500 mb-1">Total Days</div>
                <div class="text-sm font-semibold text-gray-900">
                    <?php echo (int)$booking['total_days']; ?> days
                </div>
            </div>

            <div class="bg-gray-50 rounded-2xl p-4">
                <div class="text-xs text-gray-500 mb-1">Monthly Rent</div>
                <div class="text-sm font-semibold text-blue-600">
                    ₹<?php echo number_format((float)$booking['room_price']); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Extend Form -->
    <div class="p-5">
        <h3 class="text-base font-bold text-gray-900 mb-4">Choose New Check-out Date</h3>

        <form id="extendBookingForm">
            <input type="hidden" name="booking_id" value="<?php echo (int)$booking['id']; ?>">

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-2">New Check-out Date</label>
                <input type="date"
                       name="new_check_out"
                       id="new_check_out"
                       min="<?php echo date('Y-m-d', strtotime($booking['check_out'] . ' +1 day')); ?>"
                       class="w-full h-14 px-4 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       onchange="calculateExtension()"
                       required>
            </div>

            <!-- Extension Summary -->
            <div class="bg-gray-50 rounded-2xl p-4 mb-5">
                <h4 class="text-sm font-bold text-gray-900 mb-3">Extension Summary</h4>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Extra Days</span>
                        <span class="font-semibold text-gray-900" id="extra_days_text">0 days</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Extra Months</span>
                        <span class="font-semibold text-gray-900" id="extra_months_text">0</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Additional Rent</span>
                        <span class="font-semibold text-blue-600" id="extra_amount_text">₹0</span>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Reason / Note (Optional)</label>
                <textarea name="note"
                          id="note"
                          rows="4"
                          class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                          placeholder="Write any note for booking extension..."></textarea>
            </div>

            <button type="button"
                    id="submitBtn"
                    onclick="submitExtension()"
                    class="w-full h-14 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-base transition shadow-lg shadow-blue-500/20">
                Confirm Extension
            </button>
        </form>
    </div>
</div>

<script>
const currentCheckout = "<?php echo date('Y-m-d', strtotime($booking['check_out'])); ?>";
const monthlyRent = <?php echo (float)$booking['room_price']; ?>;

function goBack() {
    window.history.back();
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 2200);
}

function calculateExtension() {
    const newCheckOut = document.getElementById('new_check_out').value;

    if (!newCheckOut) {
        document.getElementById('extra_days_text').textContent = '0 days';
        document.getElementById('extra_months_text').textContent = '0';
        document.getElementById('extra_amount_text').textContent = '₹0';
        return;
    }

    const oldDate = new Date(currentCheckout + 'T00:00:00');
    const newDate = new Date(newCheckOut + 'T00:00:00');

    const diffMs = newDate - oldDate;
    const extraDays = Math.ceil(diffMs / (1000 * 60 * 60 * 24));

    if (extraDays <= 0) {
        document.getElementById('extra_days_text').textContent = '0 days';
        document.getElementById('extra_months_text').textContent = '0';
        document.getElementById('extra_amount_text').textContent = '₹0';
        return;
    }

    const extraMonths = Math.ceil(extraDays / 30);
    const extraAmount = extraMonths * monthlyRent;

    document.getElementById('extra_days_text').textContent = extraDays + ' days';
    document.getElementById('extra_months_text').textContent = extraMonths;
    document.getElementById('extra_amount_text').textContent = '₹' + extraAmount.toLocaleString('en-IN');
}

async function submitExtension() {
    const form = document.getElementById('extendBookingForm');
    const formData = new FormData(form);
    const newCheckOut = document.getElementById('new_check_out').value;
    const btn = document.getElementById('submitBtn');

    if (!newCheckOut) {
        showToast('Please select new check-out date', 'error');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

    try {
        const response = await fetch('/api/extend-booking', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showToast(data.message || 'Booking extended successfully', 'success');

            setTimeout(() => {
                window.location.href = 'booking-details?id=' + encodeURIComponent(<?php echo (int)$booking['id']; ?>);
            }, 1200);
        } else {
            showToast(data.message || 'Failed to extend booking', 'error');
        }
    } catch (error) {
        showToast('Something went wrong. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Confirm Extension';
    }
}
</script>

</body>
</html>