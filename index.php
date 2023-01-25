<!-- Connect to database. -->
<?php
require_once "../../../conn/db.php";
?>

<!DOCTYPE html>
<html lang = "eng">

<head>
    <link rel="stylesheet" href="style.css">
    <script src = "settingsj.js"></script>
    <title>Settings</title>
</head>

<body>
    <h1 id = "center">Settings test</h1>

    <div class = "border">
        <h2>Theme</h2>
    
        <div>
            <!-- Start with dark mode -->
            <!-- <label class = "switch"  id = "center"> 
                <input type = "checkbox" checked onclick = "myFunction()">
                <span class = "dot"></span>
            </label> -->
            <!-- Start with light mode -->
            <label class = "switch"  id = "center"> 
                <input type = "checkbox" onclick = "theme()">
                <span class = "dot"></span>
            </label>
            <p class = "right">Dark mode</p>
        </div>

        <h2>Language</h2>
        <?php 
                $query = "SELECT * FROM `language`";
                $result = mysqli_query($conn, $query);
        ?>
        <select class = "language">
            <?php while ($row = mysqli_fetch_array($result)):; ?>
            <option><?php echo $row[1];?></option>
            <?php endwhile; ?>
        </select>
        <br>

        <br><h2>Region</h2>
        <?php 
                $query2 = "SELECT * FROM `region`";
                $receive = mysqli_query($conn, $query2);
        ?>

        <div class = "dropdown">
            <div id = "drop-down" class = "select-box">
                <div class = "region" id = "selected-region" onclick = "dropdown()">
                    Select region
                </div>
                
                <div class = "search-bar">
                    <input id = "search-input" onkeyup = "search()" type = "text"
                    placeholder = "Search region...">
                </div>

                <div id = "regions" class = "all-options">
                    <?php while ($line = mysqli_fetch_array($receive)):; ?>
                    <div class = "option">
                        <input type = "radio" class = "radio">
                        <label onclick = "new_region()"><?php echo $line[1];?></label>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

        </div>

        <br><br>
    </div>

</body>

</html>