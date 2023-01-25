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
                    <div class="card" id="card0" style="opacity: 1;">
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
                    </div>
                </div>
            </div>

            <div id="Currently Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                    <div class="card" id="card0" style="opacity: 1;">
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
                    </div>
                </div>
            </div>

            <div id="Finished Watching" class ="tabcontent">
                <div class="cardcontainer" id="cardcontainerID">
                    <div class="card" id="card0" style="opacity: 1;">
                        <div class="imagebox">
                            <img src="https://m.media-amazon.com/images/M/MV5BMzdjNjI5MmYtODhiNS00NTcyLWEzZmUtYzVmODM5YzExNDE3XkEyXkFqcGdeQXVyMTAyMjQ3NzQ1._V1_QL75_UX190_CR0,2,190,281_.jpg">
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
                    </div>
                </div>
            </div>
            
        </div>

        <?php require_once("footer.php")?>
        
        <script type="text/javascript" src="watchlist.js"></script>
    </body>
</html> 