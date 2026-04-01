<?php
require_once '../common/auth.php';
requirePageRole([ROLE_OWNER]);
require_once '../common/layout.php';

renderHeader('Room Details');
renderSidebarMenu('rooms', 'owner');
renderMainContentStart('Room Details', $_SESSION['username'] ?? 'Owner');
?>

<style>

/* PAGE */
.details-page{display:flex;flex-direction:column;gap:20px;}

/* Container center everything horizontally */
.room-title-container {
  display: flex;
  /*justify-content: center;*/
  align-items: center;    /* vertical center */
  margin: 10px 0;
}


.room-title {
  display: flex;
  align-items: center; 
  gap: 8px;          
  font-size: 16px;
  font-weight: 600;
}

/* PG Badge */
.pg-badge {
  display: inline-block;
  font-size: 12px;
  font-weight: 600;
  padding: 4px 10px;
  border-radius: 15px;
  color: #fff;
}

/* Colors by type */
.pg-badge.men {
  background-color: #007bff;
}

.pg-badge.women {
  background-color: #e91e63;
}

.pg-badge.unisex {
  background-color: #4caf50;
}
/* CARD */
.details-card{
background:#fff;
border-radius:16px;
padding:20px;
border:1px solid #edf1f5;
}

/* BACK */
.back-btn{
    cursor: pointer;
    padding: 5px;
    width: 100px;
    text-align: center;
    background: #ff783a;
    color: #ffffff;
    font-weight: 600;
}

/* SLIDER */
.slider{
position:relative;
width:100%;
height:280px;
overflow:hidden;
border-radius:14px;
background:#000;
}

.slider img{
width:100%;
height:280px;
object-fit:cover;
display:none;
}

.slider img.active{
display:block;
}

/* BUTTONS */
.slider-btn{
position:absolute;
top:50%;
transform:translateY(-50%);
background:rgba(0,0,0,0.5);
color:#fff;
border:none;
padding:10px 14px;
cursor:pointer;
border-radius:8px;
font-size:18px;
}

.slider-btn.prev{left:10px;}
.slider-btn.next{right:10px;}

/* THUMBNAILS */
.thumbnail-row{
display:flex;
gap:8px;
margin-top:10px;
overflow-x:auto;
}

.thumbnail{
width:70px;
height:50px;
object-fit:cover;
border-radius:6px;
cursor:pointer;
opacity:0.6;
border:2px solid transparent;
}

.thumbnail.active{
opacity:1;
border-color:#ff6b35;
}

/* TEXT */
.room-title{font-size:22px;font-weight:700;margin-top:10px;}
.room-price{color:#ff6b35;font-weight:700;margin-top:5px;}
.room-desc{margin-top:10px;color:#555;}

/* STATUS */
.status-pill{
padding:6px 12px;
border-radius:20px;
font-size:13px;
font-weight:600;
display:inline-block;
margin-top:10px;
}

.status-pending{background:#fff7ed;color:#ea580c;}
.status-approved{background:#ecfdf5;color:#16a34a;}
.status-rejected{background:#fef2f2;color:#dc2626;}

@media(max-width:768px){
.slider{height:220px;}
.slider img{height:220px;}
}

/* INFO */
.room-info{
margin-top:8px;
color:#444;
}

/* RATING */
.room-rating{
margin-top:8px;
font-weight:600;
color:#f59e0b;
}

/* SECTION */
.room-section{
margin-top:15px;
}

.section-title{
font-weight:700;
margin-bottom:6px;
}

/* FACILITIES */
.facility-list{
display:flex;
flex-wrap:wrap;
gap:6px;
}

.facility{
background:#f1f5f9;
padding:5px 10px;
border-radius:20px;
font-size:12px;
}
.text-blue-500{color:blue}
.rating-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: #878787;
  color: #fff;
  font-size: 14px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 20px;
}

.rating-badge i {
  color: #ffd700; /* gold star */
}

</style>

<div class="details-page">

<div class="back-btn" onclick="history.back()">Back</div>

<div id="roomDetails">Loading...</div>

</div>

<script>

let currentIndex = 0;
let images = [];
let autoSlide;

/* STATUS CLASS */
function getStatusClass(status){
    status = (status || '').toLowerCase();
    if(status === 'pending') return 'status-pending';
    if(status === 'approved') return 'status-approved';
    if(status === 'rejected') return 'status-rejected';
    return '';
}

/* SLIDER FUNCTIONS */
function showImage(index){
    const imgs = document.querySelectorAll('.slider img');
    const thumbs = document.querySelectorAll('.thumbnail');

    if(!imgs.length) return;

    currentIndex = (index + imgs.length) % imgs.length;

    imgs.forEach(i=>i.classList.remove('active'));
    thumbs.forEach(t=>t.classList.remove('active'));

    imgs[currentIndex].classList.add('active');
    if(thumbs[currentIndex]) thumbs[currentIndex].classList.add('active');
}

function nextImage(){ showImage(currentIndex + 1); }
function prevImage(){ showImage(currentIndex - 1); }

/* AUTO SLIDE */
function startAuto(){
    autoSlide = setInterval(nextImage, 3000);
}
function stopAuto(){
    clearInterval(autoSlide);
}

/* SWIPE */
function enableSwipe(){
    const slider = document.querySelector('.slider');
    let startX = 0;

    slider.addEventListener('touchstart', e=>{
        startX = e.touches[0].clientX;
        stopAuto();
    });

    slider.addEventListener('touchend', e=>{
        let endX = e.changedTouches[0].clientX;

        if(startX - endX > 50) nextImage();
        else if(endX - startX > 50) prevImage();

        startAuto();
    });
}

/* LOAD ROOM */
async function loadRoom(){

    const id = new URL(window.location.href).searchParams.get('id');
    const el = document.getElementById('roomDetails');

    try{
        const res = await fetch(`../api/owner/get-room?room_id=${id}`);
        const data = await res.json();

        if(data.status !== 'success'){
            el.innerHTML = 'Failed to load';
            return;
        }

        const r = data.data.room;
        images = r.images || [];

        let sliderHTML = '';

        if(images.length){
            sliderHTML = `
            <div class="slider">
                ${images.map((img,i)=>`
                    <img src="${img.image_url}" class="${i===0?'active':''}">
                `).join('')}
                <button class="slider-btn prev" onclick="prevImage()">‹</button>
                <button class="slider-btn next" onclick="nextImage()">›</button>
            </div>

            <div class="thumbnail-row">
                ${images.map((img,i)=>`
                    <img src="${img.image_url}" 
                         class="thumbnail ${i===0?'active':''}"
                         onclick="showImage(${i})">
                `).join('')}
            </div>
            `;
        } else if(r.primary_image){
            sliderHTML = `
            <div class="slider">
                <img src="${r.primary_image}" class="active">
            </div>`;
        } else {
            sliderHTML = `<div>No Images</div>`;
        }

   el.innerHTML = `
<div class="details-card">

    ${sliderHTML}
    
<div class="room-title-container">
  <div class="room-title">
    ${r.title} 
    <span class="pg-badge ${r.pg_type.toLowerCase()}">
      ${r.pg_type.charAt(0).toUpperCase() + r.pg_type.slice(1).toLowerCase()}
    </span>
  </div>
</div>


    <div class="room-price">₹${r.price}</div>

    <span class="status-pill ${getStatusClass(r.status)}">
        ${r.status}
    </span>

<div class="room-rating">
  <span class="rating-badge">
    <i class="fa-solid fa-star"></i>
    ${r.rating || '4.2'} / 5
  </span>
</div>

    <div class="room-info">
        <i class="fas fa-map-marker-alt text-blue-500"></i> ${r.location || 'Address not available'}
    </div>
       <div class="room-info">
         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ff0000" viewBox="0 0 24 24">
    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/>
  </svg> ${r.distance_text || 'Distance not available'}
    </div>

    <div class="room-info">
        <i class="fas fa-phone text-blue-500"></i> ${r.host_phone || 'Not provided'}
    </div>

    <!-- 🛎️ FACILITIES -->
    <div class="room-section">
        <div class="section-title">Facilities</div>
        <div class="facility-list">
            ${
                r.facilities 
                ? r.facilities.split(',').map(f=>`<span class="facility">${f.trim()}</span>`).join('')
                : '<span>No facilities</span>'
            }
        </div>
    </div>

    <!-- 📝 ABOUT -->
    <div class="room-section">
        <div class="section-title">About</div>
        <div class="room-desc">
            ${r.description || 'No description available'}
        </div>
    </div>

</div>
`;

        startAuto();
        enableSwipe();

    }catch(e){
        console.error(e);
        el.innerHTML = 'Something went wrong';
    }
}

/* INIT */
loadRoom();

</script>

<?php renderFooter('owner'); ?>