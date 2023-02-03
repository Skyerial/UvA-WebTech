<footer id="footerID">
    <div class="footer-content">
        <ul>
            <li><a href="../page_home/index.php">Home</a></li>
            <li><a href="../page_about/about.php">About</a></li>
            <li><a href="../page_about/about.php">Privacy</a></li>
            <?php
                if (isset($_COOKIE['checker']) && isset($_COOKIE['login'])
                && check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])): ?>
                <li><a href="../page_watchlist/future.php">Watchlist</a></li>
                <li><a href="../page_settings/settings.php">Settings</a></li>
                <li><a href="../account_verification/logout.php">Logout</a></li>
            <?php else: ?>
            <li><a href="../page_login/login.php">Login</a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="footer-bottom">
        <p>This website uses the TMDB API through the streaming availablity API, but is not endorsed or certified by TMDB</p>
        <p>Copyright &copy;2023 WebTech-IN01</p>
    </div>
</footer>
