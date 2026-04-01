<?php
require_once '../common/auth.php';
requirePageRole([ROLE_OWNER]);
require_once '../common/layout.php';

renderHeader('My Rooms');
renderSidebarMenu('rooms', 'owner');
renderMainContentStart('My Rooms', $_SESSION['username'] ?? 'Owner');
?>

<style>
.rooms-page{display:flex;flex-direction:column;gap:24px;}

.page-card{
background:#fff;
border:1px solid #edf1f5;
border-radius:20px;
box-shadow:0 4px 14px rgba(0,0,0,0.05);
overflow:hidden;
}

.page-card-header{
display:flex;
justify-content:space-between;
padding:18px 20px;
border-bottom:1px solid #edf1f5;
}

.page-card-title{
font-size:18px;
font-weight:700;
}

.room-list-grid{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:18px;
}

.room-card{
border:1px solid #edf1f5;
border-radius:16px;
overflow:hidden;
background:#fff;
cursor:pointer;
transition:0.2s;
}

.room-card:hover{
transform:translateY(-4px);
box-shadow:0 6px 18px rgba(0,0,0,0.08);
}

.room-image-wrap{
height:180px;
background:#f1f5f9;
}

.room-image{
width:100%;
height:100%;
object-fit:fill;
}

.room-no-image{
display:flex;
justify-content:center;
align-items:center;
height:100%;
color:#999;
}

.room-card-body{padding:5px;}

.room-price{
    color: #ff6b35;
    font-weight: 600;
    font-size: 13px;
}

.status-pill{
padding: 5px 10px;
    border-radius: 8px;
    text-align: center;
    width: -webkit-fill-available;
    font-size: 12px;
    display: inline-block;
    margin-top: 5px;
    font-weight: 600;
}

.empty-state,.error-state{
text-align:center;
padding:40px;
}

@media(max-width:768px){
.room-list-grid{grid-template-columns:1fr;}
}
#ownerRoomList{
    margin: 10px;
}



.status-pending{
background:#ea580c;
color:#fff; 
}

.status-approved{
    background: #16a34a;
    color: #ffffff;
}

.status-rejected{
    background: #ff0000;
    color: #fffcfc;
}
.room-title{color: #11a900;
    font-weight: 500;
    font-size: 14px;}
</style>

<div class="rooms-page">
<div class="page-card">
<div class="page-card-header">
<div class="page-card-title">My Rooms</div>
</div>

<div class="page-card-body">
<div id="ownerRoomList">Loading...</div>
</div>
</div>
</div>

<script>

/* API */
async function apiGet(url){
    const res = await fetch(url);
    return await res.json();
}

function getStatusClass(status){
    status = (status || '').toLowerCase();

    if(status === 'pending') return 'status-pending';
    if(status === 'approved') return 'status-approved';
    if(status === 'rejected') return 'status-rejected';

    return '';
}

/* LOAD ROOMS */
async function loadOwnerRooms(){

    const el = document.getElementById('ownerRoomList');
    el.innerHTML = 'Loading...';

    try{
        const res = await apiGet('../api/owner/list-rooms');

        if(res.status !== 'success'){
            el.innerHTML = '<div class="error-state">Failed to load</div>';
            return;
        }

        const rooms = res.data.rooms;

        if(!rooms.length){
            el.innerHTML = '<div class="empty-state">No Rooms Found</div>';
            return;
        }

        let html = '<div class="room-list-grid">';

        rooms.forEach(r=>{
            html += `
            <div class="room-card" onclick="openRoom(${r.id})">

                <div class="room-image-wrap">
                    ${r.primary_image 
                        ? `<img src="${r.primary_image}" class="room-image">`
                        : `<div class="room-no-image">No Image</div>`}
                </div>

                <div class="room-card-body">
                    <div class="room-title">${r.title}</div>
                     <div class="room-price">₹${r.price}</div>
                    <span class="status-pill ${getStatusClass(r.status)}">${r.status}</span>
                </div>

            </div>`;
        });

        html += '</div>'; // IMPORTANT

        el.innerHTML = html;

    }catch(e){
        console.error(e);
        el.innerHTML = '<div class="error-state">Something went wrong</div>';
    }
}

/* REDIRECT TO DETAILS */
function openRoom(id){
    window.location.href = `room-details.php?id=${id}`;
}

/* INIT */
loadOwnerRooms();

</script>

<?php renderFooter('owner'); ?>