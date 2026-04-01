<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($ticket_id <= 0) {
    header("Location: support.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Ticket Details - StayEase</title>
<script src="/static/js/script.js"></script>
<link rel="stylesheet" href="/static/css/style.css">
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

* { font-family: 'Inter', sans-serif; }

body {
    background: #f3f4f6;
}

.app-container {
    max-width: 414px;
    margin: auto;
    background: #ffffff;
    min-height: 100vh;
    position: relative;
    box-shadow: 0 0 30px rgba(0,0,0,0.05);
}

.header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 24px 20px 32px;
    border-bottom-left-radius: 32px;
    border-bottom-right-radius: 32px;
    position: relative;
    overflow: hidden;
}

.header::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -20%;
    width: 240px;
    height: 240px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
}

.header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
}

.status-open {
    background: #D1FAE5;
    color: #047857;
}

.status-in-progress {
    background: #FEF3C7;
    color: #B45309;
}

.status-resolved,
.status-closed {
    background: #E5E7EB;
    color: #4B5563;
}

.card {
    background: #fff;
    border-radius: 18px;
    padding: 16px;
    border: 1px solid #F3F4F6;
    box-shadow: 0 8px 24px rgba(0,0,0,0.04);
}

.meta-row {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid #F3F4F6;
    font-size: 13px;
}

.meta-row:last-child {
    border-bottom: none;
}

.chat-wrap {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.chat-bubble {
    max-width: 85%;
    padding: 12px 14px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.5;
    word-break: break-word;
}

.chat-user {
    align-self: flex-end;
    background: #DBEAFE;
    color: #1E3A8A;
    border-bottom-right-radius: 4px;
}

.chat-support {
    align-self: flex-start;
    background: #F3F4F6;
    color: #1F2937;
    border-bottom-left-radius: 4px;
}

.chat-meta {
    font-size: 11px;
    margin-top: 6px;
    opacity: 0.75;
}

.form-textarea {
    width: 100%;
    border: 2px solid #E5E7EB;
    border-radius: 16px;
    padding: 14px;
    font-size: 14px;
    resize: none;
    outline: none;
}

.form-textarea:focus {
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.bottom-space {
    height: 90px;
}

.attachment-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-top: 10px;
}

.attachment-item {
    display: block;
    border-radius: 12px;
    overflow: hidden;
    background: #F3F4F6;
    border: 1px solid #E5E7EB;
    cursor: pointer;
    position: relative;
}

.attachment-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    display: block;
}

.upload-box {
    border: 2px dashed #D1D5DB;
    border-radius: 14px;
    padding: 16px;
    text-align: center;
    background: #F9FAFB;
    cursor: pointer;
}

.upload-box:hover {
    border-color: #3B82F6;
    background: #EFF6FF;
}

.file-preview-list {
    margin-top: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.file-preview-item {
    background: #F3F4F6;
    border-radius: 10px;
    padding: 8px 10px;
    font-size: 12px;
    color: #374151;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.image-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.86);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 99999;
    padding: 16px;
}

.image-modal.show {
    display: flex;
}

.image-modal-content {
    position: relative;
    width: 100%;
    max-width: 900px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-modal img {
    max-width: 100%;
    max-height: 85vh;
    border-radius: 14px;
    display: block;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4);
}

.image-modal-close {
    position: absolute;
    top: -14px;
    right: 0;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: none;
    background: #ffffff;
    color: #111827;
    font-size: 18px;
    cursor: pointer;
    z-index: 5;
}

.modal-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 42px;
    height: 42px;
    border: none;
    border-radius: 50%;
    background: rgba(255,255,255,0.95);
    color: #111827;
    font-size: 18px;
    cursor: pointer;
    z-index: 5;
    box-shadow: 0 8px 24px rgba(0,0,0,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-prev {
    left: 8px;
}

.modal-next {
    right: 8px;
}

.modal-counter {
    position: absolute;
    bottom: -34px;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    background: rgba(255,255,255,0.12);
    padding: 6px 12px;
    border-radius: 999px;
}

@media (max-width: 480px) {
    .modal-nav-btn {
        width: 38px;
        height: 38px;
        font-size: 16px;
    }

    .modal-prev {
        left: 2px;
    }

    .modal-next {
        right: 2px;
    }
}
</style>
</head>
<body>

<div class="app-container">
    <div class="header">
        <div class="relative z-10">
            <button onclick="history.back()" class="text-white text-xl mb-4">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1 class="text-white text-2xl font-bold">Ticket Details</h1>
            <p class="text-white/80 text-sm mt-1">Track your issue and chat with support</p>
        </div>
    </div>

    <div class="px-4 -mt-4 relative z-10 pb-6">
        <div id="messageBox" class="mb-4 hidden"></div>

        <div id="ticketCard" class="card mb-4 hidden"></div>

        <div class="card mb-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800">Conversation</h3>
                <span class="text-xs text-gray-400" id="replyCount">0 replies</span>
            </div>

            <div class="chat-wrap" id="repliesContainer">
                <div class="text-center py-8 text-sm text-gray-500 bg-gray-50 rounded-xl">
                    Loading conversation...
                </div>
            </div>
        </div>

        <div class="card" id="replySection">
            <h3 class="font-bold text-gray-800 mb-3">Send Reply</h3>

            <div id="ticketClosedMessage" class="hidden">
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-center">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-lock text-gray-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-800 mb-1">This ticket is closed</h4>
                    <p class="text-sm text-gray-500">You can no longer send replies or upload attachments for this ticket.</p>
                </div>
            </div>

            <form id="replyForm" enctype="multipart/form-data">
                <textarea
                    name="reply_message"
                    rows="4"
                    class="form-textarea"
                    placeholder="Write your message here..."
                    required
                ></textarea>

                <div class="mt-4">
                    <label for="replyImages" class="upload-box block">
                        <i class="fas fa-image text-2xl text-gray-400 mb-2"></i>
                        <p class="text-sm text-gray-600">Upload reply images</p>
                        <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG, WEBP • Max 5MB each</p>
                    </label>
                    <input type="file" id="replyImages" name="attachments[]" accept="image/png,image/jpeg,image/jpg,image/webp" multiple class="hidden">
                    <div id="filePreviewList" class="file-preview-list"></div>
                </div>

                <button
                    type="submit"
                    class="w-full mt-4 bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3.5 rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition shadow-lg"
                >
                    <i class="fas fa-paper-plane mr-2"></i> Send Reply
                </button>
            </form>
        </div>

        <div class="bottom-space"></div>
    </div>
</div>

<div id="imageModal" class="image-modal" onclick="handleModalOutsideClick(event)">
    <div class="image-modal-content">
        <button type="button" class="image-modal-close" onclick="closeImageModal()">
            <i class="fas fa-times"></i>
        </button>

        <button type="button" class="modal-nav-btn modal-prev" onclick="event.stopPropagation(); showPrevImage()">
            <i class="fas fa-chevron-left"></i>
        </button>

        <img id="modalImage" src="" alt="Preview">

        <button type="button" class="modal-nav-btn modal-next" onclick="event.stopPropagation(); showNextImage()">
            <i class="fas fa-chevron-right"></i>
        </button>

        <div id="modalCounter" class="modal-counter">1 / 1</div>
    </div>
</div>

<script>
const ticketId = <?php echo $ticket_id; ?>;
let modalImages = [];
let currentModalIndex = 0;
let currentTicketStatus = '';

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text || '';
    return div.innerHTML;
}

function normalizeAttachments(attachments) {
    if (!attachments) return [];
    if (Array.isArray(attachments)) return attachments;

    try {
        const parsed = JSON.parse(attachments);
        return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        return [];
    }
}

function openImageModal(images, index = 0) {
    modalImages = Array.isArray(images) ? images : [];
    currentModalIndex = index;

    if (!modalImages.length) return;

    const modal = document.getElementById('imageModal');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    updateModalImage();
}

function updateModalImage() {
    const modalImage = document.getElementById('modalImage');
    const modalCounter = document.getElementById('modalCounter');
    const prevBtn = document.querySelector('.modal-prev');
    const nextBtn = document.querySelector('.modal-next');

    if (!modalImages.length) return;

    modalImage.src = modalImages[currentModalIndex];
    modalCounter.textContent = `${currentModalIndex + 1} / ${modalImages.length}`;

    if (modalImages.length <= 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
    } else {
        prevBtn.style.display = 'flex';
        nextBtn.style.display = 'flex';
    }
}

function showPrevImage() {
    if (!modalImages.length) return;
    currentModalIndex = (currentModalIndex - 1 + modalImages.length) % modalImages.length;
    updateModalImage();
}

function showNextImage() {
    if (!modalImages.length) return;
    currentModalIndex = (currentModalIndex + 1) % modalImages.length;
    updateModalImage();
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    modal.classList.remove('show');
    modalImage.src = '';
    modalImages = [];
    currentModalIndex = 0;
    document.body.style.overflow = '';
}

function handleModalOutsideClick(event) {
    if (event.target.id === 'imageModal') {
        closeImageModal();
    }
}

document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('imageModal');
    if (!modal.classList.contains('show')) return;

    if (event.key === 'Escape') {
        closeImageModal();
    } else if (event.key === 'ArrowLeft') {
        showPrevImage();
    } else if (event.key === 'ArrowRight') {
        showNextImage();
    }
});

function renderAttachmentHtml(attachments) {
    const files = normalizeAttachments(attachments);

    if (!files.length) return '';

    const jsArray = JSON.stringify(files);

    return `
        <div class="attachment-grid">
            ${files.map((file, index) => `
                <div class="attachment-item" onclick='openImageModal(${jsArray}, ${index})'>
                    <img src="${escapeHtml(file)}" alt="attachment">
                </div>
            `).join('')}
        </div>
    `;
}

function showMessage(message, type = 'success') {
    const box = document.getElementById('messageBox');
    box.innerHTML = `
        <div class="p-4 rounded-xl ${type === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'}">
            <div class="flex items-center gap-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span class="text-sm font-medium">${escapeHtml(message)}</span>
            </div>
        </div>
    `;
    box.classList.remove('hidden');
}

function getStatusClass(status) {
    if (status === 'in-progress') return 'status-in-progress';
    if (status === 'resolved') return 'status-resolved';
    if (status === 'closed') return 'status-closed';
    return 'status-open';
}

function isTicketClosed(status) {
    const value = String(status || '').toLowerCase();
    return value === 'closed' || value === 'resolved';
}

function updateReplySection(status) {
    const form = document.getElementById('replyForm');
    const closedBox = document.getElementById('ticketClosedMessage');

    if (!form || !closedBox) return;

    if (isTicketClosed(status)) {
        form.classList.add('hidden');
        closedBox.classList.remove('hidden');
    } else {
        form.classList.remove('hidden');
        closedBox.classList.add('hidden');
    }
}

async function loadTicketDetails() {
    try {
        const response = await fetch(`/api/ticket-details-api?action=get_ticket_details&ticket_id=${ticketId}`);
        const data = await response.json();

        if (!data.success) {
            showMessage(data.message || 'Failed to load ticket details', 'error');
            setTimeout(() => {
                window.location.href = '/support';
            }, 1500);
            return;
        }

        renderTicket(data.ticket);
        renderReplies(data.replies || []);
    } catch (error) {
        showMessage('Something went wrong while loading ticket details', 'error');
    }
}

function renderTicket(ticket) {
    const ticketCard = document.getElementById('ticketCard');
    const statusClass = getStatusClass(ticket.status);
    const formattedStatus = (ticket.status || '').replace('-', ' ');
    const createdAt = ticket.created_at || '';

    currentTicketStatus = ticket.status || '';
    updateReplySection(currentTicketStatus);

    ticketCard.innerHTML = `
        <div class="flex items-start justify-between gap-3 mb-3">
            <div>
                <div class="text-xs text-gray-500 font-semibold mb-1">Ticket Number</div>
                <div class="text-base font-bold text-gray-800">#${escapeHtml(ticket.ticket_number)}</div>
            </div>

            <div>
                <span class="status-pill ${statusClass}">
                    ${escapeHtml(formattedStatus.charAt(0).toUpperCase() + formattedStatus.slice(1))}
                </span>
            </div>
        </div>

        <div class="text-lg font-semibold text-gray-900 mb-3">
            ${escapeHtml(ticket.subject)}
        </div>

        <div class="meta-row">
            <span class="text-gray-500">Category</span>
            <span class="font-medium text-gray-800">${escapeHtml((ticket.category || '').charAt(0).toUpperCase() + (ticket.category || '').slice(1))}</span>
        </div>

        <div class="meta-row">
            <span class="text-gray-500">Priority</span>
            <span class="font-medium text-gray-800">${escapeHtml((ticket.priority || '').charAt(0).toUpperCase() + (ticket.priority || '').slice(1))}</span>
        </div>

        <div class="meta-row">
            <span class="text-gray-500">Created</span>
            <span class="font-medium text-gray-800">${escapeHtml(createdAt)}</span>
        </div>

        ${ticket.room_name ? `
        <div class="meta-row">
            <span class="text-gray-500">Related Booking</span>
            <span class="font-medium text-gray-800">${escapeHtml(ticket.room_name)}</span>
        </div>` : ''}

        <div class="pt-4">
            <div class="text-xs text-gray-500 font-semibold mb-2">Issue Description</div>
            <div class="text-sm text-gray-700 leading-6 bg-gray-50 rounded-xl p-3">
                ${escapeHtml(ticket.description).replace(/\n/g, '<br>')}
            </div>
            ${renderAttachmentHtml(ticket.attachments)}
        </div>
    `;
    ticketCard.classList.remove('hidden');
}

function renderReplies(replies) {
    const container = document.getElementById('repliesContainer');
    const replyCount = document.getElementById('replyCount');
    replyCount.textContent = `${replies.length} replies`;

    if (!replies.length) {
        container.innerHTML = `
            <div class="text-center py-8 text-sm text-gray-500 bg-gray-50 rounded-xl">
                No replies yet. You can send a message below.
            </div>
        `;
        return;
    }

    container.innerHTML = replies.map(reply => {
        const isUser = reply.sender_name === 'You';
        return `
            <div class="chat-bubble ${isUser ? 'chat-user' : 'chat-support'}">
                <div class="font-semibold text-xs mb-1">${escapeHtml(reply.sender_name)}</div>
                <div>${escapeHtml(reply.message).replace(/\n/g, '<br>')}</div>
                ${renderAttachmentHtml(reply.attachments)}
                <div class="chat-meta">${escapeHtml(reply.created_at)}</div>
            </div>
        `;
    }).join('');
}

const replyImagesInput = document.getElementById('replyImages');
if (replyImagesInput) {
    replyImagesInput.addEventListener('change', function() {
        const preview = document.getElementById('filePreviewList');
        preview.innerHTML = '';

        const files = Array.from(this.files);
        if (!files.length) return;

        files.forEach(file => {
            const item = document.createElement('div');
            item.className = 'file-preview-item';
            item.innerHTML = `
                <span><i class="fas fa-image mr-2 text-blue-500"></i>${escapeHtml(file.name)}</span>
                <span>${(file.size / 1024 / 1024).toFixed(2)} MB</span>
            `;
            preview.appendChild(item);
        });
    });
}

const replyForm = document.getElementById('replyForm');
if (replyForm) {
    replyForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (isTicketClosed(currentTicketStatus)) {
            showMessage('This ticket is closed. Reply is not allowed.', 'error');
            return;
        }

        const formData = new FormData(this);
        formData.append('action', 'send_reply');
        formData.append('ticket_id', ticketId);

        try {
            const response = await fetch('/api/ticket-details-api', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                showMessage(data.message, 'success');
                this.reset();
                document.getElementById('filePreviewList').innerHTML = '';
                loadTicketDetails();
            } else {
                showMessage(data.message || 'Failed to send reply', 'error');
            }
        } catch (error) {
            showMessage('Something went wrong while sending reply', 'error');
        }
    });
}

loadTicketDetails();
</script>

</body>
</html>