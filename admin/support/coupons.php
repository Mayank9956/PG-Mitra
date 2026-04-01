<?php
require_once '../common/auth.php';
requirePageRole([ROLE_SUPPORT]);
require_once '../common/layout.php';

renderHeader('Support Coupons');
renderSidebarMenu('coupons', 'support');
renderMainContentStart('Coupons', $_SESSION['username'] ?? 'Support');
?>

<div class="support-coupons">

    <!-- Header -->
    <div class="welcome-section">
        <div>
            <h2>Coupon Management</h2>
            <p>Create and manage discount coupons</p>
        </div>
    </div>

    <!-- Form -->
    <div class="card">
        <div class="table-header">
            <h3>Add New Coupon</h3>
        </div>

        <form id="supportCouponForm" class="form-grid">

            <input type="text" name="code" placeholder="Coupon Code" required>
            <input type="text" name="description" placeholder="Description">

            <input type="number" name="discount_value" placeholder="Discount Value" required>

            <select name="discount_type">
                <option value="fixed">Fixed</option>
                <option value="percentage">Percentage</option>
            </select>

            <input type="number" name="min_order_amount" placeholder="Min Order Amount">
            <input type="number" name="max_discount_amount" placeholder="Max Discount">

            <input type="date" name="valid_from">
            <input type="date" name="valid_to">

            <input type="number" name="usage_limit" placeholder="Usage Limit">

            <button type="submit" class="btn primary">Save Coupon</button>

        </form>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-header">
            <h3>All Coupons</h3>
        </div>

        <div class="table-responsive">
            <table class="table" id="supportCouponsTable">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Min Order</th>
                        <th>Max Discount</th>
                        <th>Limit</th>
                        <th>Status</th>
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

async function apiPost(url,data){
    const res = await fetch(url,{method:'POST',body:data});
    return await res.json();
}

function formatNumber(n){
    return Number(n || 0).toLocaleString('en-IN');
}

function statusClass(s){
    return s == 1 ? 'success' : 'danger';
}

// SUBMIT
document.getElementById('supportCouponForm').addEventListener('submit', async function(e){
    e.preventDefault();

    const res = await apiPost('../api/support/manage-coupons', new FormData(this));
    showAlert(res.message);

    if(res.status === 'success'){
        this.reset();
        loadSupportCoupons();
    }
});

// LOAD COUPONS
async function loadSupportCoupons(){
    const tbody=document.querySelector('#supportCouponsTable tbody');

    try{
        const res=await apiGet('../api/support/list-coupons');

        if(res.status==='success' && res.data.coupons.length){
            let html='';

            res.data.coupons.forEach(function(c){
                html+=
                '<tr>'
                +'<td>'+c.coupon_code+'</td>'
                +'<td>'+c.discount_value+' ('+c.discount_type+')</td>'
                +'<td>₹'+formatNumber(c.min_order_amount)+'</td>'
                +'<td>₹'+formatNumber(c.max_discount_amount)+'</td>'
                +'<td>'+(c.usage_limit || '∞')+'</td>'
                +'<td><span class="status-badge '+statusClass(c.is_active)+'">'+(c.is_active==1?'Active':'Inactive')+'</span></td>'
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
    loadSupportCoupons();
});
</script>

<style>
.support-coupons{
    display:flex;
    flex-direction:column;
    gap:24px;
}

/* Form grid */
.form-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:12px;
}

.form-grid input,
.form-grid select{
    padding:10px;
    border:1px solid #ddd;
    border-radius:8px;
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
}

.status-badge.success{ background:#d1e7dd; color:#0f5132; }
.status-badge.danger{ background:#f8d7da; color:#842029; }

/* Button */
.btn{
    padding:8px 12px;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

.btn.primary{ background:#0d6efd;color:#fff; }

/* Responsive */
@media(max-width:768px){
    .form-grid{
        grid-template-columns:1fr;
    }
}
</style>

<?php renderFooter('support'); ?>