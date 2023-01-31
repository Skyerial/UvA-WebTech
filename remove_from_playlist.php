<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "account_verification/session_token.php";
require_once "modify_playlist.php";
require_once "/../../../conn/db.php";


////////////////////////////////////////////////////////////////////////////////
// Error log file definement:
////////////////////////////////////////////////////////////////////////////////
define("ERROR_LOG_FILE", "errorLog/error.txt");

////////////////////////////////////////////////////////////////////////////////
// Remove an item from a playlist:
////////////////////////////////////////////////////////////////////////////////

$body = file_get_contents('php://input');
$json = json_decode($body);
$id = $json->id;
$playlist = $json->playlist;


if(!isset($_SESSION)) { session_start(); }

// Check if the user is logged in, if not redirect the user to the login page.
if (isset($_COOKIE['login']) && isset($_COOKIE['checker'])) {
    if (!check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])) {
        if (is_resource($conn)) { mysqli_close($conn); }
        header("Location: login.php");
        exit("conn");
    }
} else {
    if (is_resource($conn)) { mysqli_close($conn); }
    header("Location: login.php");
    exit("cookie");
}

$data = $_SESSION['displayed_cards'][$id];
//var_dump($data["movieTitle"]);
$title = $data["movieTitle"];
$picture = $data["moviePoster"];

if($data["prime"]) {
    $service_url = $data["prime"];
    remove_from_playlist($title, $picture, $service_url, $playlist);
}
//sleep(1);
if($data["netflix"]) {
    //sleep(1);
    $service_url = $data["netflix"];
    remove_from_playlist($title, $picture, $service_url, $playlist);
}
//sleep(1);
if($data["disney"]) {
    //sleep(1);
    $service_url = $data["disney"];
    remove_from_playlist($title, $picture, $service_url, $playlist);
}
//sleep(1);
if($data["hbo"]) {
    //sleep(1);
    $service_url = $data["hbo"];
    remove_from_playlist($title, $picture, $service_url,  $playlist);
}
//sleep(1);
if($data["hulu"]) {
    //sleep(1);
    $service_url = $data["hulu"];
    remove_from_playlist($title, $picture, $service_url, $playlist);
}
//sleep(1);
if($data["apple"]) {
    //sleep(1);
    $service_url = $data["apple"];
    remove_from_playlist($title, $picture, $service_url, $playlist);
}

?>