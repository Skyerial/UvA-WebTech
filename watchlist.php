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
        <script type="text/javascript" src="scripts.js"></script>
        <script type="text/javascript" src="watchlist.js" defer></script>
        <script type="text/javascript" src="switch.js"></script>
        <script type="text/javascript" src="menuScript.js" defer></script>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    </head>
    <body onload="showFooter()">

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

            //Show playlist:
            function display_playlist($conn, $playlist) {
                try {
                    //echo($conn);
                    retrieve_playlist($conn, $_COOKIE['checker'], $playlist);
                    global $displayed_cards;
                    $_SESSION['displayed_cards'] = $displayed_cards;
                    //var_dump($displayed_cards);
                    //echo json_encode($displayed_cards);
                } catch (Exception $err) {
                    $err_file = fopen(ERROR_LOG_FILE, "a");
                    fwrite($err_file, $err->getMessage() . "\n");
                    fclose($err_file);
                }
            }

        ?>
        <main id="mainID">
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
                    <?php display_playlist($conn, "future watching"); ?>
                </div>
            </div>

            <div id="Currently Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                <?php display_playlist($conn, "currently watching"); ?>
                </div>
            </div>

            <div id="Finished Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                    <?php display_playlist($conn, "finished watching"); ?>
                </div>
            </div>

        </div>
        </main>

        <?php require_once("footer.php")?>
    </body>
</html>

