<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "account_verification/session_token.php";
require_once "/../../../conn/db.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAV</title>

    <link rel="stylesheet" href="styles/nav.css">
</head>

<body >
    <div class="navbar" id="navdiv">
        <nav>
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="about.php">ABOUT</a></li>
                <?php
                if (isset($_COOKIE['checker']) && isset($_COOKIE['login'])
                && check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])): ?>
                <li><a href="watchlist.php">WATCHLIST</a></li>
                <li><a href="settings.php">SETTINGS</a></li>
                <li><a href="account_verification/logout.php">LOGOUT</a></li>
                <!-- <li>
                    <div id="container">
                        <div id="name"></div>
                    </div>
                </li> -->

                <?php else: ?>
                <li><a href="login.php">LOGIN</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</body>

</html>