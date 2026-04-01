<?php
require_once '../common/auth.php';
requirePageRole([ROLE_ADMIN]);
require_once '../common/layout.php';


renderHeader('Manage Rooms');
renderSidebarMenu('rooms', 'admin');
renderMainContentStart('Manage Rooms', $_SESSION['username'] ?? 'Admin');
?>

<style>
/* ========== MAIN LAYOUT ========== */
.rooms-page{
    display:flex;
    flex-direction:column;
    gap:24px;
}

.page-card{
    background:#fff;
    border:1px solid #edf1f5;
    border-radius:20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.04);
    overflow:hidden;
}

.page-card-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
    padding:18px 20px;
    border-bottom:1px solid #edf1f5;
}

.page-card-title{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:18px;
    font-weight:700;
    color:#102a43;
}

.page-card-title i{
    color:#ff6b35;
}

.page-card-body{
    padding:20px;
}

/* ========== ROOMS GRID ========== */
.room-list-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:24px;
}

.room-card{
    background:#fff;
    border:1px solid #e9edf2;
    border-radius:24px;
    box-shadow:0 4px 12px rgba(0,0,0,0.03);
    overflow:hidden;
    transition:all 0.2s ease;
}

.room-card:hover{
    box-shadow:0 12px 24px rgba(0,0,0,0.08);
    transform:translateY(-2px);
}

.room-image-wrap{
    width:100%;
    height:240px;
    background:#f1f5f9;
    overflow:hidden;
    position:relative;
}

.room-image{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:transform 0.3s ease;
}

.room-card:hover .room-image{
    transform:scale(1.02);
}

.room-no-image{
    width:100%;
    height:100%;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    gap:12px;
    background:#f8fafc;
    color:#94a3b8;
}

.room-no-image i{
    font-size:42px;
    color:#cbd5e1;
}

.room-card-body{
    padding:20px;
    display:flex;
    flex-direction:column;
    gap:14px;
}

.room-card-header-row{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
    flex-wrap:wrap;
}

.room-title{
    font-size:1.25rem;
    font-weight:700;
    color:#0f172a;
    margin:0;
    line-height:1.3;
    flex:1;
}

.room-price{
    font-size:1.3rem;
    font-weight:800;
    color:#ff6b35;
    white-space:nowrap;
}

.status-pill{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:4px 12px;
    border-radius:40px;
    font-size:12px;
    font-weight:600;
    text-transform:capitalize;
    width:fit-content;
}

.status-pill.approved{
    background:#e0f2e9;
    color:#0c6b4b;
}
.status-pill.pending{
    background:#fff1e6;
    color:#c2410c;
}
.status-pill.rejected{
    background:#fee9e6;
    color:#b91c1c;
}
.status-pill.default{
    background:#eef2ff;
    color:#1e40af;
}

/* Room details grid */
.room-details-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill, minmax(200px,1fr));
    gap:12px;
    background:#f9fafc;
    padding:14px 16px;
    border-radius:18px;
    margin:4px 0;
}

.detail-item{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:13px;
    color:#334155;
}

.detail-item i{
    width:18px;
    color:#ff6b35;
    font-size:14px;
}

.detail-item strong{
    font-weight:600;
    color:#0f172a;
    margin-right:4px;
}

.room-description{
    color:#475569;
    font-size:14px;
    line-height:1.5;
    background:#ffffff;
    padding:12px 0;
    border-top:1px solid #edf2f7;
    border-bottom:1px solid #edf2f7;
    margin:6px 0;
}

.host-info{
    display:flex;
    align-items:center;
    gap:12px;
    padding:10px 0;
    border-bottom:1px solid #edf2f7;
    font-size:13px;
    color:#334155;
}

.host-info i{
    color:#ff6b35;
    width:18px;
}

.room-card-actions{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    margin-top:8px;
    padding-top:8px;
}

/* Buttons */
.btn{
    border:none;
    outline:none;
    cursor:pointer;
    border-radius:40px;
    padding:8px 16px;
    font-size:13px;
    font-weight:600;
    transition:all 0.2s ease;
    display:inline-flex;
    align-items:center;
    gap:8px;
}

.btn-sm{
    padding:6px 14px;
    font-size:12px;
}

.btn-primary{
    background:#ff6b35;
    color:#fff;
}
.btn-primary:hover{
    background:#e55a2a;
    transform:translateY(-1px);
}

.btn-success{
    background:#10b981;
    color:#fff;
}
.btn-success:hover{
    background:#0e9f6e;
}

.btn-warning{
    background:#f59e0b;
    color:#fff;
}
.btn-warning:hover{
    background:#e08c00;
}

.btn-danger{
    background:#ef4444;
    color:#fff;
}
.btn-danger:hover{
    background:#dc2626;
}

.btn-outline{
    background:transparent;
    border:1px solid #cbd5e1;
    color:#1e293b;
}
.btn-outline:hover{
    background:#f8fafc;
    border-color:#ff6b35;
}

/* Modal Styles */
.modal{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.5);
    backdrop-filter:blur(3px);
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
    z-index:1300;
}
.modal.hidden{
    display:none;
}
.modal-dialog{
    width:100%;
    max-width:800px;
    max-height:90vh;
    background:#fff;
    border-radius:28px;
    overflow:auto;
    box-shadow:0 25px 40px rgba(0,0,0,0.2);
}
.modal-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:18px 24px;
    border-bottom:1px solid #edf2f7;
    position:sticky;
    top:0;
    background:#fff;
    z-index:10;
}
.modal-title{
    font-size:1.25rem;
    font-weight:700;
    display:flex;
    align-items:center;
    gap:10px;
    color:#0f172a;
}
.modal-body{
    padding:24px;
}
.modal-actions{
    display:flex;
    gap:12px;
    justify-content:flex-end;
    padding-top:20px;
    border-top:1px solid #edf2f7;
    margin-top:20px;
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:18px;
}
.form-group{
    display:flex;
    flex-direction:column;
    gap:6px;
}
.form-group.full-width{
    grid-column:1/-1;
}
.form-label{
    font-size:13px;
    font-weight:600;
    color:#334155;
}
.form-control, .form-select, .form-textarea{
    padding:10px 14px;
    border:1px solid #d1d9e8;
    border-radius:14px;
    font-size:14px;
    transition:0.2s;
}
.form-control:focus, .form-select:focus, .form-textarea:focus{
    border-color:#ff6b35;
    outline:none;
    box-shadow:0 0 0 3px rgba(255,107,53,0.1);
}
.form-textarea{
    min-height:80px;
    resize:vertical;
}

.current-image-preview{
    background:#f8fafc;
    border-radius:16px;
    padding:12px;
    text-align:center;
}
.current-image-preview img{
    max-width:100%;
    max-height:180px;
    border-radius:12px;
    object-fit:cover;
}
.helper-text{
    font-size:11px;
    color:#6c757d;
    margin-top:4px;
}

.loading-state, .empty-state{
    text-align:center;
    padding:50px 20px;
    color:#64748b;
}
.empty-state i{
    font-size:48px;
    color:#cbd5e1;
    margin-bottom:16px;
}

.rooms-toolbar{
    display:flex;
    align-items:center;
    gap:12px;
}
.rooms-summary{
    font-size:14px;
    background:#f1f5f9;
    padding:6px 14px;
    border-radius:40px;
    color:#1e293b;
}

@media (max-width: 900px){
    .room-list-grid{
        grid-template-columns:1fr;
    }
    .form-grid{
        grid-template-columns:1fr;
    }
}
@media (max-width: 640px){
    .room-card-header-row{
        flex-direction:column;
    }
    .room-card-actions{
        flex-direction:column;
    }
    .btn{
        justify-content:center;
    }
}
</style>

<div class="rooms-page">
    <section class="page-card">
        <div class="page-card-header">
            <div class="page-card-title">
                <i class="fas fa-hotel"></i>
                <span>All Rooms Inventory</span>
            </div>
            <div class="rooms-toolbar">
                <div class="rooms-summary" id="roomCountText">Loading rooms...</div>
            </div>
        </div>
        <div class="page-card-body">
            <div id="roomList">
                <div class="loading-state"><i class="fas fa-spinner fa-pulse fa-2x"></i><div>Loading rooms...</div></div>
            </div>
        </div>
    </section>
</div>

<!-- EDIT MODAL -->
<div id="editRoomModal" class="modal hidden">
    <div class="modal-dialog">
        <div class="modal-header">
            <div class="modal-title"><i class="fas fa-pen"></i> Edit Room Details</div>
            <button type="button" class="icon-btn" onclick="closeAdminEditModal()" style="background:transparent; border:none; font-size:24px;">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editRoomForm" onsubmit="return false;">
                <input type="hidden" id="edit_room_id">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Room Title *</label>
                        <input type="text" id="edit_title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Price (₹) *</label>
                        <input type="number" id="edit_price" class="form-control" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Security Deposit</label>
                        <input type="number" id="edit_security_deposit" class="form-control" step="0.01">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bedrooms</label>
                        <input type="number" id="edit_bedrooms" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bathrooms</label>
                        <input type="number" id="edit_bathrooms" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Max Guests</label>
                        <input type="number" id="edit_max_guests" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Room Type</label>
                        <input type="text" id="edit_room_type" class="form-control" placeholder="e.g., Private, Shared">
                    </div>
                    <div class="form-group">
                        <label class="form-label">PG Type</label>
                        <input type="text" id="edit_pg_type" class="form-control" placeholder="Men/Women/Co-ed">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location (City/Area)</label>
                        <input type="text" id="edit_location" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <input type="text" id="edit_address" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">City</label>
                        <input type="text" id="edit_city" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Distance (km)</label>
                        <input type="text" id="edit_distance" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status *</label>
                        <select id="edit_status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Description</label>
                        <textarea id="edit_description" class="form-textarea" rows="3"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Amenities (comma separated)</label>
                        <input type="text" id="edit_amenities" class="form-control" placeholder="WiFi, AC, Parking, ...">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Facilities (comma separated)</label>
                        <input type="text" id="edit_facilities" class="form-control" placeholder="Gym, Laundry, ...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Host Name</label>
                        <input type="text" id="edit_host_name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Host Phone</label>
                        <input type="text" id="edit_host_phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Host Email</label>
                        <input type="email" id="edit_host_email" class="form-control">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Current Image</label>
                        <div id="currentImagePreview" class="current-image-preview"></div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-outline" onclick="closeAdminEditModal()">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitRoomEdit()">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Helper: Safe image path
function getSafeImagePath(url) {
    if (!url) return '';
    if (url.startsWith('http')) return url;
    if (url.startsWith('/')) return url;
    return '../uploads/rooms/' + url;
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    }).replace(/[\uD800-\uDBFF][\uDC00-\uDFFF]/g, function(c) {
        return c;
    });
}

function formatPrice(price) {
    let num = Number(price) || 0;
    return num.toLocaleString('en-IN');
}

function getStatusClass(status) {
    let s = (status || '').toLowerCase();
    if (s === 'approved') return 'approved';
    if (s === 'pending') return 'pending';
    if (s === 'rejected') return 'rejected';
    return 'default';
}

function setRoomCountText(count) {
    let el = document.getElementById('roomCountText');
    if(el) el.textContent = count + ' room' + (count === 1 ? '' : 's') + ' available';
}

let allRooms = [];

function renderRoomCard(room) {
    let primaryImg = room.primary_image;
    let imageHtml = primaryImg 
        ? `<div class="room-image-wrap"><img class="room-image" src="${primaryImg}" alt="${escapeHtml(room.title)}" onerror="this.onerror=null;this.src='';this.parentElement.innerHTML='<div class=\'room-no-image\'><i class=\'fas fa-image\'></i><span>Image failed</span></div>'"></div>`
        : `<div class="room-image-wrap"><div class="room-no-image"><i class="fas fa-image"></i><span>No Image</span></div></div>`;
    
    let amenitiesList = room.amenities ? (typeof room.amenities === 'string' ? room.amenities : (Array.isArray(room.amenities) ? room.amenities.join(', ') : '')) : '';
    let facilitiesList = room.facilities ? (typeof room.facilities === 'string' ? room.facilities : (Array.isArray(room.facilities) ? room.facilities.join(', ') : '')) : '';
    
    return `
    <div class="room-card" data-room-id="${room.id}">
        ${imageHtml}
        <div class="room-card-body">
            <div class="room-card-header-row">
                <h3 class="room-title">${escapeHtml(room.title)}</h3>
                <div class="room-price">₹${formatPrice(room.price)}</div>
            </div>
            <div class="status-pill ${getStatusClass(room.status)}">
                <i class="fas ${room.status === 'approved' ? 'fa-check-circle' : (room.status === 'pending' ? 'fa-clock' : 'fa-times-circle')}"></i> ${escapeHtml(room.status || 'unknown')}
            </div>
            <div class="room-details-grid">
                <div class="detail-item"><i class="fas fa-bed"></i> <strong>${room.bedrooms || 0}</strong> Bedrooms</div>
                <div class="detail-item"><i class="fas fa-bath"></i> <strong>${room.bathrooms || 0}</strong> Bathrooms</div>
                <div class="detail-item"><i class="fas fa-users"></i> Max: <strong>${room.max_guests || 1}</strong> guests</div>
                <div class="detail-item"><i class="fas fa-door-open"></i> ${escapeHtml(room.room_type || 'N/A')}</div>
                <div class="detail-item"><i class="fas fa-venus-mars"></i> ${escapeHtml(room.pg_type || 'Co-ed')}</div>
                <div class="detail-item"><i class="fas fa-map-marker-alt"></i> ${escapeHtml(room.location || room.city || 'Location')}</div>
                ${room.distance ? `<div class="detail-item"><i class="fas fa-road"></i> Distance: ${escapeHtml(room.distance)} ${room.distance_text ? '('+escapeHtml(room.distance_text)+')' : ''}</div>` : ''}
                <div class="detail-item"><i class="fas fa-shield-alt"></i> Deposit: ₹${formatPrice(room.security_deposit)}</div>
                ${room.non_refundable ? `<div class="detail-item"><i class="fas fa-exclamation-triangle"></i> Non-refundable fee: ₹${formatPrice(room.non_refundable)}</div>` : ''}
            </div>
            ${room.description ? `<div class="room-description"><i class="fas fa-align-left"></i> ${escapeHtml(room.description.substring(0, 180))}${room.description.length > 180 ? '...' : ''}</div>` : ''}
            ${amenitiesList || facilitiesList ? `<div class="detail-item" style="flex-wrap:wrap;"><i class="fas fa-concierge-bell"></i> <strong>Amenities/Facilities:</strong> ${escapeHtml(amenitiesList)} ${facilitiesList ? '| '+escapeHtml(facilitiesList) : ''}</div>` : ''}
            <div class="host-info">
                <i class="fas fa-user-circle"></i> <strong>${escapeHtml(room.host_name || 'Host')}</strong> 
                ${room.host_phone ? `• <i class="fas fa-phone-alt"></i> ${escapeHtml(room.host_phone)}` : ''}
                ${room.host_email ? `• ✉️ ${escapeHtml(room.host_email)}` : ''}
            </div>
            <div class="room-card-actions">
                <button class="btn btn-primary btn-sm" onclick="openAdminEditModalById(${room.id})"><i class="fas fa-edit"></i> Edit Full Details</button>
                <button class="btn btn-success btn-sm" onclick="approveRoom(${room.id}, 'approved')"><i class="fas fa-check"></i> Approve</button>
                <button class="btn btn-warning btn-sm" onclick="approveRoom(${room.id}, 'rejected')"><i class="fas fa-ban"></i> Reject</button>
                <button class="btn btn-danger btn-sm" onclick="deleteRoom(${room.id})"><i class="fas fa-trash-alt"></i> Delete</button>
            </div>
        </div>
    </div>`;
}

async function loadRooms() {
    let container = document.getElementById('roomList');
    if(!container) return;
    container.innerHTML = '<div class="loading-state"><i class="fas fa-spinner fa-pulse fa-2x"></i><div>Loading rooms...</div></div>';
    try {
        let res = await apiGet('../api/admin/list-rooms');
        if(res && res.status === 'success' && res.data && Array.isArray(res.data.rooms)) {
            allRooms = res.data.rooms;
            setRoomCountText(allRooms.length);
            if(allRooms.length === 0) {
                container.innerHTML = '<div class="empty-state"><i class="fas fa-door-closed"></i><h3>No rooms found</h3><p>No rooms added yet.</p></div>';
                return;
            }
            let html = '<div class="room-list-grid">';
            allRooms.forEach(room => { html += renderRoomCard(room); });
            html += '</div>';
            container.innerHTML = html;
        } else {
            container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Failed to load</h3><p>' + escapeHtml(res?.message || 'Unknown error') + '</p></div>';
        }
    } catch(e) {
        console.error(e);
        container.innerHTML = '<div class="empty-state"><i class="fas fa-wifi"></i><h3>Network error</h3><p>Please check your connection.</p></div>';
    }
}

async function approveRoom(id, status) {
    let action = status === 'approved' ? 'approve' : 'reject';
    if(!confirm(`Are you sure you want to ${action} this room?`)) return;
    let fd = new FormData();
    fd.append('room_id', id);
    fd.append('status', status);
    try {
        let res = await apiPost('../api/admin/room-approval', fd);
        showToast(res?.message || `Room ${action}d`, res?.status === 'success' ? 'success' : 'error');
        loadRooms();
    } catch(e) {
        showToast('Failed to update status', 'error');
    }
}

async function deleteRoom(id) {
    if(!confirm('Permanently delete this room? This action cannot be undone.')) return;
    let fd = new FormData();
    fd.append('room_id', id);
    try {
        let res = await apiPost('../api/admin/delete-room', fd);
        showToast(res?.message || 'Room deleted', 'success');
        loadRooms();
    } catch(e) {
        showToast('Delete failed', 'error');
    }
}

// Fetch full room data for modal
async function openAdminEditModalById(roomId) {
    let room = allRooms.find(r => r.id == roomId);
    if(!room) {
        // fallback fetch
        try {
            let res = await apiGet(`../api/admin/get-room?id=${roomId}`);
            if(res && res.status === 'success') room = res.data;
        } catch(e) {}
    }
    if(!room) { showToast('Could not load room data', 'error'); return; }
    
    // populate modal fields
    document.getElementById('edit_room_id').value = room.id;
    document.getElementById('edit_title').value = room.title || '';
    document.getElementById('edit_price').value = room.price || 0;
    document.getElementById('edit_security_deposit').value = room.security_deposit || 0;
    document.getElementById('edit_bedrooms').value = room.bedrooms || '';
    document.getElementById('edit_bathrooms').value = room.bathrooms || '';
    document.getElementById('edit_max_guests').value = room.max_guests || '';
    document.getElementById('edit_room_type').value = room.room_type || '';
    document.getElementById('edit_pg_type').value = room.pg_type || '';
    document.getElementById('edit_location').value = room.location || '';
    document.getElementById('edit_address').value = room.address || '';
    document.getElementById('edit_city').value = room.city || '';
    document.getElementById('edit_distance').value = room.distance || '';
    document.getElementById('edit_status').value = room.status || 'pending';
    document.getElementById('edit_description').value = room.description || '';
    
    let amenitiesVal = (typeof room.amenities === 'string') ? room.amenities : (Array.isArray(room.amenities) ? room.amenities.join(', ') : '');
    document.getElementById('edit_amenities').value = amenitiesVal || '';
    let facilitiesVal = (typeof room.facilities === 'string') ? room.facilities : (Array.isArray(room.facilities) ? room.facilities.join(', ') : '');
    document.getElementById('edit_facilities').value = facilitiesVal || '';
    document.getElementById('edit_host_name').value = room.host_name || '';
    document.getElementById('edit_host_phone').value = room.host_phone || '';
    document.getElementById('edit_host_email').value = room.host_email || '';
    
    let previewDiv = document.getElementById('currentImagePreview');
    let imgUrl = room.primary_image ? getSafeImagePath(room.primary_image) : (room.image_url ? getSafeImagePath(room.image_url) : null);
    if(imgUrl) {
        previewDiv.innerHTML = `<img src="${imgUrl}" alt="current room image" onerror="this.src=''">`;
    } else {
        previewDiv.innerHTML = `<div class="helper-text">No image uploaded</div>`;
    }
    
    document.getElementById('editRoomModal').classList.remove('hidden');
}

function closeAdminEditModal() {
    document.getElementById('editRoomModal').classList.add('hidden');
}

async function submitRoomEdit() {
    let roomId = document.getElementById('edit_room_id').value;
    let formData = new FormData();
    formData.append('room_id', roomId);
    formData.append('title', document.getElementById('edit_title').value);
    formData.append('price', document.getElementById('edit_price').value);
    formData.append('security_deposit', document.getElementById('edit_security_deposit').value);
    formData.append('bedrooms', document.getElementById('edit_bedrooms').value);
    formData.append('bathrooms', document.getElementById('edit_bathrooms').value);
    formData.append('max_guests', document.getElementById('edit_max_guests').value);
    formData.append('room_type', document.getElementById('edit_room_type').value);
    formData.append('pg_type', document.getElementById('edit_pg_type').value);
    formData.append('location', document.getElementById('edit_location').value);
    formData.append('address', document.getElementById('edit_address').value);
    formData.append('city', document.getElementById('edit_city').value);
    formData.append('distance', document.getElementById('edit_distance').value);
    formData.append('status', document.getElementById('edit_status').value);
    formData.append('description', document.getElementById('edit_description').value);
    formData.append('amenities', document.getElementById('edit_amenities').value);
    formData.append('facilities', document.getElementById('edit_facilities').value);
    formData.append('host_name', document.getElementById('edit_host_name').value);
    formData.append('host_phone', document.getElementById('edit_host_phone').value);
    formData.append('host_email', document.getElementById('edit_host_email').value);
    
    try {
        let res = await apiPost('../api/admin/update-room', formData);
        showToast(res?.message || 'Room updated', res?.status === 'success' ? 'success' : 'error');
        if(res?.status === 'success') {
            closeAdminEditModal();
            loadRooms();
        }
    } catch(e) {
        showToast('Update failed: ' + e.message, 'error');
    }
}

document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') closeAdminEditModal();
});

document.addEventListener('DOMContentLoaded', loadRooms);
</script>

<?php renderFooter('admin'); ?>