<?php
require_once '../common/auth.php';
requirePageRole([ROLE_OWNER]);
require_once '../common/layout.php';


$user = currentUser();

// Get owner's rooms
$rooms = [];
try {
    $stmt = $pdo->prepare("SELECT id, title, city, price, pg_type FROM rooms WHERE owner_id = ? ORDER BY id DESC");
    $stmt->execute([$user['id']]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error
}

$selected_room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : (count($rooms) > 0 ? $rooms[0]['id'] : 0);

renderHeader('Manage Rules');
renderSidebarMenu('rules', 'owner');
renderMainContentStart('Manage Rules & Instructions', $_SESSION['username'] ?? 'Owner');
?>

<style>
/* Room Selector Styles */
.room-selector-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 24px;
    color: white;
}

.room-selector-title {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.room-selector {
    background: white;
    border-radius: 12px;
    padding: 12px 16px;
    width: 100%;
    font-size: 16px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    color: #1e293b;
}

.room-selector:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,255,255,0.3);
}

.selected-room-info {
    margin-top: 12px;
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    font-size: 13px;
}

.selected-room-info span {
    background: rgba(255,255,255,0.2);
    padding: 4px 12px;
    border-radius: 20px;
}

/* Rules Section Styles */
.rules-section {
    background: white;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e9ecef;
}

.section-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
}

.add-btn {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.add-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}

.rules-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.rules-list li {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #f8f9fa;
    border-radius: 10px;
    margin-bottom: 8px;
    transition: all 0.2s ease;
}

.rules-list li:hover {
    background: #f1f5f9;
    transform: translateX(4px);
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #94a3b8;
    background: #f8f9fa;
    border-radius: 12px;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 12px;
    display: block;
}

.rule-content {
    display: flex;
    align-items: center;
    gap: 12px;
    flex: 1;
}

.rule-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 10px;
    font-size: 18px;
}

.rule-text {
    font-size: 14px;
    color: #374151;
}

.rule-actions {
    display: flex;
    gap: 8px;
}

.edit-btn, .delete-btn {
    background: none;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 14px;
}

.edit-btn {
    color: #3b82f6;
}

.edit-btn:hover {
    background: #dbeafe;
}

.delete-btn {
    color: #ef4444;
}

.delete-btn:hover {
    background: #fee2e2;
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(4px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.modal {
    background: white;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    max-height: 85vh;
    overflow-y: auto;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
    from {
        transform: translateY(-30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    background: white;
    border-radius: 20px 20px 0 0;
}

.modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #94a3b8;
    width: 32px;
    height: 32px;
    border-radius: 8px;
}

.modal-close:hover {
    background: #f1f5f9;
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    position: sticky;
    bottom: 0;
    background: white;
}

/* Icon Picker */
.icon-picker-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(55px, 1fr));
    gap: 8px;
    max-height: 200px;
    overflow-y: auto;
    padding: 12px;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    background: #f8f9fa;
    margin-top: 8px;
}

.icon-option {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 10px;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
    border: 1px solid #e9ecef;
}

.icon-option:hover {
    background: #eff6ff;
    border-color: #3b82f6;
}

.icon-option.selected {
    background: #3b82f6;
    border-color: #3b82f6;
    color: white;
}

.icon-option i {
    font-size: 18px;
}

.icon-option span {
    font-size: 9px;
    text-align: center;
}

/* Form Styles */
.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: block;
    font-weight: 500;
    color: #374151;
    font-size: 14px;
    margin-bottom: 6px;
}

.form-group select,
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
}

.btn-cancel {
    background: #f1f5f9;
    color: #475569;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    cursor: pointer;
}

.btn-save {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 8px 20px;
    border-radius: 8px;
    cursor: pointer;
}

.btn-save:hover {
    background: #2563eb;
}

/* Toast - Replace your existing toast CSS with this */
.toast {
    position: fixed !important;
    bottom: 24px !important;
    right: 24px !important;
    left: auto !important;
    top: auto !important;
    padding: 12px 20px !important;
    border-radius: 8px !important;
    color: white !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    z-index: 9999999 !important;
    opacity: 0 !important;
    visibility: hidden !important;
    transform: translateY(20px) !important;
    transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55) !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2) !important;
    pointer-events: none !important;
    min-width: 200px !important;
    max-width: 350px !important;
}

.toast.show {
    opacity: 1 !important;
    visibility: visible !important;
    transform: translateY(0) !important;
    pointer-events: auto !important;
}

.toast.success { 
    background: linear-gradient(135deg, #10b981, #059669) !important;
}

.toast.error { 
    background: linear-gradient(135deg, #ef4444, #dc2626) !important;
}

.toast.info { 
    background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
}

/* Loading */
.loading {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid white;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<!-- Main Content -->
<div class="room-selector-card">
    <div class="room-selector-title">Select Room to Manage Rules & Instructions</div>
    <select id="roomSelector" class="room-selector" onchange="changeRoom()">
        <option value="">-- Select a Room --</option>
        <?php foreach ($rooms as $room): ?>
            <option value="<?php echo $room['id']; ?>" <?php echo $selected_room_id == $room['id'] ? 'selected' : ''; ?>>
                #<?php echo $room['id']; ?> - <?php echo htmlspecialchars($room['title']); ?> (₹<?php echo $room['price']; ?>) - <?php echo ucfirst($room['pg_type']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <div id="selectedRoomInfo" class="selected-room-info">
        <!-- Dynamic room info will be shown here -->
    </div>
</div>

<!-- Rules Section -->
<div class="rules-section">
    <div class="section-header">
        <h3><i class="fas fa-gavel"></i> Rules & Policies</h3>
        <button class="add-btn" onclick="openRuleModal()">
            <i class="fas fa-plus"></i> Add Rule
        </button>
    </div>
    
    <div id="allowedRulesContainer">
        <h4 style="color: #10b981; margin-bottom: 12px;">
            <i class="fas fa-check-circle"></i> Allowed
        </h4>
        <ul id="allowedRulesList" class="rules-list">
            <li class="empty-state">Select a room to view rules</li>
        </ul>
    </div>
    
    <div id="notAllowedRulesContainer" style="margin-top: 24px;">
        <h4 style="color: #ef4444; margin-bottom: 12px;">
            <i class="fas fa-times-circle"></i> Not Allowed
        </h4>
        <ul id="notAllowedRulesList" class="rules-list">
            <li class="empty-state">Select a room to view rules</li>
        </ul>
    </div>
</div>

<!-- Instructions Section -->
<div class="rules-section">
    <div class="section-header">
        <h3><i class="fas fa-info-circle"></i> Important Instructions</h3>
        <button class="add-btn" onclick="openInstructionModal()">
            <i class="fas fa-plus"></i> Add Instruction
        </button>
    </div>
    
    <ul id="instructionsList" class="rules-list">
        <li class="empty-state">Select a room to view instructions</li>
    </ul>
</div>

<!-- Rule Modal -->
<div id="ruleModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 id="ruleModalTitle">Add Rule</h3>
            <button class="modal-close" onclick="closeRuleModal()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="ruleId">
            <input type="hidden" id="ruleRoomId">
            
            <div class="form-group">
                <label>Rule Type</label>
                <select id="ruleType">
                    <option value="allowed">✅ Allowed</option>
                    <option value="not_allowed">❌ Not Allowed</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Rule Text</label>
                <input type="text" id="ruleText" placeholder="e.g., Couples allowed">
            </div>
            
            <div class="form-group">
                <label>Select Icon</label>
                <div id="iconPickerGrid" class="icon-picker-grid"></div>
                <input type="hidden" id="selectedIcon" value="fa-check-circle">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeRuleModal()">Cancel</button>
            <button class="btn-save" onclick="saveRule()">Save Rule</button>
        </div>
    </div>
</div>

<!-- Instruction Modal -->
<div id="instructionModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 id="instructionModalTitle">Add Instruction</h3>
            <button class="modal-close" onclick="closeInstructionModal()">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="instructionId">
            <input type="hidden" id="instructionRoomId">
            
            <div class="form-group">
                <label>Instruction Text</label>
                <textarea id="instructionText" rows="3" placeholder="e.g., Carry valid ID proof at check-in"></textarea>
            </div>
            
            <div class="form-group">
                <label>Select Icon</label>
                <div id="instructionIconPickerGrid" class="icon-picker-grid"></div>
                <input type="hidden" id="selectedInstructionIcon" value="fa-info-circle">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeInstructionModal()">Cancel</button>
            <button class="btn-save" onclick="saveInstruction()">Save Instruction</button>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay">
    <div class="modal" style="max-width: 400px;">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="deleteMessage">Are you sure you want to delete this item?</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button class="btn-save" id="confirmDeleteBtn" style="background: #ef4444;">Delete</button>
        </div>
    </div>
</div>

<!-- Toast -->
<div id="toast" class="toast"></div>

<script>
// ================= FONT AWESOME ICONS =================
const fontAwesomeIcons = [
    { name: "fa-check-circle", class: "fas fa-check-circle", label: "Check" },
    { name: "fa-times-circle", class: "fas fa-times-circle", label: "Cross" },
    { name: "fa-clock", class: "fas fa-clock", label: "Clock" },
    { name: "fa-id-card", class: "fas fa-id-card", label: "ID Card" },
    { name: "fa-rupee-sign", class: "fas fa-rupee-sign", label: "Rupee" },
    { name: "fa-calendar", class: "fas fa-calendar", label: "Calendar" },
    { name: "fa-users", class: "fas fa-users", label: "Users" },
    { name: "fa-smoking-ban", class: "fas fa-smoking-ban", label: "No Smoking" },
    { name: "fa-glass-cheers", class: "fas fa-glass-cheers", label: "Alcohol" },
    { name: "fa-paw", class: "fas fa-paw", label: "Pets" },
    { name: "fa-wifi", class: "fas fa-wifi", label: "WiFi" },
    { name: "fa-parking", class: "fas fa-parking", label: "Parking" },
    { name: "fa-utensils", class: "fas fa-utensils", label: "Food" },
    { name: "fa-tv", class: "fas fa-tv", label: "TV" },
    { name: "fa-snowflake", class: "fas fa-snowflake", label: "AC" },
    { name: "fa-fan", class: "fas fa-fan", label: "Fan" },
    { name: "fa-bed", class: "fas fa-bed", label: "Bed" },
    { name: "fa-shower", class: "fas fa-shower", label: "Shower" },
    { name: "fa-laptop", class: "fas fa-laptop", label: "Study" },
    { name: "fa-dumbbell", class: "fas fa-dumbbell", label: "Gym" },
    { name: "fa-male", class: "fas fa-male", label: "Men" },
    { name: "fa-female", class: "fas fa-female", label: "Women" },
    { name: "fa-venus-mars", class: "fas fa-venus-mars", label: "Unisex" },
    { name: "fa-car", class: "fas fa-car", label: "Car" },
    { name: "fa-key", class: "fas fa-key", label: "Security" },
    { name: "fa-camera", class: "fas fa-camera", label: "CCTV" },
    { name: "fa-bell", class: "fas fa-bell", label: "Alert" },
    { name: "fa-fire-extinguisher", class: "fas fa-fire-extinguisher", label: "Fire Safety" },
    { name: "fa-tint", class: "fas fa-tint", label: "Water" },
    { name: "fa-bolt", class: "fas fa-bolt", label: "Power" },
    { name: "fa-trash-alt", class: "fas fa-trash-alt", label: "Waste" },
    { name: "fa-hand-sparkles", class: "fas fa-hand-sparkles", label: "Hygiene" },
    { name: "fa-phone-alt", class: "fas fa-phone-alt", label: "Contact" },
    { name: "fa-map-marker-alt", class: "fas fa-map-marker-alt", label: "Location" },
    { name: "fa-building", class: "fas fa-building", label: "Building" },
    { name: "fa-home", class: "fas fa-home", label: "Home" },
    { name: "fa-heart", class: "fas fa-heart", label: "Heart" },
    { name: "fa-star", class: "fas fa-star", label: "Star" }
];

// ================= GLOBAL VARIABLES =================
let currentRoomId = <?php echo $selected_room_id; ?>;

// ================= POPULATE ICON PICKERS =================
function populateIconPickers() {
    const grids = ['iconPickerGrid', 'instructionIconPickerGrid'];
    
    grids.forEach(gridId => {
        const grid = document.getElementById(gridId);
        if (!grid) return;
        
        grid.innerHTML = '';
        fontAwesomeIcons.forEach(icon => {
            const div = document.createElement('div');
            div.className = 'icon-option';
            div.setAttribute('data-icon', icon.name);
            div.innerHTML = `<i class="${icon.class}"></i><span>${icon.label}</span>`;
            div.onclick = () => selectIcon(gridId, icon.name, div);
            grid.appendChild(div);
        });
    });
}

function selectIcon(gridId, iconName, element) {
    const grid = document.getElementById(gridId);
    grid.querySelectorAll('.icon-option').forEach(opt => opt.classList.remove('selected'));
    element.classList.add('selected');
    
    if (gridId === 'iconPickerGrid') {
        document.getElementById('selectedIcon').value = iconName;
    } else {
        document.getElementById('selectedInstructionIcon').value = iconName;
    }
}

// ================= CHANGE ROOM =================
function changeRoom() {
    const selector = document.getElementById('roomSelector');
    currentRoomId = parseInt(selector.value);
    
    if (currentRoomId) {
        document.getElementById('ruleRoomId').value = currentRoomId;
        document.getElementById('instructionRoomId').value = currentRoomId;
        loadRulesAndInstructions();
        updateRoomInfo();
    } else {
        // Reset displays
        document.getElementById('allowedRulesList').innerHTML = '<li class="empty-state">Select a room to view rules</li>';
        document.getElementById('notAllowedRulesList').innerHTML = '<li class="empty-state">Select a room to view rules</li>';
        document.getElementById('instructionsList').innerHTML = '<li class="empty-state">Select a room to view instructions</li>';
        document.getElementById('selectedRoomInfo').innerHTML = '';
    }
}

function updateRoomInfo() {
    const selector = document.getElementById('roomSelector');
    const selectedOption = selector.options[selector.selectedIndex];
    const infoDiv = document.getElementById('selectedRoomInfo');
    
    if (selectedOption.value) {
        infoDiv.innerHTML = `
            <span><i class="fas fa-home"></i> Room ID: ${selectedOption.value}</span>
            <span><i class="fas fa-tag"></i> ${selectedOption.text.split(' - ')[1] || ''}</span>
        `;
    }
}

// ================= LOAD RULES & INSTRUCTIONS =================
function loadRulesAndInstructions() {
    if (!currentRoomId) return;
    
    fetch(`../api/owner/get-room-rules?room_id=${currentRoomId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                renderRules(data.data.allowed, 'allowed');
                renderRules(data.data.not_allowed, 'not_allowed');
                renderInstructions(data.data.instructions);
            }
        })
        .catch(err => {
            console.error('Error:', err);
            showToast('Error loading data', 'error');
        });
}

function renderRules(rules, type) {
    const containerId = type === 'allowed' ? 'allowedRulesList' : 'notAllowedRulesList';
    const container = document.getElementById(containerId);
    
    if (!rules || rules.length === 0) {
        container.innerHTML = '<li class="empty-state"><i class="fas fa-info-circle"></i> No rules added yet</li>';
        return;
    }
    
    container.innerHTML = rules.map(rule => `
        <li data-id="${rule.id}">
            <div class="rule-content">
                <div class="rule-icon"><i class="fas ${rule.icon || 'fa-check-circle'}"></i></div>
                <span class="rule-text">${escapeHtml(rule.rule_text)}</span>
            </div>
            <div class="rule-actions">
                <button class="edit-btn" onclick="editRule(${rule.id}, '${rule.rule_type}', '${escapeHtml(rule.rule_text)}', '${rule.icon}')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="delete-btn" onclick="confirmDelete('rule', ${rule.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </li>
    `).join('');
}

function renderInstructions(instructions) {
    const container = document.getElementById('instructionsList');
    
    if (!instructions || instructions.length === 0) {
        container.innerHTML = '<li class="empty-state"><i class="fas fa-info-circle"></i> No instructions added yet</li>';
        return;
    }
    
    container.innerHTML = instructions.map(inst => `
        <li data-id="${inst.id}">
            <div class="rule-content">
                <div class="rule-icon"><i class="fas ${inst.icon || 'fa-info-circle'}"></i></div>
                <span class="rule-text">${escapeHtml(inst.instruction_text)}</span>
            </div>
            <div class="rule-actions">
                <button class="edit-btn" onclick="editInstruction(${inst.id}, '${escapeHtml(inst.instruction_text)}', '${inst.icon}')">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="delete-btn" onclick="confirmDelete('instruction', ${inst.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </li>
    `).join('');
}

// ================= RULE CRUD =================
function openRuleModal() {
    if (!currentRoomId) {
        showToast('Please select a room first', 'error');
        return;
    }
    
    document.getElementById('ruleId').value = '';
    document.getElementById('ruleType').value = 'allowed';
    document.getElementById('ruleText').value = '';
    document.getElementById('selectedIcon').value = 'fa-check-circle';
    document.getElementById('ruleModalTitle').innerText = 'Add Rule';
    
    // Reset icon selection
    const grid = document.getElementById('iconPickerGrid');
    grid.querySelectorAll('.icon-option').forEach(opt => {
        opt.classList.remove('selected');
        if (opt.getAttribute('data-icon') === 'fa-check-circle') {
            opt.classList.add('selected');
        }
    });
    
    document.getElementById('ruleModal').classList.add('active');
}

function closeRuleModal() {
    document.getElementById('ruleModal').classList.remove('active');
}

function editRule(id, type, text, icon) {
    document.getElementById('ruleId').value = id;
    document.getElementById('ruleType').value = type;
    document.getElementById('ruleText').value = text;
    document.getElementById('selectedIcon').value = icon || 'fa-check-circle';
    document.getElementById('ruleModalTitle').innerText = 'Edit Rule';
    
    const grid = document.getElementById('iconPickerGrid');
    grid.querySelectorAll('.icon-option').forEach(opt => {
        opt.classList.remove('selected');
        if (opt.getAttribute('data-icon') === (icon || 'fa-check-circle')) {
            opt.classList.add('selected');
        }
    });
    
    document.getElementById('ruleModal').classList.add('active');
}

function saveRule() {
    if (!currentRoomId) {
        showToast('Please select a room first', 'error');
        return;
    }
    
    const id = document.getElementById('ruleId').value;
    const ruleType = document.getElementById('ruleType').value;
    const ruleText = document.getElementById('ruleText').value;
    const icon = document.getElementById('selectedIcon').value;
    
    if (!ruleText.trim()) {
        showToast('Please enter rule text', 'error');
        return;
    }
    
    const url = id ? '../api/owner/update-room-rule' : '../api/owner/add-room-rule';
    const body = id ? { id, rule_type: ruleType, rule_text: ruleText, icon } 
                    : { room_id: currentRoomId, rule_type: ruleType, rule_text: ruleText, icon };
    
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message, 'success');
            closeRuleModal();
            loadRulesAndInstructions();
        } else {
            showToast(data.message || 'Error saving rule', 'error');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Network error', 'error');
    });
}

// ================= INSTRUCTION CRUD =================
function openInstructionModal() {
    if (!currentRoomId) {
        showToast('Please select a room first', 'error');
        return;
    }
    
    document.getElementById('instructionId').value = '';
    document.getElementById('instructionText').value = '';
    document.getElementById('selectedInstructionIcon').value = 'fa-info-circle';
    document.getElementById('instructionModalTitle').innerText = 'Add Instruction';
    
    const grid = document.getElementById('instructionIconPickerGrid');
    grid.querySelectorAll('.icon-option').forEach(opt => {
        opt.classList.remove('selected');
        if (opt.getAttribute('data-icon') === 'fa-info-circle') {
            opt.classList.add('selected');
        }
    });
    
    document.getElementById('instructionModal').classList.add('active');
}

function closeInstructionModal() {
    document.getElementById('instructionModal').classList.remove('active');
}

function editInstruction(id, text, icon) {
    document.getElementById('instructionId').value = id;
    document.getElementById('instructionText').value = text;
    document.getElementById('selectedInstructionIcon').value = icon || 'fa-info-circle';
    document.getElementById('instructionModalTitle').innerText = 'Edit Instruction';
    
    const grid = document.getElementById('instructionIconPickerGrid');
    grid.querySelectorAll('.icon-option').forEach(opt => {
        opt.classList.remove('selected');
        if (opt.getAttribute('data-icon') === (icon || 'fa-info-circle')) {
            opt.classList.add('selected');
        }
    });
    
    document.getElementById('instructionModal').classList.add('active');
}

function saveInstruction() {
    if (!currentRoomId) {
        showToast('Please select a room first', 'error');
        return;
    }
    
    const id = document.getElementById('instructionId').value;
    const instructionText = document.getElementById('instructionText').value;
    const icon = document.getElementById('selectedInstructionIcon').value;
    
    if (!instructionText.trim()) {
        showToast('Please enter instruction text', 'error');
        return;
    }
    
    const url = id ? '../api/owner/update-room-instruction' : '../api/owner/add-room-instruction';
    const body = id ? { id, instruction_text: instructionText, icon } 
                    : { room_id: currentRoomId, instruction_text: instructionText, icon };
    
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message, 'success');
            closeInstructionModal();
            loadRulesAndInstructions();
        } else {
            showToast(data.message || 'Error saving instruction', 'error');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Network error', 'error');
    });
}

// ================= DELETE =================
let deleteType = null;
let deleteId = null;

function confirmDelete(type, id) {
    deleteType = type;
    deleteId = id;
    
    const modal = document.getElementById('deleteModal');
    const message = document.getElementById('deleteMessage');
    message.innerHTML = `Are you sure you want to delete this ${type}? This action cannot be undone.`;
    modal.classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    deleteType = null;
    deleteId = null;
}

document.getElementById('confirmDeleteBtn').onclick = () => {
    if (!deleteType || !deleteId) return;
    
    const url = deleteType === 'rule' ? '../api/owner/delete-room-rule' : '../api/owner/delete-room-instruction';
    
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: deleteId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(data.message, 'success');
            loadRulesAndInstructions();
        } else {
            showToast(data.message || 'Error deleting', 'error');
        }
        closeDeleteModal();
    })
    .catch(err => {
        console.error('Error:', err);
        showToast('Network error', 'error');
        closeDeleteModal();
    });
};

// ================= HELPER FUNCTIONS =================
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

function showToast(message, type = 'info') {
    // Remove existing toast
    let toast = document.getElementById('toast');
    if (toast) toast.remove();
    
    // Create new toast
    toast = document.createElement('div');
    toast.id = 'toast';
    toast.className = `toast ${type}`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Force reflow
    toast.offsetHeight;
    
    // Show toast
    toast.classList.add('show');
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            if (toast.parentNode) toast.remove();
        }, 300);
    }, 3000);
}

// ================= INITIALIZE =================
document.addEventListener('DOMContentLoaded', () => {
    populateIconPickers();
    
    if (currentRoomId) {
        document.getElementById('ruleRoomId').value = currentRoomId;
        document.getElementById('instructionRoomId').value = currentRoomId;
        loadRulesAndInstructions();
        updateRoomInfo();
    }
});

// Close modals on escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeRuleModal();
        closeInstructionModal();
        closeDeleteModal();
    }
});

// Close modals on outside click
window.onclick = (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        closeRuleModal();
        closeInstructionModal();
        closeDeleteModal();
    }
};
</script>

<?php renderFooter('owner'); ?>