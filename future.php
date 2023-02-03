<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require_once("headtags.php") ?>
        <link rel="stylesheet" href="styles/watchlist.css">
        <title>Watchlist</title>
        <script type="text/javascript" src="switch.js"></script>
        <script type="text/javascript" src="menuScript.js" defer></script>
        <script src="https://kit.fontawesome.com/817fab420e.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php
            require_once "nav.php";
            require_once "../../../conn/db.php";
            require_once "retrieve_watchlist.php";

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
        <main id="mainID">
        <div class="content" id="contentID" style="background: rgb(108, 132, 140);">

            <div class="tabrow">
                <div class="tab">
                    <a><button class="tablinks" style="background-color: #B8DBD9; color: #2f4550;">Future <i class="fa-solid fa-clock"></i></button></a>
                    <a href="current.php"><button class="tablinks" href="current.php">Current <i class="fa-solid fa-eye"></i></button></a>
                    <a href="watched.php"><button class="tablinks" href="watched.php">Finished <i class="fa-solid fa-eye-slash"></i></button></a>
                </div>
            </div>

            <div class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">

                    <?php if (display_watchlist($conn, "future watching") == 1) {
                        ?>   <div class="banner">
                                <img src="streaming_img/watchlist.png">
                                <h3> Your watchlist is emtpy, please click the "icon" to add to your current watchlist. </h3>
                            </div>
                    <?php }?>
                </div>
            </div>
        </div>
        </main>

        <?php require_once("footer.php")?>
    </body>
</html>
