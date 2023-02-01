<!DOCTYPE html>
<html>
    <head>
        <?php require_once("headtags.php") ?>
        <link rel="stylesheet" type="text/css" href="styles/loading.css">
        <title>wheretowatch.com</title>
        <script type="text/javascript" src="scripts.js"></script>
        <script type="text/javascript" src="menuScript.js" defer></script>
        <script type="text/javascript" src="homepageScript.js" defer></script>
        <script type="text/javascript" src="add.js"></script>
        <script src="https://kit.fontawesome.com/817fab420e.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <!-- insert the nav bar -->
        <?php require_once("nav.php"); ?>

        <main id="mainID">
            <div class="titlerow" id="title">
                <h1 class="title" id="titletext">wheretowatch.com</h1>
            </div>

            <div class="content" id="contentID">
                <div class="searchrow" id="formID">
                    <form class="search-bar" name="search" action="#" onsubmit="searchbutton();showLoadingAnimation();return false">
                        <input type="text" autocomplete="off" placeholder="search a title" name="name" id="textbar">
                        <button type="submit" id="buttonID"></button>
                    </form>
                </div>

                <div class="cardcontainer" id="cardcontainerID">
                    <!-- <div class="card">
                        <div class="imagebox">
                            <img src="https://image.tmdb.org/t/p/original/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg"/>

                        </div>
                        <h3>Avatar</h3>
                        <div class="hover-content">
                            <button class="cardbutton">A</button>
                            <button class="cardbutton">B</button>
                            <button class="cardbutton">C</button>
                        </div>
                    </div> -->
                </div>
            </div>
        </main>

        <?php require_once("footer.php"); ?>

    </body>
</html>
