<?php
require_once '../common/auth.php';
requirePageRole([ROLE_SUPPORT]);
require_once '../common/layout.php';

renderHeader('Support Dashboard');
renderSidebarMenu('dashboard', 'support');
renderMainContentStart('Dashboard', $_SESSION['username'] ?? 'Support');
?>

<div class="support-dashboard">

    <!-- Welcome -->
    <div class="welcome-section">
        <div>
            <h2>Welcome Back, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Support'); ?>!</h2>
            <p>Here’s what’s happening in support today.</p>
        </div>
        <div class="date-box">
            <i class="fas fa-calendar-alt"></i>
            <span id="currentDateTime"></span>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid" id="supportStats">
        <div class="stat-card">
            <div class="stat-info">
                <h3>Loading...</h3>
                <p>Please wait</p>
            </div>
        </div>
    </div>

    <!-- Recent Activity (Optional future use) -->
    <div class="card">
        <div class="table-header">
            <h3>Recent Support Activity</h3>
        </div>

        <div class="table-responsive">
            <table class="table" id="supportActivity">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Issue</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="5">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function formatNumber(n){
    return Number(n || 0).toLocaleString('en-IN');
}

function updateDateTime(){
    const now = new Date();
    document.getElementById('currentDateTime').textContent =
        now.toLocaleString('en-IN',{dateStyle:'medium',timeStyle:'short'});
}

function statusClass(s){
    s = (s || '').toLowerCase();
    if(s === 'resolved') return 'success';
    if(s === 'pending') return 'warning';
    if(s === 'open') return 'info';
    if(s === 'closed') return 'danger';
    return 'secondary';
}

async function loadSupportStats(){
    const box = document.getElementById('supportStats');

    try{
        const res = await apiGet('../api/support/dashboard-stats');

        if(res.status === 'success'){
            const d = res.data;

            box.innerHTML =
            '<div class="stat-card"><div class="stat-info"><h3>'+formatNumber(d.total_chats)+'</h3><p>Total Chats</p></div></div>'
            +'<div class="stat-card"><div class="stat-info"><h3>'+formatNumber(d.open_tickets)+'</h3><p>Open Tickets</p></div></div>'
            +'<div class="stat-card"><div class="stat-info"><h3>'+formatNumber(d.total_coupons)+'</h3><p>Coupons</p></div></div>'
            +'<div class="stat-card"><div class="stat-info"><h3>'+formatNumber(d.referral_rewards)+'</h3><p>Referral Rewards</p></div></div>';
        }else{
            box.innerHTML = 'Error loading stats';
        }

    }catch(e){
        box.innerHTML = 'Error loading stats';
    }
}

function formatDate(d){
    if(!d) return 'N/A';
    const date = new Date(d);
    return date.toLocaleDateString('en-IN');
}

function escapeHtml(v){
    if(!v) return '';
    const d=document.createElement('div');
    d.textContent=v;
    return d.innerHTML;
}

async function loadSupportActivity(){
    const tbody = document.querySelector('#supportActivity tbody');

    try{
        const res = await apiGet('../api/support/recent-activity?limit=5');

        if(res.status === 'success' && res.data.length){
            let html = '';

            res.data.forEach(function(a){
                html +=
                '<tr>'
                +'<td>#'+a.id+'</td>'
                +'<td>'+escapeHtml(a.user_name)+'</td>'
                +'<td>'+escapeHtml(a.issue)+'</td>'
                +'<td><span class="status-badge '+statusClass(a.status)+'">'+a.status+'</span></td>'
                +'<td>'+formatDate(a.created_at)+'</td>'
                +'</tr>';
            });

            tbody.innerHTML = html;
        }else{
            tbody.innerHTML = '<tr><td colspan="5">No data</td></tr>';
        }

    }catch(e){
        tbody.innerHTML = '<tr><td colspan="5">Error loading data</td></tr>';
    }
}

document.addEventListener('DOMContentLoaded', function(){
    updateDateTime();
    setInterval(updateDateTime,1000);
    loadSupportStats();
      loadSupportActivity();
});
</script>

<style>
.support-dashboard{
    display:flex;
    flex-direction:column;
    gap:24px;
}

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

.table{
    width:100%;
    border-collapse:collapse;
}

.table th,.table td{
    padding:10px;
    border-bottom:1px solid #eee;
}

@media(max-width:768px){
    .stats-grid{
        grid-template-columns:1fr;
    }
}
</style>

<?php renderFooter('support'); ?>