<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAV</title>

    <link rel="stylesheet" href="style.css">
</head>

<body >
    <div class="navbar" id="navdiv">
        <nav>
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
        </nav>
    </div>
</body>

</html>