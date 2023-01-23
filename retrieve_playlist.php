<?php

// find_user_id finds the corresponding user id to $email from the database.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email of the user.
// Output:
//  The user id if the email exists, false otherwise.
function retrieve_uid($conn, $email) {
    // Prepare SQL statement to retrieve the user id:
    $find_uid = $conn->prepare("SELECT uid FROM user WHERE email = ?");

    if (!$find_uid->bind_param("s", $email)) {
        exit("Could not bind parameters.");
    }
    if (!$find_uid->execute()) { exit("Could not execute query."); }

    $find_uid_result = $find_uid->get_result();

    if($find_uid_result->num_rows === 0) {
        // No user with the corresponding email is found:
        $find_uid->close();
        return false;
    }

    $user_id = $find_uid_result->fetch_assoc();
    $user_id = $user_id['uid'];
    $find_uid->close();

    return $user_id;
}

// retrieve_playlist retrieves the title, picture URL and streaming service
// from a playlist named $name from a user with $uid.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $user_id: The id of the user, whose playlist must be retrieved.
//  $name: The name of the playlist to be retrieved.
// Output:
//  All items of the playlist are pushed to HTML.
function retrieve_playlist($conn, $user_id, $name) {
    // Prepare SQL statement to retrieve all items with their characteristics:
    $retrieve = "SELECT item.title, item.picture_url,
                streaming_service.streaming_service
                FROM item
                JOIN streaming_service ON item.ssid = streaming_service.ssid
                JOIN playlist_item ON item.iid = playlist_item.iid
                JOIN playlist_name ON playlist_item.pid = playlist_name.pid
                JOIN playlist_user ON playlist_name.pid = playlist_user.pid
                WHERE playlist_user.uid = ? AND playlist_name.name = ?";

    $retrieve = $conn->prepare($retrieve);

    if (!$retrieve->bind_param("is", $user_id, $name)) {
        exit("Could not bind parameters.");
    }
    if (!$retrieve->execute()) { exit("Could not execute query."); }

    // Push all items to HTML:
    $result = $retrieve->get_result();
    while ($row = $result->fetch_assoc()) {
        // Note: the variables are arrays, so title = $row['title'];
        // Note: $row['streaming_service'] is all lowercase (e.g. netflix);
        // Note: after the close-statement, the variables no longer exist!
        echo $row['title'] . " " . $row['picture_url'] . " " . $row['streaming_service'];
    }

    $retrieve->close();
}

require_once "/../../../conn/db.php";

if(!isset($_SESSION)) { session_start(); }

// Check if the user is logged in, if not exit with an error message:
if (!(isset($_SESSION['login']))) {
    if (is_resource($conn)) {
        mysqli_close($conn);
    }
    exit("You are not logged in.");
}

// Retrieve the user id:
$user_id = retrieve_uid($conn, $_SESSION['login']);
if (!$user_id) { exit("No user found."); }

// This variable holds the name of the playlist, you must retrieve it somehow.
$playlist = "finished watching";

// Show playlist:
retrieve_playlist($conn, $user_id, $playlist);

?>