<?php
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - StayEase</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body{
            margin:0;
            font-family: Arial, sans-serif;
            background:#f4f6fb;
            display:flex;
            align-items:center;
            justify-content:center;
            min-height:100vh;
            padding:20px;
        }
        .card{
            width:100%;
            max-width:420px;
            background:#fff;
            border-radius:8px;
            box-shadow:0 20px 50px rgba(0,0,0,0.08);
            overflow:hidden;
        }
        .head{
            background:linear-gradient(145deg,#2563eb,#7c3aed);
            color:#fff;
            padding:28px 24px;
            text-align:center;
        }
        .body{
            padding:24px;
        }
        .input-wrap{
            position:relative;
            margin-bottom:16px;
        }
        .input{
            width:100%;
            padding:14px 46px 14px 16px;
            border:2px solid #e5e7eb;
            border-radius:8px;
            font-size:15px;
            box-sizing:border-box;
        }
        .input:focus{
            outline:none;
            border-color:#2563eb;
        }
        .toggle-password{
            position:absolute;
            right:14px;
            top:50%;
            transform:translateY(-50%);
            color:#6b7280;
            cursor:pointer;
            font-size:16px;
            user-select:none;
        }
        .btn{
            width:100%;
            border:none;
            padding:14px;
            border-radius:8px;
            color:#fff;
            font-size:15px;
            font-weight:bold;
            cursor:pointer;
            background:linear-gradient(145deg,#2563eb,#7c3aed);
        }
        .msg{
            margin-bottom:14px;
            font-size:14px;
            text-align:center;
        }
        .success{ color:#059669; }
        .error{ color:#dc2626; }
    </style>
</head>
<body>
    <div class="card">
        <div class="head">
            <h2 style="margin:0;">Reset Password</h2>
            <p style="margin:8px 0 0;opacity:.95;">Choose a new password for your account</p>
        </div>
        <div class="body">
            <div id="message" class="msg"></div>

            <div class="input-wrap">
                <input type="password" id="password" class="input" placeholder="New Password">
                <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
            </div>

            <div class="input-wrap">
                <input type="password" id="confirm_password" class="input" placeholder="Confirm New Password">
                <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password', this)"></i>
            </div>

            <button class="btn" onclick="resetPassword()">Reset Password</button>
        </div>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        function resetPassword() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const message = document.getElementById('message');

            if (!password || !confirmPassword) {
                message.className = 'msg error';
                message.textContent = 'Please fill in all fields';
                return;
            }

            const formData = new FormData();
            formData.append('action', 'reset_password');
            formData.append('email', <?php echo json_encode($email); ?>);
            formData.append('token', <?php echo json_encode($token); ?>);
            formData.append('password', password);
            formData.append('confirm_password', confirmPassword);

            fetch('/api/auth-handler', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    message.className = 'msg success';
                    message.textContent = data.message;
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 1500);
                } else {
                    message.className = 'msg error';
                    message.textContent = data.message;
                }
            })
            .catch(() => {
                message.className = 'msg error';
                message.textContent = 'Something went wrong. Please try again.';
            });
        }
    </script>
</body>
</html>