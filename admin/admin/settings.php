<?php
require_once '../common/auth.php';
requirePageRole([ROLE_ADMIN]);
require_once '../common/layout.php';

renderHeader('Settings');
renderSidebarMenu('settings', 'admin');
renderMainContentStart('Settings', $_SESSION['username'] ?? 'Admin');
?>

<style>
.settings-page{
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

.settings-grid{
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

.input-control{
    width:100%;
    border:1px solid #dbe3eb;
    border-radius:12px;
    background:#fff;
    color:#1f2937;
    padding:12px 14px;
    outline:none;
    transition:.2s ease;
}

.input-control:focus{
    border-color:#ff6b35;
    box-shadow:0 0 0 3px rgba(255,107,53,0.10);
}

.helper-text{
    font-size:12px;
    color:#6b7280;
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

.info-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:18px;
}

.info-card{
    background:#fff;
    border:1px solid #edf1f5;
    border-radius:18px;
    box-shadow:0 2px 10px rgba(0,0,0,0.04);
    padding:18px;
    display:flex;
    flex-direction:column;
    gap:10px;
}

.info-label{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:14px;
    font-weight:600;
    color:#475569;
}

.info-label i{
    color:#ff6b35;
}

.info-value{
    font-size:24px;
    font-weight:800;
    color:#102a43;
    line-height:1.2;
    word-break:break-word;
}

.loading-state,
.empty-state{
    background:#fff;
    border:1px dashed #d8e2ec;
    border-radius:18px;
    padding:48px 20px;
    text-align:center;
    color:#64748b;
}

.loading-state i,
.empty-state i{
    font-size:36px;
    color:#ff6b35;
    margin-bottom:12px;
}

.loading-state h3,
.empty-state h3{
    font-size:18px;
    color:#102a43;
    margin-bottom:8px;
}

.loading-state p,
.empty-state p{
    margin:0;
    font-size:14px;
}

@media (max-width: 768px){
    .settings-page{
        gap:18px;
    }

    .page-card-header,
    .page-card-body{
        padding:16px;
    }

    .page-card-title{
        font-size:16px;
    }

    .settings-grid,
    .info-grid{
        grid-template-columns:1fr;
        gap:14px;
    }

    .form-actions{
        flex-direction:column;
        align-items:stretch;
    }

    .form-actions .btn{
        width:100%;
    }

    .info-value{
        font-size:20px;
    }
}

@media (max-width: 480px){
    .input-control{
        padding:11px 12px;
        font-size:14px;
    }

    .info-value{
        font-size:18px;
    }
}
</style>

<div class="settings-page">
    <section class="page-card">
        <div class="page-card-header">
            <div class="page-card-title">
                <i class="fas fa-sliders"></i>
                <span>System Settings</span>
            </div>
        </div>

        <div class="page-card-body">
            <form id="settingsForm">
                <div class="settings-grid">
                    <div class="form-group">
                        <label class="form-label" for="referral_amount">Referral Amount</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="referral_amount"
                            name="referral_amount"
                            class="input-control"
                            placeholder="Enter referral amount"
                        >
                        <div class="helper-text">Amount rewarded for each eligible referral.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="commission_percent">Commission Percent</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="commission_percent"
                            name="commission_percent"
                            class="input-control"
                            placeholder="Enter commission percent"
                        >
                        <div class="helper-text">Commission percentage applied to bookings.</div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </form>
        </div>
    </section>

    <section class="page-card">
        <div class="page-card-header">
            <div class="page-card-title">
                <i class="fas fa-circle-info"></i>
                <span>Current Settings</span>
            </div>
        </div>

        <div class="page-card-body">
            <div id="settingsInfo">
                <div class="loading-state">
                    <i class="fas fa-spinner fa-pulse"></i>
                    <h3>Loading settings...</h3>
                    <p>Please wait while we fetch current settings.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function escapeHtml(value) {
    if (value === null || value === undefined) return '';
    const div = document.createElement('div');
    div.textContent = String(value);
    return div.innerHTML;
}

function formatMoney(value) {
    const num = Number(value || 0);
    return num.toLocaleString('en-IN', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}

function formatPercent(value) {
    const num = Number(value || 0);
    return num.toLocaleString('en-IN', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });
}

function renderSettingsInfo(data) {
    const box = document.getElementById('settingsInfo');
    if (!box) return;

    const referralAmount = formatMoney(data.referral_amount || 0);
    const commissionPercent = formatPercent(data.commission_percent || 0);

    box.innerHTML =
        '<div class="info-grid">'
        +   '<div class="info-card">'
        +       '<div class="info-label"><i class="fas fa-gift"></i><span>Referral Amount</span></div>'
        +       '<div class="info-value">₹' + referralAmount + '</div>'
        +   '</div>'
        +   '<div class="info-card">'
        +       '<div class="info-label"><i class="fas fa-percent"></i><span>Commission Percent</span></div>'
        +       '<div class="info-value">' + commissionPercent + '%</div>'
        +   '</div>'
        + '</div>';
}

function renderSettingsError(message) {
    const box = document.getElementById('settingsInfo');
    if (!box) return;

    box.innerHTML =
        '<div class="empty-state">'
        + '<i class="fas fa-circle-exclamation"></i>'
        + '<h3>Unable to load settings</h3>'
        + '<p>' + escapeHtml(message || 'Something went wrong while fetching settings.') + '</p>'
        + '</div>';
}

async function loadSettings() {
    try {
        const res = await apiGet('../api/admin/settings');

        if (res && res.status === 'success' && res.data) {
            document.querySelector('[name="referral_amount"]').value = res.data.referral_amount || '';
            document.querySelector('[name="commission_percent"]').value = res.data.commission_percent || '';
            renderSettingsInfo(res.data);
        } else {
            renderSettingsError((res && res.message) ? res.message : 'Failed to load settings');
        }
    } catch (error) {
        console.error('Load settings error:', error);
        renderSettingsError('Failed to load settings. Please try again.');
    }
}

document.getElementById('settingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';

    try {
        const res = await apiPost('../api/admin/settings', new FormData(this));
        showToast((res && res.message) ? res.message : 'Settings saved', res && res.status === 'success' ? 'success' : 'error');

        if (res && res.status === 'success') {
            loadSettings();
        }
    } catch (error) {
        console.error('Save settings error:', error);
        showToast('Failed to save settings', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
});
</script>

<?php renderFooter('admin'); ?>