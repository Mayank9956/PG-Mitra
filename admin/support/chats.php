<?php
require_once '../common/auth.php';
requirePageRole([ROLE_SUPPORT]);
require_once '../common/layout.php';

renderHeader('Support Chats');
renderSidebarMenu('chats', 'support');
renderMainContentStart('Support Chats', $_SESSION['username'] ?? 'Support');
?>

<div class="support-chat">

    <!-- LEFT: Conversations -->
    <div class="card conversation-card">
        <div class="card-header">
            <h3>Conversations</h3>
            <button class="refresh-btn" onclick="loadConversations()">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
        <div id="conversationList" class="conversation-list">
            <p>Loading...</p>
        </div>
    </div>

    <!-- RIGHT: Chat Area -->
    <div class="chat-section">

        <div class="card chat-card">
            <div class="chat-header-section">
                <div>
                    <h3 id="chatTitle">Select a conversation</h3>
                    <span id="chatStatus" class="status-badge"></span>
                </div>
            </div>
            <div class="chat-box" id="chatMessages">
                <p>Select a conversation</p>
            </div>
        </div>

        <!-- Status Update Section - Above Quick Replies -->
        <div class="card status-update-card">
            <h3>Update Chat Status</h3>
            <div class="status-update-section">
                <select id="chatStatusSelect" class="status-select">
                    <option value="active">🟢 Active</option>
                    <option value="resolved">✅ Resolved</option>
                    <option value="closed">🔴 Closed</option>
                </select>
                <button class="update-status-btn" onclick="updateChatStatus()">
                    <i class="fas fa-check-circle"></i> Update Status
                </button>
            </div>
        </div>

        <!-- Quick Replies -->
        <div class="card quick-replies">
            <h3>Quick Replies</h3>
            <div id="quickReplyList"></div>
        </div>

        <div class="card reply-card">
            <form id="replyForm">
                <input type="hidden" name="conversation_id" id="conversation_id">
                <textarea name="message" placeholder="Type your reply..." id="replyMessage"></textarea>
                <button type="submit" class="primary">Send Reply</button>
            </form>
        </div>

    </div>

</div>

<!-- Alert Modal -->
<div id="alertModal" class="modal">
    <div class="modal-content">
        <p id="alertMessage"></p>
        <button onclick="closeModal()">OK</button>
    </div>
</div>

<style>
.support-chat {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 20px;
    height: calc(100vh - 200px);
}

/* Conversation Card */
.conversation-card {
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.card-header h3 {
    margin: 0;
}

.refresh-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 5px;
    transition: 0.2s;
}

.refresh-btn:hover {
    background: #f0f0f0;
}

.refresh-btn i {
    font-size: 16px;
    color: #666;
}

/* Conversation List - Scrollable */
.conversation-list {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.conversation-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    border-radius: 10px;
    cursor: pointer;
    background: #f8f9fa;
    transition: 0.2s;
    border: 1px solid transparent;
}

.conversation-item:hover {
    background: #e9ecef;
    transform: translateX(2px);
}

.conversation-item.active {
    background: #e3f2fd;
    border-color: #2196f3;
}

.conv-left {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.conv-name {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
}

.conv-msg {
    font-size: 12px;
    color: #666;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 180px;
}

.conv-right {
    text-align: right;
    margin-left: 10px;
}

.conv-time {
    font-size: 10px;
    color: #999;
    margin-bottom: 4px;
}

/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 500;
    text-transform: capitalize;
}

.status-badge.success { background: #d1e7dd; color: #0f5132; }
.status-badge.warning { background: #fff3cd; color: #664d03; }
.status-badge.danger { background: #f8d7da; color: #842029; }
.status-badge.info { background: #cff4fc; color: #055160; }

/* Chat Section */
.chat-section {
    display: flex;
    flex-direction: column;
    gap: 16px;
    height: 100%;
    overflow: hidden;
}

.chat-card {
    display: flex;
    flex-direction: column;
    flex: 1;
    overflow: hidden;
}

.chat-header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
    flex-shrink: 0;
}

.chat-header-section h3 {
    margin: 0 0 5px 0;
    font-size: 16px;
}

/* Status Update Card */
.status-update-card {
    flex-shrink: 0;
}

.status-update-card h3 {
    margin-bottom: 12px;
    font-size: 14px;
}

.status-update-section {
    display: flex;
    gap: 12px;
    align-items: center;
}

.status-select {
    flex: 1;
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ddd;
    font-size: 14px;
    outline: none;
    font-weight: 500;
    cursor: pointer;
    background: white;
}

.update-status-btn {
    padding: 10px 20px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: 0.2s;
    white-space: nowrap;
}

.update-status-btn:hover {
    background: #218838;
    transform: translateY(-1px);
}

.update-status-btn:active {
    transform: translateY(0);
}

/* Chat Box - Scrollable */
.chat-box {
    flex: 1;
    overflow-y: auto;
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: #fafafa;
}

.msg {
    padding: 10px 14px;
    border-radius: 12px;
    max-width: 70%;
    word-wrap: break-word;
    position: relative;
}

.msg.self {
    background: #d1e7dd;
    align-self: flex-end;
}

.msg:not(.self) {
    background: white;
    align-self: flex-start;
    border: 1px solid #e0e0e0;
}

.msg-time {
    font-size: 10px;
    color: #999;
    margin-top: 5px;
    display: block;
}

/* Quick Replies */
.quick-replies {
    flex-shrink: 0;
}

.quick-replies h3 {
    margin-bottom: 10px;
    font-size: 14px;
}

#quickReplyList {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.quick-btn {
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #ddd;
    background: white;
    cursor: pointer;
    font-size: 13px;
    transition: 0.2s;
}

.quick-btn:hover {
    background: #f0f0f0;
    border-color: #2196f3;
}

/* Reply Card */
.reply-card {
    flex-shrink: 0;
}

.reply-card textarea {
    width: 100%;
    height: 80px;
    padding: 10px;
    border-radius: 10px;
    border: 1px solid #ddd;
    margin-bottom: 10px;
    resize: vertical;
    font-family: inherit;
}

.reply-card button {
    padding: 10px 20px;
    background: #28a745;
    color: white;
    width: -webkit-fill-available;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: 0.2s;
}

.reply-card button:hover {
    background: #218838;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    justify-content: center;
    align-items: center;
}

.modal.active {
    display: flex;
}

.modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    min-width: 300px;
}

.modal-content p {
    margin-bottom: 20px;
}

.modal-content button {
    padding: 8px 20px;
    background: #2196f3;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

/* Scrollbar Styling */
.conversation-list::-webkit-scrollbar,
.chat-box::-webkit-scrollbar {
    width: 6px;
}

.conversation-list::-webkit-scrollbar-track,
.chat-box::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.conversation-list::-webkit-scrollbar-thumb,
.chat-box::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.conversation-list::-webkit-scrollbar-thumb:hover,
.chat-box::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Loading State */
.loading {
    text-align: center;
    padding: 20px;
    color: #999;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 40px;
    color: #999;
}

/* Mobile Responsive */
@media(max-width: 768px) {
    .support-chat {
        grid-template-columns: 1fr;
        height: auto;
    }
    
    .conversation-card {
        max-height: 300px;
    }
    
    .chat-section {
        min-height: 500px;
    }
    
    .status-update-section {
        flex-direction: column;
    }
    
    .status-select {
        width: 100%;
    }
    
    .update-status-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
// Set timezone to Kolkata (Asia/Kolkata)
const TIMEZONE = 'Asia/Kolkata';

// Function to format time in Kolkata timezone
function formatTimeKolkata(timestamp) {
    if (!timestamp) return '';
    
    const date = new Date(timestamp);
    
    const options = {
        timeZone: TIMEZONE,
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    };
    
    const formattedDate = date.toLocaleString('en-IN', options);
    
    const today = new Date();
    const kolkataToday = new Date(today.toLocaleString('en-US', { timeZone: TIMEZONE }));
    const messageDate = new Date(date.toLocaleString('en-US', { timeZone: TIMEZONE }));
    
    const isToday = kolkataToday.toDateString() === messageDate.toDateString();
    
    if (isToday) {
        return date.toLocaleString('en-IN', {
            timeZone: TIMEZONE,
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }
    
    return formattedDate;
}

// Function to format time for conversation list (shorter format)
function formatTimeShort(timestamp) {
    if (!timestamp) return '';
    
    const date = new Date(timestamp);
    const now = new Date();
    const kolkataNow = new Date(now.toLocaleString('en-US', { timeZone: TIMEZONE }));
    const kolkataDate = new Date(date.toLocaleString('en-US', { timeZone: TIMEZONE }));
    
    const diffMs = kolkataNow - kolkataDate;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'Just now';
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays}d ago`;
    
    return date.toLocaleString('en-IN', {
        timeZone: TIMEZONE,
        month: 'short',
        day: 'numeric'
    });
}

let activeConversationId = null;
let pollTimer = null;
let currentConversationStatus = null;

// STATUS CLASS
function getStatusClass(s) {
    s = (s || '').toLowerCase();
    if (s === 'resolved') return 'success';
    if (s === 'closed') return 'danger';
    return 'info';
}

// SHOW ALERT
function showAlert(message, isError = false) {
    const modal = document.getElementById('alertModal');
    const alertMsg = document.getElementById('alertMessage');
    alertMsg.textContent = message;
    modal.classList.add('active');
    
    setTimeout(() => {
        closeModal();
    }, 3000);
}

function closeModal() {
    document.getElementById('alertModal').classList.remove('active');
}

// API HELPER with better error handling
async function apiGet(url) {
    try {
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const text = await response.text();
        
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON:', text);
            return { status: 'error', message: 'Invalid server response' };
        }
    } catch (error) {
        console.error('API Error:', error);
        return { status: 'error', message: error.message || 'Network error' };
    }
}

async function apiPost(url, data) {
    try {
        const response = await fetch(url, { 
            method: 'POST', 
            body: data 
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const text = await response.text();
        
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON:', text);
            return { status: 'error', message: 'Invalid server response' };
        }
    } catch (error) {
        console.error('API Error:', error);
        return { status: 'error', message: error.message || 'Network error' };
    }
}

function getStatusConfig(status) {
    status = (status || '').toLowerCase();

    if (status === 'resolved') {
        return { label: 'Resolved', class: 'success' };
    }
    if (status === 'closed') {
        return { label: 'Closed', class: 'danger' };
    }
    return { label: 'Active', class: 'info' };
}

// LOAD CONVERSATIONS
async function loadConversations() {
    const container = document.getElementById('conversationList');
    container.innerHTML = '<div class="loading">Loading conversations...</div>';
    
    const res = await apiGet('../api/support/list-conversations');
    let html = '';

    if (res.status === 'success' && res.data && res.data.conversations && res.data.conversations.length > 0) {
        res.data.conversations.forEach(c => {
            const isActive = activeConversationId === c.conversation_id;
            const lastMessage = c.last_message || 'No messages';
            const truncatedMsg = lastMessage.length > 40 ? lastMessage.substring(0, 40) + '...' : lastMessage;
            const formattedTime = formatTimeShort(c.updated_at);
            const statusObj = getStatusConfig(c.status);

            html += `
            <div class="conversation-item ${isActive ? 'active' : ''}" onclick="loadMessages(${c.conversation_id})">
                <div class="conv-left">
                    <div class="conv-name">
                        ${escapeHtml(c.user_name || 'User')}
                    </div>
                    <div class="conv-msg">
                        ${escapeHtml(truncatedMsg)}
                    </div>
                </div>
                <div class="conv-right">
                    <div class="conv-time">
                        ${formattedTime}
                    </div>
                    <span class="status-badge ${statusObj.class}">
                        ${statusObj.label}
                    </span>
                </div>
            </div>`;
        });
    } else {
        html = '<div class="empty-state">No conversations found</div>';
    }

    container.innerHTML = html;
}

// LOAD MESSAGES
async function loadMessages(id) {
    if (!id) {
        showAlert('Invalid conversation ID', true);
        return;
    }
    
    activeConversationId = id;
    document.getElementById('conversation_id').value = id;
    
    // Show loading state
    document.getElementById('chatMessages').innerHTML = '<div class="loading">Loading messages...</div>';
    
    // Highlight active conversation
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Find and highlight the active conversation
    const conversationItems = document.querySelectorAll('.conversation-item');
    for (let item of conversationItems) {
        const onclickAttr = item.getAttribute('onclick');
        if (onclickAttr && onclickAttr.includes(`loadMessages(${id})`)) {
            item.classList.add('active');
            break;
        }
    }
    
    // Load messages
    const res = await apiGet('../api/support/get-messages?conversation_id=' + id);
    renderMessages(res);
    
    // Get conversation status
    const convRes = await apiGet('../api/support/get-conversation?conversation_id=' + id);
    if (convRes.status === 'success' && convRes.data) {
        currentConversationStatus = convRes.data.status;
        updateChatHeader(convRes.data);
         console.log(convRes.data);
        // Set the select value to current status
        const statusSelect = document.getElementById('chatStatusSelect');
        if (statusSelect) {
            statusSelect.value = convRes.data.status || 'active';
        }
    } else {
        // Update header with default values if conversation fetch fails
        updateChatHeader({ user_name: 'User', status: 'active' });
    }
    
    // Start polling
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(() => {
        if (activeConversationId) loadMessagesSilently(activeConversationId);
    }, 4000);
}

function updateChatHeader(conversation) {
    const chatTitle = document.getElementById('chatTitle');
    const chatStatus = document.getElementById('chatStatus');
    
    if (chatTitle) {
        chatTitle.textContent = `Chat with ${conversation.user_name || 'User'}`;
    }
    if (chatStatus) {
        const status = conversation.status || 'active';
        chatStatus.textContent = getStatusConfig(status).label;
        chatStatus.className = `status-badge ${getStatusClass(status)}`;
    }
}

function renderMessages(res) {
    let html = '';
    const box = document.getElementById('chatMessages');
    
    if (res.status === 'success' && res.data && res.data.messages && res.data.messages.length > 0) {
        res.data.messages.forEach(msg => {
            const isSelf = msg.sender_type === 'support';
            const formattedTime = formatTimeKolkata(msg.created_at);
            
            html += `
            <div class="msg ${isSelf ? 'self' : ''}">
                ${escapeHtml(msg.message)}
                <span class="msg-time">${formattedTime}</span>
            </div>`;
        });
    } else if (res.status === 'error') {
        html = `<div class="empty-state">Error: ${escapeHtml(res.message)}</div>`;
    } else {
        html = '<div class="empty-state">No messages yet</div>';
    }
    
    if (box) {
        box.innerHTML = html;
        box.scrollTop = box.scrollHeight;
    }
}

async function loadMessagesSilently(id) {
    if (!activeConversationId || activeConversationId !== id) return;
    
    const res = await apiGet('../api/support/get-messages?conversation_id=' + id);
    
    // Only update if there are messages and the current display might be outdated
    if (res.status === 'success' && res.data && res.data.messages) {
        const currentMessages = document.querySelectorAll('#chatMessages .msg');
        if (currentMessages.length !== res.data.messages.length) {
            renderMessages(res);
        }
    }
}

// UPDATE CHAT STATUS
async function updateChatStatus() {
    if (!activeConversationId) {
        showAlert('Please select a conversation first', true);
        return;
    }
    
    const newStatus = document.getElementById('chatStatusSelect').value;
    const statusText = {
        'active': 'Active',
        'resolved': 'Resolved',
        'closed': 'Closed'
    }[newStatus] || newStatus;
    
    const formData = new FormData();
    formData.append('conversation_id', activeConversationId);
    formData.append('status', newStatus);
    
    const res = await apiPost('../api/support/update-chat-status', formData);
    
    if (res.status === 'success') {
        showAlert(`Chat status updated to ${statusText}`);
        currentConversationStatus = newStatus;
        
        // Update header status display
        const chatStatus = document.getElementById('chatStatus');
        if (chatStatus) {
            chatStatus.textContent = statusText;
            chatStatus.className = `status-badge ${getStatusClass(newStatus)}`;
        }
        
        // Update in conversation list
        await loadConversations();
    } else {
        showAlert(res.message || 'Failed to update status', true);
    }
}

// SEND REPLY
document.getElementById('replyForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (!activeConversationId) {
        showAlert('Please select a conversation first', true);
        return;
    }
    
    const message = this.message.value.trim();
    if (!message) {
        showAlert('Please enter a message', true);
        return;
    }
    
    const formData = new FormData(this);
    const res = await apiPost('../api/support/reply-chat', formData);
    
    if (res.status === 'success') {
        this.message.value = '';
        await loadMessages(activeConversationId);
        await loadConversations(); // Refresh conversation list to show last message
    } else {
        showAlert(res.message || 'Failed to send message', true);
    }
});

// LOAD QUICK REPLIES
async function loadQuickReplies() {
    const res = await apiGet('../api/support/quick-replies');
    let html = '';
    
    if (res.status === 'success' && res.data && res.data.quick_replies && res.data.quick_replies.length > 0) {
        res.data.quick_replies.forEach(q => {
            const responseText = q.response || q.display_text;
            html += `
            <button class="quick-btn" data-msg="${escapeHtml(responseText)}">
                ${escapeHtml(q.display_text)}
            </button>`;
        });
    } else {
        html = '<div class="empty-state">No quick replies available</div>';
    }
    
    const container = document.getElementById('quickReplyList');
    if (container) {
        container.innerHTML = html;
        
        document.querySelectorAll('.quick-btn').forEach(btn => {
            btn.onclick = function() {
                const replyTextarea = document.getElementById('replyMessage');
                if (replyTextarea) {
                    replyTextarea.value = this.dataset.msg;
                }
            };
        });
    }
}

// ESCAPE HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// INIT - Load conversations only when page is ready
document.addEventListener('DOMContentLoaded', function() {
    loadConversations();
    loadQuickReplies();
});

// Clean up on page unload
window.addEventListener('beforeunload', () => {
    if (pollTimer) clearInterval(pollTimer);
});
</script>

<?php renderFooter('support'); ?>