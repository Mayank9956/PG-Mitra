<?php
require_once '../common/auth.php';
requirePageRole([ROLE_SUPPORT]);
require_once '../common/layout.php';

renderHeader('Support Tickets');
renderSidebarMenu('tickets', 'support');
renderMainContentStart('Tickets', $_SESSION['username'] ?? 'Support');
?>

<div class="support-tickets">

    <!-- Header -->
    <div class="welcome-section">
        <div>
            <h2>Support Tickets</h2>
            <p>Manage all customer issues and tickets</p>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-header">
            <h3>All Tickets</h3>
        </div>

        <div class="table-responsive">
            <table class="table" id="ticketsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Issue</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="6">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
async function apiGet(url){
    const res = await fetch(url);
    return await res.json();
}

function formatDate(d){
    if(!d) return 'N/A';
    return new Date(d).toLocaleDateString('en-IN');
}

function statusClass(s){
    s=(s||'').toLowerCase();
    if(s==='resolved') return 'success';
    if(s==='pending') return 'warning';
    if(s==='closed') return 'danger';
    return 'info';
}

// LOAD TICKETS
async function loadTickets(){
    const tbody=document.querySelector('#ticketsTable tbody');

    try{
        const res=await apiGet('../api/support/list-tickets');

        if(res.status==='success' && res.data.tickets.length){
            let html='';

            res.data.tickets.forEach(function(t){
                html+=
                '<tr>'
                +'<td>#'+t.id+'</td>'
                +'<td>'+ (t.user_name || 'User') +'</td>'
                +'<td>'+ (t.issue || '-') +'</td>'
                +'<td><span class="status-badge '+statusClass(t.status)+'">'+t.status+'</span></td>'
                +'<td>'+formatDate(t.created_at)+'</td>'
                +'<td>'
                    +'<button class="btn small primary">View</button> '
                    +'<button class="btn small secondary">Resolve</button>'
                +'</td>'
                +'</tr>';
            });

            tbody.innerHTML=html;
        }else{
            tbody.innerHTML='<tr><td colspan="6">No data</td></tr>';
        }

    }catch(e){
        tbody.innerHTML='<tr><td colspan="6">Error loading data</td></tr>';
    }
}

document.addEventListener('DOMContentLoaded', function(){
    loadTickets();
});
</script>

<style>
.support-tickets{
    display:flex;
    flex-direction:column;
    gap:24px;
}

.welcome-section{
    display:flex;
    justify-content:space-between;
}

/* Table */
.table{
    width:100%;
    border-collapse:collapse;
}

.table th,.table td{
    padding:10px;
    border-bottom:1px solid #eee;
}

/* Status */
.status-badge{
    padding:4px 8px;
    border-radius:8px;
    font-size:12px;
}

.status-badge.success{ background:#d1e7dd; color:#0f5132; }
.status-badge.warning{ background:#fff3cd; color:#664d03; }
.status-badge.danger{ background:#f8d7da; color:#842029; }
.status-badge.info{ background:#cff4fc; color:#055160; }

/* Buttons */
.btn{
    padding:6px 10px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:12px;
}

.btn.primary{ background:#0d6efd;color:#fff; }
.btn.secondary{ background:#f1f1f1; }

.btn.small{ padding:4px 8px;font-size:11px; }

@media(max-width:768px){
    .table th,.table td{
        font-size:12px;
        padding:8px;
    }
}
</style>

<?php renderFooter('support'); ?>