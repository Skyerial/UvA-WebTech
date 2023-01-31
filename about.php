<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>About</title>
        <link rel="stylesheet" href="styles/nav.css">
        <link rel="stylesheet" href="styles/about.css">
        <script type="text/javascript" src="scripts.js"></script>
        <script type="text/javascript" src="menuScript.js" defer></script>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    </head>
    <body onload="showFooter(); showLogo()">
        <!-- insert the nav bar -->
        <?php require_once("nav.php")?>

        <main id="mainID" class="reg-form">
            <div class="reg-header">
                <h2>About</h2>
            </div>

            <p class = "text">
                This website is a project commisioned by the University of
                Amsterdam. Our project group exists of five students who study
                computer science.

                <br></br>

                WhereToWatch.com was created by M. Blok, T. van den Kommer, J. Kops, J. Lauppe and D. Oppenhuizen.
            </p>
        </main>


        <?php require_once("footer.php")?>
    </body>
</html>