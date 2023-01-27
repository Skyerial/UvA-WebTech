<nav id="navID">
    <div class="nav-logo" id="logoID">
        <p><a href="index.php">wheretowatch.com</a></p>
    </div>
    <div class="navbar" id="sidebar">
        <div class="toggle-button" id="toggleButtonID">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul>
            <li><a href="index.php">HOME</a></li>
            <li><a href="about.php">ABOUT</a></li>
            <?php
            if(!isset($_SESSION)) { session_start(); }
            if (isset($_SESSION['login'])): ?>
            <li><a href="watchlist.php">WATCHLIST</a></li>
            <li><a href="settings.php">SETTINGS</a></li>
            <li><a href="logout.php">LOGOUT</a></li>
            <!-- <li>
                <div id="container">
                    <div id="name"></div>
                </div>
            </li> -->

            <?php else: ?>
            <li><a href="login.php">LOGIN</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
