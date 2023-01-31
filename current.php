<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Watchlist</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="stylesheet" href="styles/homepage.css">
        <link rel="stylesheet" href="styles/nav.css">
        <link rel="stylesheet" href="styles/watchlist.css">
        <script type="text/javascript" src="switch.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <script src="https://kit.fontawesome.com/817fab420e.js" crossorigin="anonymous"></script>
    </head>
    <body>

        <!-- insert the nav bar -->
        <?php
            require_once "nav.php";
            require_once "../../../conn/db.php";
            require_once "retrieve_playlist.php";

            if(!isset($_SESSION)) { session_start(); }

            // Check if the user is logged in, if not redirect the user to the login page.
            if (isset($_COOKIE['login']) && isset($_COOKIE['checker'])) {
                if (!check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])) {
                    if (is_resource($conn)) { mysqli_close($conn); }
                    header("Location: login.php");
                    exit(0);
                }
            } else {
                if (is_resource($conn)) { mysqli_close($conn); }
                header("Location: login.php");
                exit(0);
            }

        ?>

        <div class="content" id="contentID" style="background: rgb(108, 132, 140);">

            <div class="tabrow">
                <div class="tab">
                    <a href="future.php"><button class="tablinks" href="future.php">Future Watching</button></a>
                    <a><button class="tablinks" style="background-color: #B8DBD9; color: black;">Currently Watching</button></a>
                    <a href="watched.php"><button class="tablinks" href="watched.php">Finished Watching</button></a>
                </div>
            </div>

            <div class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                    <?php display_playlist($conn, "currently watching"); ?>
                </div>
            </div>
        </div>

    </body>
</html>