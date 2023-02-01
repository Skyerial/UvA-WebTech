<!DOCTYPE html>
<html>
    <head>
        <?php require_once("headtags.php") ?>
        <title>About</title>
        <link rel="stylesheet" href="styles/nav.css">
        <link rel="stylesheet" href="styles/about.css">
        <script type="text/javascript" src="scripts.js"></script>
        <script type="text/javascript" src="menuScript.js" defer></script>
    </head>
    <body>
        <!-- insert the nav bar -->
        <?php require_once("nav.php")?>

        <main id="mainID" class="reg-form">
            <div class="reg-header">
                <h2>About</h2>
            </div>

            <p class = "text">
                This website is a project commissioned by the University 
                of Amsterdam. Our project group exists of five students who study 
                computer science. <br><br>
                WhereToWatch.com was created by
                M. Blok, T. van den Kommer, J. Kops, J. Lauppe and D. Oppenhuizen.
            </p>
        </main>


        <?php require_once("footer.php")?>
    </body>
</html>