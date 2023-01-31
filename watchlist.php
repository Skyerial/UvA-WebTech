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
        <script src="https://kit.fontawesome.com/817fab420e.js" crossorigin="anonymous"></script>
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

        <main>
            <div class="content" id="contentID" style="background: rgb(108, 132, 140);">

                <div class="tabrow">
                    <div class="tab">
                        <button class="tablinks" onclick="openWatch(event, 'Future Watching')">Future <i class="fa-solid fa-clock"></i></button>
                        <button class="tablinks" onclick="openWatch(event, 'Currently Watching')" id="defaultOpen">Current <i class="fa-solid fa-eye"></i></button>
                        <button class="tablinks" onclick="openWatch(event, 'Finished Watching')">Finished <i class="fa-solid fa-eye-slash"></i></button>
                    </div>
                </div>

                <div id="Future Watching" class ="tabcontent">
                    <div class="cardcontainer" id="cardcontainerID">
                    <div class="banner">
                        <img src="streaming_img/watchlist.png">
                        <h3> Your current watchlist is emtpy, please click the "icon" to add to your current watchlist. </h3>
                    </div>
                        <!-- <?php //retrieve_playlist($conn, $user_id, "future watching"); ?> -->
                    </div>
                </div>

                <div id="Currently Watching" class ="tabcontent">
                    <div class="cardcontainer" id="cardcontainerID">
                        <div class="card" id="card3" style="opacity: 1 !important;">
                            <div class="imagebox">
                                <img class="poster" src="https://image.tmdb.org/t/p/w500/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg">
                                <div class="streamingservicebox">
                                    <a href="https://www.primevideo.com/detail/0OSFD03ISY4N0L55EX9B39PBC8/ref=atv_dp" target="_blank" class="streamingservice">
                                        <img src="streaming_img/prime.png">
                                    </a>
                                    <a href="https://www.disneyplus.com/movies/avatar/2YOnkRN4LwZZ" target="_blank" class="streamingservice">
                                        <img src="streaming_img/disney.png">
                                    </a>
                                </div>
                            </div>
                            <div>
                                <h3>Upsim lipsum blablabla carpe diem enzo pecunia non olet<span class="tooltiptext">Upsim lipsum blablabla carpe diem enzo pecunia non olet</span></h3>
                            </div>
                            <div class="hover-content">
                                <a href="javascript:void(0)" onclick="to_watch(3); return false;" class="cardbutton"><i class="fa-solid fa-clock"></i><span class="tooltiptext">Future Watching</span></a>
                                <a href="javascript:void(0)" onclick="watched(3); return false;" class="cardbutton"><i class="fa-solid fa-eye-slash"></i><span class="tooltiptext">Finished Watching</span></a>
                                <a href="javascript:void(0)" onclick="delete_item('3' , 'currently watching'); return false;" class="cardbutton"><i style = "color: red;" class="fa-solid fa-trash"></i><span class="tooltiptext">Delete</span></a>
                            </div>
                        </div>
                        <!-- <?php //retrieve_playlist($conn, $user_id, "currently watching"); ?> -->
                    </div>
                </div>

                <div id="Finished Watching" class ="tabcontent">
                    <div class="cardcontainer" id="cardcontainerID">
                        <!-- <?php //retrieve_playlist($conn, $user_id, "finished watching"); ?> -->
                    </div>
                </div>

            </div>
        </main>

        <?php require_once("footer.php"); ?>

    </body>
</html>