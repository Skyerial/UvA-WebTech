<nav>
    <div class="navbar" id="navdiv">
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
