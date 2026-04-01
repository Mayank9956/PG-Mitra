<?php
require_once 'config/session.php';
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role_name'] ?? '';
    if ($role === 'Admin') header('Location: admin/index.php');
    elseif ($role === 'Owner') header('Location: owner/index.php');
    elseif ($role === 'Support') header('Location: support/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Room Booking System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Login Container */
        .login-container {
            width: 100%;
            max-width: 400px;
        }

        /* Card */
        .login-card {
            background: #fff;
            border-radius: 24px;
            padding: 40px 32px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        /* Logo */
        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 64px;
            height: 64px;
            background: #ff6b35;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .logo-icon i {
            font-size: 32px;
            color: white;
        }

        .logo-section h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1a2c3e;
            margin-bottom: 4px;
        }

        .logo-section p {
            color: #6c757d;
            font-size: 14px;
        }

        /* Form */
        .login-form {
            margin-top: 24px;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            font-size: 16px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 40px 12px 42px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
            outline: none;
        }

        .input-group input:focus {
            border-color: #ff6b35;
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
        }

        .input-group input.error {
            border-color: #f44336;
            background: #fff5f5;
        }

        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #adb5bd;
            font-size: 16px;
        }

        .password-toggle:hover {
            color: #ff6b35;
        }

        /* Options */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
            color: #495057;
        }

        .checkbox-label input {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .forgot-link {
            color: #ff6b35;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Button */
        .login-btn {
            width: 100%;
            padding: 12px;
            background: #ff6b35;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .login-btn:hover {
            background: #e55a2a;
        }

        .login-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Toast */
        .toast {
            position: fixed;
           top: 5px;
         background: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 3px solid;
            z-index: 1000;
            animation: slideIn 0.3s ease;
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

        .toast.success {
            border-left-color: #4caf50;
        }

        .toast.error {
            border-left-color: #f44336;
        }

        .toast i.success {
            color: #4caf50;
        }

        .toast i.error {
            color: #f44336;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .loading-spinner {
            background: white;
            padding: 20px 30px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .loading-spinner i {
            font-size: 24px;
            color: #ff6b35;
        }

 
        #toggleIcon{    left: -15px;}
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="fas fa-hotel"></i>
                </div>
                <h2>Welcome Back</h2>
                <p>Sign in to your account</p>
            </div>

            <form id="loginForm" class="login-form">
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email address" autocomplete="email">
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Password" autocomplete="current-password">
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </span>
                </div>

                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Remember me</span>
                    </label>
                    <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
                </div>

                <button type="submit" class="login-btn" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In</span>
                </button>
            </form>
        </div>
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-pulse"></i>
            <span>Logging in...</span>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const existingToasts = document.querySelectorAll('.toast');
            existingToasts.forEach(toast => toast.remove());
            
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            
            toast.innerHTML = `
                <i class="fas ${icon} ${type}"></i>
                <span>${message}</span>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.querySelector('input[name="email"]').value;
            const password = document.querySelector('input[name="password"]').value;
            
            if (!email || !password) {
                showToast('Please enter both email and password', 'error');
                return;
            }
            
            const loginBtn = document.getElementById('loginBtn');
            const loadingOverlay = document.getElementById('loadingOverlay');
            
            // Show loading
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<i class="fas fa-spinner fa-pulse"></i><span>Signing in...</span>';
            loadingOverlay.style.display = 'flex';
            
            try {
                const formData = new FormData(this);
                const remember = document.getElementById('remember').checked;
                formData.append('remember', remember);
                
                const res = await fetch('api/auth/login', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await res.json();
                
                // Reset button
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i><span>Sign In</span>';
                loadingOverlay.style.display = 'none';
                
                if (data.status === 'success') {
                    showToast(data.message || 'Login successful!', 'success');
                    
                    const role = data.data.user.role_name;
                    
                    setTimeout(() => {
                        if (role === 'Admin') {
                            window.location.href = 'admin/index.php';
                        } else if (role === 'Owner') {
                            window.location.href = 'owner/index.php';
                        } else if (role === 'Support') {
                            window.location.href = 'support/index.php';
                        } else {
                            window.location.reload();
                        }
                    }, 500);
                } else {
                    showToast(data.message || 'Invalid email or password', 'error');
                    
                    // Highlight error fields
                    const inputs = document.querySelectorAll('.input-group input');
                    inputs.forEach(input => {
                        input.classList.add('error');
                        setTimeout(() => input.classList.remove('error'), 2000);
                    });
                }
            } catch (error) {
                console.error('Login error:', error);
                loginBtn.disabled = false;
                loginBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i><span>Sign In</span>';
                loadingOverlay.style.display = 'none';
                showToast('Network error. Please try again.', 'error');
            }
        });
        
        // Remove error on focus
        document.querySelectorAll('.input-group input').forEach(input => {
            input.addEventListener('focus', function() {
                this.classList.remove('error');
            });
        });
        
        // Remember me functionality
        const savedEmail = localStorage.getItem('rememberedEmail');
        if (savedEmail) {
            document.querySelector('input[name="email"]').value = savedEmail;
            document.getElementById('remember').checked = true;
        }
        
        document.getElementById('loginForm').addEventListener('submit', function() {
            const remember = document.getElementById('remember').checked;
            const email = document.querySelector('input[name="email"]').value;
            
            if (remember) {
                localStorage.setItem('rememberedEmail', email);
            } else {
                localStorage.removeItem('rememberedEmail');
            }
        });
        
        // Enter key support
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('loginForm').dispatchEvent(new Event('submit'));
                }
            });
        });
    </script>
</body>
</html>