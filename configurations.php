<?php
// Configurations Page
// Start session with enhanced security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Set security headers
header("Content-Security-Policy: default-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com https://cdnjs.cloudflare.com; script-src 'self' https://cdnjs.cloudflare.com 'unsafe-inline'; style-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https://use.fontawesome.com https://stackpath.bootstrapcdn.com");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
//header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

include('server/getconfigurations.php');

// Get current configurations
$configs = [];
try {
    $result = $conn->query("SELECT config_key, config_value FROM configurations");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $configs[$row['config_key']] = $row['config_value'];
        }
    }
} catch (Exception $e) {
    error_log("Error fetching configurations: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <!-- Add CSRF token meta -->
    <meta name="csrf-token" content="<?php echo htmlspecialchars(hash_hmac('sha256', session_id(), 'Blackkarmaholyspirit.01234?')); ?>">
    <title>HealthCare Friends - Configurations</title>
    <link rel="icon" href="assets/images/FCS_Holdix_Logo.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js" integrity="sha512-vJ3hR5OeYZ5dB6U5/3eBoTEfH9Nz+IQwFOk/7ixBHZY1T4cWlPOZ0QeYqziIFbUGA5g/Kjf/p9zrXr3D5K6JA==" crossorigin="anonymous"></script>
    <!-- Add SRI hashes for local scripts -->
    <script nonce="<?php echo htmlspecialchars(base64_encode(random_bytes(32))); ?>">
        // Add security measures for JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Add CSRF token to all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
    <style>
        :root {
            --primary: #4CC9B0;
            --secondary: #6C63FF;
            --light: #F0F7FF;
            --dark: #2D3748;
            --success: #48BB78;
            --warning: #ED8936;
            --error: #E53E3E;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--dark);
            min-height: 100vh;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 5%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-icon {
            width: 35px;
            height: 35px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--dark);
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            color: var(--dark);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-link:hover, 
        .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .nav-link i {
            margin-right: 1rem;
        }

        .hamburger-menu {
            display: none;
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1002;
            background: white;
            border-radius: 50%;
            padding: 0.8rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .hamburger-menu {
                display: block;
            }
            
            .navbar {
                display: none;
            }
        }

        .hamburger-box {
            width: 30px;
            height: 24px;
            display: inline-block;
            position: relative;
        }

        .hamburger-inner {
            display: block;
            top: 50%;
            margin-top: -2px;
        }

        .hamburger-inner, .hamburger-inner::before, .hamburger-inner::after {
            width: 30px;
            height: 2px;
            background-color: var(--primary);
            border-radius: 4px;
            position: absolute;
            transition-property: transform;
            transition-duration: 0.15s;
            transition-timing-function: ease;
        }

        .hamburger-inner::before, .hamburger-inner::after {
            content: "";
            display: block;
        }

        .hamburger-inner::before {
            top: -10px;
        }

        .hamburger-inner::after {
            bottom: -10px;
        }

        /* Spin animation */
        .hamburger-menu.active .hamburger-inner {
            transform: rotate(225deg);
            transition-delay: 0.12s;
            transition-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
        }

        .hamburger-menu.active .hamburger-inner::before {
            top: 0;
            opacity: 0;
            transition: top 0.1s ease-out, opacity 0.1s 0.12s ease-out;
        }

        .hamburger-menu.active .hamburger-inner::after {
            bottom: 0;
            transform: rotate(-90deg);
            transition: bottom 0.1s ease-out, transform 0.22s 0.12s cubic-bezier(0.215, 0.61, 0.355, 1);
        }

        /* Mobile nav styles */
        .mobile-nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            padding-top: 5rem;
            z-index: 1001;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            display: block;
        }

        .mobile-nav.active {
            transform: translateX(0);
        }

        .mobile-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .mobile-nav ul li {
            opacity: 0;
            transform: translateX(-20px);
            transition: all 0.3s ease;
        }

        .mobile-nav.active ul li {
            opacity: 1;
            transform: translateX(0);
        }

        .mobile-nav.active ul li:nth-child(1) { transition-delay: 0.1s; }
        .mobile-nav.active ul li:nth-child(2) { transition-delay: 0.2s; }
        .mobile-nav.active ul li:nth-child(3) { transition-delay: 0.3s; }
        .mobile-nav.active ul li:nth-child(4) { transition-delay: 0.4s; }
        .mobile-nav.active ul li:nth-child(5) { transition-delay: 0.5s; }

        .mobile-nav ul li a {
            text-decoration: none;
            color: var(--dark);
            font-size: 1.2rem;
            display: block;
            padding: 1rem 2rem;
            transition: all 0.3s ease;
        }

        .mobile-nav ul li a:hover {
            background: rgba(76, 201, 176, 0.1);
            color: var(--primary);
            padding-left: 2.5rem;
        }

        .mobile-nav-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: none;
            border: none;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .mobile-nav-close:hover {
            background: rgba(76, 201, 176, 0.1);
        }

        .mobile-nav-close span {
            font-size: 2rem;
            color: var(--primary);
            line-height: 1;
        }

        @media (max-width: 768px) {
            .navbar{
                display: none;
            }

            .nav-links {
                display: none;
            }
        }

        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            background: var(--primary);
            border-radius: 50%;
            opacity: 0.2;
        }
        .main-content {
            margin-top: 100px;
            padding: 2rem;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-title {
            font-size: 2.5rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
        }

        .page-subtitle {
            font-size: 1.1rem;
            color: var(--dark);
            opacity: 0.8;
        }

        .config-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }

        .form-input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(76, 201, 176, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 50px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
            min-width: 150px;
            justify-content: center;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(76, 201, 176, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 201, 176, 0.4);
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
            box-shadow: 0 4px 15px rgba(108, 99, 255, 0.3);
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 99, 255, 0.4);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .loading {
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
        }

        @keyframes spin {
            to { transform: translateY(-50%) rotate(360deg); }
        }

        /*-------- website message error / success --------*/
        #webmessage_red {
            background-color: var(--error);
            font-weight: bold;
            text-align: center;
            position: fixed;
            top: 120px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 20px rgba(229, 62, 62, 0.3);
            z-index: 2000;
            animation: slideIn 0.3s ease-out forwards, slideOut 0.3s ease-out forwards 5s;
            max-width: 400px;
        }

        #webmessage_green{
            background-color: var(--success);
            font-weight: bold;
            text-align: center;
            position: fixed;
            top: 120px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 20px rgba(72, 187, 120, 0.3);
            z-index: 2000;
            animation: slideIn 0.3s ease-out forwards, slideOut 0.3s ease-out forwards 5s;
            max-width: 400px;
        }

        #webmessage_yellow {
            background-color: var(--warning);
            font-weight: bold;
            text-align: center;
            position: fixed;
            top: 120px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 20px rgba(237, 137, 54, 0.3);
            z-index: 2000;
            animation: slideIn 0.3s ease-out forwards, slideOut 0.3s ease-out forwards 5s;
            max-width: 400px;
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

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .config-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .test-result {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 10px;
            display: none;
        }

        .test-success {
            background: rgba(72, 187, 120, 0.1);
            border: 1px solid var(--success);
            color: var(--success);
        }

        .test-error {
            background: rgba(229, 62, 62, 0.1);
            border: 1px solid var(--error);
            color: var(--error);
        }

        .footer {
            background: linear-gradient(135deg, #2D3748 0%, #1A202C 100%);
            color: #fff;
            padding: 4rem 1rem 1rem;
            position: relative;
            margin-top: 4rem;
            text-align: center;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: -3px;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            padding-bottom: 2rem;
            justify-items: center;
        }

        .footer-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 300px;
        }

        .footer-section h3 {
            color: var(--primary);
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
            text-align: center;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: 0;
            width: 50px;
            height: 2px;
            background: var(--primary);
            transform: translateX(-50%);
        }

        .footer-section p {
            color: #A0AEC0;
            margin-bottom: 0.8rem;
            transition: color 0.3s ease;
            text-align: center;
        }

        .footer-section p:hover {
            color: #fff;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .footer-section ul li {
            margin-bottom: 0.8rem;
            width: 100%;
            text-align: center;
        }

        .footer-section ul li a {
            color: #A0AEC0;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            padding-left: 0;
            display: inline-block;
        }

        .footer-section ul li a::before {
            content: '→';
            position: absolute;
            left: -1.5rem;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--primary);
            transform: translateX(10px);
        }

        .footer-section ul li a:hover::before {
            opacity: 1;
            transform: translateX(0);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1.5rem;
            text-align: center;
            margin-top: 2rem;
        }

        .footer-bottom p {
            color: #718096;
            font-size: 0.9rem;
        }

        /* ===== RESPONSIVE DESIGN - MOBILE FIRST (1024px and below) ===== */
        @media (max-width: 1024px) {
            /* Navigation */
            .navbar {
                display: none;
            }
            
            .hamburger-menu {
                display: block;
            }
            
            .nav-links {
                display: none;
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            /* Footer */
            .footer {
                padding: 3rem 1rem 1rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .footer-section {
                text-align: center;
            }

            .footer-section h3::after {
                left: 50%;
                transform: translateX(-50%);
            }

            .footer-section ul li a {
                padding-left: 0;
            }

            .footer-section ul li a:hover {
                padding-left: 0.5rem;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                display: none !important;
            }
            
            .hamburger-menu {
                display: block !important;
            }
            
            .main-content {
                padding: 1rem;
                margin-top: 80px;
            }
            
            .config-container {
                padding: 1.5rem;
                margin: 1rem 0;
            }
            
            .config-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .button-group {
                flex-direction: column;
                gap: 0.8rem;
            }
            
            .btn {
                width: 100%;
                padding: 1rem;
                font-size: 1rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .page-subtitle {
                font-size: 1rem;
            }
            
            /* Message positioning for mobile */
            #webmessage_red,
            #webmessage_green,
            #webmessage_yellow {
                top: 80px;
                right: 10px;
                left: 10px;
                max-width: none;
                margin: 0 auto;
                font-size: 0.9rem;
            }

            .footer {
                padding: 3rem 1rem 1rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .footer-section {
                text-align: center;
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .hamburger-menu {
                top: 0.5rem;
                right: 0.5rem;
                padding: 10px;
            }
            
            .mobile-nav ul li a {
                font-size: 1.2rem;
                padding: 0.8rem 1.5rem;
            }

            .nav-link {
                padding: 0.6rem 0.8rem;
                font-size: 0.9rem;
            }
            
            .config-container {
                padding: 1rem;
                border-radius: 15px;
            }
            
            .form-input {
                padding: 0.8rem;
                font-size: 0.9rem;
            }
            
            .page-title {
                font-size: 1.8rem;
            }
            
            .mobile-nav-close {
                width: 45px;
                height: 45px;
            }
            
            .mobile-nav-close span {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 320px) {
            .nav-link {
                padding: 0.5rem 0.6rem;
                font-size: 0.8rem;
            }
            
            .main-content {
                padding: 0.5rem;
            }
            
            .config-container {
                padding: 0.8rem;
                margin: 0.5rem 0;
            }
            
            .page-title {
                font-size: 1.6rem;
            }
            
            .page-subtitle {
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 0.8rem;
                font-size: 0.9rem;
            }
        }

        /* Ensure desktop nav shows on larger screens */
        @media (min-width: 769px) {
            .navbar {
                display: block !important;
            }
            
            .hamburger-menu {
                display: none !important;
            }
            
            .mobile-nav {
                display: none !important;
            }
        }

        /* Add subtle animation for footer appearance */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .footer-section {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .footer-section:nth-child(2) {
            animation-delay: 0.2s;
        }

        .footer-bottom {
            animation: fadeInUp 0.6s ease-out 0.4s forwards;
            opacity: 0;
        }
    </style>
</head>
<body>
    <div class="floating-particles" id="particles"></div>
    <!-- Navigation Bar -->
    <?php require_once 'layouts/navbar.php'; ?>
    
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title">Model Configurations</h1>
            <p class="page-subtitle">Configure your AI model settings and API endpoints</p>
        </div>

        <div class="config-container">
            <form id="configForm">
                <div class="config-grid">
                    <div class="form-group">
                        <label for="project_id" class="form-label">Project ID</label>
                        <input type="text" id="project_id" name="project_id" class="form-input" 
                               value="<?php echo htmlspecialchars($configs['project_id'] ?? ''); ?>" 
                               placeholder="Enter your project ID">
                    </div>

                    <div class="form-group">
                        <label for="endpoint_url" class="form-label">Endpoint URL</label>
                        <input type="url" id="endpoint_url" name="endpoint_url" class="form-input" 
                               value="<?php echo htmlspecialchars($configs['endpoint_url'] ?? ''); ?>" 
                               placeholder="https://us-south.ml.cloud.ibm.com">
                    </div>

                    <div class="form-group">
                        <label for="api_key" class="form-label">API Key</label>
                        <input type="password" id="api_key" name="api_key" class="form-input" 
                               value="<?php echo htmlspecialchars($configs['api_key'] ?? ''); ?>" 
                               placeholder="Enter your API key">
                    </div>

                    <div class="form-group">
                        <label for="granite33_model" class="form-label">Granite 3.3 Model</label>
                        <input type="text" id="granite33_model" name="granite33_model" class="form-input" 
                               value="<?php echo htmlspecialchars($configs['granite33_model'] ?? ''); ?>" 
                               placeholder="ibm/granite-3-3-8b-instruct">
                    </div>

                    <div class="form-group">
                        <label for="granite40_model" class="form-label">Granite 4.0 Model</label>
                        <input type="text" id="granite40_model" name="granite40_model" class="form-input" 
                               value="<?php echo htmlspecialchars($configs['granite40_model'] ?? ''); ?>" 
                               placeholder="ibm/granite-4-0-tiny">
                    </div>

                    <div class="form-group">
                        <label for="iam_token" class="form-label">IAM Token</label>
                        <textarea id="iam_token" name="iam_token" class="form-input form-textarea" 
                                  placeholder="Enter your IAM token"><?php echo htmlspecialchars($configs['iam_token'] ?? ''); ?></textarea>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        💾 Save Configuration
                    </button>
                    <button type="button" class="btn btn-secondary" id="testBtn">
                        🔗 Test Connection
                    </button>
                </div>

                <div class="test-result" id="testResult"></div>
            </form>
        </div>
    </div>

    <!-- footer.php -->
    <?php require_once 'layouts/footer.php'; ?>

    <script src="js/configurations.js"></script>
    <script src="js/main.js"></script>
</body>
</html>