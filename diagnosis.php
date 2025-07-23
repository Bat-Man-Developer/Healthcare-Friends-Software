<?php
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

//if user has already logged in then take user to account page
if(!isset($_SESSION['logged_in'])){
	header('location: login.php');
	exit;
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
    <title>HealthCare Friends</title>
    <link rel="icon" href="assets/images/FCS_Holdix_Logo.png" type="image/x-icon">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js" integrity="sha512-vJ3hR5OeYZ5dB6U5/3eBoTEfH9Nz+IQwFOk/7ixBHZY1T4cWlPOZ0QeYqziIFbUGA5g/Kjf/p9zrXr3D5K6JA==" crossorigin="anonymous"></script>
    <!-- Add SRI hashes for local scripts -->
    <script nonce="<?php echo htmlspecialchars(base64_encode(random_bytes(32))); ?>">
        // Add security measures for JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Sanitize all user inputs
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                input.addEventListener('input', function(e) {
                    this.value = DOMPurify.sanitize(this.value);
                });
            });

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

        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 6rem 1rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, var(--primary) 0%, transparent 70%);
            opacity: 0.1;
            animation: pulse 15s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .hero-content {
            max-width: 800px;
            z-index: 1;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: titleReveal 1s ease-out;
        }

        @keyframes titleReveal {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero p {
            font-size: 1.2rem;
            color: var(--dark);
            margin-bottom: 2rem;
            animation: fadeIn 1s ease-out 0.5s both;
        }

        .btn {
            display: inline-block;
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            margin: 0.5rem;
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
            background: white;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-secondary:hover {
            background: var(--primary);
            color: white;
        }

        /*-------- website message error / success --------*/
        #webmessage_red {
            background-color: red;
            font-weight: bold;
            text-align: center;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 2000;
            animation: slideIn 0.3s ease-out forwards, slideOut 0.3s ease-out forwards 5s;
        }

        #webmessage_green{
            background-color: green;
            font-weight: bold;
            text-align: center;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 2000;
            animation: slideIn 0.3s ease-out forwards, slideOut 0.3s ease-out forwards 5s;
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

        .features {
            padding: 5rem 1rem;
            background: white;
        }

        .features-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2.5rem;
            color: var(--dark);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 1rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-10px);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: var(--light);
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary);
        }

        .footer {
            background: linear-gradient(135deg, #2D3748 0%, #1A202C 100%);
            color: #fff;
            padding: 4rem 1rem 1rem;
            position: relative;
            margin-top: 4rem;
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
        }

        .footer-section h3 {
            color: var(--primary);
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .footer-section h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background: var(--primary);
        }

        .footer-section p {
            color: #A0AEC0;
            margin-bottom: 0.8rem;
            transition: color 0.3s ease;
        }

        .footer-section p:hover {
            color: #fff;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin-bottom: 0.8rem;
        }

        .footer-section ul li a {
            color: #A0AEC0;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            padding-left: 1.5rem;
        }

        .footer-section ul li a::before {
            content: '→';
            position: absolute;
            left: 0;
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--primary);
            padding-left: 1.8rem;
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

        @media (max-width: 768px) {
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

        /* mobile navigation */
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

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 2.5rem;
            }
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        .sidebar {
            background: white;
            padding: 2rem 1rem;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            height: 100vh;
            width: 250px;
            transition: all 0.3s ease;
            overflow-y: auto;
            z-index: 100;
        }

        .main-content {
            flex: 1;
            padding: 2rem 1rem;
            background: #F7FAFC;
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }

        /* Responsive styles */
        @media (max-width: 600px) {
            .sidebar {
                width: 200px;
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1.5rem;
            }
        }

        @media (max-width: 500px) {
            .sidebar {
                width: 180px;
            }
            
            .nav-link {
                padding: 0.6rem 0.8rem;
                font-size: 0.9rem;
            }
            
            .user-info {
                padding-bottom: 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .user-avatar {
                width: 60px;
                height: 60px;
                font-size: 1.2rem;
            }
        }

        @media (max-width: 400px) {
            .sidebar {
                width: 180px;
            }
            
            .nav-link {
                padding: 0.5rem 0.6rem;
                font-size: 0.85rem;
            }
            
            .user-info h3 {
                font-size: 1rem;
            }
            
            .user-info p {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 300px) {
            .sidebar {
                width: 180px;
            }
            
            .nav-link {
                padding: 0.4rem 0.5rem;
                font-size: 0.8rem;
            }
            
            .user-avatar {
                width: 50px;
                height: 50px;
                font-size: 1rem;
            }
        }

        @media (max-width: 250px) {
            .sidebar {
                width: 180px;
            }
            
            .nav-link {
                padding: 0.3rem 0.4rem;
                font-size: 0.75rem;
            }
            
            .user-info h3 {
                font-size: 0.9rem;
            }
            
            .user-info p {
                font-size: 0.7rem;
            }
        }

        /* Add toggle button for mobile */
        .sidebar-toggle {
            display: none;
            position: fixed;
            left: 1rem;
            top: 1rem;
            z-index: 1001;
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
        }

        @media (max-width: 600px) {
            .sidebar-toggle {
                display: block;
            }
        }

        .user-info {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #E2E8F0;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            background: var(--primary);
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
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

        .nav-link:hover, .nav-link.active {
            background: var(--primary);
            color: white;
        }

        .nav-link i {
            margin-right: 1rem;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.1rem;
            color: var(--dark);
            margin: 0;
        }

        .card-value {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary);
        }

        .chart-container {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .recent-activity {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #E2E8F0;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .activity-details {
            flex: 1;
        }

        .activity-time {
            color: #718096;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
        }

        /* Animation keyframes */
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

        .dashboard-card {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        .dashboard-card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dashboard-card:nth-child(3) {
            animation-delay: 0.4s;
        }

        .dashboard-card:nth-child(4) {
            animation-delay: 0.6s;
        }
        .diagnosis-form {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a365d;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .form-section {
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #2d3748;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            background-color: #fff;
            transition: border-color 0.15s ease-in-out;
        }

        .form-control:focus {
            outline: none;
            border-color: #4299e1;
            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
        }

        .symptoms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .symptom-checkbox, .condition-checkbox {
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.15s ease-in-out;
        }

        .symptom-checkbox:hover, .condition-checkbox:hover {
            background-color: #f7fafc;
        }


        .symptom-checkbox input[type="checkbox"] {
            margin-right: 0.5rem;
        }

        .severity-slider-container {
            padding: 1rem 0;
        }

        .severity-slider {
            width: 100%;
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            outline: none;
            -webkit-appearance: none;
        }

        .severity-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            background: #4299e1;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
        }

        .severity-slider::-webkit-slider-thumb:hover {
            background: #2b6cb0;
        }

        .submit-btn {
            background-color: #4299e1;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 600;
            transition: background-color 0.15s ease-in-out;
        }

        .submit-btn:hover {
            background-color: #2b6cb0;
        }

        .emergency-warning {
            border-color: #f56565;
        }

        @media (max-width: 768px) {
            .diagnosis-form {
                padding: 1rem;
            }
            
            .grid {
                grid-template-columns: 1fr !important;
            }
            
            .submit-btn {
                width: 100%;
            }
        }

        .diagnosis-results {
            display: none;
            margin-top: 2rem;
            padding: 1.5rem;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .diagnosis-results.active {
            display: block;
        }

        .result-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
            display: none;
        }

        .result-section.active {
            display: block;
            animation: fadeIn 0.5s ease-out;
        }

        .severity-container {
            margin: 1.5rem 0;
        }

        .severity-indicator {
            display: flex;
            align-items: center;
            margin: 1rem 0;
        }

        .severity-bar {
            flex: 1;
            height: 8px;
            background: #E2E8F0;
            border-radius: 4px;
            overflow: hidden;
            margin-right: 1rem;
        }

        .severity-fill {
            height: 100%;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .severity-text {
            margin-top: 0.5rem;
            font-weight: bold;
            text-align: center;
        }

        .recommendation-list {
            list-style: none;
            padding: 0;
        }

        .recommendation-item {
            display: flex;
            align-items: flex-start;
            padding: 1rem 0;
            border-bottom: 1px solid #E2E8F0;
        }

        .recommendation-item:last-child {
            border-bottom: none;
        }

        .recommendation-icon {
            width: 32px;
            height: 32px;
            background: var(--light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .hidden {
            display: none;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <button class="sidebar-toggle">☰</button>
    <!-- Navigation Bar -->
    <?php require_once 'layouts/navbar.php'; ?>

    <!--------- Website Message ------------>
    <?php if(isset($_GET['error'])){ ?>
        <p class="text-center" id="webmessage_red"><?php if(isset($_GET['error'])){ echo $_GET['error']; }?></p>
    <?php } ?>
    <?php if(isset($_GET['success'])){ ?>
        <p class="text-center" id="webmessage_green"><?php if(isset($_GET['success'])){ echo $_GET['success']; }?></p>
    <?php } ?>
	

    <div class="dashboard-container">
        <?php require_once 'layouts/sidebar.php'; ?>

        <main class="main-content">
            <h2 class="section-title"><br>New Diagnosis</h2>
            
            <form class="diagnosis-form" id="diagnosisForm">
                <!-- Emergency Warning -->
                <div class="emergency-warning bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">
                                If you are experiencing chest pain, severe breathing difficulties, or other life-threatening symptoms, 
                                please call emergency services (911) immediately or visit your nearest emergency room.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Patient Information -->
                <section class="form-section">
                    <h3 class="section-title">Patient Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="fullName">Full Legal Name</label>
                            <input type="text" id="fullName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="preferredName">Preferred Name (if different)</label>
                            <input type="text" id="preferredName" class="form-control">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="form-group">
                            <label for="dateOfBirth">Date of Birth</label>
                            <input type="date" id="dateOfBirth" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender Identity</label>
                            <select id="gender" class="form-control" required>
                                <option value="">Select gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="transgender_male">Transgender Male</option>
                                <option value="transgender_female">Transgender Female</option>
                                <option value="non_binary">Non-binary</option>
                                <option value="other">Other</option>
                                <option value="prefer_not_to_say">Prefer not to say</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pronouns">Preferred Pronouns</label>
                            <select id="pronouns" class="form-control">
                                <option value="">Select pronouns</option>
                                <option value="he_him">He/Him</option>
                                <option value="she_her">She/Her</option>
                                <option value="they_them">They/Them</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Home Address</label>
                        <input type="text" id="address" class="form-control" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="state">State/Province</label>
                            <input type="text" id="state" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="zipCode">ZIP/Postal Code</label>
                            <input type="text" id="zipCode" class="form-control" required>
                        </div>
                    </div>
                </section>

                <!-- Insurance Information -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Insurance Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="insuranceProvider">Insurance Provider</label>
                            <input type="text" id="insuranceProvider" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="policyNumber">Policy Number</label>
                            <input type="text" id="policyNumber" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="groupNumber">Group Number (if applicable)</label>
                        <input type="text" id="groupNumber" class="form-control">
                    </div>
                </section>

                <!-- Medical History -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Comprehensive Medical History</h3>
                    
                    <div class="form-group">
                        <label class="font-semibold">Existing Medical Conditions</label>
                        <div class="conditions-grid grid grid-cols-2 md:grid-cols-3 gap-4">
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="diabetes">
                                <span>Diabetes</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="hypertension">
                                <span>Hypertension</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="heart_disease">
                                <span>Heart Disease</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="asthma">
                                <span>Asthma</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="cancer">
                                <span>Cancer</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="thyroid">
                                <span>Thyroid Disease</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="arthritis">
                                <span>Arthritis</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="mental_health">
                                <span>Mental Health Condition</span>
                            </label>
                            <label class="condition-checkbox flex items-center space-x-2">
                                <input type="checkbox" name="conditions" value="autoimmune">
                                <span>Autoimmune Disease</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="form-group">
                            <label for="otherConditions">Other Medical Conditions</label>
                            <textarea id="otherConditions" class="form-control" rows="3" 
                                placeholder="Please list any other medical conditions not mentioned above"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="surgeries">Previous Surgeries</label>
                            <textarea id="surgeries" class="form-control" rows="3"
                                placeholder="List any previous surgeries with dates"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="allergies">Allergies</label>
                            <textarea id="allergies" class="form-control" rows="3"
                                placeholder="List all known allergies (medications, food, environmental)"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="medications">Current Medications</label>
                            <textarea id="medications" class="form-control" rows="3"
                                placeholder="List all current medications with dosages"></textarea>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label class="font-semibold">Family Medical History</label>
                        <div class="family-history-grid grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="familyHeartDisease">Heart Disease</label>
                                <select id="familyHeartDisease" class="form-control">
                                    <option value="none">None</option>
                                    <option value="parents">Parents</option>
                                    <option value="siblings">Siblings</option>
                                    <option value="grandparents">Grandparents</option>
                                </select>
                            </div>
                            <div>
                                <label for="familyDiabetes">Diabetes</label>
                                <select id="familyDiabetes" class="form-control">
                                    <option value="none">None</option>
                                    <option value="parents">Parents</option>
                                    <option value="siblings">Siblings</option>
                                    <option value="grandparents">Grandparents</option>
                                </select>
                            </div>
                            <div>
                                <label for="familyCancer">Cancer</label>
                                <select id="familyCancer" class="form-control">
                                    <option value="none">None</option>
                                    <option value="parents">Parents</option>
                                    <option value="siblings">Siblings</option>
                                    <option value="grandparents">Grandparents</option>
                                </select>
                            </div>
                            <div>
                                <label for="familyMentalHealth">Mental Health Conditions</label>
                                <select id="familyMentalHealth" class="form-control">
                                    <option value="none">None</option>
                                    <option value="parents">Parents</option>
                                    <option value="siblings">Siblings</option>
                                    <option value="grandparents">Grandparents</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Current Symptoms -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Current Symptoms Assessment</h3>
                    
                    <div class="form-group">
                        <label for="mainSymptom">Primary Complaint</label>
                        <input type="text" id="mainSymptom" class="form-control" required
                            placeholder="What is your main symptom or concern?">
                    </div>

                    <div class="form-group mt-4">
                        <label class="font-semibold">Associated Symptoms</label>
                        <div class="symptoms-grid grid grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- General Symptoms -->
                            <div class="symptom-category">
                                <h4 class="text-sm font-semibold mb-2">General</h4>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="fever">
                                    <span>Fever</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="fatigue">
                                    <span>Fatigue</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="weakness">
                                    <span>Weakness</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="weight_loss">
                                    <span>Weight Loss</span>
                                </label>
                            </div>

                            <!-- Pain Symptoms -->
                            <div class="symptom-category">
                                <h4 class="text-sm font-semibold mb-2">Pain</h4>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="headache">
                                    <span>Headache</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="chest_pain">
                                    <span>Chest Pain</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="abdominal_pain">
                                    <span>Abdominal Pain</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="joint_pain">
                                    <span>Joint Pain</span>
                                </label>
                            </div>

                            <!-- Respiratory Symptoms -->
                            <div class="symptom-category">
                                <h4 class="text-sm font-semibold mb-2">Respiratory</h4>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="cough">
                                    <span>Cough</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="shortness_breath">
                                    <span>Shortness of Breath</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="wheezing">
                                    <span>Wheezing</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="sputum">
                                    <span>Sputum Production</span>
                                </label>
                            </div>

                            <!-- Gastrointestinal Symptoms -->
                            <div class="symptom-category">
                                <h4 class="text-sm font-semibold mb-2">Gastrointestinal</h4>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="nausea">
                                    <span>Nausea</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="vomiting">
                                    <span>Vomiting</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="diarrhea">
                                    <span>Diarrhea</span>
                                </label>
                                <label class="symptom-checkbox flex items-center space-x-2">
                                    <input type="checkbox" name="symptoms" value="constipation">
                                    <span>Constipation</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="form-group">
                            <label for="symptomOnset">When did symptoms begin?</label>
                            <input type="date" id="symptomOnset" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="duration">Duration of Symptoms</label>
                            <select id="duration" class="form-control" required>
                                <option value="">Select duration</option>
                                <option value="1">Less than 24 hours</option>
                                <option value="2">1-3 days</option>
                                <option value="3">4-7 days</option>
                                <option value="4">More than a week</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <label for="severity">Severity Level</label>
                        <div class="severity-slider-container">
                            <input type="range" id="severity" min="1" max="10" class="severity-slider" required>
                            <div class="severity-labels flex justify-between text-sm">
                                <span>Mild</span>
                                <span>Moderate</span>
                                <span>Severe</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Lifestyle Factors -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Lifestyle Factors</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="form-group">
                            <label for="smoking">Smoking Status</label>
                            <select id="smoking" class="form-control">
                                <option value="never">Never Smoked</option>
                                <option value="former">Former Smoker</option>
                                <option value="current">Current Smoker</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="alcohol">Alcohol Consumption</label>
                            <select id="alcohol" class="form-control">
                                <option value="none">None</option>
                                <option value="occasional">Occasional</option>
                                <option value="moderate">Moderate</option>
                                <option value="heavy">Heavy</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exercise">Exercise Frequency</label>
                            <select id="exercise" class="form-control">
                                <option value="none">None</option>
                                <option value="occasional">1-2 times/week</option>
                                <option value="regular">3-4 times/week</option>
                                <option value="frequent">5+ times/week</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="diet">Dietary Habits</label>
                            <textarea id="diet" class="form-control" rows="3"
                                placeholder="Describe your typical daily diet and any dietary restrictions"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="stress">Stress Levels and Management</label>
                            <textarea id="stress" class="form-control" rows="3"
                                placeholder="Describe your current stress levels and how you manage stress"></textarea>
                        </div>
                    </div>
                </section>

                <!-- Additional Information -->
                <section class="form-section mt-8">
                    <h3 class="section-title">Additional Information</h3>
                    
                    <div class="form-group">
                        <label for="description">Detailed Symptom Description</label>
                        <textarea id="description" class="form-control" rows="4" 
                            placeholder="Please provide a detailed description of your symptoms, including any patterns, triggers, or relieving factors"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="treatments">Previous Treatments</label>
                        <textarea id="treatments" class="form-control" rows="3"
                            placeholder="List any treatments or medications you've tried for these symptoms"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="questions">Questions for Healthcare Provider</label>
                        <textarea id="questions" class="form-control" rows="3"
                            placeholder="List any specific questions you have for your healthcare provider"></textarea>
                    </div>
                </section>

                <!-- Consent and Submission -->
                <section class="form-section mt-8">
                    <div class="consent-box bg-gray-50 p-4 rounded-lg">
                        <label class="flex items-start space-x-2">
                            <input type="checkbox" required class="mt-1">
                            <span class="text-sm">
                                I confirm that the information provided is accurate to the best of my knowledge. I understand that this 
                                online assessment is not a substitute for professional medical advice, diagnosis, or treatment. In case 
                                of emergency, I will call emergency services or visit the nearest emergency room.
                            </span>
                        </label>
                    </div>

                    <button type="submit" class="submit-btn mt-6 w-full md:w-auto">
                        Get Initial Assessment
                    </button>
                </section>
            </form>

            <div class="results-container">
                <h2>Diagnosis Results</h2>
                
                <!-- Severity Indicator -->
                <div class="severity-container">
                    <h3>Severity Level</h3>
                    <div class="severity-bar">
                        <div id="severityFill" class="severity-fill"></div>
                    </div>
                    <div id="severityText" class="severity-text"></div>
                </div>

                <!-- Diagnosis Result -->
                <div id="diagnosisResult" class="diagnosis-details">
                    <!-- This will be populated by JavaScript -->
                </div>

                <!-- Recommendations -->
                <div class="recommendations">
                    <h3>Recommendations</h3>
                    <ul id="recommendationsList" class="recommendations-list">
                        <!-- This will be populated by JavaScript -->
                    </ul>
                </div>
            </div>
        </main>

        <script>
            const durationMap = {
                'hours': '1',
                'days_1_3': '2',
                'days_4_7': '3',
                'weeks_1_2': '4',
                'weeks_more': '4'
            };

            document.getElementById('diagnosisForm').addEventListener('submit', async (e) => {
                e.preventDefault();

                // Get all checked symptoms
                const checkedSymptoms = Array.from(document.querySelectorAll('input[name="symptoms"]:checked'))
                    .map(cb => cb.value);

                // Get risk factors
                const riskFactors = [];
                if (document.querySelector('input[name="conditions"][value="diabetes"]').checked) {
                    riskFactors.push('diabetes');
                }
                if (document.querySelector('input[name="conditions"][value="hypertension"]').checked) {
                    riskFactors.push('hypertension');
                }

                // Calculate age group based on date of birth
                const dob = document.getElementById('dateOfBirth').value;
                const age = calculateAgeGroup(dob);

                // Prepare the data object matching backend expectations
                const formData = {
                    mainSymptom: document.getElementById('mainSymptom').value.toLowerCase(),
                    symptoms: checkedSymptoms,
                    duration: document.getElementById('duration').value,
                    age: age,
                    riskFactors: riskFactors,
                    userId: '<?php echo isset($_SESSION["flduserid"]) ? $_SESSION["flduserid"] : ""; ?>'
                };

                try {
                    const response = await fetch('server/getdiagnosis.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    
                    if (result.error) {
                        throw new Error(result.message || 'An error occurred');
                    }

                    // Show results section
                    document.querySelector('.results-container').style.display = 'block';

                    // Update severity indicator
                    updateSeverityIndicator(result.severity);

                    // Update diagnosis result
                    updateDiagnosisResult(result);

                    // Update recommendations
                    updateRecommendations(result.recommendations);

                    // Show urgent care warning if needed
                    if (result.urgent) {
                        showUrgentCareWarning();
                    }

                    // Show differential diagnoses if available
                    if (result.differential_diagnoses) {
                        updateDifferentialDiagnoses(result.differential_diagnoses);
                    }

                } catch (error) {
                    console.error('Error:', error);
                    showError('An error occurred while processing your diagnosis: ' + error.message);
                }
            });

            // Helper functions
            function calculateAgeGroup(dateOfBirth) {
                const today = new Date();
                const birthDate = new Date(dateOfBirth);
                const age = today.getFullYear() - birthDate.getFullYear();
                
                if (age < 18) return 'child';
                if (age > 65) return 'elderly';
                return 'adult';
            }

            function updateSeverityIndicator(severity) {
                const severityFill = document.getElementById('severityFill');
                const severityText = document.getElementById('severityText');
                
                severityFill.style.width = `${severity}%`;
                severityText.textContent = getSeverityText(severity);
                
                // Update color based on severity
                if (severity > 70) {
                    severityFill.style.backgroundColor = '#ef4444'; // red
                } else if (severity > 40) {
                    severityFill.style.backgroundColor = '#f59e0b'; // yellow
                } else {
                    severityFill.style.backgroundColor = '#10b981'; // green
                }
            }

            function updateDiagnosisResult(result) {
                const diagnosisResult = document.getElementById('diagnosisResult');
                diagnosisResult.innerHTML = `
                    <div class="diagnosis-card p-4 bg-white rounded-lg shadow-md">
                        <h3 class="text-xl font-semibold mb-2">Primary Diagnosis</h3>
                        <p class="text-lg mb-2">${result.condition}</p>
                        <p class="text-gray-600">${result.description}</p>
                        <div class="mt-2">
                            <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                Confidence: ${result.confidence}%
                            </span>
                        </div>
                    </div>
                `;
            }

            function updateRecommendations(recommendations) {
                const recommendationsList = document.getElementById('recommendationsList');
                recommendationsList.innerHTML = recommendations.map(rec => `
                    <li class="recommendation-item">
                        <div class="recommendation-icon">💡</div>
                        <div class="recommendation-text">${rec}</div>
                    </li>
                `).join('');
            }

            function updateDifferentialDiagnoses(differentials) {
                const differentialSection = document.createElement('div');
                differentialSection.className = 'mt-4';
                differentialSection.innerHTML = `
                    <h3 class="text-lg font-semibold mb-2">Other Possible Conditions</h3>
                    <ul class="space-y-2">
                        ${differentials.map(diff => `
                            <li class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                <span>${diff.condition}</span>
                                <span class="text-sm text-gray-500">Confidence: ${diff.confidence}%</span>
                            </li>
                        `).join('')}
                    </ul>
                `;
                document.getElementById('diagnosisResult').appendChild(differentialSection);
            }

            function showUrgentCareWarning() {
                const warning = document.createElement('div');
                warning.className = 'bg-red-100 border-l-4 border-red-500 text-red-700 p-4 my-4';
                warning.innerHTML = `
                    <div class="flex items-center">
                        <div class="flex-shrink-0">⚠️</div>
                        <div class="ml-3">
                            <p class="font-bold">Urgent Care Recommended</p>
                            <p>Based on your symptoms, immediate medical attention is advised.</p>
                        </div>
                    </div>
                `;
                document.querySelector('.results-container').insertBefore(warning, document.querySelector('.severity-container'));
            }

            function showError(message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative';
                errorDiv.role = 'alert';
                errorDiv.innerHTML = `
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">${message}</span>
                `;
                document.querySelector('.diagnosis-form').prepend(errorDiv);
                setTimeout(() => errorDiv.remove(), 5000);
            }

            function getSeverityText(severity) {
                if (severity > 70) return 'Severe';
                if (severity > 40) return 'Moderate';
                return 'Mild';
            }

            function showResults() {
                const resultSection = document.getElementById('resultSection');
                resultSection.classList.remove('hidden');
                resultSection.classList.add('active');
                
                // Scroll to results
                resultSection.scrollIntoView({ behavior: 'smooth' });
            }

            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth > 600) {
                    sidebar.classList.remove('active');
                }
            });
        </script>

        <!-- Main JS -->
        <script src="js/main.js"></script>
    </div>
</body>
</html>