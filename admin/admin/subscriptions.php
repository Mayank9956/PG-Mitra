<?php
require_once '../common/auth.php';
require_once '../common/layout.php';

requirePageRole([ROLE_ADMIN]);

renderHeader('Manage Subscriptions');
renderSidebarMenu('subscriptions', 'admin');
renderMainContentStart('Subscription Plans Management', $_SESSION['username'] ?? 'Admin');
?>

<style>
/* ===== PAGE STYLES ===== */
.subscription-page {
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
    padding: 18px 24px;
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

.page-card-body {
    padding: 24px;
}

/* ===== BUTTONS ===== */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
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

.btn-warning {
    background: #f59e0b;
    color: #fff;
}

.btn-warning:hover {
    background: #d97706;
}

.btn-info {
    background: #3b82f6;
    color: #fff;
}

.btn-info:hover {
    background: #2563eb;
}

/* ===== TABLE ===== */
.table-container {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.plans-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    min-width: 800px;
}

.plans-table thead {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}

.plans-table th {
    padding: 14px 12px;
    text-align: left;
    font-weight: 600;
    color: #1e293b;
}

.plans-table td {
    padding: 14px 12px;
    border-bottom: 1px solid #edf1f5;
    vertical-align: middle;
}

.plans-table tr:hover {
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
}

.status-badge.active {
    background: #dcfce7;
    color: #10b981;
}

.status-badge.inactive {
    background: #fee2e2;
    color: #ef4444;
}

/* Price Styling */
.price {
    font-weight: 700;
    color: #ff6b35;
}

.original-price {
    text-decoration: line-through;
    font-size: 12px;
    color: #94a3b8;
    margin-right: 6px;
}

.discounted-price {
    font-weight: 700;
    color: #10b981;
}

.discount-badge {
    background: #fef3c7;
    color: #f59e0b;
    padding: 2px 6px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    margin-left: 6px;
}

/* ===== MODAL STYLES ===== */
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
    max-width: 600px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    animation: slideDown 0.3s ease;
}

.modal-header {
    padding: 20px 24px;
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
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #1e293b;
    font-size: 14px;
}

.form-group label i {
    margin-right: 6px;
    color: #ff6b35;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #ff6b35;
    box-shadow: 0 0 0 3px rgba(255,107,53,0.1);
}

select.form-control {
    cursor: pointer;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-hint {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 6px;
}

/* Loading & Empty States */
.loading-spinner {
    text-align: center;
    padding: 60px;
    color: #64748b;
}

.loading-spinner i {
    font-size: 40px;
    margin-bottom: 16px;
    display: block;
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

/* Animations */
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

/* Responsive */
@media (max-width: 768px) {
    .page-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .modal-content {
        width: 95%;
        margin: 10px auto;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .plans-table {
        font-size: 12px;
        min-width: 700px;
    }
    
    .plans-table th,
    .plans-table td {
        padding: 10px 8px;
    }
    
    .btn-sm {
        padding: 4px 8px;
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .page-card-body {
        padding: 16px;
    }
    
    .modal-footer {
        flex-direction: column-reverse;
    }
    
    .modal-footer .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="subscription-page">

    <!-- Header Actions -->
    <section class="page-card">
        <div class="page-card-header">
            <div>
                <i class="fas fa-crown"></i> Subscription Plans
            </div>
            <button class="btn btn-primary" onclick="openPlanModal()">
                <i class="fas fa-plus"></i> Add New Plan
            </button>
        </div>
        <div class="page-card-body">
            <div class="table-container">
                <div id="plansList">
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-pulse"></i>
                        <div>Loading subscription plans...</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- Add/Edit Plan Modal -->
<div id="planModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle"><i class="fas fa-plus"></i> Add New Plan</h3>
            <span class="close-modal" onclick="closePlanModal()">&times;</span>
        </div>
        <form id="planForm" onsubmit="savePlan(event)">
            <input type="hidden" id="planId" name="id" value="0">
            <div class="modal-body">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Plan Name</label>
                    <input type="text" class="form-control" id="planName" name="name" required placeholder="e.g., StayLite Monthly">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-link"></i> Slug</label>
                    <input type="text" class="form-control" id="planSlug" name="slug" required placeholder="e.g., staylite-monthly">
                    <div class="form-hint">URL-friendly identifier (auto-generated from name)</div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-calendar-alt"></i> Billing Type</label>
                        <select class="form-control" id="billingType" name="billing_type" required>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-clock"></i> Duration (Days)</label>
                        <input type="number" class="form-control" id="durationDays" name="duration_days" required placeholder="30">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-rupee-sign"></i> Price (₹)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required placeholder="199.00">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-percent"></i> Discount (%)</label>
                        <input type="number" class="form-control" id="discountPercent" name="discount_percent" value="0" placeholder="0">
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-toggle-on"></i> Status</label>
                    <select class="form-control" id="isActive" name="is_active">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                
                <div class="form-group" id="pricePreview">
                    <label><i class="fas fa-calculator"></i> Price Preview</label>
                    <div class="form-control" style="background: #f8fafc; cursor: default;">
                        <span id="previewText">₹199.00 / month</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closePlanModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Plan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-trash-alt"></i> Delete Plan</h3>
            <span class="close-modal" onclick="closeDeleteModal()">&times;</span>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete the plan <strong id="deletePlanName"></strong>?</p>
            <p class="text-sm text-gray-500 mt-2">This action cannot be undone. Users with active subscriptions will not be affected.</p>
            <input type="hidden" id="deletePlanId">
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="btn btn-danger" onclick="confirmDelete()">
                <i class="fas fa-trash"></i> Delete Permanently
            </button>
        </div>
    </div>
</div>

<script>
// ================= API HELPERS =================
async function apiGet(url) {
    try {
        const res = await fetch(url);
        const text = await res.text();

        try {
            return JSON.parse(text);
        } catch {
            console.error("Invalid JSON:", text);
            return { status: 'error', message: 'Invalid JSON response' };
        }

    } catch (err) {
        return { status: 'error', message: err.message };
    }
}

// FIXED: Proper API POST with FormData to avoid [object Object]
async function apiPost(url, data) {
    try {
        console.log('📤 Sending to:', url);
        console.log('📦 Data object:', data);
        
        // Convert to FormData instead of JSON
        const formData = new FormData();
        for (let key in data) {
            if (data.hasOwnProperty(key)) {
                formData.append(key, data[key]);
                console.log(`   ${key}: ${data[key]}`);
            }
        }
        
        const res = await fetch(url, {
            method: 'POST',
            body: formData  // Using FormData, not JSON
        });

        const text = await res.text();
        console.log('📥 Response:', text);

        try {
            return JSON.parse(text);
        } catch {
            console.error("❌ Invalid JSON:", text);
            return { status: 'error', message: 'Invalid JSON response from server' };
        }

    } catch (err) {
        console.error('❌ API Error:', err);
        return { status: 'error', message: err.message };
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

function formatPrice(price, discount = 0, billingType = 'monthly') {
    const originalPrice = parseFloat(price);
    const discountedPrice = discount > 0 ? originalPrice * (1 - discount / 100) : originalPrice;
    const period = billingType === 'monthly' ? 'month' : 'year';
    
    let html = '';
    if (discount > 0) {
        html += `<span class="original-price">₹${originalPrice.toFixed(2)}</span>`;
        html += `<span class="discounted-price">₹${discountedPrice.toFixed(2)}</span>`;
        html += `<span class="discount-badge">${discount}% OFF</span>`;
        html += `<span class="text-gray-500 ml-1">/ ${period}</span>`;
    } else {
        html += `<span class="price">₹${originalPrice.toFixed(2)}</span>`;
        html += `<span class="text-gray-500 ml-1">/ ${period}</span>`;
    }
    return html;
}

function getStatusBadge(isActive) {
    if (isActive == 1) {
        return '<span class="status-badge active"><i class="fas fa-check-circle"></i> Active</span>';
    } else {
        return '<span class="status-badge inactive"><i class="fas fa-ban"></i> Inactive</span>';
    }
}

// Load plans list
async function loadPlans() {
    const container = document.getElementById('plansList');
    container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-pulse"></i><div>Loading subscription plans...</div></div>';
    
    const res = await apiGet('../api/admin/list-plans');
    
    if (res.status === 'success' && res.data && res.data.plans && res.data.plans.length > 0) {
        let html = `
        <table class="plans-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Plan Name</th>
                    <th>Slug</th>
                    <th>Billing</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Discount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        `;
        
        res.data.plans.forEach(plan => {
            const priceHtml = formatPrice(plan.price, plan.discount_percent, plan.billing_type);
            html += `
                <tr>
                    <td>${plan.id}</td>
                    <td><strong>${escapeHtml(plan.name)}</strong></td>
                    <td><code class="text-xs">${escapeHtml(plan.slug)}</code></td>
                    <td><span class="capitalize">${plan.billing_type}</span></td>
                    <td>${priceHtml}</td>
                    <td>${plan.duration_days} days</td>
                    <td>${plan.discount_percent}%</td>
                    <td>${getStatusBadge(plan.is_active)}</td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="editPlan(${plan.id})" title="Edit">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="openDeleteModal(${plan.id}, '${escapeHtml(plan.name)}')" title="Delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
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
        container.innerHTML = '<div class="empty-state"><i class="fas fa-crown"></i> No subscription plans found. Click "Add New Plan" to create one.</div>';
    } else {
        container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i> Failed to load plans: ' + (res.message || 'Unknown error') + '</div>';
    }
}

// Edit plan function
async function editPlan(id) {
    openPlanModal(id);
}

// Open modal for add/edit
function openPlanModal(id = 0) {
    const modal = document.getElementById('planModal');
    const title = document.getElementById('modalTitle');
    
    if (id > 0) {
        title.innerHTML = '<i class="fas fa-edit"></i> Edit Plan';
        loadPlanData(id);
    } else {
        title.innerHTML = '<i class="fas fa-plus"></i> Add New Plan';
        document.getElementById('planForm').reset();
        document.getElementById('planId').value = '0';
        document.getElementById('price').value = '';
        document.getElementById('discountPercent').value = '0';
        document.getElementById('durationDays').value = '';
        document.getElementById('isActive').value = '1';
        updatePricePreview();
    }
    
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

async function loadPlanData(id) {
    const res = await apiGet(`../api/admin/get-plan?id=${id}`);
    
    if (res.status === 'success' && res.data) {
        const plan = res.data;
        document.getElementById('planId').value = plan.id;
        document.getElementById('planName').value = plan.name;
        document.getElementById('planSlug').value = plan.slug;
        document.getElementById('billingType').value = plan.billing_type;
        document.getElementById('durationDays').value = plan.duration_days;
        document.getElementById('price').value = plan.price;
        document.getElementById('discountPercent').value = plan.discount_percent;
        document.getElementById('isActive').value = plan.is_active;
        updatePricePreview();
    } else {
        showToast('Failed to load plan data', 'error');
        closePlanModal();
    }
}

function closePlanModal() {
    document.getElementById('planModal').style.display = 'none';
    document.body.style.overflow = '';
}

// FIXED: Save plan using FormData (no [object Object] issue)
async function savePlan(event) {
    event.preventDefault();
    
    // Create FormData object - this will NOT send [object Object]
    const formData = new FormData();
    formData.append('id', document.getElementById('planId').value || '0');
    formData.append('name', document.getElementById('planName').value.trim());
    formData.append('slug', document.getElementById('planSlug').value.trim().toLowerCase());
    formData.append('billing_type', document.getElementById('billingType').value);
    formData.append('duration_days', document.getElementById('durationDays').value);
    formData.append('price', document.getElementById('price').value);
    formData.append('discount_percent', document.getElementById('discountPercent').value || '0');
    formData.append('is_active', document.getElementById('isActive').value);
    
    // Validation
    const name = formData.get('name');
    const slug = formData.get('slug');
    const duration_days = parseInt(formData.get('duration_days'));
    const price = parseFloat(formData.get('price'));
    const discount_percent = parseInt(formData.get('discount_percent'));
    
    if (name.length < 3) {
        showToast('Plan name must be at least 3 characters', 'error');
        return;
    }
    
    if (!slug.match(/^[a-z0-9-]+$/)) {
        showToast('Slug can only contain lowercase letters, numbers, and hyphens', 'error');
        return;
    }
    
    if (duration_days <= 0) {
        showToast('Duration must be greater than 0 days', 'error');
        return;
    }
    
    if (price <= 0) {
        showToast('Price must be greater than 0', 'error');
        return;
    }
    
    if (discount_percent < 0 || discount_percent > 100) {
        showToast('Discount must be between 0 and 100', 'error');
        return;
    }
    
    // Show loading on button
    const submitBtn = document.querySelector('#planForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Saving...';
    submitBtn.disabled = true;
    
    // Determine which API to call
    let apiUrl = '';
    const planId = parseInt(formData.get('id'));
    if (planId > 0) {
        apiUrl = '../api/admin/update-plan';
    } else {
        apiUrl = '../api/admin/create-plan';
    }
    
    console.log('Calling API:', apiUrl);
    console.log('FormData entries:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    const res = await apiPost(apiUrl, formData);
    
    if (res.status === 'success') {
        showToast(res.message, 'success');
        closePlanModal();
        loadPlans();
    } else {
        showToast(res.message || 'Failed to save plan', 'error');
    }
    
    submitBtn.innerHTML = originalText;
    submitBtn.disabled = false;
}

// Update price preview
function updatePricePreview() {
    const price = parseFloat(document.getElementById('price').value) || 0;
    const discount = parseInt(document.getElementById('discountPercent').value) || 0;
    const billingType = document.getElementById('billingType').value;
    
    const discountedPrice = discount > 0 ? price * (1 - discount / 100) : price;
    const period = billingType === 'monthly' ? 'month' : 'year';
    
    let previewHtml = '';
    if (discount > 0 && price > 0) {
        previewHtml = `<span style="text-decoration: line-through; color: #94a3b8;">₹${price.toFixed(2)}</span> 
                       <span style="color: #10b981; font-weight: bold;">₹${discountedPrice.toFixed(2)}</span>
                       <span style="background: #fef3c7; color: #f59e0b; padding: 2px 8px; border-radius: 20px; font-size: 11px; margin-left: 8px;">${discount}% OFF</span>
                       <span style="color: #64748b;"> / ${period}</span>`;
    } else if (price > 0) {
        previewHtml = `<span style="color: #ff6b35; font-weight: bold;">₹${price.toFixed(2)}</span>
                       <span style="color: #64748b;"> / ${period}</span>`;
    } else {
        previewHtml = `<span style="color: #94a3b8;">Enter price above</span>`;
    }
    
    document.getElementById('previewText').innerHTML = previewHtml;
}

// Auto-generate slug from name
document.addEventListener('DOMContentLoaded', function() {
    const planNameInput = document.getElementById('planName');
    if (planNameInput) {
        planNameInput.addEventListener('input', function() {
            const name = this.value;
            const planId = document.getElementById('planId').value;
            if (planId == '0') {
                const slug = name.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                document.getElementById('planSlug').value = slug;
            }
        });
    }
    
    // Update preview on price/discount change
    const priceInput = document.getElementById('price');
    const discountInput = document.getElementById('discountPercent');
    const billingTypeSelect = document.getElementById('billingType');
    
    if (priceInput) priceInput.addEventListener('input', updatePricePreview);
    if (discountInput) discountInput.addEventListener('input', updatePricePreview);
    if (billingTypeSelect) billingTypeSelect.addEventListener('change', updatePricePreview);
});

// Delete modal functions
let deleteId = 0;

function openDeleteModal(id, name) {
    deleteId = id;
    document.getElementById('deletePlanName').innerText = name;
    document.getElementById('deletePlanId').value = id;
    document.getElementById('deleteModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    document.body.style.overflow = '';
    deleteId = 0;
}

async function confirmDelete() {
    const id = deleteId;
    if (!id) return;
    
    const deleteBtn = document.querySelector('#deleteModal .btn-danger');
    const originalText = deleteBtn.innerHTML;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i> Deleting...';
    deleteBtn.disabled = true;
    
    const formData = new FormData();
    formData.append('id', id);
    
    const res = await apiPost('../api/admin/delete-plan', formData);
    
    if (res.status === 'success') {
        showToast(res.message, 'success');
        closeDeleteModal();
        loadPlans();
    } else {
        showToast(res.message || 'Failed to delete plan', 'error');
    }
    
    deleteBtn.innerHTML = originalText;
    deleteBtn.disabled = false;
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
    const planModal = document.getElementById('planModal');
    const deleteModal = document.getElementById('deleteModal');
    if (event.target === planModal) {
        closePlanModal();
    }
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
}

// Initial load
loadPlans();
</script>

<?php renderFooter('admin'); ?>