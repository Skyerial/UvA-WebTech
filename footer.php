<footer id="footerID">
    <div class="footer-content">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <?php
                if (isset($_COOKIE['checker']) && isset($_COOKIE['login'])
                && check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])): ?>
                <li><a href="watchlist.php">Watchlist</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="account_verification/logout.php">Logout</a></li>
            <!-- <li>
                <div id="container">
                    <div id="name"></div>
                </div>
            </li> -->

            <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="footer-bottom">
        <p>This website uses the TMDB API through the streaming availablity API, but is not endorsed or certified by TMDB</p>
        <p>Copyright &copy;2023 WebTech-IN01</p>
    </div>
</footer>
