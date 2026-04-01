<?php
require_once '../common/auth.php';
requirePageRole([ROLE_SUPPORT]);
require_once '../common/layout.php';

renderHeader('Referral Rewards');
renderSidebarMenu('referrals', 'support');
renderMainContentStart('Referral Rewards', $_SESSION['username'] ?? 'Support');
?>

<div class="support-referrals">

    <!-- Header -->
    <div class="welcome-section">
        <div>
            <h2>Referral Rewards</h2>
            <p>Track all referral earnings and rewards</p>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-header">
            <h3>All Rewards</h3>
        </div>

        <div class="table-responsive">
            <table class="table" id="referralRewardsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Referral</th>
                        <th>Amount</th>
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
async function apiGet(url){
    const res = await fetch(url);
    return await res.json();
}

function formatNumber(n){
    return Number(n || 0).toLocaleString('en-IN');
}

function formatDate(d){
    if(!d) return 'N/A';
    const date = new Date(d);
    return date.toLocaleDateString('en-IN');
}

// LOAD DATA
async function loadReferralRewards(){
    const tbody=document.querySelector('#referralRewardsTable tbody');

    try{
        const res=await apiGet('../api/support/referral-rewards.php');

        if(res.status==='success' && res.data.rewards.length){
            let html='';

            res.data.rewards.forEach(function(r){
                html+=
                '<tr>'
                +'<td>#'+r.id+'</td>'
                +'<td>'+ (r.user_id || 'N/A') +'</td>'
                +'<td>'+ (r.referral_id || 'N/A') +'</td>'
                +'<td>₹'+formatNumber(r.reward_amount)+'</td>'
                +'<td>'+formatDate(r.created_at)+'</td>'
                +'</tr>';
            });

            tbody.innerHTML=html;
        }else{
            tbody.innerHTML='<tr><td colspan="5">No data</td></tr>';
        }

    }catch(e){
        tbody.innerHTML='<tr><td colspan="5">Error loading data</td></tr>';
    }
}

document.addEventListener('DOMContentLoaded', function(){
    loadReferralRewards();
});
</script>

<style>
.support-referrals{
    display:flex;
    flex-direction:column;
    gap:24px;
}

/* Header */
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

/* Responsive */
@media(max-width:768px){
    .table th,.table td{
        font-size:12px;
        padding:8px;
    }
}
</style>

<?php renderFooter('support'); ?>