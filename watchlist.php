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
        <?php require_once("nav.php")?>

        <div class="content" id="contentID" style="background: rgb(108, 132, 140);">

            <div class="searchrow" id="formID">
                <form class="search-bar" name="search" action="#" onsubmit="searchbutton();return false">
                    <input type="text" autocomplete="off" placeholder="search a title" name="name" id="textbar">
                    <button type="submit" id="buttonID"></button>
                </form>
            </div>

            <div class="tabrow">
                <div class="tab">
                    <button class="tablinks" onclick="openWatch(event, 'Future Watching')" id="defaultOpen">Future Watching</button>
                    <button class="tablinks" onclick="openWatch(event, 'Currently Watching')">Currently Watching</button>
                    <button class="tablinks" onclick="openWatch(event, 'Finished Watching')">Finished Watching</button>
                </div>
            </div>

            <div id="Future Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                    <?php
                        $movieTitle = "Cars";
                        $moviePoster = "https://image.tmdb.org/t/p/original/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg";
                        $movieService = "streaming_img/netflix.png";
                        for ($i = 0; $i < 10; $i++) {
                    ?>
                    <!-- -------------- -->
                    <div class="card" id="card">
                        <div class="imagebox">
                            <img class="poster" src="<?=$moviePoster?>"/>
                            <div class="streamingservicebox">
                                <div class="streamingservice">
                                    <img src="<?=$movieService?>">
                                </div>
                                <div class="streamingservice">
                                    <img src="<?=$movieService?>">
                                </div>
                                <div class="streamingservice">
                                    <img src="<?=$movieService?>">
                                </div>
                            </div>
                        </div>
                        <h3><?=$movieTitle?></h3>
                        <div class="hover-content">
                            <a href="" onclick="to_watch(); return false;" class="cardbutton">Future</a>
                            <a href="" onclick="cur_watching(); return false;" class="cardbutton">Current</a>
                            <a href="" onclick="watched(); return false;" class="cardbutton">Finished</a>
                        </div>
                    </div>
            <!-- -------------- -->
                    <?php
                        }
                    ?>
                </div>
            </div>

            <div id="Currently Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                    <!-- CURRENTLY WATCHING CARDS -->
                </div>
            </div>

            <div id="Finished Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                    <!-- FINISHED WATCHING -->
                </div>
            </div>

        </div>

    </body>
</html>