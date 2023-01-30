<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "account_verification/session_token.php";
require_once "/../../../../conn/db.php";

////////////////////////////////////////////////////////////////////////////////
// Error log file definement:
////////////////////////////////////////////////////////////////////////////////
define("ERROR_LOG_FILE", "errorLog/error.txt");

////////////////////////////////////////////////////////////////////////////////
// Functions:
////////////////////////////////////////////////////////////////////////////////

// retrieve_playlist retrieves the title, picture URL and streaming service
// from a playlist named $name from a user with $uid.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $user_id: The id of the user, whose playlist must be retrieved.
//  $name: The name of the playlist to be retrieved.
//
// Side effect:
//  All items of the playlist are pushed to HTML.
function retrieve_playlist($conn, $email, $name) {
    // Prepare SQL statement to retrieve all items with their characteristics:
    $retrieve = "SELECT DISTINCT
                item.title, item.picture_url, item_ssid.ss_link,
                streaming_service.streaming_service
                FROM item
                JOIN item_ssid ON item.iid = item_ssid.iid
                JOIN streaming_service ON item_ssid.ssid = streaming_service.ssid
                JOIN playlist_item ON item.iid = playlist_item.iid
                JOIN playlist_name ON playlist_item.pid = playlist_name.pid
                JOIN playlist_user ON playlist_name.pid = playlist_user.pid
                JOIN user ON playlist_user.uid = user.uid
                WHERE user.email = ? AND playlist_name.name = ?";

    $retrieve = $conn->prepare($retrieve);

    if (!$retrieve->bind_param("ss", $email, $name)) {
        throw new Exception ("[retrieve_playlist] Could not bind parameters.");
    }
    if (!$retrieve->execute()) {
        throw new Exception ("[retrieve_playlist] Could not execute query.");
    }

    // Push all items to HTML:
    $result = $retrieve->get_result();
    while ($row = $result->fetch_assoc()) {
        // Note: the variables are arrays, so title = $row['title'];
        // Note: $row['streaming_service'] is all lowercase (e.g. netflix);
        // Note: after the close-statement, the variables no longer exist!
        echo $row['title'] . ' ' . $row['streaming_service'] . ' ';
    }

    $retrieve->close();
}

////////////////////////////////////////////////////////////////////////////////
// Retrieve playlists:
////////////////////////////////////////////////////////////////////////////////

// Check if the user is logged in, if not redirect the user to the login page.
if (isset($_COOKIE['login']) && isset($_COOKIE['checker'])) {
    if (!check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])) {
        if (is_resource($conn)) { mysqli_close($conn); }
        header("Location: login.php");
        exit(0);
    }
} else {
    if (is_resource($conn)) { mysqli_close($conn); }
    header("Location: login.php");
    exit(0);
}

// This variable holds the name of the playlist, you must retrieve it somehow.
$playlist = "future watching";

// Show playlist:
try {
    retrieve_playlist($conn, $_COOKIE['checker'], $playlist);
} catch (Exception $err) {
    $err_file = fopen(ERROR_LOG_FILE, "a");
    fwrite($err_file, $err->getMessage() . "\n");
    fclose($err_file);
}

?>