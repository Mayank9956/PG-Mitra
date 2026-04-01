<?php
require_once '../common/auth.php';
requirePageRole([ROLE_SUPPORT]);
require_once '../common/layout.php';

renderHeader('Support Bookings');
renderSidebarMenu('bookings', 'support');
renderMainContentStart('Bookings', $_SESSION['username'] ?? 'Support');
?>

<div class="support-bookings">

    <!-- Header -->
    <div class="welcome-section">
        <div>
            <h2>Bookings Management</h2>
            <p>View and manage all bookings</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="table-header">
            <h3>All Bookings</h3>
        </div>

        <div class="table-responsive">
            <table class="table" id="supportBookingsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Room</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Staff</th>
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
function formatNumber(n){
    return Number(n || 0).toLocaleString('en-IN');
}

function escapeHtml(v){
    if(!v) return '';
    const d=document.createElement('div');
    d.textContent=v;
    return d.innerHTML;
}

function statusClass(s){
    s=(s||'').toLowerCase();
    if(s==='confirmed'||s==='completed') return 'success';
    if(s==='pending') return 'warning';
    if(s==='cancelled') return 'danger';
    return 'info';
}

async function apiGet(url){
    const res = await fetch(url);
    return await res.json();
}

// LOAD BOOKINGS
async function loadSupportBookings(){
    const tbody=document.querySelector('#supportBookingsTable tbody');

    try{
        const res=await apiGet('../api/support/list-bookings');

        if(res.status==='success' && res.data.bookings.length){
            let html='';

            res.data.bookings.forEach(function(b){
                html+=
                '<tr>'
                +'<td>#'+b.id+'</td>'
                +'<td>'+escapeHtml(b.room_name || 'N/A')+'</td>'
                +'<td><span class="status-badge '+statusClass(b.status)+'">'+b.status+'</span></td>'
                +'<td>₹'+formatNumber(b.total_amount)+'</td>'
                +'<td>'+escapeHtml(b.staff_id || 'Not Assigned')+'</td>'
                +'<td>'
                    +'<button class="btn small primary">View</button> '
                    +'<button class="btn small secondary">Assign</button>'
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
    loadSupportBookings();
});
</script>

<style>
.support-bookings{
    display:flex;
    flex-direction:column;
    gap:24px;
}

/* Welcome */
.welcome-section{
    display:flex;
    justify-content:space-between;
    flex-wrap:wrap;
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

/* Status badge */
.status-badge{
    padding:4px 8px;
    border-radius:8px;
    font-size:12px;
    text-transform:capitalize;
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

/* Responsive */
@media(max-width:768px){
    .table th,.table td{
        font-size:12px;
        padding:8px;
    }
}
</style>

<?php renderFooter('support'); ?>