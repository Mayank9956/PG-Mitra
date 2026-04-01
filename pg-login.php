<?php
// session_start();
// Redirect if already logged in
// if(isset($_SESSION['owner_id'])) {
//     header("Location: owner-dashboard.php");
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>PG Owner Login | StayEase</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #ffc300 0%, #ff0000 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
        }
        .glass-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);
            transform: translateY(0);
            transition: all 0.3s ease;
        }
        .hero-section {
            background: linear-gradient(135deg, #0F2B3D 0%, #1B4D6E 100%);
            padding:15px 15px 45px 15px;
            position: relative;
        }
        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 100%;
            height: 40px;
            background: white;
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
        }
        .badge-icon {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
        }
        .tab-buttons {
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 5px;
            display: flex;
            gap: 8px;
            margin-top: 20px;
        }
        .tab-btn {
            flex: 1;
            text-align: center;
            padding: 12px 0;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            background: transparent;
            color: white;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }
        .tab-btn.active {
            background: white;
            color: #1B4D6E;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .form-container {
            padding: 10px 15px 10px 15px;
            background: white;
        }
        .input-group {
            position: relative;
            margin-bottom: 24px;
        }
        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 18px;
            z-index: 1;
        }
        .input-field {
            width: 100%;
            padding: 16px 20px 16px 52px;
            border: 2px solid #E5E7EB;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background: #F9FAFB;
        }
        .input-field:focus {
            border-color: #1B4D6E;
            outline: none;
            background: white;
            box-shadow: 0 0 0 3px rgba(27,77,110,0.1);
        }
        .toggle-pwd {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            cursor: pointer;
            z-index: 2;
        }
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #0F2B3D 0%, #1B4D6E 100%);
            color: white;
            border: none;
            border-radius: 44px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 10px 25px rgba(27,77,110,0.3);
        }
        .btn-login:active {
            transform: scale(0.98);
        }
        .btn-login.loading {
            opacity: 0.7;
            pointer-events: none;
        }
        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
      .toast-message {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    
    background: #1F2937;
    backdrop-filter: blur(12px);
    color: white;
    padding: 12px 24px;
    border-radius: 16px;
    font-weight: 500;
    z-index: 10000;

    animation: slideUp 0.3s ease;

    white-space: normal;
    max-width: 80%;
    text-align: center;

    font-size: 14px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.toast-message.success { background: #10B981; }
.toast-message.error { background: #EF4444; }

@keyframes slideUp {
    from { 
        opacity: 0; 
        transform: translate(-50%, -40%);
    }
    to { 
        opacity: 1; 
        transform: translate(-50%, -50%);
    }
}
        .forgot-link {
            text-align: right;
            margin: -10px 0 10px 0;
        }
        .forgot-link a {
            color: #1B4D6E;
            font-size: 14px;
            text-decoration: none;
            font-weight: 500;
        }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 15px 0;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #E5E7EB;
        }
        .divider::before { margin-right: 15px; }
        .divider::after { margin-left: 15px; }
        .divider span {
            color: #9CA3AF;
            font-size: 13px;
        }
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 20000;
            padding: 20px;
        }
        .modal-card {
            background: white;
            max-width: 380px;
            width: 100%;
            border-radius: 32px;
            overflow: hidden;
            animation: modalPop 0.25s ease;
        }
        @keyframes modalPop {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }


.support-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 30000; 

    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
}

.support-modal.active {
    opacity: 1;
    pointer-events: auto;
}

.support-box {
    background: rgba(31, 41, 55, 0.9);
    backdrop-filter: blur(20px);
    padding: 25px;
    border-radius: 20px;
    width: 90%;
    max-width: 350px;
    text-align: center;
    color: white;

    transform: scale(0.9);
    transition: all 0.3s ease;
}

.support-modal.active .support-box {
    transform: scale(1);
}

.support-box h2 {
    margin-bottom: 8px;
}

.support-box p {
    font-size: 14px;
    color: #d1d5db;
    margin-bottom: 20px;
}

.support-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.option {
    padding: 12px;
    border-radius: 12px;
    text-decoration: none;
    color: white;
    font-weight: 500;
    transition: 0.2s;
}

.option:hover {
    transform: translateY(-2px);
}

.whatsapp { background: #25D366; }
.telegram { background: #0088cc; }
.email { background: #374151; }

.close-btn {
    margin-top: 15px;
    background: transparent;
    border: none;
    color: #9ca3af;
    cursor: pointer;
}
    </style>
</head>
<body>

<div class="login-container">
    <div class="glass-card">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="badge-icon">
                <i class="fas fa-building"></i>
                <span>PG Owner Portal</span>
            </div>
            <h1 class="text-3xl font-bold text-white mt-3">Welcome Back</h1>
            <p class="text-white/80 text-sm mt-2">Login to manage your properties</p>
            
            <!-- Tab Buttons -->
            <div class="tab-buttons">
                <button class="tab-btn active" id="loginTabBtn" onclick="window.location.href='/pg-login'">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>
                <button class="tab-btn" id="registerTabBtn" onclick="window.location.href='/pg-register'">
                    <i class="fas fa-user-plus mr-2"></i> Register
                </button>
            </div>
        </div>
        
        <!-- Login Form -->
        <div class="form-container">
            <form id="loginForm" onsubmit="handleLogin(event)">
                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="text" id="loginIdentifier" class="input-field" placeholder="Email or Phone Number" autocomplete="username">
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="loginPassword" class="input-field" placeholder="Password" autocomplete="current-password">
                    <i class="fas fa-eye toggle-pwd" onclick="togglePassword('loginPassword', this)"></i>
                </div>
                
                <div class="forgot-link">
                    <a href="javascript:void(0)" onclick="openForgotModal()">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-arrow-right-to-bracket mr-2"></i> Login to Dashboard
                </button>
            </form>
            
            <div class="divider">
                <span>Owner Exclusive Access</span>
            </div>
            
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-shield-alt mr-1"></i> Secure access to manage listings, bookings & earnings
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div id="forgotModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg">Reset Password</h3>
            <button onclick="closeForgotModal()" class="text-gray-400 text-2xl">&times;</button>
        </div>
        <div class="p-5">
            <p>Please contact customer support to reset your login password</p>
            </div>
        
        <!--<div class="p-5">-->
        <!--    <p class="text-gray-500 text-sm mb-4">Enter your registered email or phone to receive reset instructions.</p>-->
        <!--    <input type="text" id="resetIdentifier" class="w-full border-2 border-gray-200 rounded-2xl p-3 px-5 focus:border-[#1B4D6E] focus:outline-none" placeholder="Email or Phone number">-->
        <!--</div>-->
        <div class="p-4 bg-gray-50 flex gap-3">
            <button onclick="closeForgotModal()" class="flex-1 py-3 rounded-2xl border border-gray-300 font-medium">Cancel</button>

<!-- Customer Care Button -->
<button onclick="openSupportModal()" 
class="flex-1 bg-[#0F2B3D] text-white rounded-2xl py-3 font-semibold">
    Customer Care
</button>

            <!--<button onclick="sendResetLink()" class="flex-1 bg-[#0F2B3D] text-white rounded-2xl py-3 font-semibold" id="resetBtn">Send Link</button>-->
        </div>
    </div>
</div>


<!-- Support -->
<div id="supportModal" class="support-modal">
    <div class="support-box">
        <h2>Contact Support</h2>
        <p>Choose your preferred way to reach us</p>

        <div class="support-options">
            <a href="https://wa.me/91XXXXXXXXXX?text=Hello%20I%20need%20help%20with%20my%20account" target="_blank" class="option whatsapp">
                <i class="fab fa-whatsapp mr-2"></i> WhatsApp
            </a>

            <a href="https://t.me/yourusername" target="_blank" class="option telegram">
                <i class="fab fa-telegram mr-2"></i> Telegram
            </a>

            <a href="mailto:support@example.com?subject=Support%20Request" class="option email">
                <i class="fas fa-envelope mr-2"></i> Email
            </a>
        </div>

        <button onclick="closeSupportModal()" class="close-btn">Close</button>
    </div>
</div>

<script>
    // Toast Notification
    function showToast(message, type = 'info') {
        let existing = document.querySelector('.toast-message');
        if(existing) existing.remove();
        
        let toast = document.createElement('div');
        toast.className = `toast-message ${type}`;
        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle')} mr-2"></i> ${message}`;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Toggle Password Visibility
    function togglePassword(inputId, icon) {
        let input = document.getElementById(inputId);
        if(input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Email Validation
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    // Handle Login
    async function handleLogin(event) {
        event.preventDefault();
        
        const identifier = document.getElementById('loginIdentifier').value.trim();
        const password = document.getElementById('loginPassword').value;
        const loginBtn = document.getElementById('loginBtn');
        
        if(!identifier || !password) {
            showToast('Please enter email/phone and password', 'error');
            return;
        }
        
        // Show loading state
        loginBtn.classList.add('loading');
        loginBtn.disabled = true;
        loginBtn.innerHTML = 'Logging in...';
        
        const formData = new FormData();
        formData.append('action', 'owner_login');
        formData.append('identifier', identifier);
        formData.append('password', password);
        
        try {
            const response = await fetch('/api/owner-auth-handler', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            loginBtn.classList.remove('loading');
            loginBtn.disabled = false;
            loginBtn.innerHTML = '<i class="fas fa-arrow-right-to-bracket mr-2"></i> Login to Dashboard';
            
            if(data.success) {
                showToast('Login successful! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = '/admin/owner/index';
                }, 1000);
            } else {
                showToast(data.message || 'Invalid credentials', 'error');
            }
        } catch(error) {
            loginBtn.classList.remove('loading');
            loginBtn.disabled = false;
            loginBtn.innerHTML = '<i class="fas fa-arrow-right-to-bracket mr-2"></i> Login to Dashboard';
            showToast('Connection error. Please try again.', 'error');
            console.error('Error:', error);
        }
    }
    
    // Forgot Password Modal
    function openForgotModal() {
        document.getElementById('forgotModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeForgotModal() {
        document.getElementById('forgotModal').style.display = 'none';
        document.body.style.overflow = '';
        document.getElementById('resetIdentifier').value = '';
    }
    
    // async function sendResetLink() {
    //     const identifier = document.getElementById('resetIdentifier').value.trim();
    //     const resetBtn = document.getElementById('resetBtn');
        
    //     if(!identifier) {
    //         showToast('Please enter email or phone number', 'error');
    //         return;
    //     }
        
    //     resetBtn.innerHTML = 'Sending...';
    //     resetBtn.disabled = true;
        
    //     const formData = new FormData();
    //     formData.append('action', 'owner_forgot');
    //     formData.append('identifier', identifier);
        
    //     try {
    //         const response = await fetch('/api/owner-auth-handler', {
    //             method: 'POST',
    //             body: formData
    //         });
            
    //         const data = await response.json();
            
    //         if(data.success) {
    //             showToast('Reset link sent to your registered contact', 'success');
    //             closeForgotModal();
    //         } else {
    //             showToast(data.message || 'Account not found', 'error');
    //         }
    //     } catch(error) {
    //         showToast('Unable to send reset link', 'error');
    //     } finally {
    //         resetBtn.innerHTML = 'Send Link';
    //         resetBtn.disabled = false;
    //     }
    // }
    
 function openSupportModal() {
    const modal = document.getElementById('supportModal');
    if(modal) modal.classList.add('active');
}

function closeSupportModal() {
    const modal = document.getElementById('supportModal');
    if(modal) modal.classList.remove('active');
}

// click outside close
document.addEventListener('click', function(e) {
    const modal = document.getElementById('supportModal');
    if(modal && e.target === modal) {
        closeSupportModal();
    }
});

    // Close modal on outside click
    window.onclick = function(e) {
        let modal = document.getElementById('forgotModal');
        if(e.target === modal) closeForgotModal();
    };
    
    // Enter key submit
    document.addEventListener('keydown', function(e) {
        if(e.key === 'Enter' && document.getElementById('forgotModal').style.display === 'flex') {
            sendResetLink();
        }
    });
    

</script>
</body>
</html>