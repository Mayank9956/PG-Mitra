<?php
require_once '../common/auth.php';
requirePageRole([ROLE_OWNER]);
require_once '../common/layout.php';


$facilities = [];

try {
    $stmt = $pdo->query("SELECT id, f_name FROM facilities ORDER BY f_name");
    $facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error silently
}

renderHeader('Add Room');
renderSidebarMenu('add-room', 'owner');
renderMainContentStart('Add Room', $_SESSION['username'] ?? 'Owner');
?>

<div class="room-card">
    <div class="room-table-header">
        <h3>Add New Room</h3>
    </div>

    <form id="roomAddForm" class="room-form-grid">
        <!-- Basic Information -->
        <div class="room-form-group">
            <label>Room Title <span class="room-optional">*</span></label>
            <input type="text" name="title" placeholder="Enter room title" required>
        </div>
        
        <div class="room-form-group">
            <label>Price (₹) <span class="room-optional">*</span></label>
            <input type="number" name="price" placeholder="Enter price per month" required>
        </div>
        
        <div class="room-form-group">
            <label>Security Deposit (₹)</label>
            <input type="number" name="security_deposit" placeholder="Enter security deposit amount">
        </div>
        
        <div class="room-form-group">
            <label>Non-Refundable Amount (₹)</label>
            <input type="number" name="non_refundable" placeholder="Enter non-refundable amount">
        </div>
        
        <div class="room-form-group">
            <label>City</label>
            <input type="text" name="city" placeholder="Enter city name">
        </div>
        
        <div class="room-form-group">
            <label>Area/Locality</label>
            <input type="text" name="area" placeholder="Enter area or locality">
        </div>
        
        <div class="room-form-group">
            <label>College Name</label>
            <input type="text" name="college_name" placeholder="Enter nearby college name">
        </div>
        
        <div class="room-form-group">
            <label>Distance from College</label>
            <input type="number" name="distance" placeholder="Enter distance in km EX -1">
        </div>
        
        <div class="room-form-group">
            <label>Distance Text</label>
            <input type="text" name="distance_text" placeholder="e.g., 2.0 km away">
        </div>
        
        <div class="room-form-group">
            <label>Full Location</label>
            <input type="text" name="location" placeholder="e.g., Manali, Himachal Pradesh">
        </div>
        
        <div class="room-form-group">
            <label>Room Description</label>
            <textarea name="description" placeholder="Describe the room in detail"></textarea>
        </div>
        
        <div class="room-form-group">
            <label>No of Members</label>
                <select name="max_guests">
                <option value="">Select Number of Members</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
            <!--<input type="number" name="max_guests" placeholder="Maximum number of guests">-->
        </div>
        
          <div class="room-form-group">
            <label>Beds</label>
             <select name="bedrooms">
                <option value="">Number of Beds</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
            
            <!--<input type="number" name="bedrooms" placeholder="Maximum number of bedrooms">-->
        </div>
          <div class="room-form-group">
            <label>Bathrooms</label>
                   <select name="bathroom_type">
                <option value="">Bathrooms Types</option>
                <option value="seperate">Seperate</option>
                <option value="sharing">sharing</option>
                
            </select>
            <!--<input type="number" name="bathrooms" placeholder="Maximum number of bathrooms">-->
        </div>
        <div class="room-form-group">
            <label>Room Type</label>
            <select name="room_type">
                <option value="">Select Room Type</option>
                <option value="apartment">Apartment</option>
                <option value="hotel">Hotel</option>
                <option value="villa">Villa</option>
                <option value="cottage">Cottage</option>
                <option value="hostel">Rooms</option>
                <option value="pg">PG</option>
            </select>
        </div>
        
        <div class="room-form-group">
            <label>PG Type</label>
            <select name="pg_type">
                <option value="">Select PG Type</option>
                <option value="men">Men Only</option>
                <option value="women">Women Only</option>
                <option value="unisex">Unisex</option>
            </select>
        </div>
        
        <div class="room-form-group">
            <label>Facilities</label>
            <div class="room-facilities-container">
                <?php if (!empty($facilities)): ?>
                    <?php foreach ($facilities as $facility): ?>
                        <label class="room-facility-checkbox">
                            <input type="checkbox" name="facilities[]" value="<?php echo htmlspecialchars($facility['id']); ?>">
                            <span><?php echo htmlspecialchars($facility['f_name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="room-no-facilities">No facilities available</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- IMAGE UPLOADER -->
        <div class="room-form-group room-full-width">
            <label>Images <span class="room-optional">(Max 10 images, Min 1 image)</span></label>
            
            <div id="roomDropArea" class="room-upload-box">
                <i class="fas fa-cloud-upload-alt"></i>
                <p>Click or Drag Images Here</p>
                <small>Supported formats: JPG, PNG, GIF (Max 5MB each)</small>
                <input type="file" id="roomImageInput" multiple accept="image/*" hidden>
            </div>
            
            <div id="roomPreviewContainer" class="room-preview-grid"></div>
        </div>
        
        <div class="room-form-actions">
            <button type="button" class="room-btn room-btn-secondary" onclick="roomShowConfirmModal('reset')">Reset Form</button>
            <button type="submit" class="room-btn room-btn-primary">Save Room</button>
        </div>
    </form>
</div>

<!-- IMAGE MODAL -->
<div id="roomImgModal" class="room-img-modal">
    <span class="room-close" onclick="roomClosePreview()">×</span>
    <img src="" alt="Preview">
</div>

<!-- CUSTOM CONFIRM MODAL -->
<div id="roomConfirmModal" class="room-confirm-modal">
    <div class="room-confirm-modal-content">
        <div class="room-confirm-modal-header">
            <i class="fas fa-question-circle"></i>
            <h3 id="roomConfirmTitle">Confirm Action</h3>
        </div>
        <div class="room-confirm-modal-body">
            <p id="roomConfirmMessage">Are you sure you want to proceed?</p>
        </div>
        <div class="room-confirm-modal-footer">
            <button class="room-btn-confirm room-btn-cancel" onclick="roomHideConfirmModal()">Cancel</button>
            <button class="room-btn-confirm room-btn-confirm-action" id="roomConfirmActionBtn">Confirm</button>
        </div>
    </div>
</div>

<!-- TOAST NOTIFICATION -->
<div id="roomToast" class="room-toast"></div>

<script>
let roomFilesArray = [];
let roomPrimaryIndex = 0;
const ROOM_MAX_FILES = 10;
const ROOM_MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

const roomDropArea = document.getElementById('roomDropArea');
const roomInput = document.getElementById('roomImageInput');
const roomPreview = document.getElementById('roomPreviewContainer');
const roomSubmitBtn = document.querySelector('.room-btn-primary');

// Click to upload
roomDropArea.onclick = () => roomInput.click();

// File selection
roomInput.addEventListener('change', e => {
    roomAddFiles(e.target.files);
});

// Drag and drop events
roomDropArea.addEventListener('dragover', e => {
    e.preventDefault();
    roomDropArea.classList.add('room-drag');
});

roomDropArea.addEventListener('dragleave', () => {
    roomDropArea.classList.remove('room-drag');
});

roomDropArea.addEventListener('drop', e => {
    e.preventDefault();
    roomDropArea.classList.remove('room-drag');
    roomAddFiles(e.dataTransfer.files);
});

// Add files with validation
function roomAddFiles(files) {
    const validFiles = Array.from(files).filter(file => {
        // Check file type
        if (!file.type.startsWith('image/')) {
            roomShowToast('Invalid file type: ' + file.name, 'error');
            return false;
        }
        
        // Check file size
        if (file.size > ROOM_MAX_FILE_SIZE) {
            roomShowToast('File too large: ' + file.name + ' (Max 5MB)', 'error');
            return false;
        }
        
        // Check total files limit
        if (roomFilesArray.length + 1 > ROOM_MAX_FILES) {
            roomShowToast('Maximum ' + ROOM_MAX_FILES + ' images allowed', 'error');
            return false;
        }
        
        return true;
    });
    
    if (validFiles.length > 0) {
        roomFilesArray.push(...validFiles);
        roomRenderPreview();
        roomShowToast(validFiles.length + ' image(s) added successfully', 'success');
    }
}

// Render preview grid
function roomRenderPreview() {
    roomPreview.innerHTML = '';
    
    if (roomFilesArray.length === 0) {
        roomPreview.innerHTML = '<div class="room-empty-preview">No images selected</div>';
        return;
    }
    
    roomFilesArray.forEach((file, index) => {
        const url = URL.createObjectURL(file);
        
        const imgBox = document.createElement('div');
        imgBox.className = 'room-img-box';
        imgBox.setAttribute('draggable', 'true');
        imgBox.setAttribute('data-index', index);
        
        imgBox.innerHTML = `
            <img src="${url}" onclick="roomOpenPreview('${url}')">
            ${index === roomPrimaryIndex ? '<span class="room-primary-badge">★ Primary</span>' : ''}
            <div class="room-image-actions">
                <button type="button" class="room-action-btn room-primary-btn" onclick="roomSetPrimary(${index})" title="Set as primary">
                    <i class="fas fa-star"></i>
                </button>
                <button type="button" class="room-action-btn room-delete-btn" onclick="roomShowConfirmModal('remove', ${index})" title="Remove">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        
        roomPreview.appendChild(imgBox);
    });
    
    roomEnableDragAndDrop();
    
    // Cleanup old object URLs
    roomFilesArray.forEach((file, index) => {
        if (file.url) URL.revokeObjectURL(file.url);
        file.url = URL.createObjectURL(file);
    });
}

// Remove image
function roomRemoveImage(index) {
    roomFilesArray.splice(index, 1);
    
    // Adjust primary index
    if (roomPrimaryIndex >= roomFilesArray.length) {
        roomPrimaryIndex = roomFilesArray.length > 0 ? roomFilesArray.length - 1 : 0;
    } else if (roomPrimaryIndex === index) {
        roomPrimaryIndex = Math.min(roomPrimaryIndex, roomFilesArray.length - 1);
    } else if (roomPrimaryIndex > index) {
        roomPrimaryIndex--;
    }
    
    roomRenderPreview();
    roomShowToast('Image removed', 'info');
}

// Set primary image
function roomSetPrimary(index) {
    roomPrimaryIndex = index;
    roomRenderPreview();
    roomShowToast('Primary image updated', 'success');
}

// Enable drag and drop reordering
let roomDragStartIndex = null;

function roomEnableDragAndDrop() {
    const boxes = document.querySelectorAll('.room-img-box');
    
    boxes.forEach(box => {
        box.addEventListener('dragstart', (e) => {
            roomDragStartIndex = parseInt(box.getAttribute('data-index'));
            box.style.opacity = '0.5';
        });
        
        box.addEventListener('dragend', (e) => {
            box.style.opacity = '1';
            roomDragStartIndex = null;
        });
        
        box.addEventListener('dragover', (e) => {
            e.preventDefault();
            box.style.transform = 'scale(1.02)';
        });
        
        box.addEventListener('dragleave', () => {
            box.style.transform = 'scale(1)';
        });
        
        box.addEventListener('drop', (e) => {
            e.preventDefault();
            box.style.transform = 'scale(1)';
            
            const dragEndIndex = parseInt(box.getAttribute('data-index'));
            
            if (roomDragStartIndex !== null && roomDragStartIndex !== dragEndIndex) {
                // Reorder array
                const [movedItem] = roomFilesArray.splice(roomDragStartIndex, 1);
                roomFilesArray.splice(dragEndIndex, 0, movedItem);
                
                // Update primary index
                if (roomPrimaryIndex === roomDragStartIndex) {
                    roomPrimaryIndex = dragEndIndex;
                } else if (roomPrimaryIndex > roomDragStartIndex && roomPrimaryIndex <= dragEndIndex) {
                    roomPrimaryIndex--;
                } else if (roomPrimaryIndex < roomDragStartIndex && roomPrimaryIndex >= dragEndIndex) {
                    roomPrimaryIndex++;
                }
                
                roomRenderPreview();
                roomShowToast('Order updated', 'success');
            }
        });
    });
}

// Modal functions
function roomOpenPreview(src) {
    const modal = document.getElementById('roomImgModal');
    const modalImg = document.querySelector('#roomImgModal img');
    modalImg.src = src;
    modal.classList.add('room-show');
    document.body.style.overflow = 'hidden';
}

function roomClosePreview() {
    const modal = document.getElementById('roomImgModal');
    modal.classList.remove('room-show');
    document.body.style.overflow = 'auto';
}

// Close modal on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        roomClosePreview();
        roomHideConfirmModal();
    }
});

// Click outside modal to close
document.getElementById('roomImgModal').addEventListener('click', (e) => {
    if (e.target === document.getElementById('roomImgModal')) {
        roomClosePreview();
    }
});

// Custom Confirm Modal
function roomShowConfirmModal(action, index = null) {
    const modal = document.getElementById('roomConfirmModal');
    const title = document.getElementById('roomConfirmTitle');
    const message = document.getElementById('roomConfirmMessage');
    const confirmBtn = document.getElementById('roomConfirmActionBtn');
    
    if (action === 'reset') {
        title.innerHTML = '<i class="fas fa-undo-alt"></i> Reset Form';
        message.innerHTML = 'Are you sure you want to reset all form fields? This action cannot be undone.';
        confirmBtn.onclick = () => {
            roomResetForm();
            roomHideConfirmModal();
        };
    } else if (action === 'remove') {
        title.innerHTML = '<i class="fas fa-trash-alt"></i> Remove Image';
        message.innerHTML = 'Are you sure you want to remove this image? This action cannot be undone.';
        confirmBtn.onclick = () => {
            roomRemoveImage(index);
            roomHideConfirmModal();
        };
    }
    
    modal.classList.add('room-show');
    document.body.style.overflow = 'hidden';
}

function roomHideConfirmModal() {
    const modal = document.getElementById('roomConfirmModal');
    modal.classList.remove('room-show');
    document.body.style.overflow = 'auto';
}

// Toast notification
function roomShowToast(message, type = 'info') {
    const toast = document.getElementById('roomToast');
    toast.textContent = message;
    toast.className = `room-toast room-show ${type}`;
    
    setTimeout(() => {
        toast.classList.remove('room-show');
    }, 3000);
}

// Reset form
function roomResetForm() {
    document.getElementById('roomAddForm').reset();
    roomFilesArray = [];
    roomPrimaryIndex = 0;
    roomRenderPreview();
    roomShowToast('Form reset successfully', 'info');
}

// Submit form
document.getElementById('roomAddForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Validate at least one image
    if (roomFilesArray.length === 0) {
        roomShowToast('Please add at least one image', 'error');
        return;
    }
    
    // Validate required fields
    const title = this.querySelector('[name="title"]').value;
    const price = this.querySelector('[name="price"]').value;
    
    if (!title || !price) {
        roomShowToast('Please fill in all required fields', 'error');
        return;
    }
    
    // Show loading state
    roomSubmitBtn.classList.add('room-loading');
    roomSubmitBtn.disabled = true;
    
    const formData = new FormData(this);
    
    // Append images
    roomFilesArray.forEach(file => {
        formData.append('images[]', file);
    });
    formData.append('primary_index', roomPrimaryIndex);
    
    // Get selected facilities
    const selectedFacilities = [];
    document.querySelectorAll('input[name="facilities[]"]:checked').forEach(checkbox => {
        selectedFacilities.push(checkbox.value);
    });
    formData.append('facilities', JSON.stringify(selectedFacilities));
    
    try {
        const response = await fetch('../api/owner/add-room', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            roomShowToast(data.message || 'Room added successfully!', 'success');
            
            // Reset form
            roomFilesArray = [];
            roomPrimaryIndex = 0;
            roomRenderPreview();
            this.reset();
            
            // Reset checkboxes
            document.querySelectorAll('input[name="facilities[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Optional: Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = 'rooms';
            }, 2000);
        } else {
            roomShowToast(data.message || 'Failed to add room', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        roomShowToast('Network error. Please try again.', 'error');
    } finally {
        roomSubmitBtn.classList.remove('room-loading');
        roomSubmitBtn.disabled = false;
    }
});

// Initialize
roomRenderPreview();
</script>

<style>
/* Global Styles - All prefixed with room- to avoid conflicts */
.room-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin: 20px;
    overflow: hidden;
}

.room-table-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
}

.room-table-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1e293b;
}

/* Form Styles */
.room-form-grid {
    padding: 24px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.room-form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.room-full-width {
    grid-column: span 2;
}

.room-form-group label {
    font-weight: 500;
    color: #374151;
    font-size: 14px;
}

.room-optional {
    font-size: 12px;
    color: #6b7280;
    font-weight: normal;
}

.room-form-group input:not([type="file"]),
.room-form-group textarea,
.room-form-group select {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.2s ease;
    background: #fff;
}

.room-form-group input:focus,
.room-form-group textarea:focus,
.room-form-group select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.room-form-group textarea {
    min-height: 100px;
    resize: vertical;
}

/* Facilities Checkbox Styling */
.room-facilities-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 12px;
    padding: 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    background: #fafbfc;
    max-height: 200px;
    overflow-y: auto;
}

.room-facility-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 14px;
    color: #374151;
}

.room-facility-checkbox input[type="checkbox"] {
    width: 16px;
    height: 16px;
    cursor: pointer;
}

.room-facility-checkbox span {
    user-select: none;
}

.room-no-facilities {
    color: #9ca3af;
    text-align: center;
    padding: 20px;
}

/* Upload Box */
.room-upload-box {
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 32px;
    text-align: center;
    cursor: pointer;
    background: #fafbfc;
    transition: all 0.2s ease;
}

.room-upload-box:hover {
    border-color: #3b82f6;
    background: #f0f9ff;
}

.room-upload-box.room-drag {
    border-color: #3b82f6;
    background: #eff6ff;
    transform: scale(0.98);
}

.room-upload-box i {
    font-size: 48px;
    color: #9ca3af;
    margin-bottom: 12px;
    display: block;
}

.room-upload-box p {
    margin: 0 0 5px 0;
    color: #6b7280;
    font-size: 14px;
}

.room-upload-box small {
    color: #9ca3af;
    font-size: 12px;
}

/* Preview Grid */
.room-preview-grid {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    margin-top: 20px;
    min-height: 100px;
}

.room-empty-preview {
    width: 100%;
    text-align: center;
    padding: 20px;
    color: #9ca3af;
    font-size: 14px;
}

.room-img-box {
    position: relative;
    width: 100px;
    height: 100px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    cursor: grab;
    transition: all 0.2s ease;
    background: #f3f4f6;
}

.room-img-box:active {
    cursor: grabbing;
}

.room-img-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.room-img-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
}

/* Primary Badge */
.room-primary-badge {
    position: absolute;
    bottom: 6px;
    left: 6px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 20px;
    letter-spacing: 0.5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    z-index: 1;
}

/* Image Actions */
.room-image-actions {
    position: absolute;
    top: 6px;
    right: 6px;
    display: flex;
    gap: 6px;
    z-index: 1;
}

.room-action-btn {
    border: none;
    background: rgba(0,0,0,0.7);
    backdrop-filter: blur(4px);
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.room-action-btn:hover {
    transform: scale(1.1);
}

.room-primary-btn:hover {
    background: #f59e0b;
}

.room-delete-btn:hover {
    background: #ef4444;
}

/* Image Modal */
.room-img-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.85);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 9999;
}

.room-img-modal.room-show {
    opacity: 1;
    visibility: visible;
}

.room-img-modal img {
    max-width: 90%;
    max-height: 90%;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.room-img-modal .room-close {
    position: absolute;
    top: 20px;
    right: 30px;
    color: white;
    font-size: 40px;
    font-weight: 300;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(0,0,0,0.5);
}

.room-img-modal .room-close:hover {
    background: rgba(0,0,0,0.8);
    transform: rotate(90deg);
}

/* Custom Confirm Modal */
.room-confirm-modal {
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
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 10000;
}

.room-confirm-modal.room-show {
    opacity: 1;
    visibility: visible;
}

.room-confirm-modal-content {
    background: #fff;
    border-radius: 16px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    animation: roomModalSlideIn 0.3s ease;
}

@keyframes roomModalSlideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.room-confirm-modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 12px;
}

.room-confirm-modal-header i {
    font-size: 24px;
    color: #f59e0b;
}

.room-confirm-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1e293b;
}

.room-confirm-modal-body {
    padding: 24px;
}

.room-confirm-modal-body p {
    margin: 0;
    font-size: 14px;
    color: #475569;
    line-height: 1.5;
}

.room-confirm-modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.room-btn-confirm {
    padding: 8px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: inherit;
}

.room-btn-cancel {
    background: #f1f5f9;
    color: #475569;
}

.room-btn-cancel:hover {
    background: #e2e8f0;
}

.room-btn-confirm-action {
    background: #ef4444;
    color: white;
}

.room-btn-confirm-action:hover {
    background: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(239,68,68,0.3);
}

/* Button Styles */
.room-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
        width: 50%;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: inherit;
}

.room-btn-primary {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.room-btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.room-btn-secondary:hover {
    background: #e5e7eb;
}

.room-btn-primary:hover:not(:disabled) {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59,130,246,0.3);
}

.room-btn-primary:active {
    transform: translateY(0);
}

.room-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Form Actions */
.room-form-actions {
    grid-column: span 2;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 10px;
}

/* Toast Notification */
.room-toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    padding: 12px 20px;
    border-radius: 8px;
    color: white;
    font-size: 14px;
    font-weight: 500;
    z-index: 10001;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    max-width: 300px;
}

.room-toast.room-show {
    opacity: 1;
    visibility: visible;
}

.room-toast.success {
    background: #10b981;
}

.room-toast.error {
    background: #ef4444;
}

.room-toast.info {
    background: #3b82f6;
}

/* Loading State */
.room-btn-primary.room-loading {
    position: relative;
    color: transparent;
}

.room-btn-primary.room-loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid white;
    border-top-color: transparent;
    border-radius: 50%;
    animation: roomSpin 0.6s linear infinite;
}

@keyframes roomSpin {
    to { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .room-form-grid {
        grid-template-columns: 1fr;
        gap: 16px;
        padding: 20px;
    }
    
    .room-full-width,
    .room-form-actions {
        grid-column: span 1;
    }
    
    .room-table-header {
        padding: 16px 20px;
    }
    
    .room-img-box {
        width: 80px;
        height: 80px;
    }
    
    .room-upload-box {
        padding: 24px;
    }
    
    .room-upload-box i {
        font-size: 36px;
    }
    
    .room-form-actions {
        flex-direction: column;
    }
    
    .room-btn {
        width: 100%;
    }
    
    .room-confirm-modal-content {
        width: 95%;
        margin: 20px;
    }
    
    .room-facilities-container {
        grid-template-columns: 1fr;
    }
}

/* Scrollbar Styling */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<?php renderFooter('owner'); ?>