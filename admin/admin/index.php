<?php
require_once '../common/layout.php';
require_once '../common/auth.php';
requirePageRole([ROLE_ADMIN]);

renderHeader('Admin Dashboard');
renderSidebarMenu('dashboard', 'admin');
renderMainContentStart('Dashboard', $_SESSION['username'] ?? 'Admin');
?>

<style>
.dashboard-page{
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.welcome-card{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 18px;
    flex-wrap: wrap;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid #edf1f5;
    border-radius: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    padding: 24px;
}

.welcome-content h2{
    font-size: 20px;
    font-weight: 700;
    color: #1a2c3e;
    margin-bottom: 6px;
    line-height: 1.2;
}

.welcome-content p{
    margin: 0;
    color: #6b7280;
    font-size: 14px;
}

.date-box{
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    border: 1px solid #edf1f5;
    border-radius: 14px;
    padding: 12px 16px;
    color: #4b5563;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    font-size: 14px;
    font-weight: 500;
}

.date-box i{
    color: #ff6b35;
}

.dashboard-stats{
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
}

.stat-card{
    background: #fff;
    border: 1px solid #edf1f5;
    border-radius: 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    padding: 22px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    min-height: 128px;
}

.stat-icon{
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: rgba(255,107,53,0.10);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon i{
    font-size: 20px;
    color: #ff6b35;
}

.stat-info{
    min-width: 0;
}

.stat-info h3{
    margin: 0 0 6px;
    color: #102a43;
    font-size: 20px;
    line-height: 1.2;
    font-weight: 800;
    word-break: break-word;
}

.stat-info p{
    margin: 0;
    color: #6b7280;
    font-size: 14px;
}

.panel-card{
    background: #fff;
    border: 1px solid #edf1f5;
    border-radius: 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    overflow: hidden;
}

.panel-header{
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    padding: 18px 20px;
    border-bottom: 1px solid #edf1f5;
}

.panel-title{
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: #102a43;
    font-size: 18px;
    font-weight: 700;
}

.panel-title i{
    color: #ff6b35;
}

.view-link{
    color: #ff6b35;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.view-link:hover{
    color: #e85b28;
}

.recent-table{
    width: 100%;
    min-width: 760px;
    border-collapse: collapse;
}

.recent-table thead th{
    background: #f8fafc;
    color: #3f4d5a;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    padding: 14px 18px;
    text-align: left;
    border-bottom: 1px solid #edf1f5;
    white-space: nowrap;
}

.recent-table tbody td{
    padding: 16px 18px;
    border-bottom: 1px solid #edf1f5;
    color: #1f2937;
    font-size: 14px;
    vertical-align: middle;
}

.recent-table tbody tr:last-child td{
    border-bottom: none;
}

.recent-table tbody tr:hover{
    background: #fcfdff;
}

.room-name{
    font-weight: 600;
    color: #102a43;
}

.amount-text{
    font-weight: 700;
    white-space: nowrap;
}

.table-center-state{
    text-align: center;
    padding: 42px 20px !important;
    color: #6b7280;
}

.table-center-state i{
    display: inline-block;
    font-size: 30px;
    margin-bottom: 10px;
    color: #ff6b35;
}

.quick-actions-wrap{
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.section-heading{
    display: inline-flex;
    align-items: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 700;
    color: #102a43;
}

.section-heading i{
    color: #ff6b35;
}

.quick-actions{
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
}

.quick-card{
    display: flex;
    align-items: center;
    gap: 14px;
    background: #fff;
    border: 1px solid #edf1f5;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    padding: 18px;
    transition: all .25s ease;
}

.quick-card:hover{
    transform: translateY(-3px);
    box-shadow: 0 10px 24px rgba(0,0,0,0.07);
}

.quick-card-icon{
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: rgba(255,107,53,0.10);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.quick-card-icon i{
    color: #ff6b35;
    font-size: 19px;
}

.quick-card-text{
    font-size: 14px;
    font-weight: 600;
    color: #102a43;
    line-height: 1.4;
}

@media (max-width: 1200px){
    .dashboard-stats{
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .quick-actions{
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 768px){
    .dashboard-page{
        gap: 18px;
    }

    .welcome-card{
        padding: 18px;
        flex-direction: column;
        align-items: flex-start;
    }

    .welcome-content h2{
        font-size: 18px;
    }

    .welcome-content p{
        font-size: 13px;
    }

    .date-box{
        width: 100%;
        justify-content: center;
        text-align: center;
        padding: 11px 14px;
        font-size: 13px;
    }

    .dashboard-stats{
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .stat-card{
        min-height: auto;
        padding: 18px 16px;
    }

    .stat-info h3{
        font-size: 18px;
    }

    .stat-info p{
        font-size: 13px;
    }

    .panel-header{
        padding: 16px;
    }

    .panel-title{
        font-size: 16px;
    }

    .recent-table{
        min-width: 680px;
    }

    .recent-table thead th,
    .recent-table tbody td{
        padding: 12px 14px;
        font-size: 12px;
    }

    .quick-actions{
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .quick-card{
        padding: 16px;
    }

    .section-heading{
        font-size: 16px;
    }
}

@media (max-width: 480px){
    .welcome-card{
        padding: 16px;
        border-radius: 16px;
    }

    .welcome-content h2{
        font-size: 17px;
    }

    .welcome-content p{
        font-size: 12px;
    }

    .date-box{
        font-size: 12px;
        padding: 10px 12px;
    }

    .stat-icon{
        width: 44px;
        height: 44px;
    }

    .stat-icon i{
        font-size: 18px;
    }

    .stat-info h3{
        font-size: 17px;
    }

    .recent-table thead th,
    .recent-table tbody td{
        padding: 10px 12px;
        font-size: 11px;
    }

    .quick-card{
        border-radius: 14px;
    }

    .quick-card-icon{
        width: 42px;
        height: 42px;
    }

    .quick-card-icon i{
        font-size: 17px;
    }

    .quick-card-text{
        font-size: 13px;
    }
}
</style>

<div class="dashboard-page">
    <section class="welcome-card">
        <div class="welcome-content">
            <h2>Welcome Back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>!</h2>
            <p>Here’s what’s happening with your booking system today.</p>
        </div>

        <div class="date-box">
            <i class="fas fa-calendar-alt"></i>
            <span id="currentDateTime">Loading date...</span>
        </div>
    </section>

    <section class="dashboard-stats" id="adminStats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="stat-info">
                <h3>Loading...</h3>
                <p>Please wait</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="stat-info">
                <h3>Loading...</h3>
                <p>Please wait</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="stat-info">
                <h3>Loading...</h3>
                <p>Please wait</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-spinner fa-pulse"></i>
            </div>
            <div class="stat-info">
                <h3>Loading...</h3>
                <p>Please wait</p>
            </div>
        </div>
    </section>

    <section class="panel-card">
        <div class="panel-header">
            <div class="panel-title">
                <i class="fas fa-clock"></i>
                <span>Recent Bookings</span>
            </div>
            <a href="bookings.php" class="view-link">
                <span>View All</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="recent-table" id="recentBookings">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Room</th>
                        <th>Customer</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="table-center-state">
                            <i class="fas fa-spinner fa-pulse"></i>
                            <div>Loading bookings...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <section class="quick-actions-wrap">
        <div class="section-heading">
            <i class="fas fa-bolt"></i>
            <span>Quick Actions</span>
        </div>

        <div class="quick-actions">
            <a href="rooms.php" class="quick-card">
                <div class="quick-card-icon">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="quick-card-text">Manage Rooms</div>
            </a>

            <a href="bookings.php" class="quick-card">
                <div class="quick-card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="quick-card-text">View Bookings</div>
            </a>

            <a href="staff.php" class="quick-card">
                <div class="quick-card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="quick-card-text">Manage Staff</div>
            </a>

            <a href="settings.php" class="quick-card">
                <div class="quick-card-icon">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="quick-card-text">Settings</div>
            </a>
        </div>
    </section>
</div>

<script>
function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };

    const el = document.getElementById('currentDateTime');
    if (el) {
        el.textContent = now.toLocaleString('en-US', options);
    }
}

function formatNumber(num) {
    const value = Number(num || 0);
    return value.toLocaleString('en-IN');
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';

    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;

    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
}

function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(value);
    return div.innerHTML;
}

function getStatusClass(status) {
    const s = String(status || '').toLowerCase();

    if (s === 'confirmed' || s === 'completed' || s === 'success') return 'success';
    if (s === 'pending') return 'warning';
    if (s === 'cancelled' || s === 'failed' || s === 'rejected') return 'danger';
    return 'info';
}

function renderStats(data) {
    const statsBox = document.getElementById('adminStats');
    if (!statsBox) return;

    const totalRooms = formatNumber(data.total_rooms || 0);
    const totalBookings = formatNumber(data.total_bookings || 0);
    const totalRevenue = formatNumber(data.total_revenue || 0);
    const pendingApprovals = formatNumber(data.pending_approvals || 0);

    let html = '';
    html += '<div class="stat-card">';
    html += '  <div class="stat-icon"><i class="fas fa-door-open"></i></div>';
    html += '  <div class="stat-info"><h3>' + totalRooms + '</h3><p>Total Rooms</p></div>';
    html += '</div>';

    html += '<div class="stat-card">';
    html += '  <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>';
    html += '  <div class="stat-info"><h3>' + totalBookings + '</h3><p>Total Bookings</p></div>';
    html += '</div>';

    html += '<div class="stat-card">';
    html += '  <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>';
    html += '  <div class="stat-info"><h3>₹' + totalRevenue + '</h3><p>Total Revenue</p></div>';
    html += '</div>';

    html += '<div class="stat-card">';
    html += '  <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>';
    html += '  <div class="stat-info"><h3>' + pendingApprovals + '</h3><p>Pending Approvals</p></div>';
    html += '</div>';

    statsBox.innerHTML = html;
}

function renderStatsError(message) {
    const statsBox = document.getElementById('adminStats');
    if (!statsBox) return;

    statsBox.innerHTML =
        '<div class="stat-card">' +
            '<div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>' +
            '<div class="stat-info"><h3>Error</h3><p>' + escapeHtml(message || 'Failed to load stats') + '</p></div>' +
        '</div>';
}

async function loadDashboardStats() {
    try {
        const res = await apiGet('../api/admin/dashboard-stats');

        if (res && res.status === 'success' && res.data) {
            renderStats(res.data);
        } else {
            renderStatsError((res && res.message) ? res.message : 'Failed to load stats');
            showToast((res && res.message) ? res.message : 'Failed to load dashboard stats', 'error');
        }
    } catch (error) {
        console.error('Dashboard stats error:', error);
        renderStatsError('Network error');
    }
}

async function loadRecentBookings() {
    const tbody = document.querySelector('#recentBookings tbody');
    if (!tbody) return;

    try {
        const res = await apiGet('../api/admin/recent-bookings?limit=5');

        if (res && res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {
            let rows = '';

            res.data.forEach(function (booking) {
                rows += '<tr>';
                rows += '<td>#' + escapeHtml(booking.id) + '</td>';
                rows += '<td class="room-name">' + escapeHtml(booking.room_name) + '</td>';
                rows += '<td>' + escapeHtml(booking.customer_name) + '</td>';
                rows += '<td>' + escapeHtml(formatDate(booking.check_in)) + '</td>';
                rows += '<td>' + escapeHtml(formatDate(booking.check_out)) + '</td>';
                rows += '<td><span class="status-badge ' + getStatusClass(booking.status) + '">' + escapeHtml(booking.status) + '</span></td>';
                rows += '<td class="amount-text">₹' + formatNumber(booking.amount || 0) + '</td>';
                rows += '</tr>';
            });

            tbody.innerHTML = rows;
        } else {
            tbody.innerHTML =
                '<tr>' +
                    '<td colspan="7" class="table-center-state">' +
                        '<i class="fas fa-calendar-times"></i>' +
                        '<div>No recent bookings found</div>' +
                    '</td>' +
                '</tr>';
        }
    } catch (error) {
        console.error('Recent bookings error:', error);
        tbody.innerHTML =
            '<tr>' +
                '<td colspan="7" class="table-center-state">' +
                    '<i class="fas fa-exclamation-circle"></i>' +
                    '<div>Failed to load bookings</div>' +
                '</td>' +
            '</tr>';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    updateDateTime();
    setInterval(updateDateTime, 1000);
    loadDashboardStats();
    loadRecentBookings();
});
</script>

<?php renderFooter('admin'); ?>