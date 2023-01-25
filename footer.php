<footer>
    <div class="footer-content">
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About</a></li>
            <?php
            if(!isset($_SESSION)) { session_start(); }
            if (isset($_SESSION['login'])): ?>
            <li><a href="watchlist.php">Watchlist</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
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