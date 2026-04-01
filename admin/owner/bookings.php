<?php
require_once '../common/auth.php';
requirePageRole([ROLE_OWNER]);
require_once '../common/layout.php';

renderHeader('Owner Bookings');
renderSidebarMenu('bookings', 'owner');
renderMainContentStart('My Bookings', $_SESSION['username'] ?? 'Owner');
?>

<style>

.booking-grid{
display:grid;
grid-template-columns:repeat(2,1fr);
gap:18px;
}

.booking-card{
border:1px solid #edf1f5;
border-radius:16px;
padding:16px;
background:#fff;
display:flex;
flex-direction:column;
gap:10px;
}

.booking-header{
display:flex;
justify-content:space-between;
align-items:center;
}

.booking-id{
font-weight:700;
}

.booking-row{
font-size:14px;
color:#334155;
}

/* STATUS */
.status-pill{
padding:5px 10px;
border-radius:20px;
font-size:12px;
font-weight:600;
}

.status-pending{background:#fff7ed;color:#ea580c;}
.status-confirmed{background:#ecfdf5;color:#16a34a;}
.status-cancelled{background:#fef2f2;color:#dc2626;}
.status-completed{background:#eff6ff;color:#1d4ed8;}

/* ACTION */
.action-row{
display:flex;
gap:8px;
margin-top:10px;
}

.select-control{
padding:8px;
border-radius:8px;
border:1px solid #ccc;
}

.btn{
padding:8px 12px;
border:none;
border-radius:8px;
background:#ff6b35;
color:#fff;
cursor:pointer;
}

.empty-state{
text-align:center;
padding:40px;
}

@media(max-width:768px){
.booking-grid{grid-template-columns:1fr;}
}

</style>

<div id="ownerBookingList">Loading...</div>

<script>

/* HELPERS */
function statusClass(s){
s=(s||'').toLowerCase();
if(s==='pending') return 'status-pending';
if(s==='confirmed') return 'status-confirmed';
if(s==='cancelled') return 'status-cancelled';
if(s==='completed') return 'status-completed';
return '';
}

function formatMoney(v){
return Number(v||0).toLocaleString('en-IN');
}

/* LOAD */
async function loadOwnerBookings(){

const el=document.getElementById('ownerBookingList');
el.innerHTML='Loading...';

try{
const res=await fetch('../api/owner/list-bookings');
const data=await res.json();

if(data.status!=='success'){
el.innerHTML='Error';
return;
}

const bookings=data.data.bookings;

if(!bookings.length){
el.innerHTML='<div class="empty-state">No bookings</div>';
return;
}

let html='<div class="booking-grid">';

bookings.forEach(b => {
    html += `
    <div class="booking-card">

        <div class="booking-header">
            <div class="booking-id">#${b.id}</div>
            <span class="status-pill ${statusClass(b.status)}">${b.status}</span>
        </div>

        <div class="booking-row"><i class="fas fa-hotel"></i> Room: ${b.room_name}</div>
        <div class="booking-row"><i class="fas fa-user"></i> ${b.user_name}</div>
        <div class="booking-row"><i class="fas fa-phone"></i> ${b.user_phone}</div>
        <div class="booking-row"><i class="fas fa-envelope"></i> ${b.user_email}</div>

        <div class="booking-row"><i class="fas fa-calendar-alt"></i> ${b.check_in} → ${b.check_out}</div>

        <div class="booking-row"><i class="fas fa-indian-rupee-sign"></i> ₹${formatMoney(b.final_amount)}</div>

        <div class="action-row">
            <select id="status_${b.id}" class="select-control">
                <option value="pending" ${b.status==='pending'?'selected':''}>Pending</option>
                <option value="confirmed" ${b.status==='confirmed'?'selected':''}>Confirmed</option>
                <option value="cancelled" ${b.status==='cancelled'?'selected':''}>Cancelled</option>
                <option value="completed" ${b.status==='completed'?'selected':''}>Completed</option>
            </select>

            <button class="btn" onclick="updateStatus(${b.id})">Update</button>
        </div>

    </div>
    `;
});

html += '</div>';
el.innerHTML = html;

}catch(e){
console.error(e);
el.innerHTML='Error loading';
}
}

/* UPDATE */
async function updateStatus(id){

const status=document.getElementById('status_'+id).value;

let fd=new FormData();
fd.append('booking_id',id);
fd.append('status',status);

const res=await fetch('../api/owner/booking-status-update',{
method:'POST',
body:fd
});

const data=await res.json();
alert(data.message);

loadOwnerBookings();
}

/* INIT */
loadOwnerBookings();

</script>

<?php renderFooter('owner'); ?>