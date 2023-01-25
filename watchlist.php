<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Watchlist</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>TITEL</title>
        <link rel="stylesheet" href="styles/homepage.css">
        <link rel="stylesheet" href="styles/nav.css">
        <link rel="stylesheet" href="styles/watchlist.css">
        <script type="text/javascript" src="scripts.js"></script>
        <script type="text/javascript" src="watchlist.js" defer></script>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    </head>
    <body>

        <!-- insert the nav bar -->
        <?php
            require_once("nav.php");
            require_once("retrieve_playlist.php");
            require_once "/../../../conn/db.php";

            if(!isset($_SESSION)) { session_start(); }

            // Check if the user is logged in, if not exit with an error message:
            if (!(isset($_SESSION['login']))) {
                if (is_resource($conn)) {
                    mysqli_close($conn);
                }
                exit("You are not logged in.");
            }

            // Retrieve the user id:
            $user_id = retrieve_uid($conn, $_SESSION['login']);
            if (!$user_id) { exit("No user found."); }

            // This variable holds the name of the playlist, you must retrieve it somehow.
            $playlist = "finished watching";


        ?>

        <div class="content" id="contentID" style="background: rgb(108, 132, 140);">

            <div class="tabrow">
                <div class="tab">
                    <button class="tablinks" onclick="openWatch(event, 'Future Watching')" id="defaultOpen">Future Watching</button>
                    <button class="tablinks" onclick="openWatch(event, 'Currently Watching')">Currently Watching</button>
                    <button class="tablinks" onclick="openWatch(event, 'Finished Watching')">Finished Watching</button>
                </div>
            </div>

            <div id="Future Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                <div class="banner">
                    <img src="streaming_img/watchlist.png">
                    <h3> Your current watchlist is emtpy, please click the "icon" to add to your current watchlist. <h3>
                </div>
                    <?php retrieve_playlist($conn, $user_id, "future watching"); ?>
                </div>
            </div>

            <div id="Currently Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                <?php retrieve_playlist($conn, $user_id, "currently watching"); ?>
                </div>
            </div>

            <div id="Finished Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                    <?php retrieve_playlist($conn, $user_id, "finished watching"); ?>
                </div>
            </div>

        </div>

    </body>
</html>