<!DOCTYPE html>
<html>
    <head>
        <?php require_once("headtags.php") ?>
        <link rel="stylesheet" type="text/css" href="styles/homepageNav.css">
        <link rel="stylesheet" type="text/css" href="styles/loading.css">
        <title>wheretowatch.com</title>
        <script type="text/javascript" src="scripts.js"></script>
        <script type="text/javascript" src="menuScript.js" defer></script>
        <script type="text/javascript" src="homepageScript.js" defer></script>
        <script type="text/javascript" src="add.js"></script>
        <script src="https://kit.fontawesome.com/817fab420e.js" crossorigin="anonymous"></script>
    </head>
    <body>
        <?php require_once("nav.php"); ?>

        <main id="mainID">
            <div class="titlerow" id="title">
                <h1 class="title" id="titletext">wheretowatch.com</h1>
            </div>

            <div class="content" id="contentID">
                <div class="searchrow" id="formID">
                    <form class="search-bar" name="search" action="#" onsubmit="searchbutton();return false">
                        <input type="text" autocomplete="off" placeholder="search a title" name="name" id="textbar">
                        <button type="submit" id="buttonID"></button>
                    </form>
                </div>
                <div class="cardcontainer" id="cardcontainerID">
                </div>
            </div>
        </main>

        <?php require_once("footer.php"); ?>

    </body>
</html>
