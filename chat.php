<?php
require_once 'common/auth.php';

// Ensure user is logged in
$user = requireAuth($conn);

// Direct values
$user_id = $user['id'];

$display_name = !empty($user['full_name']) 
    ? $user['full_name'] 
    : $user['username'];

$profile_image = !empty($user['profile_image']) 
    ? $user['profile_image'] 
    : 'https://ui-avatars.com/api/?name=' . urlencode($display_name);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Chat Support - StayEase</title>
<!-- Font Awesome 6 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

body {
    background-color: #f5f5f5;
    margin: 0;
    padding: 0;
}

.app-container {
    max-width: 414px;
    margin: 0 auto;
    background: #F8F9FA;
    min-height: 100vh;
    height: 100vh;
    box-shadow: 0 0 30px rgba(0,0,0,0.06);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
}

.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { scrollbar-width: none; }

.nav-item.active { color: #3B82F6; }

.msg-bubble {
    max-width: 75%;
    animation: pop 0.2s ease;
    word-break: break-word;
}

@keyframes pop {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.typing-dot {
    width: 7px;
    height: 7px;
    background: #9CA3AF;
    border-radius: 50%;
    animation: bounce 1.2s infinite ease-in-out;
}

.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes bounce {
    0%,60%,100% { transform: translateY(0); }
    30% { transform: translateY(-5px); }
}

#chat-messages {
    scroll-behavior: smooth;
}

.typing-indicator {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 12px 16px;
    background: white;
    border-radius: 20px;
    border-top-left-radius: 4px;
    width: fit-content;
}

.msg-time {
    opacity: 1;
    transition: opacity 0.2s;
    font-size: 10px;
    line-height: 1;
    display: inline-block;
    margin-top: 4px;
}

.chat-header {
    background: white;
    padding: 20px 20px 16px;
    border-bottom: 1px solid #F3F4F6;
    flex-shrink: 0;
    position: sticky;
    top: 0;
    z-index: 30;
}

.chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    padding-bottom: 20px;
}

.quick-replies-wrap {
    background: #F8F9FA;
    padding: 8px 16px;
    border-top: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9;
    overflow-x: auto;
    white-space: nowrap;
    flex-shrink: 0;
}

.chat-input-wrap {
    background: white;
    border-top: 1px solid #F3F4F6;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}

.bottom-nav {
    background: white;
    border-top: 1px solid #F3F4F6;
    padding: 8px 20px 10px;
    flex-shrink: 0;
}

.nav-items {
    display: flex;
    justify-content: space-around;
    align-items: center;
}

.nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    cursor: pointer;
    color: #9CA3AF;
    transition: color 0.15s;
    padding: 4px 16px;
    border-radius: 8px;
}

.nav-item i { font-size: 19px; }
.nav-item span { font-size: 10px; font-weight: 600; }
.nav-item.active { color: #3B82F6; }
.nav-item:hover:not(.active) { color: #374151; }

#quickReplies button {
    flex-shrink: 0;
}

/* Message status icons */
.msg-status {
    display: inline-flex;
    align-items: center;
    gap: 2px;
    margin-left: 4px;
}

.msg-status i {
    font-size: 10px;
}

.msg-status .fa-check-double.read {
    color: #93C5FD;
}

/* Support message time */
.support-time {
    font-size: 10px;
    color: #9CA3AF;
    margin-top: 4px;
    display: inline-block;
}

/* Avatar styling - FIXED */
.support-avatar {
    width: 30px;
    height: 30px;
    min-width: 30px;
    min-height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3B82F6, #2563EB);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 12px;
    overflow: hidden;
    flex-shrink: 0;
}

.support-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Message row */
.message-row {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    margin-bottom: 8px;
}

.user-message {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 8px;
}

.support-bubble {
    background: white;
    border-radius: 18px;
    border-bottom-left-radius: 4px;
    padding: 10px 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    max-width: 75%;
}

.user-bubble {
    background: #2563EB;
    border-radius: 18px;
    border-bottom-right-radius: 4px;
    padding: 10px 14px;
    max-width: 75%;
    box-shadow: 0 2px 10px rgba(37,99,235,0.25);
}

/* Blocked Overlay */
.blocked-overlay {
    position: fixed;
    bottom: 8%;
    left: 0;
    right: 0;
    max-width: 414px;
    margin: 0 auto;
    /*background: rgba(15,23,42,0.96);*/
    backdrop-filter: blur(10px);
    padding: 28px 24px 32px;
    text-align: center;
    border-radius: 24px 24px 0 0;
    z-index: 200;
    animation: slideUp 0.3s cubic-bezier(.22,.68,0,1.2);
    display: none;
}

.blocked-overlay.visible { display: block; }

@keyframes slideUp {
    from { transform: translateY(100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.overlay-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin: 0 auto 14px;
}

.overlay-icon.closed { background: rgba(239,68,68,0.15); color: #FCA5A5; }
.overlay-icon.resolved { background: rgba(16,185,129,0.15); color: #6EE7B7; }

.overlay-title {
    color: #000;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 8px;
}

.overlay-msg {
    color: #94A3B8;
    font-size: 13px;
    line-height: 1.6;
    margin-bottom: 22px;
}

.overlay-btn {
    border: none;
    padding: 13px 30px;
    border-radius: 30px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: transform 0.15s;
    font-family: inherit;
}

.overlay-btn:hover { transform: translateY(-2px); }
.overlay-btn:active { transform: translateY(0); }

.overlay-btn.btn-new {
    background: linear-gradient(135deg, #3B82F6, #1D4ED8);
    color: white;
    box-shadow: 0 4px 18px rgba(37,99,235,0.35);
}

.overlay-btn.btn-resume {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
    box-shadow: 0 4px 18px rgba(16,185,129,0.35);
}

/* Status badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.3px;
}
.badge-closed { background: #FEE2E2; color: #EF4444; }
.badge-resolved { background: #D1FAE5; color: #065F46; }

/* Disabled input */
.msg-input-disabled {
    opacity: 0.55;
    cursor: not-allowed;
}

.confirm-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.confirm-modal.active {
  display: flex;
}

.confirm-box {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  width: 320px;
  text-align: center;
}

.confirm-title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 10px;
}

.confirm-message {
  font-size: 14px;
  margin-bottom: 20px;
}

.confirm-actions {
  display: flex;
  justify-content: space-between;
}

.btn-cancel {
  background: #ccc;
  border: none;
  padding: 8px 15px;
  border-radius: 5px;
  cursor: pointer;
}

.btn-ok {
  background: #007bff;
  color: #fff;
  border: none;
  padding: 8px 15px;
  border-radius: 5px;
  cursor: pointer;
}
</style>
</head>
<body>

<div class="app-container">

    <div class="chat-header">
        <div class="flex items-center gap-3" style="display: flex; align-items: center; gap: 12px;">
            <button onclick="goBack()" style="background: none; border: none; color: #9CA3AF; cursor: pointer; font-size: 20px;">
                <i class="fas fa-arrow-left"></i>
            </button>

            <div class="support-avatar" id="supportAvatar" style="width: 40px; height: 40px; min-width: 40px; min-height: 40px; font-size: 14px;">
                ST
            </div>

            <div style="flex: 1;">
                <h1 style="font-size: 15px; font-weight: 700; color: #111827;" id="supportName">Support Team</h1>
                <div style="display: flex; align-items: center; gap: 6px; margin-top: 2px;">
                    <span class="w-2 h-2 rounded-full bg-green-400 inline-block" id="onlineStatus" style="width: 8px; height: 8px; border-radius: 50%;"></span>
                    <span style="font-size: 11px; color: #6B7280;" id="supportStatus">Online · Replies instantly</span>
                </div>
            </div>

            <button onclick="refreshChat()" style="background: none; border: none; color: #9CA3AF; cursor: pointer; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
    </div>

    <div id="chat-messages" class="chat-body no-scrollbar" style="flex: 1; overflow-y: auto; padding: 16px;">
        <div style="display: flex; justify-content: center; padding: 40px 0;" id="loadingMessages">
            <div class="typing-indicator" style="display: flex; align-items: center; gap: 4px; padding: 12px 16px; background: white; border-radius: 20px; border-top-left-radius: 4px;">
                <div class="typing-dot" style="width: 7px; height: 7px; background: #9CA3AF; border-radius: 50%; animation: bounce 1.2s infinite ease-in-out;"></div>
                <div class="typing-dot" style="width: 7px; height: 7px; background: #9CA3AF; border-radius: 50%; animation: bounce 1.2s infinite ease-in-out; animation-delay: 0.2s;"></div>
                <div class="typing-dot" style="width: 7px; height: 7px; background: #9CA3AF; border-radius: 50%; animation: bounce 1.2s infinite ease-in-out; animation-delay: 0.4s;"></div>
            </div>
            <span style="font-size: 12px; color: #9CA3AF; margin-left: 8px;">Loading conversations...</span>
        </div>
    </div>

    <div id="typingIndicator" style="padding: 0 16px 8px 16px; display: none; flex-shrink: 0;">
        <div style="display: flex; align-items: flex-end; gap: 8px;">
            <div class="support-avatar" id="typingAvatar" style="width: 28px; height: 28px; min-width: 28px; min-height: 28px; font-size: 10px;">ST</div>
            <div class="typing-indicator" style="display: flex; align-items: center; gap: 4px; padding: 12px 16px; background: white; border-radius: 20px; border-top-left-radius: 4px;">
                <div class="typing-dot" style="width: 7px; height: 7px; background: #9CA3AF; border-radius: 50%; animation: bounce 1.2s infinite ease-in-out;"></div>
                <div class="typing-dot" style="width: 7px; height: 7px; background: #9CA3AF; border-radius: 50%; animation: bounce 1.2s infinite ease-in-out; animation-delay: 0.2s;"></div>
                <div class="typing-dot" style="width: 7px; height: 7px; background: #9CA3AF; border-radius: 50%; animation: bounce 1.2s infinite ease-in-out; animation-delay: 0.4s;"></div>
            </div>
        </div>
    </div>

    <div id="quickReplies" class="quick-replies-wrap" style="background: #F8F9FA; padding: 8px 16px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; overflow-x: auto; white-space: nowrap; display: flex; gap: 8px;"></div>

    <div class="chat-input-wrap" id="inputBar" style="background: white; border-top: 1px solid #F3F4F6; padding: 12px 16px; display: flex; align-items: center; gap: 12px;">
        <input
            id="msg-input"
            type="text"
            placeholder="Type your message..."
            style="flex: 1; background: #F3F4F6; border: none; border-radius: 24px; padding: 11px 18px; font-size: 13.5px; color: #111827; outline: none;"
            onkeydown="if(event.key==='Enter' && !event.shiftKey){ event.preventDefault(); sendMessage(); }"
            autocomplete="off"
        >
        <button
            onclick="sendMessage()"
            class="send-btn"
            id="sendBtn"
            style="width: 42px; height: 42px; background: linear-gradient(135deg, #3B82F6, #2563EB); border: none; border-radius: 50%; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: transform 0.15s;"
            type="button"
        >
            <i class="fas fa-paper-plane" style="font-size: 14px;"></i>
        </button>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <div class="nav-items">
            <div class="nav-item " onclick="goToPage('home')">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </div>
            <div class="nav-item" onclick="goToPage('search')">
                <i class="fas fa-search"></i>
                <span>Search</span>
            </div>
            <div class="nav-item" onclick="goToPage('bookings')">
                <i class="fas fa-ticket-alt"></i>
                <span>Bookings</span>
            </div>
            <div class="nav-item" onclick="goToPage('saved-rooms')">
                <i class="fas fa-heart"></i>
                <span>Saved</span>
            </div>
            <div class="nav-item active" onclick="goToPage('profile')">
                <i class="fas fa-user"></i>
                <span>Profile</span>
            </div>
        </div>
    </div>



    <!-- Blocked Overlay for Closed/Resolved Chats -->
    <div class="blocked-overlay" id="blockedOverlay">
        <div class="overlay-icon" id="overlayIcon">
            <i class="fas fa-lock" id="overlayIconEl"></i>
        </div>
        <div class="overlay-title" id="overlayTitle">Chat Closed</div>
        <div class="overlay-msg" id="overlayMsg">
            This conversation has been closed. Start a new chat to continue.
        </div>
        <button class="overlay-btn btn-new" id="overlayBtn" onclick="overlayAction()">
            <i class="fas fa-plus-circle"></i>
            <span>Start New Chat</span>
        </button>
    </div>

</div>


<div id="confirmModal" class="confirm-modal">
  <div class="confirm-box">
    <div class="confirm-title" id="confirmTitle">Confirm</div>
    <div class="confirm-message" id="confirmMessage"></div>
    <div class="confirm-actions">
      <button id="confirmCancel" class="btn-cancel">Cancel</button>
      <button id="confirmOk" class="btn-ok">OK</button>
    </div>
  </div>
</div>

<script>
let conversationId = null;
let supportAgent = null;
let quickReplies = [];
let lastMessageTime = null;
let messageCheckInterval = null;
let convStatus = 'active'; // active, resolved, closed
let isBlocked = false;

function showConfirm(message, callback, title = "Confirm") {
    const modal = document.getElementById('confirmModal');
    const msg = document.getElementById('confirmMessage');
    const titleEl = document.getElementById('confirmTitle');
    const okBtn = document.getElementById('confirmOk');
    const cancelBtn = document.getElementById('confirmCancel');

    msg.textContent = message;
    titleEl.textContent = title;

    modal.classList.add('active');

    function cleanup() {
        modal.classList.remove('active');
        okBtn.onclick = null;
        cancelBtn.onclick = null;
    }

    okBtn.onclick = () => {
        cleanup();
        callback(true);
    };

    cancelBtn.onclick = () => {
        cleanup();
        callback(false);
    };
}

document.addEventListener('DOMContentLoaded', function () {
    loadChat();
});

function loadChat() {
    showLoading();

    fetch('/api/chat')
        .then(async response => {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON:', text);
                throw new Error('Invalid server response');
            }
        })
        .then(data => {
            hideLoading();

            if (data.success) {
                conversationId = data.data.conversation_id;
                convStatus = data.data.status || 'active';
                supportAgent = data.data.support_agent || null;
                quickReplies = Array.isArray(data.data.quick_replies) ? data.data.quick_replies : [];

                updateSupportInfo();
                renderQuickReplies();
                renderMessages(Array.isArray(data.data.messages) ? data.data.messages : []);

                const messages = Array.isArray(data.data.messages) ? data.data.messages : [];
                if (messages.length > 0) {
                    lastMessageTime = messages[messages.length - 1].created_at;
                }

                applyStatus(convStatus);
                startPolling();
            } else {
                showError(data.message || 'Failed to load chat');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showError('Connection error');
        });
}

function updateSupportInfo() {
    if (!supportAgent) return;

    document.getElementById('supportName').textContent = supportAgent.name || 'Support Team';

    const avatarDiv = document.getElementById('supportAvatar');
    if (supportAgent.profile_image) {
        avatarDiv.innerHTML = '<img src="' + escapeHtml(supportAgent.profile_image) + '" style="width: 100%; height: 100%; object-fit: cover;">';
    } else {
        const initials = (supportAgent.name || '')
            .split(' ')
            .map(n => n.charAt(0))
            .join('')
            .substring(0, 2)
            .toUpperCase();
        avatarDiv.textContent = initials;
    }

    const statusDot = document.getElementById('onlineStatus');
    const statusText = document.getElementById('supportStatus');

    if (convStatus === 'closed') {
        statusDot.style.background = '#EF4444';
        statusText.innerHTML = '<span class="status-badge badge-closed">CLOSED</span>&nbsp;Chat ended';
    } else if (convStatus === 'resolved') {
        statusDot.style.background = '#9CA3AF';
        statusText.innerHTML = '<span class="status-badge badge-resolved">RESOLVED</span>&nbsp;Tap to resume';
    } else if (parseInt(supportAgent.is_online) === 1) {
        statusDot.style.background = '#10B981';
        statusText.textContent = 'Online · Replies instantly';
    } else {
        statusDot.style.background = '#9CA3AF';
        statusText.textContent = 'Away · Will reply soon';
    }
}

function applyStatus(status) {
    const input = document.getElementById('msg-input');
    const sendBtn = document.getElementById('sendBtn');
    const overlay = document.getElementById('blockedOverlay');
    const iconWrap = document.getElementById('overlayIcon');
    const iconEl = document.getElementById('overlayIconEl');
    const titleEl = document.getElementById('overlayTitle');
    const msgEl = document.getElementById('overlayMsg');
    const btn = document.getElementById('overlayBtn');

    if (status === 'closed') {
        isBlocked = true;
        stopPolling();

        input.disabled = true;
        input.style.opacity = '0.55';
        input.placeholder = 'Start a new chat to continue…';

        sendBtn.disabled = true;
        sendBtn.style.opacity = '0.45';

        iconWrap.className = 'overlay-icon closed';
        iconEl.className = 'fas fa-lock';

        titleEl.textContent = 'Chat Closed';
        msgEl.textContent = 'This conversation has been closed. Start a new chat to continue.';

        btn.className = 'overlay-btn btn-new';
        btn.innerHTML = '<i class="fas fa-plus-circle"></i><span>Start New Chat</span>';

        overlay.classList.add('visible');

    } else if (status === 'resolved') {
        isBlocked = true;
        stopPolling();

        input.disabled = true;
        input.style.opacity = '0.55';
        input.placeholder = 'Resume chat to continue…';

        sendBtn.disabled = true;
        sendBtn.style.opacity = '0.45';

        iconWrap.className = 'overlay-icon resolved';
        iconEl.className = 'fas fa-check-circle';

        titleEl.innerHTML = '<span>Chat Resolved</span> <i class="fas fa-check"></i>';
        msgEl.textContent = 'Your issue has been marked as resolved. Want to continue chatting?';

        btn.className = 'overlay-btn btn-resume';
        btn.innerHTML = '<i class="fas fa-redo-alt"></i><span>Resume Chat</span>';

        overlay.classList.add('visible');

    } else {
        isBlocked = false;

        input.disabled = false;
        input.style.opacity = '1';
        input.placeholder = 'Type your message...';

        sendBtn.disabled = false;
        sendBtn.style.opacity = '1';

        overlay.classList.remove('visible');

        markMessagesAsRead();
        startPolling();
    }

    updateSupportInfo();
}

function overlayAction() {
    if (convStatus === 'resolved') {
        resumeChat();
    } else if (convStatus === 'closed') {
        startNewChat();
    }
}

function startNewChat() {

    showConfirm('Start a new chat? This conversation will be archived.', function(res) {
        if (!res) return;

        fetch('/api/chat?action=start-new', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(async response => {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid server response');
            }
        })
        .then(data => {
            if (data.success) {
                conversationId = data.data.conversation_id;
                supportAgent = data.data.support_agent || null;
                quickReplies = data.data.quick_replies || [];
                convStatus = 'active';
                lastMessageTime = null;

                document.getElementById('chat-messages').innerHTML = '';
                updateSupportInfo();
                renderQuickReplies();
                
                // Welcome message without emoji to avoid database issues
                const container = document.getElementById('chat-messages');
                const avatarHtml = supportAgent && supportAgent.profile_image
                    ? '<img src="' + escapeHtml(supportAgent.profile_image) + '" style="width: 100%; height: 100%; object-fit: cover;">'
                    : 'ST';
                
                container.innerHTML = `
                    <div class="message-row">
                        <div class="support-avatar" style="width: 30px; height: 30px; min-width: 30px; min-height: 30px; font-size: 12px;">
                            ${avatarHtml}
                        </div>
                        <div class="support-bubble">
                            <p style="color: #111827; font-size: 13.5px;">Hi there! Welcome to StayEase Support. How can we help you today?</p>
                            <span style="font-size: 10px; color: #9CA3AF; margin-top: 4px; display: inline-block;">${formatTime(new Date())}</span>
                        </div>
                    </div>
                `;
                
                applyStatus('active');
                markMessagesAsRead();
                showToast('New chat started!', 'success');

            } else {
                showToast(data.message || 'Failed to start new chat', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Network error', 'error');
        });

    });
}

function resumeChat() {

    showConfirm('Resume this conversation?', function(res) {
        if (!res) return;

        fetch('/api/chat?action=resume', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ conversation_id: conversationId })
        })
        .then(async response => {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid server response');
            }
        })
        .then(data => {
            if (data.success) {
                convStatus = 'active';
                applyStatus('active');
                loadChat();
                showToast('Chat resumed!', 'success');
            } else {
                showToast(data.message || 'Failed to resume', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Network error', 'error');
        });

    });
}

function renderQuickReplies() {
    const container = document.getElementById('quickReplies');

    if (!quickReplies.length || convStatus !== 'active') {
        container.innerHTML = '';
        return;
    }

    let html = '';
    quickReplies.forEach(reply => {
        const trigger = JSON.stringify(reply.trigger || '');
        const label = escapeHtml(reply.display || reply.trigger || 'Reply');

        html += `
            <button
                type="button"
                onclick='sendQuick(${trigger})'
                style="flex-shrink: 0; border: 1.5px solid #E5E7EB; background: white; color: #374151; font-size: 12px; font-weight: 600; padding: 6px 14px; border-radius: 20px; cursor: pointer; white-space: nowrap; transition: all 0.15s;"
                onmouseover="this.style.borderColor='#3B82F6'; this.style.color='#3B82F6'; this.style.background='#EFF6FF';"
                onmouseout="this.style.borderColor='#E5E7EB'; this.style.color='#374151'; this.style.background='white';"
            >
                ${label}
            </button>
        `;
    });

    container.innerHTML = html;
}

function renderMessages(messages) {
    const container = document.getElementById('chat-messages');

    if (!Array.isArray(messages) || messages.length === 0) {
        const avatarHtml = supportAgent && supportAgent.profile_image
            ? '<img src="' + escapeHtml(supportAgent.profile_image) + '" style="width: 100%; height: 100%; object-fit: cover;">'
            : '';
        
        container.innerHTML = `
            <div class="message-row">
                <div class="support-avatar" style="width: 30px; height: 30px; min-width: 30px; min-height: 30px; font-size: 12px;">
                    ${avatarHtml}
                </div>
                <div class="support-bubble">
                    <p style="color: #111827; font-size: 13.5px;">Hi there! Welcome to StayEase Support. How can we help you today?</p>
                    <span style="font-size: 10px; color: #9CA3AF; margin-top: 4px; display: inline-block;">${formatTime(new Date())}</span>
                </div>
            </div>
        `;
        scrollToBottom();
        return;
    }

    let html = '';
    let lastDate = null;

    messages.forEach(msg => {
        const msgDate = new Date(msg.created_at);
        const dateStr = formatDate(msgDate);

        if (lastDate !== dateStr) {
            html += `
                <div style="display: flex; align-items: center; gap: 10px; margin: 10px 0;">
                    <div style="flex: 1; height: 1px; background: #E5E7EB;"></div>
                    <span style="font-size: 10.5px; color: #9CA3AF; font-weight: 600;">${dateStr}</span>
                    <div style="flex: 1; height: 1px; background: #E5E7EB;"></div>
                </div>
            `;
            lastDate = dateStr;
        }

        if (msg.sender_type === 'user') {
            const isRead = parseInt(msg.is_read) === 1;
            html += `
                <div class="user-message" data-msg-id="${msg.id}">
                    <div class="user-bubble">
                        <p style="color: white; font-size: 13.5px;">${escapeHtml(msg.message || '')}</p>
                        <div style="display: flex; align-items: center; justify-content: flex-end; gap: 4px; margin-top: 4px;">
                            <span style="font-size: 10px; color: rgba(255,255,255,0.65);">${formatTime(msgDate)}</span>
                            <span class="msg-status" id="status-${msg.id}">
                                ${isRead 
                                    ? '<i class="fas fa-check-double read" style="color: #93C5FD; font-size: 10px;"></i>' 
                                    : '<i class="fas fa-check-double" style="color: rgba(255,255,255,0.65); font-size: 10px;"></i>'}
                            </span>
                        </div>
                    </div>
                </div>
            `;
        } else {
            const avatarHtml = supportAgent && supportAgent.profile_image
                ? '<img src="' + escapeHtml(supportAgent.profile_image) + '" style="width: 100%; height: 100%; object-fit: cover;">'
                : 'ST';
            
            html += `
                <div class="message-row">
                    <div class="support-avatar" style="width: 30px; height: 30px; min-width: 30px; min-height: 30px; font-size: 12px;">
                        ${avatarHtml}
                    </div>
                    <div class="support-bubble">
                        <p style="color: #111827; font-size: 13.5px;">${escapeHtml(msg.message || '')}</p>
                        <span style="font-size: 10px; color: #9CA3AF; margin-top: 4px; display: inline-block;">${formatTime(msgDate)}</span>
                    </div>
                </div>
            `;
        }
    });

    container.innerHTML = html;
    scrollToBottom();
}

function sendMessage() {
    if (isBlocked) {
        showToast(convStatus === 'resolved' ? 'Resume chat first' : 'Start a new chat first', 'error');
        return;
    }

    const input = document.getElementById('msg-input');
    const sendBtn = document.getElementById('sendBtn');
    const text = input.value.trim();

    if (!text) return;

    input.disabled = true;
    sendBtn.disabled = true;
    input.value = '';

    // Add temporary message with clock icon
    const tempId = 'temp_' + Date.now();
    addUserMessageWithTempId(text, tempId);

    fetch('/api/chat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: text })
    })
    .then(async response => {
        const raw = await response.text();
        try {
            return JSON.parse(raw);
        } catch (e) {
            console.error('Invalid JSON:', raw);
            throw new Error('Invalid server response');
        }
    })
    .then(data => {
        if (data.success) {
            // Update temp message with real ID and status
            const messageId = data.data.id;
            const isRead = data.data.is_read == 1;
            
            // Replace temp message with real one
            updateTempMessage(tempId, messageId, text, isRead);
            
            lastMessageTime = data.data && data.data.created_at ? data.data.created_at : new Date().toISOString();

            if (data.auto_reply && data.auto_reply.has_reply && data.auto_reply.message) {
                showTypingIndicator();
                setTimeout(function () {
                    hideTypingIndicator();
                    addSupportMessage(data.auto_reply.message);
                    // After adding support message, mark user message as read
                    if (messageId) {
                        setTimeout(() => checkMessageReadStatus(messageId), 1000);
                    }
                }, 1000);
            }
        } else {
            removeTempMessage(tempId);
            showToast(data.message || 'Failed to send message', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        removeTempMessage(tempId);
        showToast('Failed to send message', 'error');
    })
    .finally(() => {
        input.disabled = false;
        sendBtn.disabled = false;
        input.focus();
    });
}

function addUserMessageWithTempId(text, tempId) {
    const container = document.getElementById('chat-messages');
    const now = new Date();

    const div = document.createElement('div');
    div.className = 'user-message';
    div.setAttribute('data-temp-id', tempId);
    div.innerHTML = `
        <div class="user-bubble">
            <p style="color: white; font-size: 13.5px;">${escapeHtml(text)}</p>
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 4px; margin-top: 4px;">
                <span style="font-size: 10px; color: rgba(255,255,255,0.65);">${formatTime(now)}</span>
                <span class="msg-status"><i class="fas fa-clock" style="color: rgba(255,255,255,0.5); font-size: 10px;"></i></span>
            </div>
        </div>
    `;

    container.appendChild(div);
    scrollToBottom();
}

function updateTempMessage(tempId, messageId, text, isRead) {
    const tempElement = document.querySelector(`[data-temp-id="${tempId}"]`);
    if (!tempElement) return;
    
    // Remove temp-id attribute and add real msg-id
    tempElement.removeAttribute('data-temp-id');
    tempElement.setAttribute('data-msg-id', messageId);
    
    // Update the status from clock to double tick
    const statusSpan = tempElement.querySelector('.msg-status');
    if (statusSpan) {
        statusSpan.id = `status-${messageId}`;
        statusSpan.innerHTML = isRead 
            ? '<i class="fas fa-check-double read" style="color: #93C5FD; font-size: 10px;"></i>'
            : '<i class="fas fa-check-double" style="color: rgba(255,255,255,0.65); font-size: 10px;"></i>';
    }
}

function removeTempMessage(tempId) {
    const tempElement = document.querySelector(`[data-temp-id="${tempId}"]`);
    if (tempElement) tempElement.remove();
}

function checkMessageReadStatus(messageId) {
    fetch(`/api/chat?action=check_read&message_id=${messageId}`)
        .then(async response => {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid server response');
            }
        })
        .then(data => {
            if (data.success && data.is_read) {
                updateMessageReadStatus(messageId, true);
            }
        })
        .catch(error => {
            console.error('Error checking read status:', error);
        });
}

function updateMessageReadStatus(messageId, isRead) {
    const statusSpan = document.getElementById(`status-${messageId}`);
    if (statusSpan && isRead) {
        statusSpan.innerHTML = '<i class="fas fa-check-double read" style="color: #93C5FD; font-size: 10px;"></i>';
    }
}

function addUserMessage(text) {
    const container = document.getElementById('chat-messages');
    const now = new Date();

    const div = document.createElement('div');
    div.className = 'user-message';
    div.innerHTML = `
        <div class="user-bubble">
            <p style="color: white; font-size: 13.5px;">${escapeHtml(text)}</p>
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 4px; margin-top: 4px;">
                <span style="font-size: 10px; color: rgba(255,255,255,0.65);">${formatTime(now)}</span>
                <span class="msg-status"><i class="fas fa-clock" style="color: rgba(255,255,255,0.5); font-size: 10px;"></i></span>
            </div>
        </div>
    `;

    container.appendChild(div);
    scrollToBottom();
}

function addSupportMessage(text) {
    const container = document.getElementById('chat-messages');
    const now = new Date();

    const avatarHtml = supportAgent && supportAgent.profile_image
        ? '<img src="' + escapeHtml(supportAgent.profile_image) + '" style="width: 100%; height: 100%; object-fit: cover;">'
        : '';

    const div = document.createElement('div');
    div.className = 'message-row';
    div.innerHTML = `
        <div class="support-avatar" style="width: 30px; height: 30px; min-width: 30px; min-height: 30px; font-size: 12px;">
            ${avatarHtml}
        </div>
        <div class="support-bubble">
            <p style="color: #111827; font-size: 13.5px;">${escapeHtml(text)}</p>
            <span style="font-size: 10px; color: #9CA3AF; margin-top: 4px; display: inline-block;">${formatTime(now)}</span>
        </div>
    `;

    container.appendChild(div);
    scrollToBottom();
}

function sendQuick(trigger) {
    const quickReply = quickReplies.find(r => r.trigger === trigger);
    if (!quickReply) return;

    document.getElementById('msg-input').value = quickReply.display || quickReply.trigger || '';
    sendMessage();
}

function startPolling() {
    stopPolling();
    if (convStatus !== 'active') return;
    messageCheckInterval = setInterval(checkNewMessages, 3000);
}

function stopPolling() {
    if (messageCheckInterval) {
        clearInterval(messageCheckInterval);
        messageCheckInterval = null;
    }
}

function checkNewMessages() {
    if (!conversationId || isBlocked) return;

    fetch('/api/chat')
        .then(async response => {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Invalid server response');
            }
        })
        .then(data => {
            if (data.success && data.data) {
                if (data.data.status !== convStatus) {
                    convStatus = data.data.status;
                    applyStatus(convStatus);
                    updateSupportInfo();
                }
                
                if (Array.isArray(data.data.messages) && data.data.messages.length > 0) {
                    const lastMsg = data.data.messages[data.data.messages.length - 1];

                    if (lastMsg && lastMsg.created_at && (!lastMessageTime || lastMsg.created_at > lastMessageTime)) {
                        renderMessages(data.data.messages);
                        lastMessageTime = lastMsg.created_at;
                        markMessagesAsRead();
                    } else {
                        // Even if no new messages, update read statuses
                        data.data.messages.forEach(msg => {
                            if (msg.sender_type === 'user' && parseInt(msg.is_read) === 1) {
                                updateMessageReadStatus(msg.id, true);
                            }
                        });
                    }
                }
            }
        })
        .catch(error => {
            console.error('checkNewMessages error:', error);
        });
}

function markMessagesAsRead() {
    if (!conversationId || isBlocked) return;
    fetch('/api/chat?action=mark_read&conversation_id=' + conversationId, {
        method: 'PUT'
    }).catch(error => {
        console.error('mark read error:', error);
    });
}

function showTypingIndicator() {
    const typingAvatar = document.getElementById('typingAvatar');
    if (supportAgent && supportAgent.profile_image) {
        typingAvatar.innerHTML = '<img src="' + escapeHtml(supportAgent.profile_image) + '" style="width: 100%; height: 100%; object-fit: cover;">';
    } else {
        const initials = (supportAgent && supportAgent.name) 
            ? supportAgent.name.split(' ').map(n => n.charAt(0)).join('').substring(0, 2).toUpperCase()
            : '';
        typingAvatar.textContent = initials;
    }
    document.getElementById('typingIndicator').style.display = 'block';
    scrollToBottom();
}

function hideTypingIndicator() {
    document.getElementById('typingIndicator').style.display = 'none';
}

function refreshChat() {
    stopPolling();
    loadChat();
    showToast('Refreshing...', 'success');
}

function formatTime(date) {
    if (!(date instanceof Date)) date = new Date(date);

    let hours = date.getHours();
    let minutes = date.getMinutes();
    let ampm = hours >= 12 ? 'PM' : 'AM';

    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;

    return hours + ':' + minutes + ' ' + ampm;
}

function formatDate(date) {
    if (!(date instanceof Date)) date = new Date(date);

    const today = new Date();
    const yesterday = new Date();
    yesterday.setDate(today.getDate() - 1);

    if (date.toDateString() === today.toDateString()) {
        return 'Today';
    } else if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
    } else {
        return date.toLocaleDateString('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }
}

function escapeHtml(unsafe) {
    unsafe = unsafe == null ? '' : String(unsafe);
    return unsafe
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function scrollToBottom() {
    const container = document.getElementById('chat-messages');
    setTimeout(function () {
        container.scrollTop = container.scrollHeight;
    }, 50);
}

function showLoading() {
    const loading = document.getElementById('loadingMessages');
    if (loading) loading.style.display = 'flex';
}

function hideLoading() {
    const loading = document.getElementById('loadingMessages');
    if (loading) loading.style.display = 'none';
}

function showError(message) {
    const container = document.getElementById('chat-messages');
    container.innerHTML = `
        <div style="text-align: center; padding: 40px 0;">
            <i class="fas fa-exclamation-circle" style="font-size: 42px; color: #FCA5A5; margin-bottom: 12px;"></i>
            <p style="color: #6B7280; font-size: 13px;">${escapeHtml(message)}</p>
            <button onclick="loadChat()" style="margin-top: 14px; background: #2563EB; color: white; border: none; padding: 9px 22px; border-radius: 20px; font-size: 13px; font-weight: 600; cursor: pointer;">Try Again</button>
        </div>
    `;
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.style.position = 'fixed';
    toast.style.bottom = '100px';
    toast.style.left = '50%';
    toast.style.transform = 'translateX(-50%)';
    toast.style.backgroundColor = type === 'error' ? '#EF4444' : '#10B981';
    toast.style.color = 'white';
    toast.style.padding = '8px 20px';
    toast.style.borderRadius = '10px';
    toast.style.fontSize = '13px';
    toast.style.fontWeight = '600';
    toast.style.zIndex = '1000';
    toast.style.whiteSpace = 'nowrap';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function goBack() {
    window.history.back();
}

function goToPage(page) {
    window.location.href = page;
}

window.addEventListener('beforeunload', function () {
    stopPolling();
});
</script>

</body>
</html>