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
    <title>PG Owner Registration | StayEase</title>
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
        .register-container {
            max-width: 480px;
            width: 100%;
            margin: 0 auto;
        }
        .glass-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3);
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
            margin-top: 30px;
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
            margin-bottom: 22px;
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
            border-radius: 28px;
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
        .btn-register {
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
            position: relative;
        }
        .btn-register:active {
            transform: scale(0.98);
        }
        .btn-register.loading {
            opacity: 0.7;
            pointer-events: none;
        }
        .btn-register.loading::after {
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
        .password-strength {
            height: 6px;
            background: #F3F4F6;
            border-radius: 10px;
            margin: 8px 0 5px;
            overflow: hidden;
        }
        .strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s;
            border-radius: 10px;
        }
        .strength-text {
            font-size: 11px;
            margin-top: 5px;
            display: block;
        }
        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin: 20px 0;
        }
        .checkbox-container input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            accent-color: #1B4D6E;
        }
        .checkbox-container label {
            font-size: 13px;
            color: #4B5563;
            line-height: 1.4;
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
        .terms-content {
            max-height: 400px;
            overflow-y: auto;
            padding: 5px 0;
        }
    
    </style>
</head>
<body>

<div class="register-container">
    <div class="glass-card">
        <!-- Hero Section -->
        <div class="hero-section">
            <div class="badge-icon">
                <i class="fas fa-building"></i>
                <span>PG Owner Portal</span>
            </div>
            <h1 class="text-3xl font-bold text-white mt-5">Join as Owner</h1>
            <p class="text-white/80 text-sm mt-2">Start earning by listing your properties</p>
            
            <!-- Tab Buttons -->
            <div class="tab-buttons">
                <button class="tab-btn" id="loginTabBtn" onclick="window.location.href='/pg-login'">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </button>
                <button class="tab-btn active" id="registerTabBtn" onclick="window.location.href='/pg-register'">
                    <i class="fas fa-user-plus mr-2"></i> Register
                </button>
            </div>
        </div>
        
        <!-- Registration Form -->
        <div class="form-container">
            <form id="registerForm" onsubmit="handleRegister(event)">
                <div class="input-group">
                    <i class="fas fa-store input-icon"></i>
                    <input type="text" id="regName" class="input-field" placeholder="Full Name / Business Name" >
                </div>
                
                <div class="input-group">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" id="regEmail" class="input-field" placeholder="Email Address" >
                </div>
                
                <div class="input-group">
                    <i class="fas fa-phone-alt input-icon"></i>
                    <input type="tel" id="regPhone" class="input-field" placeholder="Phone Number" >
                </div>
                
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="regPassword" class="input-field" placeholder="Create Password" onkeyup="checkPasswordStrength(this.value)" >
                    <i class="fas fa-eye toggle-pwd" onclick="togglePassword('regPassword', this)"></i>
                </div>
                
                <div class="password-strength">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
                <span class="strength-text" id="strengthText"></span>
                
                <div class="input-group">
                    <i class="fas fa-check-circle input-icon"></i>
                    <input type="password" id="regConfirmPassword" class="input-field" placeholder="Confirm Password" onkeyup="checkPasswordMatch()" >
                    <i class="fas fa-eye toggle-pwd" onclick="togglePassword('regConfirmPassword', this)"></i>
                </div>
                <span class="strength-text" id="matchText"></span>
            
                
                <div class="checkbox-container">
                    <input type="checkbox" id="termsCheckbox" >
                    <label for="termsCheckbox">
                        I agree to the <a href="javascript:void(0)" onclick="openTermsModal()" class="text-[#1B4D6E] font-semibold">Terms & Conditions</a> and 
                        <a href="javascript:void(0)" onclick="openPrivacyModal()" class="text-[#1B4D6E] font-semibold">Privacy Policy</a>
                    </label>
                </div>
                
                <button type="submit" class="btn-register" id="registerBtn">
                    <i class="fas fa-user-plus mr-2"></i> Create Owner Account
                </button>
            </form>
            
            <div class="text-center mt-6">
                <p class="text-xs text-gray-500">
                    <i class="fas fa-chart-line mr-1"></i> 5000+ owners trust StayEase | Zero listing fee
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div id="termsModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-[#0F2B3D] to-[#1B4D6E] text-white">
            <h3 class="font-bold text-lg">Terms & Conditions</h3>
            <button onclick="closeTermsModal()" class="text-white text-2xl">&times;</button>
        </div>
        <div class="p-5 terms-content">
            <h4 class="font-bold text-gray-800 mt-2">1. Owner Eligibility</h4>
            <p class="text-sm text-gray-600 mb-3">You must be the legal owner or authorized manager of the PG property you wish to list.</p>
            
            <h4 class="font-bold text-gray-800 mt-3">2. Accurate Listings</h4>
            <p class="text-sm text-gray-600 mb-3">All property details, photos, amenities, and pricing must be truthful and up-to-date.</p>
            
            <h4 class="font-bold text-gray-800 mt-3">3. Commission Structure</h4>
            <p class="text-sm text-gray-600 mb-3">StayEase charges 8% commission per successful booking made through the platform.</p>
            
            <h4 class="font-bold text-gray-800 mt-3">4. Payment Terms</h4>
            <p class="text-sm text-gray-600 mb-3">Owner payouts are processed weekly after guest check-in, subject to verification.</p>
            
            <h4 class="font-bold text-gray-800 mt-3">5. Cancellation Policy</h4>
            <p class="text-sm text-gray-600 mb-3">Owners must honor the cancellation policy selected for their property listing.</p>
            
            <h4 class="font-bold text-gray-800 mt-3">6. Account Suspension</h4>
            <p class="text-sm text-gray-600 mb-3">Misleading information, fraudulent activities, or poor service may lead to account suspension.</p>
            
            <h4 class="font-bold text-gray-800 mt-3">7. Data Privacy</h4>
            <p class="text-sm text-gray-600 mb-3">We protect your data as per our Privacy Policy. You agree to handle guest data responsibly.</p>
        </div>
        <div class="p-4 bg-gray-50 flex gap-3">
            <button onclick="closeTermsModal()" class="flex-1 py-3 rounded-2xl border border-gray-300 font-medium">Close</button>
            <button onclick="acceptTerms()" class="flex-1 bg-[#0F2B3D] text-white rounded-2xl py-3 font-semibold">I Accept</button>
        </div>
    </div>
</div>

<!-- Privacy Modal -->
<div id="privacyModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-[#0F2B3D] to-[#1B4D6E] text-white">
            <h3 class="font-bold text-lg">Privacy Policy</h3>
            <button onclick="closePrivacyModal()" class="text-white text-2xl">&times;</button>
        </div>
        <div class="p-5 terms-content">
            <h4 class="font-bold text-gray-800 mt-2">Information We Collect</h4>
            <p class="text-sm text-gray-600 mb-3">We collect name, email, phone, property details, and transaction information.</p>
            
            <h4 class="font-bold text-gray-800 mt-3">How We Use Data</h4>
            <p class="text-sm text-gray-600 mb-3">To verify ownership, process bookings, facilitate payments, and improve services.</p>
            
            <h4 class="font-bold text-gray-800 mt-3">Data Protection</h4>
            <p class="text-sm text-gray-600 mb-3">We use encryption and secure servers. Your data is never sold to third parties.</p>
            
            <h4 class="font-bold text-gray-800 mt-3">Your Rights</h4>
            <p class="text-sm text-gray-600 mb-3">You can request data deletion or correction anytime by contacting support.</p>
        </div>
        <div class="p-4 bg-gray-50">
            <button onclick="closePrivacyModal()" class="w-full bg-[#0F2B3D] text-white rounded-2xl py-3 font-semibold">Got it</button>
        </div>
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
    
    // Toggle Password
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
    
    // Password Strength Checker
    function checkPasswordStrength(password) {
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        if(!password) {
            strengthBar.style.width = '0%';
            strengthBar.style.backgroundColor = '#F3F4F6';
            strengthText.innerHTML = '';
            return;
        }
        
        let strength = 0;
        if(password.length >= 6) strength++;
        if(password.length >= 8) strength++;
        if(/[A-Z]/.test(password) && /[a-z]/.test(password)) strength++;
        if(/[0-9]/.test(password)) strength++;
        if(/[^a-zA-Z0-9]/.test(password)) strength++;
        
        strength = Math.min(strength, 4);
        
        const widths = ['0%', '25%', '50%', '75%', '100%'];
        const colors = ['#F3F4F6', '#EF4444', '#F59E0B', '#10B981', '#059669'];
        const messages = ['', 'Weak', 'Fair', 'Good', 'Strong'];
        
        strengthBar.style.width = widths[strength];
        strengthBar.style.backgroundColor = colors[strength];
        strengthText.innerHTML = messages[strength] ? `${messages[strength]} Password` : '';
        strengthText.style.color = colors[strength];
    }
    
    // Password Match Check
    function checkPasswordMatch() {
        const password = document.getElementById('regPassword').value;
        const confirm = document.getElementById('regConfirmPassword').value;
        const matchText = document.getElementById('matchText');
        
        if(!confirm) {
            matchText.innerHTML = '';
            return;
        }
        
        if(password === confirm) {
            matchText.innerHTML = '✓ Passwords match';
            matchText.style.color = '#10B981';
        } else {
            matchText.innerHTML = '✗ Passwords do not match';
            matchText.style.color = '#EF4444';
        }
    }
    
    // Email Validation
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    // Handle Registration
    async function handleRegister(event) {
        event.preventDefault();
        
        const name = document.getElementById('regName').value.trim();
        const email = document.getElementById('regEmail').value.trim();
        const phone = document.getElementById('regPhone').value.trim();
        const password = document.getElementById('regPassword').value;
        const confirmPassword = document.getElementById('regConfirmPassword').value;
        const termsChecked = document.getElementById('termsCheckbox').checked;
        const registerBtn = document.getElementById('registerBtn');
        
        // Validations
        if(!name || !email || !phone || !password || !confirmPassword) {
            showToast('Please fill in all fields', 'error');
            return;
        }
        
        if(!isValidEmail(email)) {
            showToast('Please enter a valid email address', 'error');
            return;
        }
        
        if(phone.length < 10) {
            showToast('Please enter a valid phone number', 'error');
            return;
        }
        
        if(password.length < 6) {
            showToast('Password must be at least 6 characters', 'error');
            return;
        }
        
        if(password !== confirmPassword) {
            showToast('Passwords do not match', 'error');
            return;
        }
        
        if(!termsChecked) {
            showToast('Please accept the Terms & Conditions', 'error');
            return;
        }
        
        // Show loading state
        registerBtn.classList.add('loading');
        registerBtn.disabled = true;
        registerBtn.innerHTML = 'Creating Account...';
        
        const formData = new FormData();
        formData.append('action', 'owner_register');
        formData.append('name', name);
        formData.append('email', email);
        formData.append('phone', phone);
        formData.append('password', password);
        
        try {
            const response = await fetch('/api/owner-auth-handler', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            registerBtn.classList.remove('loading');
            registerBtn.disabled = false;
            registerBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i> Create Owner Account';
            
            if(data.success) {
                showToast('Registration successful! Redirecting to login...', 'success');
                setTimeout(() => {
                    window.location.href = '/pg-login';
                }, 1500);
            } else {
                showToast(data.message || 'Registration failed', 'error');
            }
        } catch(error) {
            registerBtn.classList.remove('loading');
            registerBtn.disabled = false;
            registerBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i> Create Owner Account';
            showToast('Connection error. Please try again.', 'error');
            console.error('Error:', error);
        }
    }
    
    // Modal Functions
    function openTermsModal() {
        document.getElementById('termsModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeTermsModal() {
        document.getElementById('termsModal').style.display = 'none';
        document.body.style.overflow = '';
    }
    
    function acceptTerms() {
        document.getElementById('termsCheckbox').checked = true;
        closeTermsModal();
        showToast('Terms accepted', 'success');
    }
    
    function openPrivacyModal() {
        document.getElementById('privacyModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closePrivacyModal() {
        document.getElementById('privacyModal').style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Close modals on outside click
    window.onclick = function(e) {
        const termsModal = document.getElementById('termsModal');
        const privacyModal = document.getElementById('privacyModal');
        if(e.target === termsModal) closeTermsModal();
        if(e.target === privacyModal) closePrivacyModal();
    };
</script>
</body>
</html>