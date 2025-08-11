<aside class="sidebar"><br><br><br>
    <div class="user-info">
        <div class="user-avatar">
            
        </div>
        <h3><?php if(isset($_SESSION['logged_in'])) {
                echo $_SESSION['flduserfirstname'] . ' ' . $_SESSION['flduserlastname']; 
            }else {
                echo 'Guest';
            } ?></h3>
        <p>Patient ID: <?php if(isset($_SESSION['logged_in'])) {
                echo $_SESSION['flduserid']; 
            }else {
                echo 'N/A';
            } ?></p>
    </div>
    <nav>
        <ul class="nav-menu">
            <?php if(isset($_SESSION['logged_in'])) { ?>
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link active">
                        <i>📊</i> Dashboard
                    </a>
                </li>
            <?php } ?>
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
            <?php if(isset($_SESSION['logged_in'])) { ?>
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
            <?php } ?>
        </ul>
    </nav>
</aside>