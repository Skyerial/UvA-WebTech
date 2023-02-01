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


// Receive data from post request.
$body = file_get_contents('php://input');
$json = json_decode($body);
$id = $json->id;
$playlist = $json->playlist;

if(!isset($_SESSION)) { session_start(); }

// Check if the user is logged in, if not redirect the user to the login page.
if (isset($_COOKIE['login']) && isset($_COOKIE['checker'])) {
    if (!check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])) {
        if (is_resource($conn)) { mysqli_close($conn); }
        exit("not logged in");
    }
} else {
    if (is_resource($conn)) { mysqli_close($conn); }
    exit("not logged in");
}

// Let file know what the class looks like.
class movie_details {
    var $movieTitle;
    var $moviePoster;
    var $prime;
    var $netflix;
    var $disney;
    var $hbo;
    var $hulu;
    var $apple;
}

// Get backend data for homepage cards.
$data = $_SESSION['displayed_cards'][0][$id];
$title = $data->movieTitle;
$poster = $data->moviePoster;

$services = ["prime", "netflix", "disney", "hbo", "hulu", "apple"];

foreach ($services as $service) {
    if ($data->{$service}) {
        $service_url = $data->{$service};
        add_to_playlist($conn, $title, $poster, $service_url, $service, $playlist);
    }
}

if (is_resource($conn)) { mysqli_close($conn); }
?>