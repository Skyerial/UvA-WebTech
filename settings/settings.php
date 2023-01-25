<!DOCTYPE html>
<html lang = "eng">

<head>
    <link rel="stylesheet" href="style.css">
    <script src = "settingsj.js"></script>
    <title>Settings</title>
</head>

<!-- Connect to database. -->
<?php
require_once "../../../../conn/db.php";
include '../pages/nav.php';

// Block the page if the user is not logged in:
// if (!isset($_SESSION)) { session_start(); }
// if (!(isset($_SESSION['login']))) { exit("You are not logged in"); }

// Receive the current region of the user.
$retrieve_region = $conn->prepare("SELECT region.region FROM user LEFT JOIN 
region ON user.rid = region.rid WHERE email = ?");
if (!$retrieve_region->bind_param("s", $_SESSION['login'])) {
    exit("Could not bind parameters.");
}
if (!$retrieve_region->execute()) {
    exit("Could not execute query.");
}
$retrieve_result = $retrieve_region->get_result();
$retrieve_row = $retrieve_result->fetch_assoc();
if(isset($retrieve_row['region'])){
    $region = $retrieve_row['region'];
} else {
    $region = "Select region";
}
?>

<body>
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
</body>

</html>