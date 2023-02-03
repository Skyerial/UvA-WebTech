<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "../account_verification/session_token.php";
require_once "../../../../conn/db.php";

?>

<nav id="navID">
    <div class="nav-logo" id="logoID">
        <p><a href="../page_home/index.php">wheretowatch.com</a></p>
    </div>
    <div class="navbar" id="sidebar">
        <div class="toggle-button" id="toggleButtonID">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <ul class="mobile-menu">
            <li class="home-mobile"><a href="../page_home/index.php">HOME</a></li>
            <li><a href="about.php">ABOUT</a></li>
            <?php
                if (isset($_COOKIE['checker']) && isset($_COOKIE['login'])
                && check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])): ?>
                <li><a href="../page_watchlist/future.php">WATCHLIST</a></li>
                <li><a href="../page_settings/settings.php">SETTINGS</a></li>
                <li><a href="../account_verification/logout.php">LOGOUT</a></li>
            <?php else: ?>
            <li><a href="../page_login/login.php">LOGIN</a></li>
            <?php endif; ?>
        </ul>
        <ul class="computer-menu">
            <li><a href="../page_home/index.php">HOME</a></li>
            <li><a href="../page_about/about.php">ABOUT</a></li>
            <?php
                if (isset($_COOKIE['checker']) && isset($_COOKIE['login'])
                && check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])): ?>
                <li><a href="../page_watchlist/future.php">WATCHLIST</a></li>
                <li><a href="../page_settings/settings.php">SETTINGS</a></li>
                <li><a href="../account_verification/logout.php">LOGOUT</a></li>
            <?php else: ?>
            <li><a href="../page_login/login.php">LOGIN</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
