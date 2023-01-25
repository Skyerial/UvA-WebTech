<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="stylesheet" type="text/css" href="styles/homepage.css">
        <link rel="stylesheet" type="text/css" href="styles/nav.css">
        <title>wheretowatch.com</title>
        <script type="text/javascript" src="watchlistadd.js"></script>
        <script type="text/javascript" src="scripts.js"></script>
        <script type="text/javascript" src="add.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    </head>
    <body>
        <!-- insert the nav bar -->
        <?php require_once("nav.php") ?>

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
                <!-- Cards go here -->
            </div>
        </div>
    </body>
</html>