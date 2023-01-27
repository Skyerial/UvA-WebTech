<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>TITEL</title>
        <link rel="stylesheet" href="styles/homepage.css">
        <link rel="stylesheet" href="styles/nav.css">
        <link rel="stylesheet" href="styles/about.css">
        <script type="text/javascript" src="scripts.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    </head>
    <body>
        <!-- insert the nav bar -->
        <?php require_once("nav.php")?>

        <br>

        <div class = "border">
            <div class="titlerow" id="title">
                <h1 class="title" id="titletext">About</h1>
            </div>

            <p class = "text">
                This website is a project created regarding an assignment given by the University 
                of Amsterdam. Our project group exists of five students who study 
                computer science. <br><br>
                WhereToWatch.com was created by
                M. Blok, T. van den Kommer, J. Kops, J. Lauppe and D. Oppenhuizen.
            </p>
        </div>


        <?php require_once("footer.php")?>
    </body>
</html>