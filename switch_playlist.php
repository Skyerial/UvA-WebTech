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
// Add item to playlist:
////////////////////////////////////////////////////////////////////////////////

$body = file_get_contents('php://input');
$json = json_decode($body);
$id = $json->id;
$playlist = $json->playlist;
$action = $json->action;


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

//let file know what the class looks like
class movie_details {
    //var $id;
    var $movie_title;
    var $movie_poster;
    var $prime;
    var $netflix;
    var $disney;
    var $hbo;
    var $hulu;
    var $apple;
}

$data = $_SESSION['displayed_cards'][1][$id];
$title = $data->movie_title;
$poster = $data->movie_poster;


if($action == "add") {
    if($data->prime) {
        $service_url = $data->prime;
        add_to_playlist($title, $poster, $service_url, "prime", $playlist);
    }
    if($data->netflix) {
        $service_url = $data->netflix;
        add_to_playlist($title, $poster, $service_url, "netflix", $playlist);
    }
    if($data->disney) {
        $service_url = $data->disney;
        add_to_playlist($title, $poster, $service_url, "disney", $playlist);
    }
    if($data->hbo) {
        $service_url = $data->hbo;
        add_to_playlist($title, $poster, $service_url, "hbo", $playlist);
    }
    if($data->hulu) {
        $service_url = $data->hulu;
        add_to_playlist($title, $poster, $service_url, "hulu", $playlist);
    }
    if($data->apple) {
        $service_url = $data->apple;
        add_to_playlist($title, $poster, $service_url, "apple", $playlist);
    }
} else if ($action == "remove") {
    if($data->prime) {
        $service_url = $data->prime;
        remove_from_playlist($title, $poster, $service_url, $playlist);
    }
    if($data->netflix) {
        $service_url = $data->netflix;
        remove_from_playlist($title, $poster, $service_url, $playlist);
    }
    if($data->disney) {
        $service_url = $data->disney;
        remove_from_playlist($title, $poster, $service_url, $playlist);
    }
    if($data->hbo) {
        $service_url = $data->hbo;
        remove_from_playlist($title, $poster, $service_url, $playlist);
    }
    if($data->hulu) {
        $service_url = $data->hulu;
        remove_from_playlist($title, $poster, $service_url, $playlist);
    }
    if($data->apple) {
        $service_url = $data->apple;
        remove_from_playlist($title, $poster, $service_url, $playlist);
    }
}

?>