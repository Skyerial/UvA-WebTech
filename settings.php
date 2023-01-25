<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>TITEL</title>
        <link rel="stylesheet" href="styles/homepage.css">
        <link rel="stylesheet" href="styles/nav.css">
        <script type="text/javascript" src="scripts.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
    </head>
    <body>
        <!-- insert the nav bar -->
        <?php require_once("nav.php")?>
        <div class="titlerow" id="title">
            <h1 class="title" id="titletext">Settings, soonTM</h1>
        </div>

        <h1 class = "title" id = "center">Settings Page</h1>

        <div class = "border">
            <br><h2>Region</h2>
            <?php
                    $region_query = "SELECT * FROM `region`";
                    $receive = mysqli_query($conn, $region_query);
            ?>

            <div class = "dropdown">
                <!-- Current region saved in database. -->
                <p>Change your region:</p>
                <div class = "region" id = "selected-region" onclick = "dropdown()">
                    <?= $region?>
                </div>
                
                <!-- Search bar to look up regions. -->
                <div class = "search-bar">
                    <input id = "search-input" onkeyup = "search()" type = "text"
                    placeholder = "Search region...">
                </div>

                <!-- Get all regions from database and put them in a dropdown -->
                <div id = "regions" class = "all-options">
                    <?php while ($line = mysqli_fetch_array($receive)):;?>
                    <div class = "option" onclick = "new_region('<?php echo $line[1];?>', 
                    '<?php echo $_SESSION['login']?>')">
                        <input type = "radio" class = "input_php">
                        <label><?php echo $line[1];?></label>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <br><br>
            <div>
                <!-- Option to change password -->
                <h2>User settings</h2>
                <div>
                    <p>Change your password:</p>
                </div>
                <div>
                    <form>
                        <input  class = "form" placeholder = "New password">
                        <br><br>
                        <input  class = "form" placeholder = "Confirm password">
                    </form>          
                </div>
            </div>
        </div>

        <?php require_once("footer.php")?>
        
    </body>
</html>