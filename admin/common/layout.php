<?php
require_once __DIR__ . '/../config/constants.php';

function renderHeader($title = 'Dashboard') {
echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title} - Room Booking System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root{
            --bg: #f5f7fb;
            --card: #ffffff;
            --text: #1a2c3e;
            --muted: #6c757d;
            --line: #e9ecef;
            --primary: #ff6b35;
            --primary-light: #ff9f4a;
            --sidebar-start: #1a2c3e;
            --sidebar-end: #0f1e2c;
            --shadow-sm: 0 2px 10px rgba(0,0,0,0.05);
            --shadow-md: 0 10px 25px rgba(0,0,0,0.08);
            --radius: 16px;
            --sidebar-width: 240px;
            --sidebar-collapsed: 72px;
        }

        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body{
            width: 100%;
            overflow-x: hidden;
        }

        body{
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        a{
            text-decoration: none;
            color: inherit;
        }

        button,
        input,
        select,
        textarea{
            font: inherit;
        }

        img{
            display: block;
            max-width: 100%;
        }

        .app-wrapper{
            min-height: 100vh;
        }

        /* =========================
           SIDEBAR
        ========================= */
        .sidebar{
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-start) 0%, var(--sidebar-end) 100%);
            color: #fff;
            z-index: 1100;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width .3s ease, transform .3s ease;
        }

        .sidebar.collapsed{
            width: var(--sidebar-collapsed);
        }

        .sidebar.collapsed .logo-area span,
        .sidebar.collapsed .nav-item span,
        .sidebar.collapsed .logout-btn span{
            display: none;
        }

        .sidebar.collapsed .logo-area,
        .sidebar.collapsed .nav-item,
        .sidebar.collapsed .logout-btn{
            justify-content: center;
        }

        .sidebar.collapsed .sidebar-header{
            padding-left: 12px;
            padding-right: 12px;
        }

        .sidebar-header{
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .logo-area{
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .logo-area i{
            font-size: 22px;
            color: var(--primary-light);
            flex-shrink: 0;
        }

        .logo-area span{
            font-size: 18px;
            font-weight: 700;
            white-space: nowrap;
        }

        .menu-toggle,
        .mobile-close-btn,
        .mobile-menu-btn{
            border: none;
            outline: none;
            cursor: pointer;
            border-radius: 10px;
            transition: .25s ease;
        }

        .menu-toggle,
        .mobile-close-btn{
            width: 38px;
            height: 38px;
            background: rgba(255,255,255,0.1);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-toggle:hover,
        .mobile-close-btn:hover{
            background: rgba(255,255,255,0.18);
        }

        .mobile-close-btn{
            display: none;
        }

        .sidebar-nav{
            padding: 18px 0 90px;
        }

        .nav-item{
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 6px 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: rgba(255,255,255,0.82);
            transition: .25s ease;
        }

        .nav-item i{
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-item span{
            font-size: 14px;
            font-weight: 500;
            white-space: nowrap;
        }

        .nav-item:hover{
            color: #fff;
            background: rgba(255,255,255,0.1);
        }

        .nav-item.active{
            color: #fff;
            background: linear-gradient(90deg, var(--primary-light), var(--primary));
            box-shadow: 0 8px 20px rgba(255,107,53,0.25);
        }

        .sidebar-footer{
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.08);
            background: rgba(255,255,255,0.02);
        }

        .logout-btn{
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            color: rgba(255,255,255,0.82);
            background: rgba(255,255,255,0.06);
            transition: .25s ease;
        }

        .logout-btn:hover{
            color: #fff;
            background: rgba(244,67,54,0.20);
        }

        /* =========================
           MAIN LAYOUT
        ========================= */
        .main-content{
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            transition: margin-left .3s ease, width .3s ease;
        }

        .sidebar.collapsed + .sidebar-overlay + .main-content,
        .sidebar.collapsed ~ .main-content{
            margin-left: var(--sidebar-collapsed);
            width: calc(100% - var(--sidebar-collapsed));
        }

        .top-bar{
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 22px;
        }

        .page-title{
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .page-title h2{
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            white-space: nowrap;
        }

        .mobile-menu-btn{
            display: none;
            width: 40px;
            height: 40px;
            background: #eef2f6;
            color: var(--text);
            align-items: center;
            justify-content: center;
        }

        .mobile-menu-btn:hover{
            background: #e3e9ef;
        }

        .user-info{
            display: flex;
            align-items: center;
            gap: 16px;
            flex-shrink: 0;
        }

        .notification-icon{
            position: relative;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4b5563;
            cursor: pointer;
        }

        .badge{
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            border-radius: 999px;
            background: var(--primary);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-profile{
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .avatar{
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
        }

        .username{
            font-size: 14px;
            font-weight: 600;
            color: var(--text);
            white-space: nowrap;
        }

        .content-wrapper{
            padding: 24px 20px;
        }

        .sidebar-overlay{
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 1090;
        }

        .sidebar-overlay.active{
            display: block;
        }

        /* =========================
           COMMON UI HELPERS
        ========================= */
        .table-responsive{
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .status-badge{
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-badge.success{
            background: #d4edda;
            color: #155724;
        }

        .status-badge.warning{
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.danger{
            background: #f8d7da;
            color: #721c24;
        }

        .status-badge.info{
            background: #d1ecf1;
            color: #0c5460;
        }

        .btn-primary{
            border: none;
            cursor: pointer;
            padding: 10px 18px;
            border-radius: 10px;
            color: #fff;
            font-weight: 600;
            background: linear-gradient(90deg, var(--primary-light), var(--primary));
        }

        .btn-primary:hover{
            box-shadow: var(--shadow-md);
        }

        /* =========================
           TOAST
        ========================= */
        .toast-notification{
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 9999;
            min-width: 260px;
            max-width: 340px;
            background: #fff;
            color: var(--text);
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.16);
            padding: 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border-left: 4px solid var(--primary);
            animation: toastSlideIn .25s ease;
        }

        .toast-notification.success{
            border-left-color: #28a745;
        }

        .toast-notification.error{
            border-left-color: #dc3545;
        }

        .toast-notification i{
            margin-top: 2px;
        }

        @keyframes toastSlideIn{
            from{
                opacity: 0;
                transform: translateY(14px);
            }
            to{
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* =========================
           RESPONSIVE
        ========================= */
        @media (max-width: 768px){
            .sidebar{
                transform: translateX(-100%);
                width: 280px;
            }

            .sidebar.mobile-open{
                transform: translateX(0);
            }

            .main-content{
                margin-left: 0 !important;
                width: 100% !important;
            }

            .menu-toggle{
                display: none;
            }

            .mobile-close-btn{
                display: flex;
            }

            .mobile-menu-btn{
                display: flex;
            }

            .top-bar{
                padding: 14px 14px;
            }

            .content-wrapper{
                padding: 14px;
            }

            .username{
                display: none;
            }

            .user-info{
                gap: 10px;
            }
        }

        @media (min-width: 769px){
            .sidebar{
                transform: translateX(0) !important;
            }

            .sidebar-overlay{
                display: none !important;
            }

            .mobile-menu-btn,
            .mobile-close-btn{
                display: none !important;
            }
        }
    </style>
</head>
<body>
<div class="app-wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-area">
                <i class="fas fa-hotel"></i>
                <span>RoomBooking</span>
            </div>
            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Toggle sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <button class="mobile-close-btn" id="mobileCloseBtn" type="button" aria-label="Close sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
HTML;
}

function renderSidebarMenu($active = 'dashboard', $role = 'admin') {
    $menuItems = [];

    if ($role === 'admin') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-chart-line', '../admin/index.php'],
            ['rooms', 'Rooms', 'fa-door-open', '../admin/rooms.php'],
            ['bookings', 'Bookings', 'fa-calendar-check', '../admin/bookings.php'],
            ['staff', 'Staff', 'fa-users', '../admin/staff.php'],
            ['aadhar', 'Aadhar Verification', 'fa-id-card', '../admin/manage-aadhar.php'],
            ['coupons', 'Coupons', 'fa-ticket-alt', '../admin/coupons.php'],
            ['subscriptions', 'Subscriptions', 'fa-credit-card', '../admin/subscriptions.php'],
            ['settings', 'Settings', 'fa-cog', '../admin/settings.php'],
          
            // ['reports', 'Reports', 'fa-chart-bar', '../admin/reports.php']
        ];
    } elseif ($role === 'owner') {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-chart-line', '../owner/index.php'],
            ['add-room', 'Add Room', 'fa-plus-square', '../owner/add-room.php'],
            ['rules', 'Manage Rules', 'fa-shield-halved', '../owner/rules.php'],
            ['rooms', 'My Rooms', 'fa-door-open', '../owner/rooms.php'],
            ['bookings', 'Bookings', 'fa-calendar-check', '../owner/bookings.php'],
            ['earnings', 'Earnings', 'fa-coins', '../owner/earnings.php'],
            ['chat', 'Support Chat', 'fa-comments', '../owner/chat.php']
            
        ];
    } else {
        $menuItems = [
            ['dashboard', 'Dashboard', 'fa-chart-line', '../support/index.php'],
            ['chats', 'Chats', 'fa-comments', '../support/chats.php'],
            ['tickets', 'Tickets', 'fa-ticket-alt', '../support/tickets.php'],
            ['bookings', 'Bookings', 'fa-calendar-alt', '../support/bookings.php'],
            ['coupons', 'Coupons', 'fa-ticket-alt', '../support/coupons.php'],
            ['referrals', 'Referrals', 'fa-share-alt', '../support/referrals.php']
            
        ];
    }

    foreach ($menuItems as $item) {
        $activeClass = ($active === $item[0]) ? 'active' : '';
        echo <<<HTML
            <a href="{$item[3]}" class="nav-item {$activeClass}">
                <i class="fas {$item[2]}"></i>
                <span>{$item[1]}</span>
            </a>
HTML;
    }

    echo '</nav>';
}

function renderMainContentStart($pageTitle = 'Dashboard', $username = 'Admin') {
    $safeTitle = htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8');
    $safeUsername = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

echo <<<HTML
        <div class="sidebar-footer">
            <a href="../logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content" id="mainContent">
        <header class="top-bar">
            <div class="page-title">
                <button class="mobile-menu-btn" id="mobileMenuBtn" type="button" aria-label="Open sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h2>{$safeTitle}</h2>
            </div>

            <div class="user-info">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </div>
                <div class="user-profile">
                    <img src="../assets/img/avatar.png" alt="User" class="avatar">
                    <span class="username">{$safeUsername}</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
HTML;
}

function renderFooter($role = 'admin') {
    $roleJs = htmlspecialchars($role, ENT_QUOTES, 'UTF-8');

echo <<<HTML
        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
(function () {
    const body = document.body;
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const menuToggle = document.getElementById('menuToggle');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileCloseBtn = document.getElementById('mobileCloseBtn');
    const mobileBreakpoint = 768;

    function isMobile() {
        return window.innerWidth <= mobileBreakpoint;
    }

    function openMobileSidebar() {
        if (!sidebar || !overlay) return;
        sidebar.classList.add('mobile-open');
        overlay.classList.add('active');
        body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        if (!sidebar || !overlay) return;
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        body.style.overflow = '';
    }

    function applyDesktopState() {
        if (!sidebar) return;

        if (isMobile()) {
            sidebar.classList.remove('collapsed');
            return;
        }

        const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        sidebar.classList.toggle('collapsed', collapsed);
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            if (isMobile()) return;
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed') ? 'true' : 'false');
        });
    }

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', openMobileSidebar);
    }

    if (mobileCloseBtn) {
        mobileCloseBtn.addEventListener('click', closeMobileSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeMobileSidebar();
        }
    });

    window.addEventListener('resize', function () {
        if (!isMobile()) {
            closeMobileSidebar();
        }
        applyDesktopState();
    });

    applyDesktopState();
})();

async function apiGet(url) {
    try {
        const res = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        return await res.json();
    } catch (error) {
        console.error('API GET Error:', error);
        showToast('Network error', 'error');
        return { status: 'error', message: error.message };
    }
}

async function apiPost(url, formData) {
    try {
        const res = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        return await res.json();
    } catch (error) {
        console.error('API POST Error:', error);
        showToast('Network error', 'error');
        return { status: 'error', message: error.message };
    }
}

function showToast(message, type) {
    const toast = document.createElement('div');
    toast.className = 'toast-notification ' + (type || 'success');

    const iconClass = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
    const iconColor = type === 'error' ? '#dc3545' : '#28a745';

    toast.innerHTML =
        '<i class="fas ' + iconClass + '" style="color:' + iconColor + ';"></i>' +
        '<div style="font-size:14px; line-height:1.5;">' + String(message || '') + '</div>';

    document.body.appendChild(toast);

    setTimeout(function () {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all .25s ease';

        setTimeout(function () {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 250);
    }, 2800);
}
</script>
<script src="../assets/js/{$roleJs}.js"></script>
</body>
</html>
HTML;
}
?>