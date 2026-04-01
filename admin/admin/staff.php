<?php
require_once '../common/auth.php';
requirePageRole([ROLE_ADMIN]);
require_once '../common/layout.php';

renderHeader('Manage Staff');
renderSidebarMenu('staff', 'admin');
renderMainContentStart('Manage Staff', $_SESSION['username'] ?? 'Admin');
?>

<style>
/* Existing styles remain the same */
.staff-page{
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

.staff-form-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:16px;
}

.form-group{
    display:flex;
    flex-direction:column;
    gap:8px;
}

.form-group.full-width{
    grid-column:1 / -1;
}

.form-label{
    font-size:14px;
    font-weight:600;
    color:#34495e;
}

.input-control,
.select-control{
    width:100%;
    border:1px solid #dbe3eb;
    border-radius:12px;
    background:#fff;
    color:#1f2937;
    padding:12px 14px;
    outline:none;
    transition:.2s ease;
}

.input-control:focus,
.select-control:focus{
    border-color:#ff6b35;
    box-shadow:0 0 0 3px rgba(255,107,53,0.10);
}

.form-actions{
    display:flex;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    margin-top:4px;
}

.btn{
    border:none;
    outline:none;
    cursor:pointer;
    border-radius:12px;
    padding:11px 18px;
    font-size:14px;
    font-weight:600;
    transition:.25s ease;
}

.btn:hover{
    transform:translateY(-1px);
}

.btn-primary{
    background:linear-gradient(90deg, #ff9f4a, #ff6b35);
    color:#fff;
}

.btn-secondary{
    background:#eef2f6;
    color:#334155;
}

.btn-secondary:hover{
    background:#e5ebf1;
}

.btn-success{
    background:#10b981;
    color:#fff;
}

.btn-success:hover{
    background:#059669;
}

.btn-warning{
    background:#f59e0b;
    color:#fff;
}

.btn-warning:hover{
    background:#d97706;
}

.btn-danger{
    background:#ef4444;
    color:#fff;
}

.btn-danger:hover{
    background:#dc2626;
}

.btn-info{
    background:#3b82f6;
    color:#fff;
}

.btn-info:hover{
    background:#2563eb;
}

.staff-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:18px;
}

.staff-card{
    background:#fff;
    border:1px solid #edf1f5;
    border-radius:18px;
    box-shadow:0 2px 10px rgba(0,0,0,0.04);
    padding:18px;
    display:flex;
    flex-direction:column;
    gap:14px;
    transition: all 0.3s ease;
}

.staff-card.pending{
    border-left:4px solid #f59e0b;
    background:#fffbeb;
}

.staff-card.blocked{
    border-left:4px solid #ef4444;
    background:#fef2f2;
}

.staff-card.active{
    border-left:4px solid #10b981;
}

.staff-top{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:12px;
    flex-wrap:wrap;
}

.staff-name{
    margin:0;
    font-size:18px;
    font-weight:700;
    color:#102a43;
    word-break:break-word;
}

.status-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
    white-space:nowrap;
}

.status-badge.pending{
    background:#fef3c7;
    color:#d97706;
}

.status-badge.active{
    background:#d1fae5;
    color:#059669;
}

.status-badge.blocked{
    background:#fee2e2;
    color:#dc2626;
}

.role-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:6px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:700;
    background:#eff6ff;
    color:#1d4ed8;
    white-space:nowrap;
}

.staff-meta{
    display:grid;
    gap:10px;
}

.staff-meta-item{
    display:flex;
    align-items:flex-start;
    gap:10px;
    color:#475569;
    font-size:14px;
    line-height:1.5;
    word-break:break-word;
}

.staff-meta-item i{
    width:16px;
    margin-top:2px;
    color:#ff6b35;
    flex-shrink:0;
}

.staff-actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:4px;
    padding-top:12px;
    border-top:1px solid #edf1f5;
}

.staff-role-box{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    align-items:center;
}

.staff-role-box .select-control{
    flex:1;
    min-width:180px;
}

.empty-state,
.loading-state{
    background:#fff;
    border:1px dashed #d8e2ec;
    border-radius:18px;
    padding:48px 20px;
    text-align:center;
    color:#64748b;
}

.empty-state i,
.loading-state i{
    font-size:36px;
    color:#ff6b35;
    margin-bottom:12px;
}

.empty-state h3,
.loading-state h3{
    font-size:18px;
    color:#102a43;
    margin-bottom:8px;
}

.empty-state p,
.loading-state p{
    margin:0;
    font-size:14px;
}

.filters{
    margin-bottom:20px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.filter-btn{
    padding:8px 16px;
    border:1px solid #dbe3eb;
    background:#fff;
    border-radius:12px;
    cursor:pointer;
    transition:all 0.3s ease;
    font-size:14px;
    font-weight:500;
}

.filter-btn.active{
    background:#ff6b35;
    border-color:#ff6b35;
    color:#fff;
}

.filter-btn:hover:not(.active){
    background:#f0f2f5;
}

/* Custom Modal Styles - LayUI Style */
.modal-mask {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9998;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.modal-mask.active {
    opacity: 1;
    visibility: visible;
}

.modal-container {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 480px;
    transform: scale(0.9);
    transition: transform 0.3s ease;
    overflow: hidden;
}

.modal-mask.active .modal-container {
    transform: scale(1);
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #edf1f5;
    display: flex;
    align-items: center;
    gap: 12px;
}

.modal-header i {
    font-size: 24px;
}

.modal-header.warning i {
    color: #f59e0b;
}

.modal-header.danger i {
    color: #ef4444;
}

.modal-header.success i {
    color: #10b981;
}

.modal-header.info i {
    color: #3b82f6;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #102a43;
}

.modal-body {
    padding: 24px;
    color: #475569;
    line-height: 1.6;
    font-size: 15px;
}

.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #edf1f5;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.modal-btn {
    padding: 8px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.modal-btn-cancel {
    background: #eef2f6;
    color: #334155;
}

.modal-btn-cancel:hover {
    background: #e5ebf1;
}

.modal-btn-confirm {
    background: #ff6b35;
    color: #fff;
}

.modal-btn-confirm:hover {
    background: #ff5722;
    transform: translateY(-1px);
}

.modal-btn-danger {
    background: #ef4444;
    color: #fff;
}

.modal-btn-danger:hover {
    background: #dc2626;
}

.modal-btn-warning {
    background: #f59e0b;
    color: #fff;
}

.modal-btn-warning:hover {
    background: #d97706;
}

@media (max-width: 768px){
    .staff-grid{
        grid-template-columns:1fr;
    }
    
    .staff-page{
        gap:18px;
    }

    .page-card-header,
    .page-card-body{
        padding:16px;
    }

    .page-card-title{
        font-size:16px;
    }

    .staff-form-grid{
        grid-template-columns:1fr;
        gap:14px;
    }

    .staff-card{
        padding:16px;
    }

    .staff-top{
        flex-direction:column;
        align-items:flex-start;
    }

    .staff-role-box{
        flex-direction:column;
    }

    .staff-role-box .select-control,
    .staff-role-box .btn{
        width:100%;
    }

    .form-actions{
        flex-direction:column;
        align-items:stretch;
    }

    .form-actions .btn{
        width:100%;
    }
    
    .modal-container {
        width: 95%;
        margin: 20px;
    }
    
    .modal-body {
        padding: 20px;
    }
}

@media (max-width: 480px){
    .staff-name{
        font-size:16px;
    }

    .staff-meta-item{
        font-size:13px;
    }

    .input-control,
    .select-control{
        padding:11px 12px;
        font-size:14px;
    }
}
</style>

<div class="staff-page">
    <section class="page-card">
        <div class="page-card-header">
            <div class="page-card-title">
                <i class="fas fa-user-plus"></i>
                <span>Add Staff User</span>
            </div>
        </div>

        <div class="page-card-body">
            <form id="staffForm">
                <div class="staff-form-grid">
                    <div class="form-group">
                        <label class="form-label" for="staff_name">Full Name</label>
                        <input type="text" id="staff_name" name="name" class="input-control" placeholder="Enter full name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="staff_email">Email</label>
                        <input type="email" id="staff_email" name="email" class="input-control" placeholder="Enter email address" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="staff_phone">Phone Number</label>
                        <input type="tel" id="staff_phone" name="phone" class="input-control" placeholder="Enter phone number" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="staff_password">Password</label>
                        <input type="password" id="staff_password" name="password" class="input-control" placeholder="Enter password" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="staffRoleSelect">Role</label>
                        <select name="role_id" id="staffRoleSelect" class="select-control" required>
                            <option value="">Loading roles...</option>
                        </select>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Add Staff</button>
                    <button type="reset" class="btn btn-secondary">Reset Form</button>
                </div>
            </form>
        </div>
    </section>

    <section class="page-card">
        <div class="page-card-header">
            <div class="page-card-title">
                <i class="fas fa-users"></i>
                <span>Staff List</span>
            </div>
        </div>

        <div class="page-card-body">
            <div class="filters">
                <button class="filter-btn active" data-filter="all">All Staff</button>
                <button class="filter-btn" data-filter="pending">Pending (0)</button>
                <button class="filter-btn" data-filter="active">Active (1)</button>
                <button class="filter-btn" data-filter="blocked">Blocked (2)</button>
            </div>
            
            <div id="staffList">
                <div class="loading-state">
                    <i class="fas fa-spinner fa-pulse"></i>
                    <h3>Loading staff...</h3>
                    <p>Please wait while we fetch staff records.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Custom Modal -->
<div id="confirmModal" class="modal-mask">
    <div class="modal-container">
        <div class="modal-header" id="modalHeader">
            <i id="modalIcon" class="fas fa-question-circle"></i>
            <h3 id="modalTitle">Confirm Action</h3>
        </div>
        <div class="modal-body" id="modalBody">
            Are you sure you want to perform this action?
        </div>
        <div class="modal-footer">
            <button class="modal-btn modal-btn-cancel" id="modalCancelBtn">Cancel</button>
            <button class="modal-btn modal-btn-confirm" id="modalConfirmBtn">Confirm</button>
        </div>
    </div>
</div>

<script>
let allStaff = [];
let allRoles = [];
let currentFilter = 'all';
let pendingAction = null;
let pendingActionData = null;

// Custom Modal Functions
function showConfirmModal(options) {
    return new Promise((resolve, reject) => {
        const modal = document.getElementById('confirmModal');
        const modalHeader = document.getElementById('modalHeader');
        const modalIcon = document.getElementById('modalIcon');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        const modalConfirmBtn = document.getElementById('modalConfirmBtn');
        const modalCancelBtn = document.getElementById('modalCancelBtn');
        
        // Set modal content
        modalTitle.textContent = options.title || 'Confirm Action';
        modalBody.innerHTML = options.message || 'Are you sure you want to perform this action?';
        
        // Set icon and styling based on type
        modalHeader.className = 'modal-header';
        if (options.type === 'warning') {
            modalHeader.classList.add('warning');
            modalIcon.className = 'fas fa-exclamation-triangle';
            modalConfirmBtn.className = 'modal-btn modal-btn-warning';
        } else if (options.type === 'danger') {
            modalHeader.classList.add('danger');
            modalIcon.className = 'fas fa-times-circle';
            modalConfirmBtn.className = 'modal-btn modal-btn-danger';
        } else if (options.type === 'success') {
            modalHeader.classList.add('success');
            modalIcon.className = 'fas fa-check-circle';
            modalConfirmBtn.className = 'modal-btn modal-btn-confirm';
        } else {
            modalHeader.classList.add('info');
            modalIcon.className = 'fas fa-question-circle';
            modalConfirmBtn.className = 'modal-btn modal-btn-confirm';
        }
        
        // Set button text
        modalConfirmBtn.textContent = options.confirmText || 'Confirm';
        modalCancelBtn.textContent = options.cancelText || 'Cancel';
        
        // Show modal
        modal.classList.add('active');
        
        // Handle confirm
        const confirmHandler = () => {
            cleanup();
            resolve(true);
        };
        
        // Handle cancel
        const cancelHandler = () => {
            cleanup();
            resolve(false);
        };
        
        // Handle close on backdrop click
        const backdropHandler = (e) => {
            if (e.target === modal) {
                cleanup();
                resolve(false);
            }
        };
        
        function cleanup() {
            modalConfirmBtn.removeEventListener('click', confirmHandler);
            modalCancelBtn.removeEventListener('click', cancelHandler);
            modal.removeEventListener('click', backdropHandler);
            modal.classList.remove('active');
        }
        
        modalConfirmBtn.addEventListener('click', confirmHandler);
        modalCancelBtn.addEventListener('click', cancelHandler);
        modal.addEventListener('click', backdropHandler);
    });
}

// Alert modal function
function showAlertModal(options) {
    return new Promise((resolve) => {
        const modal = document.getElementById('confirmModal');
        const modalHeader = document.getElementById('modalHeader');
        const modalIcon = document.getElementById('modalIcon');
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        const modalConfirmBtn = document.getElementById('modalConfirmBtn');
        const modalCancelBtn = document.getElementById('modalCancelBtn');
        
        // Set modal content
        modalTitle.textContent = options.title || 'Notification';
        modalBody.innerHTML = options.message || '';
        
        // Set icon and styling based on type
        modalHeader.className = 'modal-header';
        if (options.type === 'error') {
            modalHeader.classList.add('danger');
            modalIcon.className = 'fas fa-exclamation-circle';
            modalConfirmBtn.className = 'modal-btn modal-btn-danger';
        } else if (options.type === 'success') {
            modalHeader.classList.add('success');
            modalIcon.className = 'fas fa-check-circle';
            modalConfirmBtn.className = 'modal-btn modal-btn-confirm';
        } else if (options.type === 'warning') {
            modalHeader.classList.add('warning');
            modalIcon.className = 'fas fa-exclamation-triangle';
            modalConfirmBtn.className = 'modal-btn modal-btn-warning';
        } else {
            modalHeader.classList.add('info');
            modalIcon.className = 'fas fa-info-circle';
            modalConfirmBtn.className = 'modal-btn modal-btn-confirm';
        }
        
        // Hide cancel button for alerts
        modalCancelBtn.style.display = 'none';
        modalConfirmBtn.textContent = options.buttonText || 'OK';
        
        // Show modal
        modal.classList.add('active');
        
        // Handle confirm
        const confirmHandler = () => {
            cleanup();
            resolve(true);
        };
        
        // Handle close on backdrop click
        const backdropHandler = (e) => {
            if (e.target === modal) {
                cleanup();
                resolve(false);
            }
        };
        
        function cleanup() {
            modalConfirmBtn.removeEventListener('click', confirmHandler);
            modal.removeEventListener('click', backdropHandler);
            modal.classList.remove('active');
            modalCancelBtn.style.display = '';
        }
        
        modalConfirmBtn.addEventListener('click', confirmHandler);
        modal.addEventListener('click', backdropHandler);
    });
}

function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(value);
    return div.innerHTML;
}

function renderRoleOptions(selectedRoleId) {
    let options = '<option value="">Select Role</option>';

    allRoles.forEach(function(role) {
        const selected = String(selectedRoleId || '') === String(role.id) ? ' selected' : '';
        options += '<option value="' + escapeHtml(role.id) + '"' + selected + '>' + escapeHtml(role.name) + '</option>';
    });

    return options;
}

async function loadRoles() {
    try {
        const res = await apiGet('../api/admin/list-roles');

        if (res && res.status === 'success' && res.data && Array.isArray(res.data.roles)) {
            allRoles = res.data.roles;
            document.getElementById('staffRoleSelect').innerHTML = renderRoleOptions('');
        } else {
            allRoles = [];
            document.getElementById('staffRoleSelect').innerHTML = '<option value="">No roles found</option>';
            showAlertModal({
                title: 'Error',
                message: (res && res.message) ? res.message : 'Failed to load roles',
                type: 'error'
            });
        }
    } catch (error) {
        console.error('Load roles error:', error);
        allRoles = [];
        document.getElementById('staffRoleSelect').innerHTML = '<option value="">Failed to load roles</option>';
        showAlertModal({
            title: 'Error',
            message: 'Failed to load roles',
            type: 'error'
        });
    }
}

function getStatusBadgeClass(status) {
    if (status == 0) return 'pending';
    if (status == 1) return 'active';
    if (status == 2) return 'blocked';
    return '';
}

function getStatusText(status) {
    if (status == 0) return 'Pending Approval';
    if (status == 1) return 'Active';
    if (status == 2) return 'Blocked';
    return 'Unknown';
}

function getCardClass(status) {
    if (status == 0) return 'pending';
    if (status == 1) return 'active';
    if (status == 2) return 'blocked';
    return '';
}

function renderStaffCard(item) {
    const userId = Number(item.id) || 0;
    const name = escapeHtml(item.name || 'Unnamed Staff');
    const email = escapeHtml(item.email || 'N/A');
    const phone = escapeHtml(item.phone || 'N/A');
    const roleName = escapeHtml(item.role_name || 'N/A');
    const status = item.is_active !== undefined ? item.is_active : 1;
    const statusBadgeClass = getStatusBadgeClass(status);
    const statusText = getStatusText(status);
    const cardClass = getCardClass(status);
    
    let actionButtons = '';
    
    if (status == 0) {
        actionButtons = `
            <button type="button" class="btn btn-success" onclick="confirmUpdateStaffStatus(${userId}, 1)">Approve</button>
            <button type="button" class="btn btn-danger" onclick="confirmUpdateStaffStatus(${userId}, 2)">Block</button>
        `;
    } else if (status == 1) {
        actionButtons = `
            <button type="button" class="btn btn-warning" onclick="confirmUpdateStaffStatus(${userId}, 2)">Block</button>
        `;
    } else if (status == 2) {
        actionButtons = `
            <button type="button" class="btn btn-success" onclick="confirmUpdateStaffStatus(${userId}, 1)">Unblock</button>
        `;
    }

    return ''
        + '<div class="staff-card ' + cardClass + '" data-status="' + status + '">'
        +   '<div class="staff-top">'
        +       '<h3 class="staff-name">' + name + '</h3>'
        +       '<div style="display:flex; gap:8px; flex-wrap:wrap;">'
        +           '<span class="status-badge ' + statusBadgeClass + '">' + statusText + '</span>'
        +           '<span class="role-badge">' + roleName + '</span>'
        +       '</div>'
        +   '</div>'
        +   '<div class="staff-meta">'
        +       '<div class="staff-meta-item"><i class="fas fa-envelope"></i><span>' + email + '</span></div>'
        +       '<div class="staff-meta-item"><i class="fas fa-phone"></i><span>' + phone + '</span></div>'
        +       '<div class="staff-meta-item"><i class="fas fa-user-shield"></i><span><strong>Role:</strong> ' + roleName + '</span></div>'
        +   '</div>'
        +   '<div class="staff-role-box">'
        +       '<select id="role_' + userId + '" class="select-control">' + renderRoleOptions(item.role_id) + '</select>'
        +       '<button type="button" class="btn btn-primary" onclick="confirmUpdateStaffRole(' + userId + ')">Update Role</button>'
        +   '</div>'
        +   '<div class="staff-actions">'
        +       actionButtons
        +   '</div>'
        + '</div>';
}

function filterStaff() {
    const staffList = document.getElementById('staffList');
    let filteredStaff = allStaff;
    
    if (currentFilter !== 'all') {
        let statusValue = currentFilter === 'pending' ? 0 : (currentFilter === 'active' ? 1 : 2);
        filteredStaff = allStaff.filter(staff => staff.is_active == statusValue);
    }
    
    if (filteredStaff.length === 0) {
        staffList.innerHTML = 
            '<div class="empty-state">'
            + '<i class="fas fa-user-slash"></i>'
            + '<h3>No staff found</h3>'
            + '<p>No staff users match the current filter.</p>'
            + '</div>';
        return;
    }
    
    let html = '<div class="staff-grid">';
    filteredStaff.forEach(function(item) {
        html += renderStaffCard(item);
    });
    html += '</div>';
    
    staffList.innerHTML = html;
}

async function loadStaff() {
    const list = document.getElementById('staffList');

    list.innerHTML =
        '<div class="loading-state">'
        + '<i class="fas fa-spinner fa-pulse"></i>'
        + '<h3>Loading staff...</h3>'
        + '<p>Please wait while we fetch staff records.</p>'
        + '</div>';

    try {
        const res = await apiGet('../api/admin/list-staff');

        if (res && res.status === 'success' && res.data && Array.isArray(res.data.staff)) {
            allStaff = res.data.staff;
            
            // Update filter button counts
            const pendingCount = allStaff.filter(s => s.is_active == 0).length;
            const activeCount = allStaff.filter(s => s.is_active == 1).length;
            const blockedCount = allStaff.filter(s => s.is_active == 2).length;
            
            const filterButtons = document.querySelectorAll('.filter-btn');
            if (filterButtons.length >= 4) {
                filterButtons[1].innerHTML = `Pending (${pendingCount})`;
                filterButtons[2].innerHTML = `Active (${activeCount})`;
                filterButtons[3].innerHTML = `Blocked (${blockedCount})`;
            }
            
            filterStaff();
        } else {
            list.innerHTML =
                '<div class="empty-state">'
                + '<i class="fas fa-circle-exclamation"></i>'
                + '<h3>Unable to load staff</h3>'
                + '<p>' + escapeHtml((res && res.message) ? res.message : 'Something went wrong while fetching staff.') + '</p>'
                + '</div>';
        }
    } catch (error) {
        console.error('Load staff error:', error);
        list.innerHTML =
            '<div class="empty-state">'
            + '<i class="fas fa-wifi"></i>'
            + '<h3>Network error</h3>'
            + '<p>Failed to load staff. Please try again.</p>'
            + '</div>';
    }
}

document.getElementById('staffForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Show confirmation modal before adding staff
    const confirmed = await showConfirmModal({
        title: 'Add New Staff',
        message: 'Are you sure you want to add this staff member? They will be created with pending approval status.',
        confirmText: 'Add Staff',
        cancelText: 'Cancel',
        type: 'info'
    });
    
    if (!confirmed) return;
    
    submitBtn.disabled = true;
    submitBtn.textContent = 'Adding...';

    try {
        const formData = new FormData(this);
        // New staff users are created with is_active = 0 (pending approval)
        formData.append('is_active', 0);
        
        const res = await apiPost('../api/admin/add-staff', formData);
        
        if (res && res.status === 'success') {
            await showAlertModal({
                title: 'Success',
                message: res.message || 'Staff added successfully!',
                type: 'success',
                buttonText: 'OK'
            });
            this.reset();
            if (allRoles.length > 0) {
                document.getElementById('staffRoleSelect').innerHTML = renderRoleOptions('');
            }
            await loadStaff();
        } else {
            await showAlertModal({
                title: 'Error',
                message: (res && res.message) ? res.message : 'Failed to add staff',
                type: 'error',
                buttonText: 'OK'
            });
        }
    } catch (error) {
        console.error('Add staff error:', error);
        await showAlertModal({
            title: 'Error',
            message: 'Failed to add staff. Please try again.',
            type: 'error',
            buttonText: 'OK'
        });
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
});

async function confirmUpdateStaffRole(userId) {
    const roleField = document.getElementById('role_' + userId);
    const roleId = roleField ? roleField.value : '';

    if (!roleId) {
        await showAlertModal({
            title: 'Validation Error',
            message: 'Please select a role',
            type: 'warning',
            buttonText: 'OK'
        });
        if (roleField) roleField.focus();
        return;
    }
    
    const staff = allStaff.find(s => s.id == userId);
    const roleName = allRoles.find(r => r.id == roleId)?.name || 'Unknown';
    
    const confirmed = await showConfirmModal({
        title: 'Update Staff Role',
        message: `Are you sure you want to change ${staff?.name || 'this staff member'}'s role to "${roleName}"?`,
        confirmText: 'Update Role',
        cancelText: 'Cancel',
        type: 'info'
    });
    
    if (!confirmed) return;

    const fd = new FormData();
    fd.append('user_id', userId);
    fd.append('role_id', roleId);

    try {
        const res = await apiPost('../api/admin/update-staff-role', fd);
        
        if (res && res.status === 'success') {
            await showAlertModal({
                title: 'Success',
                message: res.message || 'Role updated successfully!',
                type: 'success',
                buttonText: 'OK'
            });
            await loadStaff();
        } else {
            await showAlertModal({
                title: 'Error',
                message: (res && res.message) ? res.message : 'Failed to update role',
                type: 'error',
                buttonText: 'OK'
            });
        }
    } catch (error) {
        console.error('Update staff role error:', error);
        await showAlertModal({
            title: 'Error',
            message: 'Failed to update role',
            type: 'error',
            buttonText: 'OK'
        });
    }
}

async function confirmUpdateStaffStatus(userId, status) {
    let actionText = '';
    let actionTitle = '';
    let actionMessage = '';
    let confirmText = '';
    let modalType = '';
    
    const staff = allStaff.find(s => s.id == userId);
    const staffName = staff?.name || 'this staff member';
    
    if (status == 1) {
        if (staff?.is_active == 0) {
            actionText = 'approve';
            actionTitle = 'Approve Staff Member';
            actionMessage = `Are you sure you want to approve ${staffName}? They will be able to access the system.`;
            confirmText = 'Approve';
            modalType = 'success';
        } else if (staff?.is_active == 2) {
            actionText = 'unblock';
            actionTitle = 'Unblock Staff Member';
            actionMessage = `Are you sure you want to unblock ${staffName}? They will be able to access the system again.`;
            confirmText = 'Unblock';
            modalType = 'success';
        }
    } else if (status == 2) {
        actionText = 'block';
        actionTitle = 'Block Staff Member';
        actionMessage = `Are you sure you want to block ${staffName}? They will lose access to the system.`;
        confirmText = 'Block';
        modalType = 'danger';
    }
    
    const confirmed = await showConfirmModal({
        title: actionTitle,
        message: actionMessage,
        confirmText: confirmText,
        cancelText: 'Cancel',
        type: modalType
    });
    
    if (!confirmed) return;

    const fd = new FormData();
    fd.append('user_id', userId);
    fd.append('is_active', status);

    try {
        const res = await apiPost('../api/admin/update-staff-status', fd);
        
        if (res && res.status === 'success') {
            await showAlertModal({
                title: 'Success',
                message: res.message || `Staff ${actionText}ed successfully!`,
                type: 'success',
                buttonText: 'OK'
            });
            await loadStaff();
        } else {
            await showAlertModal({
                title: 'Error',
                message: (res && res.message) ? res.message : `Failed to ${actionText} staff`,
                type: 'error',
                buttonText: 'OK'
            });
        }
    } catch (error) {
        console.error('Update staff status error:', error);
        await showAlertModal({
            title: 'Error',
            message: `Failed to ${actionText} staff`,
            type: 'error',
            buttonText: 'OK'
        });
    }
}

// Setup filter buttons
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.dataset.filter;
        filterStaff();
    });
});

document.addEventListener('DOMContentLoaded', async function() {
    await loadRoles();
    await loadStaff();
});
</script>

<?php renderFooter('admin'); ?>