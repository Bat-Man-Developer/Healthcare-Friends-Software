<aside class="sidebar"><br><br><br>
    <div class="user-info">
        <div class="user-avatar">
            
        </div>
        <h3><?php echo $_SESSION['flduserfirstname'] . ' ' . $_SESSION['flduserlastname']; ?></h3>
        <p>Patient ID: <?php echo $_SESSION['flduserid']; ?></p>
    </div>
    <nav>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <i>📊</i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i>📋</i> Diagnosis History
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i>📝</i> Health Records
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i>⚙️</i> Settings
                </a>
            </li>
            <li class="nav-item">
                <a href="dashboard.php?logout=1" class="nav-link">
                    <i>🚪</i> Logout
                </a>
            </li>
        </ul>
    </nav>
</aside>