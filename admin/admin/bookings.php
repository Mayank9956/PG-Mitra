<?php
require_once '../common/auth.php';
requirePageRole([ROLE_ADMIN]);
require_once '../common/layout.php';

renderHeader('Manage Bookings');
renderSidebarMenu('bookings', 'admin');
renderMainContentStart('Manage Bookings', $_SESSION['username'] ?? 'Admin');
?>

<style>
.bookings-page{
    display:flex;
    flex-direction:column;
    gap:24px;
}

.page-card{
    background:#fff;
    border:1px solid #edf1f5;
    border-radius:20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.04);
    overflow:hidden;
}

.page-card-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    padding:18px 20px;
    border-bottom:1px solid #edf1f5;
}

.page-card-title{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:18px;
    font-weight:700;
    color:#102a43;
}

.page-card-title i{
    color:#ff6b35;
}

.page-card-body{
    padding:20px;
}

.filter-row{
    display:flex;
    align-items:end;
    gap:16px;
    flex-wrap:wrap;
}

.form-group{
    display:flex;
    flex-direction:column;
    gap:8px;
    min-width:220px;
}

.form-label{
    font-size:14px;
    font-weight:600;
    color:#34495e;
}

.select-control,
.input-control{
    width:100%;
    border:1px solid #dbe3eb;
    border-radius:12px;
    background:#fff;
    color:#1f2937;
    padding:12px 14px;
    outline:none;
    transition:.2s ease;
}

.select-control:focus,
.input-control:focus{
    border-color:#ff6b35;
    box-shadow:0 0 0 3px rgba(255,107,53,0.10);
}

.booking-list-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:18px;
}

.booking-card{
    background:#fff;
    border:1px solid #edf1f5;
    border-radius:18px;
    box-shadow:0 2px 10px rgba(0,0,0,0.04);
    padding:18px;
    display:flex;
    flex-direction:column;
    gap:14px;
    min-width:0;
}

.booking-top{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
}

.booking-id{
    font-size:18px;
    font-weight:700;
    color:#102a43;
    margin:0;
}

.status-pill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
    text-transform:capitalize;
    white-space:nowrap;
}

.status-pill.pending{
    background:#fff7ed;
    color:#c2410c;
}

.status-pill.confirmed{
    background:#ecfdf3;
    color:#15803d;
}

.status-pill.cancelled{
    background:#fef2f2;
    color:#b91c1c;
}

.status-pill.completed{
    background:#eff6ff;
    color:#1d4ed8;
}

.status-pill.default{
    background:#f1f5f9;
    color:#475569;
}

.booking-meta{
    display:grid;
    gap:10px;
}

.booking-meta-item{
    display:flex;
    align-items:flex-start;
    gap:10px;
    color:#475569;
    font-size:14px;
    line-height:1.5;
    word-break:break-word;
}

.booking-meta-item i{
    width:16px;
    margin-top:2px;
    color:#ff6b35;
    flex-shrink:0;
}

.amount-text{
    font-weight:700;
    color:#102a43;
}

.assign-box{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:4px;
}

.assign-box .input-control{
    flex:1;
    min-width:140px;
}

.btn{
    border:none;
    outline:none;
    cursor:pointer;
    border-radius:12px;
    padding:11px 18px;
    font-size:14px;
    font-weight:600;
    transition:.25s ease;
}

.btn:hover{
    transform:translateY(-1px);
}

.btn-primary{
    background:linear-gradient(90deg, #ff9f4a, #ff6b35);
    color:#fff;
}

.empty-state,
.loading-state{
    background:#fff;
    border:1px dashed #d8e2ec;
    border-radius:18px;
    padding:48px 20px;
    text-align:center;
    color:#64748b;
}

.empty-state i,
.loading-state i{
    font-size:36px;
    color:#ff6b35;
    margin-bottom:12px;
}

.empty-state h3,
.loading-state h3{
    font-size:18px;
    color:#102a43;
    margin-bottom:8px;
}

.empty-state p,
.loading-state p{
    margin:0;
    font-size:14px;
}

@media (max-width: 992px){
    .booking-list-grid{
        grid-template-columns:1fr;
    }
}

@media (max-width: 768px){
    .bookings-page{
        gap:18px;
    }

    .page-card-header,
    .page-card-body{
        padding:16px;
    }

    .page-card-title{
        font-size:16px;
    }

    .filter-row{
        align-items:stretch;
    }

    .form-group{
        min-width:100%;
    }

    .booking-card{
        padding:16px;
    }

    .booking-top{
        flex-direction:column;
        align-items:flex-start;
    }

    .assign-box{
        flex-direction:column;
    }

    .assign-box .input-control,
    .assign-box .btn{
        width:100%;
    }
}

@media (max-width: 480px){
    .booking-id{
        font-size:16px;
    }

    .booking-meta-item{
        font-size:13px;
    }

    .select-control,
    .input-control{
        padding:11px 12px;
        font-size:14px;
    }
}
</style>

<div class="bookings-page">
    <section class="page-card">
        <div class="page-card-header">
            <div class="page-card-title">
                <i class="fas fa-filter"></i>
                <span>Filter Bookings</span>
            </div>
        </div>

        <div class="page-card-body">
            <div class="filter-row">
                <div class="form-group">
                    <label class="form-label" for="bookingStatusFilter">Booking Status</label>
                    <select id="bookingStatusFilter" class="select-control">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
        </div>
    </section>

    <section class="page-card">
        <div class="page-card-header">
            <div class="page-card-title">
                <i class="fas fa-calendar-check"></i>
                <span>Booking List</span>
            </div>
        </div>

        <div class="page-card-body">
            <div id="bookingList">
                <div class="loading-state">
                    <i class="fas fa-spinner fa-pulse"></i>
                    <h3>Loading bookings...</h3>
                    <p>Please wait while we fetch the booking data.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(value);
    return div.innerHTML;
}

function formatPrice(value) {
    const num = Number(value || 0);
    return num.toLocaleString('en-IN');
}

function getStatusClass(status) {
    const s = String(status || '').toLowerCase();
    if (s === 'pending') return 'pending';
    if (s === 'confirmed') return 'confirmed';
    if (s === 'cancelled') return 'cancelled';
    if (s === 'completed') return 'completed';
    return 'default';
}

function renderBookingCard(item) {
    const bookingId = Number(item.id) || 0;
    const roomName = escapeHtml(item.room_name || 'N/A');
    const userName = escapeHtml(item.user_name || 'N/A');
    const status = escapeHtml(item.status || 'unknown');
    const totalAmount = formatPrice(item.total_amount || 0);

    return ''
        + '<div class="booking-card">'
        +   '<div class="booking-top">'
        +       '<h3 class="booking-id">Booking #' + bookingId + '</h3>'
        +       '<span class="status-pill ' + getStatusClass(item.status) + '">' + status + '</span>'
        +   '</div>'
        +   '<div class="booking-meta">'
        +       '<div class="booking-meta-item"><i class="fas fa-door-open"></i><span><strong>Room:</strong> ' + roomName + '</span></div>'
        +       '<div class="booking-meta-item"><i class="fas fa-user"></i><span><strong>User:</strong> ' + userName + '</span></div>'
        +       '<div class="booking-meta-item"><i class="fas fa-indian-rupee-sign"></i><span class="amount-text"><strong>Total:</strong> ₹' + totalAmount + '</span></div>'
        +   '</div>'
        +   '<div class="assign-box">'
        +       '<input type="number" class="input-control" placeholder="Enter Staff ID" id="staff_' + bookingId + '" min="1">'
        +       '<button type="button" class="btn btn-primary" onclick="assignBooking(' + bookingId + ')">Assign</button>'
        +   '</div>'
        + '</div>';
}

async function loadBookings() {
    const list = document.getElementById('bookingList');
    const status = document.getElementById('bookingStatusFilter').value;

    list.innerHTML =
        '<div class="loading-state">'
        + '<i class="fas fa-spinner fa-pulse"></i>'
        + '<h3>Loading bookings...</h3>'
        + '<p>Please wait while we fetch the booking data.</p>'
        + '</div>';

    try {
        const res = await apiGet('../api/admin/list-bookings?status=' + encodeURIComponent(status));

        if (res && res.status === 'success' && res.data && Array.isArray(res.data.bookings)) {
            const bookings = res.data.bookings;

            if (bookings.length === 0) {
                list.innerHTML =
                    '<div class="empty-state">'
                    + '<i class="fas fa-calendar-xmark"></i>'
                    + '<h3>No bookings found</h3>'
                    + '<p>No booking records matched the selected filter.</p>'
                    + '</div>';
                return;
            }

            let html = '<div class="booking-list-grid">';
            bookings.forEach(function(item) {
                html += renderBookingCard(item);
            });
            html += '</div>';

            list.innerHTML = html;
        } else {
            list.innerHTML =
                '<div class="empty-state">'
                + '<i class="fas fa-circle-exclamation"></i>'
                + '<h3>Unable to load bookings</h3>'
                + '<p>' + escapeHtml((res && res.message) ? res.message : 'Something went wrong while fetching bookings.') + '</p>'
                + '</div>';
        }
    } catch (error) {
        console.error('Load bookings error:', error);
        list.innerHTML =
            '<div class="empty-state">'
            + '<i class="fas fa-wifi"></i>'
            + '<h3>Network error</h3>'
            + '<p>Failed to load bookings. Please try again.</p>'
            + '</div>';
    }
}

async function assignBooking(bookingId) {
    const staffInput = document.getElementById('staff_' + bookingId);
    const staffId = staffInput ? staffInput.value.trim() : '';

    if (!staffId) {
        showToast('Please enter Staff ID', 'error');
        if (staffInput) staffInput.focus();
        return;
    }

    const fd = new FormData();
    fd.append('booking_id', bookingId);
    fd.append('staff_id', staffId);

    try {
        const res = await apiPost('../api/admin/assign-booking', fd);
        showToast((res && res.message) ? res.message : 'Request completed', res && res.status === 'success' ? 'success' : 'error');
    } catch (error) {
        console.error('Assign booking error:', error);
        showToast('Failed to assign booking', 'error');
    }
}

document.getElementById('bookingStatusFilter').addEventListener('change', loadBookings);

document.addEventListener('DOMContentLoaded', function() {
    loadBookings();
});
</script>

<?php renderFooter('admin'); ?>