<nav class="navbar">
    <div class="nav-content">
        <a href="index.php" class="logo">
            <div class="logo-icon">H</div>
            HealthCare
        </a>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <li><a href="diagnosis.php">Diagnosis</a></li>
            <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="registration.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<button class="hamburger-menu" type="button">
    <div class="hamburger-box">
        <div class="hamburger-inner"></div>
    </div>
</button>

<div class="mobile-nav">
    <button class="mobile-nav-close">
        <span>&times;</span>
    </button>
    <ul style="text-align: center">
        <li><a href="home.php">Home</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="diagnosis.php">Diagnosis</a></li>
        <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="registration.php">Register</a></li>
        <?php endif; ?>
    </ul>
</div>