<?php

// retrieve_ssid finds the corresponding id value to $service from the database.
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $service: The name of the streaming service for which the id is to be found.
// Output: The id of the streaming service.
function retrieve_ssid($conn, $service) {
    // Prepare SQL statement to retrieve the ssid:
    $retrieve_ssid = $conn->prepare("SELECT ssid
                                     FROM streaming_service
                                     WHERE streaming_service = ?");

    if (!$retrieve_ssid->bind_param("s", $service)) {
        exit("Could not bind parameters.");
    }
    if (!$retrieve_ssid->execute()) { exit("Could not execute query."); }

    // Bind the result of the query to the $service_id variable:
    $retrieve_ssid->bind_result($service_id);
    $retrieve_ssid->fetch();
    $retrieve_ssid->close();

    return $service_id;
}

// Important: A movie / serie is referred to as an item.
//
// retrieve_iid checks if an item already exists in the database. If it does,
// it returns the item id of that item. If it does not, it adds a new item with
// the given variables and returns the item id of this item.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $title: The title of the item.
//  $picture: The url of the picture of the item.
//  $service_id: The ssid of the streaming service the item belongs to.
// Output: The item id of the item.
function retrieve_iid($conn, $title, $picture, $service_id) {
    // Prepare SQL statement to check if the item already exists:
    $check_dup = $conn->prepare("SELECT iid FROM item
        WHERE title = ? AND picture_url = ? AND ssid = ?");

    if (!$check_dup->bind_param("ssi", $title, $picture, $service_id)) {
        exit("Could not bind parameters.");
    }
    if (!$check_dup->execute()) { exit("Could not execute query."); }

    $check_dup->store_result();
    if ($check_dup->num_rows > 0) {
        // Item already exists. Retrieve item id:
        $check_dup->bind_result($item_id);
        $check_dup->fetch();
        $check_dup->close();

        return $item_id;
    } else {
        $check_dup->close();
        // The item doesn't exist, add the item to the database:
        $add_item = $conn->prepare("INSERT INTO item(title, picture_url, ssid) VALUES (?, ?, ?)");

        if (!$add_item->bind_param("ssi", $title, $picture, $service_id,)) {
            exit("Could not bind parameters.");
        }
        if (!$add_item->execute()) { exit("Could not execute query."); }

        $item_id = $conn->insert_id;
        $add_item->close();

        return $item_id;
    }
}

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

// retrieve_pid finds the corresponding playlist id to $user_id and $playlist
// from the database.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $user_id: The id of the user whose playlist is to be found
//  $playlist: Name of the playlist to be found
//
// Output:
//  The id of the playlist if it exists, otherwise a new playlist is created
//  with the provided name and the id of the new playlist is returned.
function retrieve_pid($conn, $user_id, $playlist) {
    // Prepare SQL statement to retrieve the playlist id:
    $list_exist = $conn->prepare("SELECT playlist_user.pid FROM playlist_name
    JOIN playlist_user ON playlist_user.pid = playlist_name.pid
    WHERE playlist_user.uid = ? AND playlist_name.name = ?");

    if (!$list_exist->bind_param("is", $user_id, $playlist)) {
        exit("Could not bind parameters.");
    }
    if (!$list_exist->execute()) { exit("Could not execute query."); }

    $list_exist->bind_result($playlist_id);
    $list_exist->fetch();
    $list_exist->close();

    if (empty($playlist_id)) {
        // No playlist exists, create a new one:
        $add_playlist = $conn->prepare("INSERT INTO playlist_user(uid)
                                        VALUES (?)");

        if (!$add_playlist->bind_param("i", $user_id)) {
            exit("Could not bind parameters.");
        }
        if (!$add_playlist->execute()) { exit("Could not execute query."); }

        $playlist_id = $conn->insert_id;
        $add_playlist->close();

        // Give the playlist a name:
        $playlist_name = $conn->prepare("INSERT INTO playlist_name(pid, name)
                                         VALUES (?, ?)");

        if (!$playlist_name->bind_param("is", $playlist_id, $playlist)) {
            exit("Could not bind parameters.");
        }
        if (!$playlist_name->execute()) { exit("Could not execute query."); }

        $playlist_name->close();
    }

    return $playlist_id;
}

function updatePlaylists($conn, $user_id, $item_id, $playlist_id) {
    $double_item = $conn->prepare("SELECT playlist_item.pid FROM playlist_item
        JOIN playlist_name ON playlist_item.pid = playlist_name.pid
        JOIN playlist_user ON playlist_name.pid = playlist_user.pid
        WHERE playlist_user.uid = ? AND playlist_item.iid = ?
        AND playlist_item.pid = ?");

    if (!$double_item->bind_param("iii", $user_id, $item_id, $playlist_id)) {
        exit("Could not bind parameters.");
    }
    if (!$double_item->execute()) { exit("Could not execute query."); }

    $double_item->store_result();
    if ($double_item->num_rows > 0) {
        // The item already exists in another playlist:
        // Remove the item from this playlist:
        $double_item->bind_result($pid);
        $double_item->fetch();
        $double_item->close();

        $delete_item = $conn->prepare("DELETE FROM playlist_item
                                       WHERE pid = ? AND iid = ?");

        if (!$delete_item->bind_param("ii", $pid, $item_id)) {
            exit("Could not bind parameters.");
        }
        if (!$delete_item->execute()) { exit("Could not execute query."); }

        $delete_item->close();
    }
}

// add_item_to_playlist adds an item to the playlist with id: $playlist_id.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $playlist_id: The playlist to which the item should be added.
//  $item_id: The id of the item to be added.
// Output:
//  None
function add_item_to_playlist($conn, $playlist_id, $item_id) {
    // Prepare SQL statement to add the item to the DB.
    // It can be that the item is already in the DB. In this case,
    // ignore the query.
    $add_item = $conn->prepare("INSERT IGNORE INTO playlist_item(pid, iid)
                                VALUES (?, ?)");

    if (!$add_item->bind_param("ii", $playlist_id, $item_id)) {
        exit("Could not bind parameters.");
    }
    if (!$add_item->execute()) { exit("Could not execute query."); }

    $add_item->close();
}

require_once "/../../../conn/db.php";

$body = file_get_contents('php://input');
$json = json_decode($body);
$title = $json->title;
$picture = $json->picture;
$service = $json->service;
$playlist = $json->playlist;

if(!isset($_SESSION)) { session_start(); }

// Check if the user is logged in, if not exit with an error message:
if (!(isset($_SESSION['login']))) {
    if (is_resource($conn)) {
        mysqli_close($conn);
    }
    exit("You are not logged in.");
}

// Retrieve necessary variable to add item to the database:
$service_id = retrieve_ssid($conn, $service);
if (!$service_id) { exit("Streaming service is not known."); }

// Add the item to the database and retrieve the corresponding item id:
$item_id = retrieve_iid($conn, $title, $picture, $service_id);

// Retrieve necessary variable to add the item to the playlist:
$user_id = retrieve_uid($conn, $_SESSION['login']);
if (!$user_id) { exit("No user found."); }

// Add the playlist to the database and retrieve the corresponding playlist id:
$playlist_id = retrieve_pid($conn, $user_id, $playlist);

// Check if the item doesn't already exist in another playlist:
updatePlaylists($conn, $user_id, $item_id, $playlist_id);

// Finally add the item to the playlist:
add_item_to_playlist($conn, $playlist_id, $item_id);

// The code has successfully executed. Close the connection to the database and
// exit with succes code '0':
if (is_resource($conn)) {
    mysqli_close($conn);
}
exit(0);
?>