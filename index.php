<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>TITEL</title>
        <link rel="stylesheet" href="style.css">
        <script type="text/javascript" src="scripts.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    </head>
    <body>

        <div class="navbar" id="navdiv">
            <nav>
                <ul>
                    <li><a href="">HOME</a></li>
                    <li><a href="">REGION</a></li>
                    <li><a href="">ABOUT</a></li>
                    <li><a href="">LOGIN</a></li>
                </ul>
            </nav>
        </div>
        <div class="titlerow" id="title">
            <h1 class="title" id="titletext">wheretowatch.com</h1>
        </div>
        <div class="searchrow" id="formID">
            <div class="search-bar">
                <input type="text" autocomplete="off" placeholder="search a title" name="name" id="textbar">
                <button onclick="searchbutton()"></button>
            </div>
        </div>

        <div class="content" id="contentID">
            <div class="cardcontainer" id="cardcontainerID"></div>
        </div>
    </body>
</html>