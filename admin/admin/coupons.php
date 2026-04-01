<?php
require_once '../common/auth.php';
require_once '../common/layout.php';

requirePageRole([ROLE_ADMIN]);

renderHeader('Manage Coupons');
renderSidebarMenu('coupons', 'admin');
renderMainContentStart('Manage Coupons', $_SESSION['username'] ?? 'Admin');
?>

<style>
/* ===== PAGE ===== */
.coupon-page {
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
}

.page-card-header i {
    margin-right: 8px;
    color: #ff6b35;
}

.page-card-body {
    padding: 20px;
}

/* ===== FORM ===== */
.form-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    align-items: end;
}

.form-control, .form-select {
    padding: 10px 14px;
    border: 1px solid #d1d9e8;
    border-radius: 12px;
    font-size: 14px;
    font-family: inherit;
    background: #fff;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #ff6b35;
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,107,53,0.1);
}

/* ===== BUTTON ===== */
.btn {
    border: none;
    border-radius: 40px;
    padding: 10px 18px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn i {
    font-size: 14px;
}

.btn-primary {
    background: #ff6b35;
    color: #fff;
}

.btn-primary:hover {
    background: #e55a2a;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #64748b;
    color: #fff;
}

.btn-secondary:hover {
    background: #475569;
    transform: translateY(-1px);
}

.btn-warning {
    background: #f59e0b;
    color: #fff;
}

.btn-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
}

.btn-danger {
    background: #ef4444;
    color: #fff;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-success {
    background: #10b981;
    color: #fff;
}

.btn-success:hover {
    background: #059669;
    transform: translateY(-1px);
}

/* ===== MODAL ===== */
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
    box-sizing: border-box;
}

.modal-content {
    background-color: #fff;
    margin: 20px auto;
    padding: 0;
    border-radius: 20px;
    width: 90%;
    max-width: 700px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    animation: slideDown 0.3s ease;
    position: relative;
}

/* Responsive Modal Styles */
@media (max-width: 768px) {
    .modal {
        padding: 10px;
        align-items: flex-start;
    }
    
    .modal-content {
        margin: 10px auto;
        width: 95%;
        border-radius: 16px;
    }
    
    .modal-header {
        padding: 16px 20px;
        position: sticky;
        top: 0;
        background: #fafcff;
        z-index: 10;
    }
    
    .modal-header h3 {
        font-size: 16px;
    }
    
    .modal-body {
        padding: 20px;
        max-height: calc(100vh - 140px);
        overflow-y: auto;
    }
    
    .modal-footer {
        padding: 16px 20px;
        position: sticky;
        bottom: 0;
        background: #fff;
        border-top: 1px solid #edf1f5;
    }
    
    .modal-footer .btn {
        flex: 1;
        padding: 10px 16px;
    }
}

@media (max-width: 480px) {
    .modal-header {
        padding: 14px 16px;
    }
    
    .modal-header h3 {
        font-size: 15px;
    }
    
    .modal-body {
        padding: 16px;
    }
    
    .modal-footer {
        padding: 14px 16px;
        gap: 10px;
    }
    
    .modal-footer .btn {
        font-size: 12px;
        padding: 8px 12px;
    }
}

/* Custom Confirmation Modal - Chinese Style Design */
.confirm-modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 0;
    border-radius: 20px;
    width: 90%;
    max-width: 420px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    animation: slideDown 0.3s ease;
}

@media (max-width: 768px) {
    .confirm-modal-content {
        margin: 20% auto;
        width: 95%;
    }
}

@media (max-width: 480px) {
    .confirm-modal-content {
        margin: 30% auto;
        width: 92%;
    }
    
    .confirm-modal-header {
        padding: 24px 20px 12px 20px;
    }
    
    .confirm-modal-header i {
        font-size: 48px;
    }
    
    .confirm-modal-header h3 {
        font-size: 18px;
    }
    
    .confirm-modal-body {
        padding: 0 20px 20px 20px;
        font-size: 14px;
    }
    
    .confirm-modal-footer {
        padding: 16px 20px 20px 20px;
        gap: 10px;
    }
    
    .confirm-modal-footer .btn {
        flex: 1;
        padding: 8px 12px;
        font-size: 12px;
    }
}

.confirm-modal-header {
    padding: 28px 24px 16px 24px;
    text-align: center;
    border-bottom: none;
}

.confirm-modal-header i {
    font-size: 56px;
    margin-bottom: 16px;
}

.confirm-modal-header.warning i {
    color: #f59e0b;
}

.confirm-modal-header.danger i {
    color: #ef4444;
}

.confirm-modal-header.success i {
    color: #10b981;
}

.confirm-modal-header.info i {
    color: #3b82f6;
}

.confirm-modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
}

.confirm-modal-body {
    padding: 0 24px 24px 24px;
    text-align: center;
    color: #475569;
    font-size: 15px;
    line-height: 1.5;
}

.confirm-modal-footer {
    padding: 16px 24px 24px 24px;
    display: flex;
    justify-content: center;
    gap: 12px;
    border-top: 1px solid #edf1f5;
}

.modal-header {
    padding: 18px 24px;
    border-bottom: 1px solid #edf1f5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fafcff;
    border-radius: 20px 20px 0 0;
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
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

/* Form fields inside modal */
.modal-body .form-group {
    margin-bottom: 18px;
}

.modal-body label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    font-size: 13px;
    color: #334155;
}

.modal-body .form-control,
.modal-body .form-select {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d9e8;
    border-radius: 12px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.modal-body .form-control:focus,
.modal-body .form-select:focus {
    border-color: #ff6b35;
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,107,53,0.1);
}

/* Two column layout for modal on larger screens */
@media (min-width: 769px) {
    .modal-body .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
}

.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #edf1f5;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: #fff;
    border-radius: 0 0 20px 20px;
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

/* ===== COUPON GRID ===== */
.coupon-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.coupon-card {
    background: #fff;
    border: 1px solid #e9edf2;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: all 0.2s ease;
    position: relative;
}

.coupon-card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    transform: translateY(-2px);
}

.coupon-code-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    padding: 8px 12px;
    border-radius: 12px;
    justify-content: space-between;
}

.coupon-code {
    font-size: 20px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.5px;
    font-family: monospace;
    word-break: break-word;
    flex: 1;
}

.copy-code-btn {
    background: #e2e8f0;
    border: none;
    border-radius: 8px;
    padding: 6px 10px;
    cursor: pointer;
    transition: all 0.2s;
    color: #475569;
}

.copy-code-btn:hover {
    background: #cbd5e1;
    color: #0f172a;
    transform: scale(1.05);
}

.copy-code-btn i {
    font-size: 14px;
}

.coupon-desc {
    font-size: 13px;
    color: #475569;
    line-height: 1.4;
    padding: 4px 0;
}

.coupon-desc i {
    margin-right: 6px;
    color: #64748b;
    font-size: 12px;
}

.coupon-badge-group {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin: 8px 0;
}

.coupon-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 40px;
    font-size: 12px;
    font-weight: 500;
    background: #f1f5f9;
    color: #1e293b;
}

.coupon-badge i {
    font-size: 11px;
}

.coupon-badge.discount {
    background: #ffedd5;
    color: #c2410c;
}

.coupon-badge.type {
    background: #e0e7ff;
    color: #3730a3;
}

.coupon-badge.active {
    background: #dcfce7;
    color: #15803d;
}

.coupon-badge.inactive {
    background: #fee2e2;
    color: #b91c1c;
}

.usage {
    font-size: 13px;
    color: #475569;
    background: #f8fafc;
    padding: 8px 12px;
    border-radius: 12px;
    margin: 4px 0;
}

.usage i {
    margin-right: 6px;
    color: #64748b;
}

.amount-info {
    font-size: 13px;
    color: #334155;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: space-between;
    background: #f8fafc;
    padding: 8px 12px;
    border-radius: 12px;
}

.amount-info i {
    margin-right: 4px;
    color: #64748b;
}

.validity {
    font-size: 12px;
    color: #5b6e8c;
    background: #f1f5f9;
    padding: 6px 10px;
    border-radius: 10px;
    text-align: center;
}

.validity i {
    margin-right: 4px;
    font-size: 11px;
}

.card-actions {
    display: flex;
    gap: 10px;
    margin-top: 8px;
}

.card-actions .btn {
    flex: 1;
}

/* ===== TOAST ===== */
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

.toast-notification i {
    font-size: 16px;
}

.toast-notification.success {
    background: #10b981;
}

.toast-notification.error {
    background: #ef4444;
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

/* ===== LOADING ===== */
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
    padding: 40px;
    color: #94a3b8;
    font-size: 15px;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 12px;
    display: block;
    color: #cbd5e1;
}

/* ===== RESPONSIVE ===== */
@media(max-width: 1100px) {
    .coupon-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media(max-width: 768px) {
    .coupon-grid {
        grid-template-columns: 1fr;
    }
    .form-grid {
        grid-template-columns: 1fr;
    }
    .page-card-header {
        padding: 14px 16px;
        font-size: 16px;
    }
    .page-card-body {
        padding: 16px;
    }
    .toast-notification {
        bottom: 16px;
        right: 16px;
        left: 16px;
        text-align: center;
        justify-content: center;
    }
}
</style>

<div class="coupon-page">

    <!-- ADD COUPON CARD -->
    <section class="page-card">
        <div class="page-card-header">
            <i class="fas fa-plus-circle"></i> Create New Coupon
        </div>
        <div class="page-card-body">
            <form id="couponForm" class="form-grid">
                <input type="text" name="code" class="form-control" placeholder="Coupon Code *" required>
                <input type="text" name="description" class="form-control" placeholder="Description" required>
                
                <select name="discount_type" class="form-select" required>
                    <option value="fixed">Flat (Fixed ₹)</option>
                    <option value="percentage">Percentage (%)</option>
                </select>
                
                <input type="number" name="discount" class="form-control" placeholder="Discount Amount *" step="any" required>
                <input type="number" name="min_order_amount" class="form-control" placeholder="Min Order Amount" step="any" value="0">
                <input type="number" name="max_discount_amount" class="form-control" placeholder="Max Discount (for %)" step="any">
                
                <input type="date" name="valid_from" class="form-control" required>
                <input type="date" name="valid_to" class="form-control" required>
                <input type="number" name="usage_limit" class="form-control" placeholder="Usage Limit Ex-10" >
                
                <select name="is_active" class="form-select">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Coupon
                </button>
            </form>
        </div>
    </section>

    <!-- COUPON LIST CARD -->
    <section class="page-card">
        <div class="page-card-header">
            <i class="fas fa-tags"></i> All Coupons
        </div>
        <div class="page-card-body">
            <div id="couponList">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-pulse"></i> Loading coupons...
                </div>
            </div>
        </div>
    </section>

</div>

<!-- Edit Modal - Fully Responsive -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Coupon</h3>
            <span class="close-modal" onclick="closeEditModal()">&times;</span>
        </div>
        <form id="editCouponForm">
            <div class="modal-body">
                <input type="hidden" name="coupon_id" id="edit_coupon_id">
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Coupon Code *</label>
                    <input type="text" name="code" id="edit_code" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description</label>
                    <input type="text" name="description" id="edit_description" class="form-control">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-percent"></i> Discount Type *</label>
                        <select name="discount_type" id="edit_discount_type" class="form-select" required>
                            <option value="fixed">Flat (Fixed ₹)</option>
                            <option value="percentage">Percentage (%)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-rupee-sign"></i> Discount Amount *</label>
                        <input type="number" name="discount" id="edit_discount" class="form-control" step="any" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-shopping-cart"></i> Min Order Amount</label>
                        <input type="number" name="min_order_amount" id="edit_min_order_amount" class="form-control" step="any" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-chart-line"></i> Max Discount (for %)</label>
                        <input type="number" name="max_discount_amount" id="edit_max_discount_amount" class="form-control" step="any">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Valid From *</label>
                        <input type="date" name="valid_from" id="edit_valid_from" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Valid To *</label>
                        <input type="date" name="valid_to" id="edit_valid_to" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-chart-bar"></i> Usage Limit</label>
                        <input type="number" name="usage_limit" id="edit_usage_limit" class="form-control" value="0">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-power-off"></i> Status</label>
                        <select name="is_active" id="edit_is_active" class="form-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Coupon
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div id="confirmModal" class="modal">
    <div class="confirm-modal-content">
        <div class="confirm-modal-header" id="confirmHeader">
            <i class="fas fa-exclamation-triangle" id="confirmIcon"></i>
            <h3 id="confirmTitle">Confirm Action</h3>
        </div>
        <div class="confirm-modal-body" id="confirmMessage">
            Are you sure you want to perform this action?
        </div>
        <div class="confirm-modal-footer">
            <button class="btn btn-secondary" id="confirmCancelBtn">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="btn btn-danger" id="confirmOkBtn">
                <i class="fas fa-check"></i> Confirm
            </button>
        </div>
    </div>
</div>

<script>
// Custom Confirmation Modal Function
function showConfirmModal(options) {
    return new Promise((resolve) => {
        const modal = document.getElementById('confirmModal');
        const header = document.getElementById('confirmHeader');
        const icon = document.getElementById('confirmIcon');
        const title = document.getElementById('confirmTitle');
        const message = document.getElementById('confirmMessage');
        const cancelBtn = document.getElementById('confirmCancelBtn');
        const okBtn = document.getElementById('confirmOkBtn');
        
        if (options.type === 'danger') {
            header.className = 'confirm-modal-header danger';
            icon.className = 'fas fa-exclamation-triangle';
            okBtn.className = 'btn btn-danger';
        } else if (options.type === 'warning') {
            header.className = 'confirm-modal-header warning';
            icon.className = 'fas fa-exclamation-circle';
            okBtn.className = 'btn btn-warning';
        } else if (options.type === 'info') {
            header.className = 'confirm-modal-header info';
            icon.className = 'fas fa-info-circle';
            okBtn.className = 'btn btn-primary';
        } else {
            header.className = 'confirm-modal-header';
            icon.className = 'fas fa-question-circle';
            okBtn.className = 'btn btn-primary';
        }
        
        title.textContent = options.title || 'Confirm Action';
        message.textContent = options.message || 'Are you sure you want to perform this action?';
        okBtn.innerHTML = `<i class="fas fa-check"></i> ${options.confirmText || 'Confirm'}`;
        cancelBtn.innerHTML = `<i class="fas fa-times"></i> ${options.cancelText || 'Cancel'}`;
        
        modal.style.display = 'flex';
        
        const handleConfirm = () => {
            cleanup();
            resolve(true);
        };
        
        const handleCancel = () => {
            cleanup();
            resolve(false);
        };
        
        const handleOutsideClick = (e) => {
            if (e.target === modal) {
                cleanup();
                resolve(false);
            }
        };
        
        const cleanup = () => {
            modal.style.display = 'none';
            okBtn.removeEventListener('click', handleConfirm);
            cancelBtn.removeEventListener('click', handleCancel);
            modal.removeEventListener('click', handleOutsideClick);
        };
        
        okBtn.addEventListener('click', handleConfirm);
        cancelBtn.addEventListener('click', handleCancel);
        modal.addEventListener('click', handleOutsideClick);
    });
}

// Helper: API GET
async function apiGet(url) {
    try {
        const res = await fetch(url);
        return await res.json();
    } catch (err) {
        console.error('GET error:', err);
        return { status: 'error', message: 'Network error' };
    }
}

// Helper: API POST
async function apiPost(url, formData) {
    try {
        const res = await fetch(url, {
            method: 'POST',
            body: formData
        });
        return await res.json();
    } catch (err) {
        console.error('POST error:', err);
        return { status: 'error', message: 'Network error' };
    }
}

// Toast notification
function showToast(message, type = 'success') {
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) existingToast.remove();

    const toast = document.createElement('div');
    toast.className = `toast-notification ${type === 'success' ? 'success' : 'error'}`;
    const icon = type === 'success' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
    toast.innerHTML = `${icon} ${message}`;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Copy to clipboard
async function copyToClipboard(code, buttonElement) {
    try {
        await navigator.clipboard.writeText(code);
        const originalIcon = buttonElement.innerHTML;
        buttonElement.innerHTML = '<i class="fas fa-check"></i>';
        buttonElement.style.background = '#10b981';
        buttonElement.style.color = 'white';
        showToast(`Coupon code "${code}" copied!`, 'success');
        setTimeout(() => {
            buttonElement.innerHTML = originalIcon;
            buttonElement.style.background = '';
            buttonElement.style.color = '';
        }, 2000);
    } catch (err) {
        showToast('Failed to copy code', 'error');
    }
}

// Format date
function formatDate(dateStr) {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function formatDateForInput(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    return date.toISOString().split('T')[0];
}

// Load coupons
async function loadCoupons() {
    const container = document.getElementById('couponList');
    container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-pulse"></i> Loading coupons...</div>';
    
    const res = await apiGet('../api/admin/list-coupons');
    
    if (res.status === 'success' && res.data && res.data.coupons && res.data.coupons.length > 0) {
        let html = `<div class="coupon-grid">`;
        
        res.data.coupons.forEach(c => {
            let discountDisplay = '';
            let discountIcon = '';
            if (c.discount_type === 'percentage') {
                discountDisplay = `${c.discount_value}% OFF`;
                discountIcon = '<i class="fas fa-percent"></i>';
            } else {
                discountDisplay = `₹${parseFloat(c.discount_value).toFixed(2)} OFF`;
                discountIcon = '<i class="fas fa-rupee-sign"></i>';
            }
            
            const isActive = c.is_active == 1;
            const statusBadge = isActive ? 
                '<span class="coupon-badge active"><i class="fas fa-check-circle"></i> Active</span>' : 
                '<span class="coupon-badge inactive"><i class="fas fa-ban"></i> Inactive</span>';
            
            const usageLimit = c.usage_limit && c.usage_limit > 0 ? c.usage_limit : '∞';
            const usedCount = c.used_count || 0;
            const minOrder = c.min_order_amount && c.min_order_amount > 0 ? `Min: ₹${parseFloat(c.min_order_amount).toFixed(2)}` : 'No min order';
            
            let maxDiscountHtml = '';
            if (c.discount_type === 'percentage' && c.max_discount_amount && c.max_discount_amount > 0) {
                maxDiscountHtml = `<span><i class="fas fa-chart-line"></i> Max ₹${parseFloat(c.max_discount_amount).toFixed(2)}</span>`;
            }
            
            const validFrom = formatDate(c.valid_from);
            const validTo = formatDate(c.valid_to);
            
            html += `
            <div class="coupon-card">
                <div class="coupon-code-wrapper">
                    <div class="coupon-code"><i class="fas fa-tag"></i> ${escapeHtml(c.coupon_code)}</div>
                    <button class="copy-code-btn" onclick="copyToClipboard('${escapeHtml(c.coupon_code)}', this)" title="Copy coupon code">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                ${c.description ? `<div class="coupon-desc"><i class="fas fa-align-left"></i> ${escapeHtml(c.description)}</div>` : ''}
                
                <div class="coupon-badge-group">
                    <span class="coupon-badge discount">${discountIcon} ${discountDisplay}</span>
                    <span class="coupon-badge type"><i class="fas ${c.discount_type === 'percentage' ? 'fa-percent' : 'fa-coins'}"></i> ${c.discount_type === 'percentage' ? 'Percentage' : 'Fixed'}</span>
                    ${statusBadge}
                </div>
                
                <div class="amount-info">
                    <span><i class="fas fa-shopping-cart"></i> ${minOrder}</span>
                    ${maxDiscountHtml}
                </div>
                
                <div class="usage">
                    <i class="fas fa-chart-bar"></i> Used: ${usedCount} / ${usageLimit}
                </div>
                
                <div class="validity">
                    <i class="fas fa-calendar-alt"></i> ${validFrom} → ${validTo}
                </div>
                
                <div class="card-actions">
                    <button class="btn btn-warning btn-sm" onclick="openEditModal(${c.id})">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteCoupon(${c.id})">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>`;
        });
        
        html += `</div>`;
        container.innerHTML = html;
    } else if (res.status === 'success') {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-ticket-alt"></i> No coupons found. Create your first coupon above!</div>';
    } else {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i> Failed to load coupons. Please refresh.</div>';
    }
}

// Open edit modal
async function openEditModal(couponId) {
    const res = await apiGet(`../api/admin/get-coupon?id=${couponId}`);
    
    if (res.status === 'success' && res.data) {
        const coupon = res.data;
        
        document.getElementById('edit_coupon_id').value = coupon.id;
        document.getElementById('edit_code').value = coupon.coupon_code;
        document.getElementById('edit_description').value = coupon.description || '';
        document.getElementById('edit_discount_type').value = coupon.discount_type;
        document.getElementById('edit_discount').value = coupon.discount_value;
        document.getElementById('edit_min_order_amount').value = coupon.min_order_amount || 0;
        document.getElementById('edit_max_discount_amount').value = coupon.max_discount_amount || '';
        document.getElementById('edit_valid_from').value = formatDateForInput(coupon.valid_from);
        document.getElementById('edit_valid_to').value = formatDateForInput(coupon.valid_to);
        document.getElementById('edit_usage_limit').value = coupon.usage_limit || 0;
        document.getElementById('edit_is_active').value = coupon.is_active;
        
        document.getElementById('editModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    } else {
        showToast('Failed to load coupon data', 'error');
    }
}

// Close edit modal
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = '';
}

// Handle edit form
document.getElementById('editCouponForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Updating...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    const res = await apiPost('../api/admin/update-coupon', formData);
    showToast(res.message, res.status === 'success' ? 'success' : 'error');
    
    if (res.status === 'success') {
        closeEditModal();
        loadCoupons();
    }
    
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
});

// Handle create form
document.getElementById('couponForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Creating...';
    submitBtn.disabled = true;
    
    const formData = new FormData(this);
    const discount = formData.get('discount');
    
    if (discount && parseFloat(discount) <= 0) {
        showToast('Discount amount must be greater than 0', 'error');
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        return;
    }
    
    const res = await apiPost('../api/admin/add-coupon', formData);
    showToast(res.message, res.status === 'success' ? 'success' : 'error');
    
    if (res.status === 'success') {
        this.reset();
        this.querySelector('[name="min_order_amount"]').value = '0';
        this.querySelector('[name="usage_limit"]').value = '0';
        this.querySelector('[name="is_active"]').value = '1';
        loadCoupons();
    }
    
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
});

// Delete coupon
async function deleteCoupon(id) {
    const confirmed = await showConfirmModal({
        type: 'danger',
        title: 'Delete Coupon',
        message: 'Are you sure you want to delete this coupon? This action cannot be undone.',
        confirmText: 'Delete',
        cancelText: 'Cancel'
    });
    
    if (!confirmed) return;
    
    const fd = new FormData();
    fd.append('coupon_id', id);
    
    const res = await apiPost('../api/admin/delete-coupon', fd);
    showToast(res.message, res.status === 'success' ? 'success' : 'error');
    
    if (res.status === 'success') {
        loadCoupons();
    }
}

// Escape HTML
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Close modals on outside click
window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const confirmModal = document.getElementById('confirmModal');
    if (event.target === editModal) {
        closeEditModal();
    }
}

// Initial load
loadCoupons();
</script>

<?php renderFooter('admin'); ?>