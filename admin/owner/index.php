<?php
require_once '../common/auth.php';
requirePageRole([ROLE_OWNER]);
require_once '../common/layout.php';

renderHeader('Owner Dashboard');
renderSidebarMenu('dashboard', 'owner');
renderMainContentStart('Dashboard', $_SESSION['username'] ?? 'Owner');
?>

<div class="owner-dashboard">

    <!-- Welcome -->
    <div class="welcome-section">
        <div>
            <h2>Welcome Back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Owner'); ?>!</h2>
            <p>Here's what's happening with your rooms today.</p>
        </div>
        <div class="date-box">
            <i class="fas fa-calendar-alt"></i>
            <span id="currentDateTime"></span>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid" id="ownerStats">
        <div class="stat-card">
            <div class="stat-info">
                <h3>Loading...</h3>
                <p>Please wait</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-header">
            <h3>Recent Bookings</h3>
            <a href="bookings.php">View All</a>
        </div>

        <div class="table-responsive">
            <table class="table" id="recentBookings">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room</th>
                        <th>Customer</th>
                        <th>CheckIn</th>
                        <th>CheckOut</th>
                        <th>Status</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="7">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function formatNumber(n){
    return Number(n||0).toLocaleString('en-IN');
}

function escapeHtml(v){
    if(!v) return '';
    const d=document.createElement('div');
    d.textContent=v;
    return d.innerHTML;
}

function formatDate(d){
    if(!d) return 'N/A';
    const date=new Date(d);
    return date.toLocaleDateString('en-IN');
}

function statusClass(s){
    s=(s||'').toLowerCase();
    if(s==='confirmed'||s==='completed') return 'success';
    if(s==='pending') return 'warning';
    if(s==='cancelled') return 'danger';
    return 'info';
}

function updateDateTime(){
    const now=new Date();
    document.getElementById('currentDateTime').textContent =
        now.toLocaleString('en-IN',{dateStyle:'medium',timeStyle:'short'});
}

async function loadDashboardStats(){
    const box=document.getElementById('ownerStats');

    try{
        const res=await apiGet('../api/owner/dashboard-stats');

        if(res.status==='success'){
            const d=res.data;

            box.innerHTML=
            '<div class="stat-card"><div class="stat-info"><h3>'+formatNumber(d.total_rooms)+'</h3><p>Rooms</p></div></div>'
            +'<div class="stat-card"><div class="stat-info"><h3>'+formatNumber(d.total_bookings)+'</h3><p>Bookings</p></div></div>'
            +'<div class="stat-card"><div class="stat-info"><h3>₹'+formatNumber(d.total_earnings)+'</h3><p>Earnings</p></div></div>'
            +'<div class="stat-card"><div class="stat-info"><h3>'+formatNumber(d.pending_rooms)+'</h3><p>Pending</p></div></div>';
        }else{
            box.innerHTML='Error loading stats';
        }

    }catch(e){
        box.innerHTML='Error loading stats';
    }
}

async function loadRecentBookings(){
    const tbody=document.querySelector('#recentBookings tbody');

    try{
        const res=await apiGet('../api/owner/recent-bookings?limit=5');

        if(res.status==='success' && res.data.length){
            let html='';

            res.data.forEach(function(b){
                html+=
                '<tr>'
                +'<td>#'+b.id+'</td>'
                +'<td>'+escapeHtml(b.room_name)+'</td>'
                +'<td>'+escapeHtml(b.customer_name)+'</td>'
                +'<td>'+formatDate(b.check_in)+'</td>'
                +'<td>'+formatDate(b.check_out)+'</td>'
                +'<td><span class="status-badge '+statusClass(b.status)+'">'+b.status+'</span></td>'
                +'<td>₹'+formatNumber(b.amount)+'</td>'
                +'</tr>';
            });

            tbody.innerHTML=html;
        }else{
            tbody.innerHTML='<tr><td colspan="7">No data</td></tr>';
        }

    }catch(e){
        tbody.innerHTML='<tr><td colspan="7">Error</td></tr>';
    }
}

document.addEventListener('DOMContentLoaded', function(){
    updateDateTime();
    setInterval(updateDateTime,1000);
    loadDashboardStats();
    loadRecentBookings();
});
</script>

<style>
.owner-dashboard{display:flex;flex-direction:column;gap:24px;}

.welcome-section{
    display:flex;
    justify-content:space-between;
    flex-wrap:wrap;
    gap:10px;
}

.date-box{
    background:#fff;
    padding:10px 14px;
    border-radius:10px;
}

.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:16px;
}

.stat-card{
    background:#fff;
    padding:16px;
    border-radius:14px;
    border:1px solid #eee;
}

.table{width:100%;border-collapse:collapse;}
.table th,.table td{padding:10px;border-bottom:1px solid #eee;}

@media(max-width:768px){
    .stats-grid{grid-template-columns:1fr;}
}
</style>

<?php renderFooter('owner'); ?>