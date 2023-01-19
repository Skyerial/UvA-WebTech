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
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    </head>
    <body>
    
        <!-- insert the nav bar -->
        <?php require_once("nav.php")?>

        <div class="tab">
            <button class="tablinks" onclick="openWatch(event, 'Future Watching')" id="defaultOpen">Future Watching</button>
            <button class="tablinks" onclick="openWatch(event, 'Currently Watching')">Currently Watching</button>
            <button class="tablinks" onclick="openWatch(event, 'Finished Watching')">Finished Watching</button>
        </div>

        <div id="Future Watching" class="tabcontent">
            <div class="moviesection">
                <div class="card">
                    <div class="imagebox">
                        <img src="https://image.tmdb.org/t/p/original/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg" class="poster">
                        <div class="streamingservice">
                            <img src="streaming_img/prime.png">
                        </div>
                    </div>
                    <div class="details">
                        <h3>Avatar</h3>
                    </div>
                    <div class="buttonlist">
                        <button>&#128064;</button>
                        <button>&#8986;</button>
                        <button>&#10003;</button>
                        <button>&#128465;</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="Currently Watching" class="tabcontent">
            <div class="moviesection">
            <?php
                for ($x = 0; $x < 15; $x += 1) {
            ?>
                <div class="card">
                    <img class="img1" src="https://m.media-amazon.com/images/M/MV5BMTc3MDcwMTc1MV5BMl5BanBnXkFtZTcwMzk4NTU3Mg@@._V1_FMjpg_UX1000_.jpg">
                    <img src="https://1000logos.net/wp-content/uploads/2017/05/Netflix-Logo-500x281.png" class="img2">
                    <div class="content">
                        <div class="row">
                            <div class="details">
                                <h3>Avatar <?=$x?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                }
            ?>
            </div>
        </div>

        <div id="Finished Watching" class="tabcontent">      
            <div class="moviesection">
                <div class="card">
                    <img class="img1" src="https://m.media-amazon.com/images/M/MV5BMzdjNjI5MmYtODhiNS00NTcyLWEzZmUtYzVmODM5YzExNDE3XkEyXkFqcGdeQXVyMTAyMjQ3NzQ1._V1_QL75_UX190_CR0,2,190,281_.jpg">
                    <div class="content">
                        <div class="row">
                            <div class="details">
                                <h3>The Menu</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script src="watchlist.js"></script>
    </body>
</html> 