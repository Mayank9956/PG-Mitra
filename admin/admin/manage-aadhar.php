<?php
require_once '../common/auth.php';
require_once '../common/layout.php';

requirePageRole([ROLE_ADMIN]);

renderHeader('Aadhar Verification');
renderSidebarMenu('aadhar', 'admin');
renderMainContentStart('Aadhar Verification', $_SESSION['username'] ?? 'Admin');
?>

<style>
/* ===== BASE RESPONSIVE ENHANCEMENTS ===== */
* {
    box-sizing: border-box;
}

/* ===== PAGE ===== */
.aadhar-page {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* ===== CARD ===== */
.page-card {
    background: #fff;
    border: 1px solid #edf1f5;
    border-radius: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    overflow: hidden;
}

.page-card-header {
    padding: 18px 20px;
    border-bottom: 1px solid #edf1f5;
    font-weight: 700;
    font-size: 18px;
    color: #102a43;
    background: #fafcff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.page-card-header i {
    margin-right: 8px;
    color: #ff6b35;
}

.page-card-header .filter-group {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.filter-select {
    padding: 6px 12px;
    border: 1px solid #d1d9e8;
    border-radius: 12px;
    font-size: 13px;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s;
}

.filter-select:focus {
    border-color: #ff6b35;
    outline: none;
}

.page-card-body {
    padding: 20px;
}

/* ===== STATS ===== */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

.stat-card {
    background: #fff;
    border: 1px solid #edf1f5;
    border-radius: 16px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.2s;
    cursor: pointer;
}

.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transform: translateY(-2px);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.stat-icon.pending {
    background: #fef3c7;
    color: #f59e0b;
}

.stat-icon.verified {
    background: #dcfce7;
    color: #10b981;
}

.stat-icon.rejected {
    background: #fee2e2;
    color: #ef4444;
}

.stat-icon.total {
    background: #e0e7ff;
    color: #6366f1;
}

.stat-info h3 {
    font-size: 24px;
    font-weight: 700;
    color: #0f172a;
    margin: 0 0 4px 0;
}

.stat-info p {
    font-size: 13px;
    color: #64748b;
    margin: 0;
}

/* ===== TABLE ===== */
.table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.aadhar-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    min-width: 600px;
}

.aadhar-table thead {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.aadhar-table th {
    padding: 14px 12px;
    text-align: left;
    font-weight: 600;
    color: #1e293b;
}

.aadhar-table td {
    padding: 14px 12px;
    border-bottom: 1px solid #edf1f5;
    vertical-align: middle;
}

.aadhar-table tr:hover {
    background: #fafcff;
}

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 40px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
}

.status-badge i {
    font-size: 11px;
}

.status-badge.pending {
    background: #fef3c7;
    color: #f59e0b;
}

.status-badge.verified {
    background: #dcfce7;
    color: #10b981;
}

.status-badge.rejected {
    background: #fee2e2;
    color: #ef4444;
}

/* Image Preview */
.image-preview {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    transition: transform 0.2s;
}

.image-preview:hover {
    transform: scale(1.1);
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border: none;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    font-family: inherit;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 11px;
}

.btn-icon {
    padding: 6px 10px;
    font-size: 12px;
}

.btn-primary {
    background: #ff6b35;
    color: #fff;
}

.btn-primary:hover {
    background: #e55a2a;
}

.btn-secondary {
    background: #e2e8f0;
    color: #1e293b;
}

.btn-secondary:hover {
    background: #cbd5e1;
}

.btn-success {
    background: #10b981;
    color: #fff;
}

.btn-success:hover {
    background: #059669;
}

.btn-danger {
    background: #ef4444;
    color: #fff;
}

.btn-danger:hover {
    background: #dc2626;
}

.btn-info {
    background: #3b82f6;
    color: #fff;
}

.btn-info:hover {
    background: #2563eb;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    animation: fadeIn 0.3s ease;
    overflow-y: auto;
    padding: 20px;
}

.modal-content {
    background-color: #fff;
    margin: 20px auto;
    padding: 0;
    border-radius: 20px;
    width: 90%;
    max-width: 900px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    animation: slideDown 0.3s ease;
}

.modal-header {
    padding: 18px 24px;
    border-bottom: 1px solid #edf1f5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fafcff;
    border-radius: 20px 20px 0 0;
    flex-wrap: wrap;
    gap: 12px;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #102a43;
}

.modal-header h3 i {
    margin-right: 8px;
    color: #ff6b35;
}

.close-modal {
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #94a3b8;
    transition: color 0.2s;
    line-height: 1;
}

.close-modal:hover {
    color: #ef4444;
}

.modal-body {
    padding: 24px;
    max-height: 70vh;
    overflow-y: auto;
}

.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #edf1f5;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: #fff;
    border-radius: 0 0 20px 20px;
    flex-wrap: wrap;
}

/* Form Elements */
.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #1e293b;
}

.form-control {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d9e8;
    border-radius: 12px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s;
}

.form-control:focus {
    border-color: #ff6b35;
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,107,53,0.1);
}

textarea.form-control {
    resize: vertical;
    min-height: 80px;
}

/* Image Grid */
.image-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin: 20px 0;
}

.image-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
}

.image-card h4 {
    margin: 0 0 12px 0;
    font-size: 14px;
    color: #475569;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.image-card img {
    width: 100%;
    max-height: 300px;
    object-fit: contain;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: transform 0.2s;
}

.image-card img:hover {
    transform: scale(1.02);
}

/* Detail Row */
.detail-row {
    display: flex;
    padding: 12px 0;
    border-bottom: 1px solid #edf1f5;
}

.detail-label {
    width: 140px;
    font-weight: 600;
    color: #475569;
    flex-shrink: 0;
}

.detail-value {
    flex: 1;
    color: #0f172a;
    word-break: break-word;
}

/* Loading */
.loading-spinner {
    text-align: center;
    padding: 40px;
    color: #64748b;
}

.loading-spinner i {
    margin-right: 8px;
    font-size: 20px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #94a3b8;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 16px;
    display: block;
    color: #cbd5e1;
}

/* Toast */
.toast-notification {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #1e293b;
    color: white;
    padding: 12px 24px;
    border-radius: 40px;
    font-size: 14px;
    z-index: 1001;
    animation: slideIn 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 8px;
}

.toast-notification.success {
    background: #10b981;
}

.toast-notification.error {
    background: #ef4444;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideDown {
    from {
        transform: translateY(-50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* ===== ENHANCED RESPONSIVE STYLES ===== */
@media (max-width: 1200px) {
    .stats-grid {
        gap: 16px;
    }
    
    .stat-card {
        padding: 14px 16px;
    }
    
    .stat-icon {
        width: 44px;
        height: 44px;
        font-size: 20px;
    }
    
    .stat-info h3 {
        font-size: 22px;
    }
}

@media (max-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    
    .page-card-header {
        padding: 16px 18px;
    }
    
    .page-card-body {
        padding: 16px;
    }
}

@media (max-width: 768px) {
    .aadhar-page {
        gap: 16px;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .stat-card {
        padding: 14px 18px;
    }
    
    .page-card-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .page-card-header .filter-group {
        justify-content: space-between;
    }
    
    .filter-select {
        flex: 1;
    }
    
    .image-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .detail-row {
        flex-direction: column;
        gap: 6px;
        padding: 10px 0;
    }
    
    .detail-label {
        width: auto;
    }
    
    .aadhar-table {
        font-size: 12px;
        min-width: 550px;
    }
    
    .aadhar-table th,
    .aadhar-table td {
        padding: 10px 8px;
    }
    
    .action-buttons {
        flex-direction: row;
        flex-wrap: wrap;
    }
    
    .btn-icon {
        padding: 5px 8px;
        font-size: 11px;
    }
    
    .modal-header {
        padding: 14px 18px;
    }
    
    .modal-body {
        padding: 18px;
    }
    
    .modal-footer {
        padding: 14px 18px;
        flex-direction: column-reverse;
    }
    
    .modal-footer .btn {
        width: 100%;
        justify-content: center;
    }
    
    .toast-notification {
        bottom: 16px;
        right: 16px;
        left: 16px;
        text-align: center;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .modal-content {
        margin: 10px auto;
        width: 95%;
    }
    
    .modal-body {
        padding: 16px;
        max-height: 65vh;
    }
    
    .page-card-header h3,
    .page-card-header div:first-child {
        font-size: 16px;
    }
    
    .stat-info h3 {
        font-size: 20px;
    }
    
    .stat-info p {
        font-size: 12px;
    }
    
    .status-badge {
        padding: 3px 8px;
        font-size: 11px;
    }
    
    .btn-icon {
        width: auto;
        min-width: 70px;
    }
    
    .image-card {
        padding: 12px;
    }
    
    .image-card img {
        max-height: 200px;
    }
}

/* Touch-friendly improvements */
@media (hover: none) and (pointer: coarse) {
    .btn,
    .stat-card,
    .filter-select,
    .close-modal {
        cursor: default;
    }
    
    .btn:active {
        transform: scale(0.98);
    }
    
    .stat-card:active {
        transform: scale(0.99);
    }
}

/* Print styles */
@media print {
    .action-buttons,
    .filter-group,
    .modal,
    .btn {
        display: none !important;
    }
    
    .page-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .aadhar-table {
        font-size: 10px;
    }
}
</style>

<div class="aadhar-page">

    <!-- Statistics Cards -->
    <div class="stats-grid" id="statsContainer">
        <div class="stat-card" onclick="filterByStatus('all')">
            <div class="stat-icon total">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3 id="totalCount">0</h3>
                <p>Total Submissions</p>
            </div>
        </div>
        <div class="stat-card" onclick="filterByStatus('0')">
            <div class="stat-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3 id="pendingCount">0</h3>
                <p>Pending Verification</p>
            </div>
        </div>
        <div class="stat-card" onclick="filterByStatus('1')">
            <div class="stat-icon verified">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3 id="verifiedCount">0</h3>
                <p>Verified</p>
            </div>
        </div>
        <div class="stat-card" onclick="filterByStatus('2')">
            <div class="stat-icon rejected">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3 id="rejectedCount">0</h3>
                <p>Rejected</p>
            </div>
        </div>
    </div>

    <!-- Aadhar List Card -->
    <section class="page-card">
        <div class="page-card-header">
            <div>
                <i class="fas fa-id-card"></i> Aadhar Verification Requests
            </div>
            <div class="filter-group">
                <select id="statusFilter" class="filter-select" onchange="loadAadharList()">
                    <option value="all">All Status</option>
                    <option value="0">Pending</option>
                    <option value="1">Verified</option>
                    <option value="2">Rejected</option>
                </select>
                <button class="btn btn-primary btn-sm" onclick="loadAadharList()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
        <div class="page-card-body">
            <div class="table-container">
                <div id="aadharList">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-pulse"></i> Loading...
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- View Details Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-id-card"></i> Aadhar Details</h3>
            <span class="close-modal" onclick="closeViewModal()">&times;</span>
        </div>
        <div class="modal-body" id="viewModalBody">
            <!-- Dynamic content -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeViewModal()">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </div>
</div>

<!-- Verify/Reject Modal -->
<div id="actionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="actionModalTitle"><i class="fas"></i> Action</h3>
            <span class="close-modal" onclick="closeActionModal()">&times;</span>
        </div>
        <div class="modal-body">
            <input type="hidden" id="actionId">
            <div class="form-group">
                <label for="actionRemarks">Remarks (Optional)</label>
                <textarea id="actionRemarks" class="form-control" rows="3" placeholder="Add remarks for this action..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeActionModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="btn btn-success" id="confirmVerifyBtn" onclick="confirmVerify()">
                <i class="fas fa-check"></i> Verify
            </button>
            <button class="btn btn-danger" id="confirmRejectBtn" onclick="confirmReject()">
                <i class="fas fa-ban"></i> Reject
            </button>
        </div>
    </div>
</div>

<script>
// Helper Functions
async function apiGet(url) {
    try {
        const res = await fetch(url);
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return await res.json();
    } catch (err) {
        console.error('GET error:', err);
        return { status: 'error', message: err.message || 'Network error' };
    }
}

async function apiPost(url, formData) {
    try {
        const res = await fetch(url, {
            method: 'POST',
            body: formData
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return await res.json();
    } catch (err) {
        console.error('POST error:', err);
        return { status: 'error', message: err.message || 'Network error' };
    }
}

function showToast(message, type = 'success') {
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) existingToast.remove();

    const toast = document.createElement('div');
    toast.className = `toast-notification ${type === 'success' ? 'success' : 'error'}`;
    const icon = type === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
    toast.innerHTML = `${icon} ${message}`;
    document.body.appendChild(toast);

    setTimeout(() => {
        if (toast && toast.remove) toast.remove();
    }, 3000);
}

function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function getStatusBadge(verified) {
    if (verified == 1) {
        return '<span class="status-badge verified"><i class="fas fa-check-circle"></i> Verified</span>';
    } else if (verified == 2) {
        return '<span class="status-badge rejected"><i class="fas fa-times-circle"></i> Rejected</span>';
    } else {
        return '<span class="status-badge pending"><i class="fas fa-clock"></i> Pending</span>';
    }
}

// Filter by clicking on stat cards
function filterByStatus(status) {
    const filterSelect = document.getElementById('statusFilter');
    if (status === 'all') {
        filterSelect.value = 'all';
    } else if (status === '0') {
        filterSelect.value = '0';
    } else if (status === '1') {
        filterSelect.value = '1';
    } else if (status === '2') {
        filterSelect.value = '2';
    }
    loadAadharList();
}

// Load statistics
async function loadStats() {
    const res = await apiGet('../api/admin/aadhar-stats');
    if (res.status === 'success' && res.data) {
        document.getElementById('totalCount').innerText = res.data.total || 0;
        document.getElementById('pendingCount').innerText = res.data.pending || 0;
        document.getElementById('verifiedCount').innerText = res.data.verified || 0;
        document.getElementById('rejectedCount').innerText = res.data.rejected || 0;
    } else {
        console.error('Failed to load stats:', res);
    }
}

// Load Aadhar list
async function loadAadharList() {
    const status = document.getElementById('statusFilter').value;
    const container = document.getElementById('aadharList');
    container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-pulse"></i> Loading...</div>';
    
    const res = await apiGet(`../api/admin/list-aadhar?status=${status}`);
    
    if (res.status === 'success' && res.data && res.data.aadhar_list && res.data.aadhar_list.length > 0) {
        let html = `
        <table class="aadhar-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Aadhar Number</th>
                    <th>Address</th>
                    <th>Submitted At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        `;
        
        res.data.aadhar_list.forEach(item => {
            const addressShort = item.address ? (item.address.substring(0, 30) + (item.address.length > 30 ? '...' : '')) : 'N/A';
            html += `
            <tr>
                <td>${item.id}</td>
                <td>${escapeHtml(String(item.user_id))}</td>
                <td><strong>${escapeHtml(item.full_name)}</strong></td>
                <td>${maskAadhar(item.aadhar_number)}</td>
                <td title="${escapeHtml(item.address || '')}">${escapeHtml(addressShort)}</td>
                <td>${formatDate(item.submitted_at)}</td>
                <td>${getStatusBadge(item.verified)}</td>
                <td class="action-buttons">
                    <button class="btn btn-info btn-icon btn-sm" onclick="viewDetails(${item.id})">
                        <i class="fas fa-eye"></i> View
                    </button>
                    ${item.verified == 0 ? `
                        <button class="btn btn-success btn-icon btn-sm" onclick="openActionModal(${item.id}, 'verify')">
                            <i class="fas fa-check"></i> Verify
                        </button>
                        <button class="btn btn-danger btn-icon btn-sm" onclick="openActionModal(${item.id}, 'reject')">
                            <i class="fas fa-ban"></i> Reject
                        </button>
                    ` : ''}
                </td>
            </tr>
            `;
        });
        
        html += `
            </tbody>
        </table>
        `;
        container.innerHTML = html;
    } else if (res.status === 'success') {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-id-card"></i> No Aadhar submissions found</div>';
    } else {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i> Failed to load data: ' + (res.message || 'Unknown error') + '</div>';
    }
}

// Mask Aadhar number
function maskAadhar(number) {
    if (!number) return 'N/A';
    const str = String(number);
    if (str.length <= 4) return 'XXXX-XXXX-' + str;
    return 'XXXX-XXXX-' + str.slice(-4);
}

// View details
async function viewDetails(id) {
    const res = await apiGet(`../api/admin/get-aadhar?id=${id}`);
    
    if (res.status === 'success' && res.data) {
        const data = res.data;
        
        const modalBody = document.getElementById('viewModalBody');
        modalBody.innerHTML = `
            <div class="detail-row">
                <div class="detail-label">Submission ID:</div>
                <div class="detail-value">#${data.id}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">User ID:</div>
                <div class="detail-value">${escapeHtml(String(data.user_id))}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Full Name:</div>
                <div class="detail-value"><strong>${escapeHtml(data.full_name)}</strong></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Aadhar Number:</div>
                <div class="detail-value">${escapeHtml(data.aadhar_number)}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Address:</div>
                <div class="detail-value">${escapeHtml(data.address || 'N/A')}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Status:</div>
                <div class="detail-value">${getStatusBadge(data.verified)}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Submitted At:</div>
                <div class="detail-value">${formatDate(data.submitted_at)}</div>
            </div>
            ${data.verified_at ? `
            <div class="detail-row">
                <div class="detail-label">Verified At:</div>
                <div class="detail-value">${formatDate(data.verified_at)}</div>
            </div>
            ` : ''}
            ${data.remarks ? `
            <div class="detail-row">
                <div class="detail-label">Remarks:</div>
                <div class="detail-value">${escapeHtml(data.remarks)}</div>
            </div>
            ` : ''}
            
            <div class="image-grid">
                <div class="image-card">
                    <h4><i class="fas fa-id-card"></i> Front Side</h4>
                    <img src="${data.aadhar_image_front}" alt="Aadhar Front" onclick="window.open(this.src, '_blank')">
                </div>
                <div class="image-card">
                    <h4><i class="fas fa-id-card"></i> Back Side</h4>
                    <img src="${data.aadhar_image_back}" alt="Aadhar Back" onclick="window.open(this.src, '_blank')">
                </div>
            </div>
        `;
        
        document.getElementById('viewModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    } else {
        showToast(res.message || 'Failed to load details', 'error');
    }
}

function closeViewModal() {
    document.getElementById('viewModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Open action modal
let currentActionId = null;
let currentActionType = null;

function openActionModal(id, type) {
    currentActionId = id;
    currentActionType = type;
    
    const modal = document.getElementById('actionModal');
    const title = document.getElementById('actionModalTitle');
    const verifyBtn = document.getElementById('confirmVerifyBtn');
    const rejectBtn = document.getElementById('confirmRejectBtn');
    
    if (type === 'verify') {
        title.innerHTML = '<i class="fas fa-check-circle"></i> Verify Aadhar';
        verifyBtn.style.display = 'inline-flex';
        rejectBtn.style.display = 'none';
    } else {
        title.innerHTML = '<i class="fas fa-ban"></i> Reject Aadhar';
        verifyBtn.style.display = 'none';
        rejectBtn.style.display = 'inline-flex';
    }
    
    document.getElementById('actionRemarks').value = '';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeActionModal() {
    document.getElementById('actionModal').style.display = 'none';
    document.body.style.overflow = '';
    currentActionId = null;
    currentActionType = null;
}

async function confirmVerify() {
    if (!currentActionId) return;
    
    const remarks = document.getElementById('actionRemarks').value;
    const formData = new FormData();
    formData.append('id', currentActionId);
    formData.append('action', 'verify');
    formData.append('remarks', remarks);
    
    const res = await apiPost('../api/admin/verify-aadhar', formData);
    showToast(res.message, res.status === 'success' ? 'success' : 'error');
    
    if (res.status === 'success') {
        closeActionModal();
        loadStats();
        loadAadharList();
    }
}

async function confirmReject() {
    if (!currentActionId) return;
    
    const remarks = document.getElementById('actionRemarks').value;
    const formData = new FormData();
    formData.append('id', currentActionId);
    formData.append('action', 'reject');
    formData.append('remarks', remarks);
    
    const res = await apiPost('../api/admin/verify-aadhar', formData);
    showToast(res.message, res.status === 'success' ? 'success' : 'error');
    
    if (res.status === 'success') {
        closeActionModal();
        loadStats();
        loadAadharList();
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Close modals on outside click
window.onclick = function(event) {
    const viewModal = document.getElementById('viewModal');
    const actionModal = document.getElementById('actionModal');
    if (event.target === viewModal) {
        closeViewModal();
    }
    if (event.target === actionModal) {
        closeActionModal();
    }
}

// Initial load
loadStats();
loadAadharList();
</script>

<?php renderFooter('admin'); ?>