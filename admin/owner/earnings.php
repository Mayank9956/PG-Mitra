<?php
require_once '../common/auth.php';
requirePageRole([ROLE_OWNER]);
require_once '../common/layout.php';

renderHeader('My Earnings');
renderSidebarMenu('earnings', 'owner');
renderMainContentStart('My Earnings', $_SESSION['username'] ?? 'Owner');
?>

<style>
.earnings-page{
    display:flex;
    flex-direction:column;
    gap:24px;
}

.summary-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:18px;
}

.summary-card{
    background:#fff;
    border:1px solid #edf1f5;
    border-radius:18px;
    padding:20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.04);
}

.summary-title{
    font-size:14px;
    color:#64748b;
    margin-bottom:8px;
}

.summary-value{
    font-size:24px;
    font-weight:800;
    color:#102a43;
}

.earnings-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0,1fr));
    gap:18px;
}

.earning-card{
    background:#fff;
    border:1px solid #edf1f5;
    border-radius:18px;
    padding:18px;
    display:flex;
    flex-direction:column;
    gap:10px;
}

.earning-title{
    font-size:16px;
    font-weight:700;
    color:#102a43;
}

.earning-meta{
    font-size:14px;
    color:#475569;
}

.status{
    font-size:12px;
    font-weight:700;
    padding:5px 10px;
    border-radius:999px;
    width:max-content;
}

.status.pending{background:#fff7ed;color:#c2410c;}
.status.confirmed{background:#ecfdf3;color:#15803d;}
.status.cancelled{background:#fef2f2;color:#b91c1c;}
.status.completed{background:#eff6ff;color:#1d4ed8;}

.empty{
    text-align:center;
    padding:40px;
    color:#64748b;
}

@media(max-width:768px){
    .summary-grid,
    .earnings-grid{
        grid-template-columns:1fr;
    }
}
</style>

<div class="earnings-page">

    <div id="earningsSummary"></div>

    <div id="earningsList"></div>

</div>

<script>
function escapeHtml(v){
    if(v==null) return '';
    const d=document.createElement('div');
    d.textContent=v;
    return d.innerHTML;
}

function money(v){
    return Number(v||0).toLocaleString('en-IN');
}

function statusClass(s){
    s=(s||'').toLowerCase();
    if(s==='pending') return 'pending';
    if(s==='confirmed') return 'confirmed';
    if(s==='cancelled') return 'cancelled';
    if(s==='completed') return 'completed';
    return '';
}

async function loadEarnings(){
    const summaryBox=document.getElementById('earningsSummary');
    const listBox=document.getElementById('earningsList');

    summaryBox.innerHTML='Loading...';
    listBox.innerHTML='';

    try{
        const res=await apiGet('../api/owner/earnings');

        if(res && res.status==='success'){
            const d=res.data;

            summaryBox.innerHTML=
                '<div class="summary-grid">'
                +'<div class="summary-card">'
                    +'<div class="summary-title">Total Earnings</div>'
                    +'<div class="summary-value">₹'+money(d.total_earnings)+'</div>'
                +'</div>'
                +'<div class="summary-card">'
                    +'<div class="summary-title">Total Bookings</div>'
                    +'<div class="summary-value">'+money(d.total_bookings)+'</div>'
                +'</div>'
                +'</div>';

            if(d.items && d.items.length){
                let html='<div class="earnings-grid">';

                d.items.forEach(function(item){
                    html+=
                    '<div class="earning-card">'
                        +'<div class="earning-title">'+escapeHtml(item.room_name)+'</div>'
                        +'<div class="status '+statusClass(item.status)+'">'+escapeHtml(item.status)+'</div>'
                        +'<div class="earning-meta">Booking ID: '+item.booking_number+'</div>'
                        +'<div class="earning-meta">Amount: ₹'+money(item.final_amount)+'</div>'
                    +'</div>';
                });

                html+='</div>';
                listBox.innerHTML=html;
            }else{
                listBox.innerHTML='<div class="empty">No earnings data found</div>';
            }

        }else{
            summaryBox.innerHTML='<div class="empty">Failed to load data</div>';
        }

    }catch(e){
        summaryBox.innerHTML='<div class="empty">Error loading earnings</div>';
    }
}

document.addEventListener('DOMContentLoaded', loadEarnings);
</script>

<?php renderFooter('owner'); ?>