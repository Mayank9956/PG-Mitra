<?php
require_once 'common/auth.php';

redirectIfLoggedIn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>PG Mitra — Sign in & Register | Find Your Perfect Stay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        body {
            background: #F1F5F9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            margin: 0;
        }

        .auth-card {
            max-width: 1120px;
            width: 100%;
            margin: 0 auto;
            background: #FFFFFF;
            border-radius: 0rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: all 0.25s ease;
        }

        .auth-grid {
            display: flex;
            flex-direction: row;
            min-height: 600px;
        }

        .hero-section {
            flex: 1.2;
            background: linear-gradient(135deg, #1E3A8A 0%, #3B82F6 50%, #7C3AED 100%);
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
        }

        .form-section {
            flex: 1;
            padding: 3rem;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Responsive behaviour */
        @media (max-width: 1024px) {
            .hero-section, .form-section {
                padding: 2rem;
            }
        }

        @media (max-width: 880px) {
            body {
                padding: 0.75rem;
                align-items: flex-start;
            }
            .auth-card {
                border-radius: 1.25rem;
                margin-top: 1rem;
                margin-bottom: 1rem;
            }
            .auth-grid {
                flex-direction: column;
            }
            .hero-section {
                padding: 2rem 1.5rem;
                min-height: auto;
            }
            .form-section {
                padding: 2rem 1.5rem;
            }
            .hero-footer {
                display: none; 
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 0rem;
            }
            .mb-6 {
    margin-bottom: 0.5rem!important;
}
            .hero-section {
                padding: 0.75rem 0.75rem;
            }
            .forgot-link {
    text-align: right;
    margin: -0.5rem 0 0rem!important;
}
            .mb-8 {
    margin-bottom: 1rem!important;
}
            .form-section {
                padding: 0.75rem 0.75rem;
            }
            .auth-card {
                margin-top: 0rem!important;
               border-radius:  0rem!important;
            }
            h1 { font-size: 1.75rem !important; }
            .tab-btn { font-size: 0.9rem !important; }
        }

        /* Tab styling — modern pill design */
        .tab-pill {
            display: inline-flex;
            background: #F1F5F9;
            border-radius: 60px;
            padding: 0.25rem;
            margin-bottom: 1.5rem;
            width: 100%;
            gap: 0.25rem;
        }
        .tab-btn {
            flex: 1;
            text-align: center;
            padding: 0.7rem 0;
            font-weight: 600;
            font-size: 0.95rem;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s;
            background: transparent;
            border: none;
            color: #64748B;
        }
        .tab-btn.active {
            background: white;
            color: #1E3A8A;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            font-weight: 700;
        }

        /* Input group */
        .input-group {
            margin-bottom: 1rem;
            position: relative;
        }
        .input-group i:not(.toggle-eye) {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 1rem;
            pointer-events: none;
            z-index: 1;
        }
        .input-group input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 2.75rem;
            border: 1.5px solid #E2E8F0;
            border-radius: 0.85rem;
            font-size: 0.95rem;
            background: #FFFFFF;
            transition: all 0.2s;
            outline: none;
            appearance: none;
        }
        .input-group input:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .toggle-eye {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            cursor: pointer;
            font-size: 0.9rem;
            z-index: 2;
            background: transparent;
            padding: 0.5rem;
        }

        .btn-primary {
            width: 100%;
            background: #1E3A8A;
            color: white;
            border: none;
            border-radius: 0.85rem;
            padding: 0.85rem;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 0.5rem;
        }
        .btn-primary:hover {
            background: #1E40AF;
            transform: translateY(-1px);
        }
        .btn-primary:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.25rem 0;
            color: #94A3B8;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #E2E8F0;
        }
        .divider::before { margin-right: 1rem; }
        .divider::after { margin-left: 1rem; }

        .owner-badge {
            background: #F8FAFE;
            text-align: center;
            padding: 0.75rem;
            border-radius: 0.85rem;
            margin-top: 0.75rem;
            font-weight: 600;
            color: #1E3A8A;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #E2E8F0;
            font-size: 0.9rem;
        }
        .owner-badge:hover {
            background: #EFF6FF;
            border-color: #BFDBFE;
        }

        .forgot-link {
            text-align: right;
            margin: -0.5rem 0 1rem;
        }
        .forgot-link a {
            color: #3B82F6;
            font-size: 0.85rem;
            font-weight: 500;
            text-decoration: none;
        }

        .strength-meter {
            height: 4px;
            background: #E2E8F0;
            border-radius: 4px;
            margin: 0.4rem 0 0.2rem;
            overflow: hidden;
        }
        .strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease;
        }
        .strength-text, .match-text {
            font-size: 0.7rem;
            display: block;
            min-height: 1rem;
        }

        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            margin: 1rem 0;
            font-size: 0.85rem;
            color: #475569;
        }
        .checkbox-group input[type="checkbox"] {
            margin-top: 0.2rem;
            width: 1rem;
            height: 1rem;
            cursor: pointer;
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1rem;
        }
        .modal-container {
            max-width: 450px;
            width: 100%;
            background: white;
            border-radius: 1.25rem;
            overflow: hidden;
            animation: modalFade 0.25s ease-out;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        @keyframes modalFade {
            from { opacity: 0; transform: translateY(10px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .modal-header {
            background: #1E3A8A;
            padding: 1.25rem 1.5rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-body {
            padding: 1.5rem;
            max-height: 60vh;
            overflow-y: auto;
        }
        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #E2E8F0;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .toast-notify {
            position: fixed;
            top: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            background: #1E293B;
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
            z-index: 1100;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: auto;
            max-width: 90%;
            animation: toastIn 0.3s ease-out;
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translate(-50%, -20px); }
            to { opacity: 1; transform: translate(-50%, 0); }
        }
        .toast-notify.success { background: #10B981; }
        .toast-notify.error { background: #EF4444; }

        .loading-spinner {
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(2px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            gap: 1rem;
            font-weight: 600;
            color: #1E3A8A;
        }
        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #E2E8F0;
            border-top: 3px solid #1E3A8A;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-grid">
        <!-- LEFT: HERO SECTION -->
        <div class="hero-section">
            <div>
                <div class="flex items-center gap-2 mb-8">
                    <div class="bg-white/20 p-2 rounded-xl backdrop-blur-md">
                        <i class="fas fa-home text-2xl"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight">PG Mitra</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight mb-6">
                    Find your next <br><span class="text-blue-200">perfect stay</span>
                </h1>
                <p class="text-blue-100 text-lg opacity-90 max-w-md">
                    Join thousands of students and professionals finding verified PGs and hostels across India.
                </p>
            </div>

            <div class="hero-footer mt-12">
                <div class="flex items-center gap-4 mb-6">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full border-2 border-blue-600 bg-blue-400 flex items-center justify-center text-xs font-bold">AJ</div>
                        <div class="w-10 h-10 rounded-full border-2 border-blue-600 bg-indigo-400 flex items-center justify-center text-xs font-bold">RK</div>
                        <div class="w-10 h-10 rounded-full border-2 border-blue-600 bg-purple-400 flex items-center justify-center text-xs font-bold">MS</div>
                    </div>
                    <div class="text-sm">
                        <div class="font-bold">20+ Users</div>
                        <div class="text-blue-200 text-xs">Trusting PG Mitra daily</div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center gap-2 bg-white/10 p-3 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-check-circle text-blue-300"></i> Verified Listings
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 p-3 rounded-xl backdrop-blur-sm">
                        <i class="fas fa-shield-alt text-blue-300"></i> Secure Payments
                    </div>
                </div>
            </div>
        </div>


        <div class="form-section">
            <div class="tab-pill">
                <button class="tab-btn active" id="loginTabBtn" onclick="switchTab('login')">Sign In</button>
                <button class="tab-btn" id="registerTabBtn" onclick="switchTab('register')">New Account</button>
            </div>

 
            <div id="loginForm">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Welcome back</h2>
                    <p class="text-slate-500 text-sm">Enter your details to access your account</p>
                </div>
                <form onsubmit="handleLogin(event)">
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="loginEmail" placeholder="Email address" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="loginPassword" placeholder="Password" required>
                        <i class="fas fa-eye toggle-eye" onclick="togglePassword('loginPassword', this)"></i>
                    </div>
                    <div class="forgot-link">
                        <a href="javascript:void(0)" onclick="openForgotModal()">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn-primary" id="loginBtn">Sign In →</button>
                    <div class="divider">Or continue with</div>
                    <div class="owner-badge" onclick="switchTab('register')">Create an account</div>
                </form>
            </div>

      
            <div id="registerForm" style="display: none;">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Create account</h2>
                    <p class="text-slate-500 text-sm">Join PG Mitra to start your search</p>
                </div>
                <form onsubmit="handleRegister(event)">
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="regName" placeholder="Full name" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="regEmail" placeholder="Email address" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="regPassword" placeholder="Create password" onkeyup="checkStrength(this.value)" required>
                        <i class="fas fa-eye toggle-eye" onclick="togglePassword('regPassword', this)"></i>
                    </div>
                    <div class="strength-meter"><div class="strength-bar" id="strengthBar"></div></div>
                    <span id="strengthText" class="strength-text"></span>

                    <div class="input-group mt-3">
                        <i class="fas fa-shield-alt"></i>
                        <input type="password" id="regConfirmPassword" placeholder="Confirm password" onkeyup="checkMatch()" required>
                    </div>
                    <span id="matchText" class="match-text"></span>

                    <div class="input-group mt-3">
                        <i class="fas fa-ticket-alt"></i>
                        <input type="text" id="invitationCode" placeholder="Invitation code (optional)" value="<?php echo isset($_GET['ref']) ? htmlspecialchars($_GET['ref']) : ''; ?>">
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="termsCheckbox">
                        <label for="termsCheckbox">I agree to the <a href="javascript:void(0)" class="text-blue-600 font-semibold" onclick="openTermsModal()">Terms & Conditions</a></label>
                    </div>
                    <button type="submit" class="btn-primary" id="registerBtn">Create account →</button>
                    <div class="divider">Already have an account?</div>
                    <div class="owner-badge" onclick="switchTab('login')">Sign in instead</div>
                    <div class="owner-badge mt-2" onclick="window.location.href='/pg-register'">
                        <i class="fas fa-hotel mr-2"></i> Register as PG Owner
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div id="termsModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header"><h3 class="font-bold">Terms & Conditions</h3><button class="text-white text-xl" onclick="closeTermsModal()">&times;</button></div>
        <div class="modal-body">
            <h4 class="font-bold mb-1">1. Acceptance</h4><p class="text-sm mb-3">By using PG Mitra you agree to these terms.</p>
            <h4 class="font-bold mb-1">2. Account responsibility</h4><p class="text-sm mb-3">You are responsible for your login credentials.</p>
            <h4 class="font-bold mb-1">3. Bookings & payments</h4><p class="text-sm mb-3">All bookings are subject to property confirmation.</p>
            <h4 class="font-bold mb-1">4. Cancellations</h4><p class="text-sm mb-3">Vary per property; review before booking.</p>
            <h4 class="font-bold mb-1">5. Privacy</h4><p class="text-sm">Your data is handled with care as per our privacy policy.</p>
        </div>
        <div class="modal-footer">
            <button class="bg-gray-100 px-5 py-2 rounded-xl text-sm font-medium" onclick="closeTermsModal()">Close</button>
            <button class="bg-blue-800 text-white px-5 py-2 rounded-xl text-sm font-medium" onclick="acceptTerms()">I Agree</button>
        </div>
    </div>
</div>


<div id="forgotModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header"><h3 class="font-bold">Reset password</h3><button class="text-white text-xl" onclick="closeForgotModal()">&times;</button></div>
        <div class="modal-body">
            <p class="text-sm text-gray-600 mb-4">We'll email you a password reset link.</p>
            <div class="input-group"><i class="fas fa-envelope"></i><input type="email" id="resetEmailInput" placeholder="Your email address"></div>
        </div>
        <div class="modal-footer">
            <button class="bg-gray-100 px-5 py-2 rounded-xl text-sm" onclick="closeForgotModal()">Cancel</button>
            <button class="bg-blue-800 text-white px-5 py-2 rounded-xl text-sm" id="sendResetBtn" onclick="sendResetLink()">Send link</button>
        </div>
    </div>
</div>

<script>

    let activeToast = null, activeLoading = null;
    function showToast(msg, type = 'info', duration = 3000) {
        if(activeToast) activeToast.remove();
        const toast = document.createElement('div');
        toast.className = `toast-notify ${type}`;
        toast.innerText = msg;
        document.body.appendChild(toast);
        activeToast = toast;
        setTimeout(() => { if(toast && toast.parentNode) toast.remove(); if(activeToast === toast) activeToast = null; }, duration);
    }
    function showLoading(text = 'Please wait...') {
        if(activeLoading) activeLoading.remove();
        const div = document.createElement('div');
        div.className = 'loading-spinner';
        div.innerHTML = `<div class="spinner"></div><div>${text}</div>`;
        document.body.appendChild(div);
        activeLoading = div;
    }
    function hideLoading() { if(activeLoading) { activeLoading.remove(); activeLoading = null; } }


    function switchTab(tab) {
        const loginTabBtn = document.getElementById('loginTabBtn');
        const registerTabBtn = document.getElementById('registerTabBtn');
        const loginFormDiv = document.getElementById('loginForm');
        const registerFormDiv = document.getElementById('registerForm');
        if(tab === 'login') {
            loginTabBtn.classList.add('active'); registerTabBtn.classList.remove('active');
            loginFormDiv.style.display = 'block'; registerFormDiv.style.display = 'none';
        } else {
            registerTabBtn.classList.add('active'); loginTabBtn.classList.remove('active');
            registerFormDiv.style.display = 'block'; loginFormDiv.style.display = 'none';
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function togglePassword(inputId, iconEl) {
        const inp = document.getElementById(inputId);
        if(inp.type === 'password') { inp.type = 'text'; iconEl.classList.remove('fa-eye'); iconEl.classList.add('fa-eye-slash'); }
        else { inp.type = 'password'; iconEl.classList.remove('fa-eye-slash'); iconEl.classList.add('fa-eye'); }
    }


    function openTermsModal() { document.getElementById('termsModal').style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    function closeTermsModal() { document.getElementById('termsModal').style.display = 'none'; document.body.style.overflow = ''; }
    function acceptTerms() { document.getElementById('termsCheckbox').checked = true; closeTermsModal(); showToast('Terms accepted', 'success'); }

    function openForgotModal() { document.getElementById('forgotModal').style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    function closeForgotModal() { document.getElementById('forgotModal').style.display = 'none'; document.body.style.overflow = ''; document.getElementById('resetEmailInput').value = ''; }

    function sendResetLink() {
        const email = document.getElementById('resetEmailInput').value.trim();
        if(!email) { showToast('Enter your email address', 'error'); return; }
        if(!isValidEmail(email)) { showToast('Valid email required', 'error'); return; }
        const btn = document.getElementById('sendResetBtn');
        btn.disabled = true; btn.classList.add('opacity-70'); showLoading('Sending link...');
        const fd = new FormData(); fd.append('action', 'forgot_password'); fd.append('email', email);
        fetch('/api/auth-handler', { method: 'POST', body: fd })
            .then(res => res.json()).then(data => {
                hideLoading(); btn.disabled = false; btn.classList.remove('opacity-70');
                if(data.success) { showToast('Reset link sent! Check your inbox.', 'success'); closeForgotModal(); }
                else { showToast(data.message || 'Failed to send', 'error'); }
            }).catch(() => { hideLoading(); btn.disabled = false; showToast('Network error', 'error'); });
    }

    // Login handler
    function handleLogin(e) {
        e.preventDefault();
        const email = document.getElementById('loginEmail').value.trim();
        const pwd = document.getElementById('loginPassword').value;
          if(!email) { showToast('Enter your email address', 'error'); return; }
         if(!pwd) { showToast('Enter your password', 'error'); return; }
        if(!isValidEmail(email)) { showToast('Invalid email format', 'error'); return; }
        const btn = document.getElementById('loginBtn');
        btn.disabled = true; btn.classList.add('opacity-70'); showLoading('Signing in...');
        const fd = new FormData(); fd.append('action', 'login'); fd.append('email', email); fd.append('password', pwd);
        fetch('/api/auth-handler', { method: 'POST', body: fd })
            .then(r => r.json()).then(data => {
                hideLoading(); btn.disabled = false; btn.classList.remove('opacity-70');
                if(data.success) { showToast('Welcome back! Redirecting...', 'success'); setTimeout(() => { window.location.href = 'index.php'; }, 1000); }
                else { showToast(data.message || 'Login failed', 'error'); }
            }).catch(() => { hideLoading(); btn.disabled = false; showToast('Connection error', 'error'); });
    }

    // Register handler
    function handleRegister(e) {
        e.preventDefault();
        const name = document.getElementById('regName').value.trim();
        const email = document.getElementById('regEmail').value.trim();
        const pwd = document.getElementById('regPassword').value;
        const confirm = document.getElementById('regConfirmPassword').value;
        const invite = document.getElementById('invitationCode').value;
        const termsOk = document.getElementById('termsCheckbox').checked;
        if(!name || !email || !pwd || !confirm) { showToast('Please fill all fields', 'error'); return; }
        if(!isValidEmail(email)) { showToast('Enter valid email', 'error'); return; }
        if(pwd.length < 6) { showToast('Password must be 6+ characters', 'error'); return; }
        if(pwd !== confirm) { showToast('Passwords do not match', 'error'); return; }
        if(!termsOk) { showToast('You must accept Terms & Conditions', 'error'); return; }
        const btn = document.getElementById('registerBtn');
        btn.disabled = true; btn.classList.add('opacity-70'); showLoading('Creating account...');
        const fd = new FormData(); fd.append('action', 'register'); fd.append('name', name); fd.append('email', email); fd.append('password', pwd); fd.append('invitation_code', invite);
        fetch('/api/auth-handler', { method: 'POST', body: fd })
            .then(r => r.json()).then(data => {
                hideLoading(); btn.disabled = false; btn.classList.remove('opacity-70');
                if(data.success) {
                    showToast('Account created! Please login.', 'success');
                    setTimeout(() => { switchTab('login'); resetRegisterForm(); }, 1500);
                } else { showToast(data.message || 'Registration failed', 'error'); }
            }).catch(() => { hideLoading(); btn.disabled = false; showToast('Server error', 'error'); });
    }

    function resetRegisterForm() {
        document.getElementById('regName').value = '';
        document.getElementById('regEmail').value = '';
        document.getElementById('regPassword').value = '';
        document.getElementById('regConfirmPassword').value = '';
        document.getElementById('invitationCode').value = '';
        document.getElementById('termsCheckbox').checked = false;
        document.getElementById('strengthBar').style.width = '0%';
        document.getElementById('strengthText').innerText = '';
        document.getElementById('matchText').innerText = '';
    }

    function isValidEmail(email) { return /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/.test(email); }

    // Password strength
    function checkStrength(pwd) {
        const bar = document.getElementById('strengthBar'); const textSpan = document.getElementById('strengthText');
        if(!pwd) { bar.style.width = '0%'; textSpan.innerText = ''; return; }
        let strength = 0;
        if(pwd.length >= 6) strength++;
        if(pwd.length >= 8) strength++;
        if(/[a-z]/.test(pwd) && /[A-Z]/.test(pwd)) strength++;
        if(/[0-9]/.test(pwd)) strength++;
        if(/[^a-zA-Z0-9]/.test(pwd)) strength++;
        strength = Math.min(strength, 4);
        const widths = ['0%', '25%', '50%', '75%', '100%']; const colors = ['', '#DC2626', '#F59E0B', '#10B981', '#059669']; const texts = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        bar.style.width = widths[strength]; bar.style.backgroundColor = colors[strength];
        textSpan.innerText = texts[strength]; textSpan.style.color = colors[strength];
    }
    function checkMatch() {
        const pwd = document.getElementById('regPassword').value; const confirm = document.getElementById('regConfirmPassword').value; const matchSpan = document.getElementById('matchText');
        if(!confirm) { matchSpan.innerText = ''; return; }
        if(pwd === confirm) { matchSpan.innerText = '✓ Passwords match'; matchSpan.style.color = '#10B981'; }
        else { matchSpan.innerText = '✗ Passwords do not match'; matchSpan.style.color = '#DC2626'; }
    }

    // Click outside modal close
    window.onclick = function(e) {
        const termsModal = document.getElementById('termsModal'); const forgotModal = document.getElementById('forgotModal');
        if(e.target === termsModal) closeTermsModal();
        if(e.target === forgotModal) closeForgotModal();
    };
</script>
</body>
</html>