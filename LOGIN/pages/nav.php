<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAV</title>

    <link rel="stylesheet" href="../css/nav.css">
</head>

<body>
    <nav>
        <div class="nav-center-links">
            <ul>
                <li><a href="tab1.php"><b>Tab 1</b></a></li>
                <li><a href="index.php"><b>Home</b></a></li>
                <li><a href="tab2.php"><b>Tab 2</b></a></li>
            </ul>
        </div>

        <?php
        if(!isset($_SESSION)) { session_start(); }
        if (isset($_SESSION['login'])): ?>
        <div class="nav-register-links">
            <ul>
                <li><a href="logout.php"><b>Logout</b></a></li>
            </ul>
        </div>

        <?php else: ?>
        <div class="nav-register-links">
            <ul>
                <li><a href="login.php"><b>Login</b></a></li>
                <li><a href="registration.php"><b>Register</b></a></li>
            </ul>
        </div>

        <?php endif; ?>

        <button onclick="switch_mode()">
            <img src="" alt="M" id="logo" onclick="change_logo()">
        </button>
    </nav>

    <form action="" class="search-bar">
        <input type="text" placeholder="search a title" name="name">
        <button type="submit"></button>
    </form>

    <script src="../js/nav.js"></script>
</body>

</html>