<?php
        // Retrieve the data from the cookies:
        $movieTitle = "Avatar";
        $moviePoster = "https://image.tmdb.org/t/p/original/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg";
        $filmService = "netflix";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Watchlist</title>
        <link rel="stylesheet" href="watchlist.css">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <script type="text/javascript" src="watchlist.js" defer></script>
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
                        $amountcards = 0;
                        $movieTitle = "Cars";
                        $moviePoster = "https://image.tmdb.org/t/p/original/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg";
                        $movieService = "streaming_img/netflix.png";
                        if($amountcards == 0) {
                    ?>
                    <div class="banner">
                        <img src="watchlist.png">
                        <h3> Your current watchlist is emtpy, please click the "icon" to add to your current watchlist. </h3>
                    </div>
                    <?php 
                        } else {
                            for ($i = 0; $i < $amountcards; $i++) {
                    ?>
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
                    <?php 
                        }}
                    ?>
                </div>
            </div>

            <div id="Currently Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                    <div class="banner">
                        <img src="watchlist.png">
                        <h3> Your current watchlist is emtpy, please click the "icon" to add to your current watchlist. <h3>
                    </div>
                    <!-- <div class="card" id="card0" style="opacity: 1;">
                        <div class="imagebox">
                            <img src="https://image.tmdb.org/t/p/original/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg">
                            <div class="buttonbox">
                                <a href="" class="cardbutton">A</a>
                                <a href="" class="cardbutton">B</a>
                                <a href="" class="cardbutton">C</a>
                            </div>
                        </div>
                        <h3>Avatar</h3>
                        <div class="hover-content">
                        <p> LOREM IPSUM NOGWATTUS </p>
                        </div>
                    </div> -->
                </div>
            </div>

            <div id="Finished Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                <?php
                        $movieTitle = "Cars";
                        $moviePoster = "https://image.tmdb.org/t/p/original/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg";
                        $movieService = "streaming_img/netflix.png";
                        for ($i = 0; $i < 10; $i++) {
                    ?>
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
                    <?php
                        }
                    ?>
                </div>
            </div>
            
        </div>
        
    </body>
</html>