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
if(isset($_SESSION['logged_in'])){
	header('location: dashboard.php');
	exit;
}

if($_GET['bool'] == true){
	session_destroy();
    unset($_POST);
    unset($_GET);
}

include("server/getregistration.php");
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
        
        .register-container {
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(1rem, 5vw, 8rem);
            background: linear-gradient(135deg, #F0F7FF 0%, #E8F0FE 100%);
        }

        .register-card {
            background: white;
            padding: clamp(1.5rem, 4vw, 3rem);
            border-radius: clamp(12px, 2vw, 20px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: min(90%, 450px);
            height: auto;
            position: relative;
            overflow: hidden;
            animation: slideIn 0.5s ease-out;
            margin-top: 2%;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 200px), 1fr));
            gap: clamp(0.5rem, 2vw, 1rem);
        }

        .form-group {
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            margin-bottom: clamp(0.3rem, 1vw, 0.5rem);
            color: var(--dark);
            font-size: clamp(0.875rem, 1.5vw, 1rem);
        }

        .form-input {
            width: 100%;
            padding: clamp(0.6rem, 1.5vw, 0.8rem);
            border: 2px solid #E2E8F0;
            border-radius: clamp(8px, 1.5vw, 10px);
            transition: border-color 0.3s ease;
            outline: none;
            font-size: clamp(0.875rem, 1.5vw, 1rem);
        }

        .form-input:focus {
            border-color: var(--primary);
        }

        .password-strength {
            height: 5px;
            background: #E2E8F0;
            border-radius: 5px;
            margin-top: clamp(0.3rem, 1vw, 0.5rem);
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .form-button {
            width: 100%;
            padding: clamp(0.8rem, 2vw, 1rem);
            background: var(--primary);
            color: white;
            border: none;
            border-radius: clamp(8px, 1.5vw, 10px);
            cursor: pointer;
            font-size: clamp(0.875rem, 1.5vw, 1rem);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .form-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 201, 176, 0.3);
        }

        .form-footer {
            text-align: center;
            margin-top: clamp(1rem, 2vw, 1.5rem);
            font-size: clamp(0.875rem, 1.5vw, 1rem);
        }

        .form-footer a {
            color: var(--primary);
            text-decoration: none;
        }

        .error-message {
            background: #FED7D7;
            color: #C53030;
            padding: clamp(0.6rem, 1.5vw, 0.8rem);
            border-radius: clamp(8px, 1.5vw, 10px);
            margin-bottom: clamp(0.8rem, 1.5vw, 1rem);
            display: none;
            font-size: clamp(0.875rem, 1.5vw, 1rem);
        }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <?php require_once 'layouts/navbar.php'; ?>

    <!--------- Website Message ------------>
    <?php if(isset($_GET['error'])){ ?>
        <p class="text-center" id="webmessage_red"><?php if(isset($_GET['error'])){ echo $_GET['error']; }?></p>
    <?php } ?>
    <?php if(isset($_GET['success'])){ ?>
        <p class="text-center" id="webmessage_green"><?php if(isset($_GET['success'])){ echo $_GET['success']; }?></p>
    <?php } ?>
				

    <div class="register-container">
        <div class="register-card">
            <h2 style="text-align: center; margin-bottom: 2rem;">Create Account</h2>
            <form id="register-form" action="registration.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="registrationfirstname">First Name</label>
                        <input type="text" id="firstName" name="flduserfirstname" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="registrationlastname">Last Name</label>
                        <input type="text" id="lastname" name="flduserlastname" class="form-input" required>
                    </div>
                </div>
                <div class="form-group  full-width">
                    <label for="registrationcountry">Country
                        <select class="form-input" id="country" name="fldusercountry" size="1" value="" required>
                            <option value="">Select Country...</option>
                        </select>
                    </label>
                </div><br>
                <div class="form-group  full-width">
                    <label for="registrationzone">Province
                        <select class="form-input" id="zone" name="flduserzone" size="1" value="" required>
                            <option value="">Select Province...</option>
                        </select>
                    </label>
                </div><br>
                <div class="form-group  full-width">
                    <label for="registrationcity">City
                        <select class="form-input" id="city" name="fldusercity" size="1" value="" required>
                            <option value="">Select City...</option>
                        </select>
                    </label>
                </div><br>
                <div class="form-group  full-width">
                    <label for="registrationlocalarea">Local Area
                        <input type="text" class="form-input" id="registrationlocalarea" name="flduserlocalarea" placeholder="Local Area"/>
                    </label>
                </div>
                <div class="form-group  full-width">
                    <label for="registrationstreetaddress">Street Address
                        <input type="text" class="form-input" id="registrationstreetaddress" name="flduserstreetaddress" placeholder="Street Address" required/>
                    </label>
                </div>
                <div class="form-group  full-width">
                    <label for="registrationpostalcode">Postal Code
                        <input type="number" class="form-input" id="registrationpostalcode" name="flduserpostalcode" placeholder="Postalcode" required/>
                    </label>
                </div>
                <div class="form-group  full-width">
                    <label for="registrationemail">Email
                        <input type="email" class="form-input" id="registrationemail" name="flduseremail" placeholder="Email" required/>
                    </label>
                </div>
                <div class="form-group  full-width">
                    <label for="registrationphonenumber">Phone Number
                        <input type="number" class="form-input" id="registrationphonenumber" name="flduserphonenumber" placeholder="Phone Number" required/>
                    </label>
                </div>
                <div class="form-group full-width">
                    <label for="registrationpassword">Password</label>
                    <input type="password" id="password" name="flduserpassword" class="form-input" required>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrength"></div>
                    </div>
                </div>
                <div class="form-group full-width">
                    <label for="registrationconfirmpassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="flduserconfirmpassword" class="form-input" required>
                </div>
                <button type="submit" name="registrationBtn" class="form-button">Create Account</button>
                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('register-form');
        const errorMessage = document.getElementById('error-message');
        const passwordInput = document.getElementById('password');
        const passwordStrength = document.getElementById('passwordStrength');

        passwordInput.addEventListener('input', updatePasswordStrength);

        function updatePasswordStrength() {
            const password = passwordInput.value;
            let strength = 0;

            if (password.length >= 8) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 25;
            if (password.match(/[^A-Za-z0-9]/)) strength += 25;

            passwordStrength.style.width = `${strength}%`;
            
            if (strength < 50) {
                passwordStrength.style.backgroundColor = '#FC8181';
            } else if (strength < 75) {
                passwordStrength.style.backgroundColor = '#F6E05E';
            } else {
                passwordStrength.style.backgroundColor = '#68D391';
            }
        }

        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
            
            // Shake animation
            errorMessage.style.animation = 'none';
            errorMessage.offsetHeight; // Trigger reflow
            errorMessage.style.animation = 'shake 0.5s ease-in-out';
        }
    </script>

    <script src="js/addressbook.js"></script>
    
    <!-- Main JS -->
    <script src="js/main.js"></script>
</body>
</html>