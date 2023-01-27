<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "account_verification/session_token.php";
require_once "/../../../conn/db.php";


////////////////////////////////////////////////////////////////////////////////
// Error log file definement:
////////////////////////////////////////////////////////////////////////////////
define("ERROR_LOG_FILE", "errorLog/error.txt");

////////////////////////////////////////////////////////////////////////////////
// Functions:
////////////////////////////////////////////////////////////////////////////////

// retrieve_item removes a row from playlist_item.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $title: The title of the item to be removed.
//  $picture_url: The picture URL of the item to be removed.
//  $ss_link: The link to the streaming service of the item to be removed.
//  $email: The email of the user.
//  $name: The name of the playlist to be removed.
//
// Output: None.
function remove_item($conn, $title, $picture_url, $ss_link, $email, $name) {
    // Prepare SQL statement to remove an item id and its corresponding
    // playlist id from the table playlist_item.
    $remove_item =
       "DELETE playlist_item FROM playlist_item
        JOIN item ON item.iid = playlist_item.iid
        JOIN item_ssid ON item_ssid.iid = item.iid
        JOIN playlist_name ON playlist_name.pid = playlist_item.pid
        JOIN playlist_user ON playlist_user.pid = playlist_name.pid
        JOIN user ON user.uid = playlist_user.uid
        WHERE item.title = ? AND item.picture_url = ? AND item_ssid.ss_link = ?
        AND user.email = ? AND playlist_name.name = ?";

    $remove_item = $conn->prepare($remove_item);

    if (!$remove_item->bind_param("sssss", $title, $picture_url, $ss_link,
        $email, $name)) {
        throw new Exception ("[remove_item] Could not bind parameters.");
    }
    if (!$remove_item->execute()) {
        throw new Exception ("[remove_item] Could not execute query.");
    }

    $remove_item->close();
}

function remove_from_playlist($title, $picture, $service_url, $playlist) {
    global $conn;
    try {
        remove_item(
            $conn,
            filter_var(htmlspecialchars($title), FILTER_SANITIZE_STRING),
            filter_var(htmlspecialchars($picture), FILTER_SANITIZE_STRING),
            filter_var(htmlspecialchars($service_url), FILTER_SANITIZE_STRING),
            filter_var($_COOKIE['checker'], FILTER_VALIDATE_EMAIL),
            filter_var(htmlspecialchars($playlist), FILTER_SANITIZE_STRING),
        );
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);
    }
}
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