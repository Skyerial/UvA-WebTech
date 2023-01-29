<!-- https://phppot.com/php/php-curl-post-json/ -->
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
        throw new Exception ("[retrieve_ssid] Could not bind parameters.");
    }
    if (!$retrieve_ssid->execute()) {
        throw new Exception ("[retrieve_ssid] Could not execute query.");
    }

    // Bind the result of the query to the $service_id variable:
    $retrieve_ssid->bind_result($service_id);
    $retrieve_ssid->fetch();
    $retrieve_ssid->close();

    return $service_id;
}

function add_link($conn, $item_id, $service_id, $service_url) {
    // Prepare SQL statement to add the service url to the database.
    // If the service_url is already in the database, do nothing.
    $add_link = $conn->prepare("INSERT IGNORE INTO item_ssid(iid, ssid, ss_link)
                                VALUES (?, ?, ?)");

    if (!$add_link->bind_param("sss", $item_id, $service_id, $service_url)) {
        throw new Exception ("[set_link] Could not bind parameters.");
    }
    if (!$add_link->execute()) {
        throw new Exception ("[set_link] Could not execute query.");
    }

    $add_link->close();
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
        WHERE title = ? AND picture_url = ?");

    if (!$check_dup->bind_param("ss", $title, $picture)) {
        throw new Exception ("[retrieve_iid] Could not bind parameters.");
    }
    if (!$check_dup->execute()) {
        throw new Exception ("[retrieve_iid] Could not execute query.");
    }

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
        $add_item = $conn->prepare("INSERT INTO item(title, picture_url)
                                    VALUES (?, ?)");

        if (!$add_item->bind_param("ss", $title, $picture)) {
            throw new Exception ("[add_item] Could not bind parameters.");
        }
        if (!$add_item->execute()) {
            throw new Exception ("[add_item] Could not execute query.");
        }

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
        throw new Exception ("[retrieve_uid] Could not bind parameters.");
    }
    if (!$find_uid->execute()) {
        throw new Exception ("[retrieve_uid] Could not execute query.");
    }

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
    $list_exist = $conn->prepare(
       "SELECT playlist_user.pid FROM playlist_user
        JOIN playlist_name ON playlist_name.pid = playlist_user.pid
        WHERE playlist_user.uid = ? AND playlist_name.name = ?"
    );

    if (!$list_exist->bind_param("is", $user_id, $playlist)) {
        throw new Exception ("[list_exist] Could not bind parameters.");
    }
    if (!$list_exist->execute()) {
        throw new Exception ("[list_exist] Could not execute query.");
    }

    $list_exist->store_result();
    if ($list_exist->num_rows > 0) {
        // Item already exists. Retrieve item id:
        $list_exist->bind_result($playlist_id);
        $list_exist->fetch();
        $list_exist->close();

        return $playlist_id;
    } else {
        // No playlist exists, create a new one:
        $add_playlist = $conn->prepare("INSERT INTO playlist_user(uid)
                                        VALUES (?)");

        if (!$add_playlist->bind_param("i", $user_id)) {
            throw new Exception ("[add_playlist] Could not bind parameters.");
        }
        if (!$add_playlist->execute()) {
            throw new Exception ("[add_playlist] Could not execute query.");
        }

        $playlist_id = $conn->insert_id;
        $add_playlist->close();

        // Give the playlist a name:
        $playlist_name = $conn->prepare("INSERT INTO playlist_name(pid, name)
                                         VALUES (?, ?)");

        if (!$playlist_name->bind_param("is", $playlist_id, $playlist)) {
            throw new Exception ("[playlist_name] Could not bind parameters.");
        }
        if (!$playlist_name->execute()) {
            throw new Exception ("[playlist_name] Could not execute query.");
        }

        $playlist_name->close();
    }

    return $playlist_id;
}

// update_playlists deletes all occurences where the item is added to other
// playlists, than the playlist where the item needs to be added.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $user_id: The id of the user whose playlists will be updated.
//  $item_id: The id of the item to be deleted from the playlists.
//  $playlist_id: The id of the playlis that the item should not be deleted
//                from
//
// Output: None
function update_playlists($conn, $user_id, $item_id, $playlist_id) {
    // Prepare SQL statement to delete the item in all other playlists:
    $delete_item = $conn->prepare(
        "DELETE playlist_item FROM playlist_item
        JOIN playlist_name ON playlist_item.pid = playlist_name.pid
        JOIN playlist_user ON playlist_name.pid = playlist_user.pid
        WHERE playlist_user.uid = ? AND playlist_item.iid = ?
        AND playlist_item.pid != ?"
    );

    if (!$delete_item->bind_param("iii", $user_id, $item_id, $playlist_id)) {
        throw new Exception ("[delete_item] Could not bind parameters.");
    }
    if (!$delete_item->execute()) {
        throw new Exception ("[delete_item] Could not execute query.");
    }

    $delete_item->close();
}

// add_item_to_playlist adds an item to the playlist with id: $playlist_id.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $playlist_id: The playlist to which the item should be added.
//  $item_id: The id of the item to be added.
// Output:
//  None
function add_item_to_playlist($conn, $playlist_id, $item_id, $ss_link) {
    // Prepare SQL statement to check if the item is already in the playlist:
    $check_item = $conn->prepare(
        "SELECT piid FROM playlist_item
        JOIN item_ssid ON playlist_item.iid = item_ssid.iid
        WHERE playlist_item.pid = ? and item_ssid.ss_link = ?"
    );

    if (!$check_item->bind_param("is", $playlist_id, $ss_link)) {
        throw new Exception ("[check_item] Could not bind parameters.");
    }
    if (!$check_item->execute()) {
        throw new Exception ("[check_item] Could not execute query.");
    }

    $check_item->store_result();
    if ($check_item->num_rows > 0) {
        // The item is already in the playlist:
        $check_item->free_result();
        $check_item->close();
    } else {
        // The item is not in the playlist:
        $check_item->free_result();
        $check_item->close();

        // Prepare SQL statement to add the item to the playlist:
        $add_item = $conn->prepare("INSERT INTO playlist_item(pid, iid)
                                    VALUES (?, ?)");

        if (!$add_item->bind_param("ii", $playlist_id, $item_id)) {
            throw new Exception ("[add_item] Could not bind parameters.");
        }
        if (!$add_item->execute()) {
            throw new Exception ("[add_item] Could not execute query.");
        }

        $add_item->close();
    }
}

function addToPlaylist($title, $picture, $service_url, $service, $playlist) {
        // check if $playlist stores a valid value.
        global $conn;
    if (!($playlist == "future watching" || $playlist == "currently watching" || $playlist == "finished watching")) {
        exit("Invalid playlist");
    }

    // Check if the user is logged in, if not redirect the user to the login page.
    if (isset($_COOKIE['login']) && isset($_COOKIE['checker'])) {
        if (!check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])) {
            if (is_resource($conn)) { mysqli_close($conn); }
            exit("You are not logged in");
        }
    } else {
        if (is_resource($conn)) { mysqli_close($conn); }
        exit("You are not logged in");
    }

        /* Start transaction */
    $conn->begin_transaction();

    // Retrieve necessary variable to add item to the database:
    try {
        $service_id = retrieve_ssid($conn, $service);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);

        $conn->rollback();
    }
    if (!$service_id) { exit("Streaming service is not known."); }

    // Add the item to the database and retrieve the corresponding item id:
    try {
        $item_id = retrieve_iid($conn, $title, $picture, $service_id);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);

        $conn->rollback();
    }

    // Add the service link to the database:
    try {
        add_link($conn, $item_id, $service_id, $service_url);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);

        $conn->rollback();
    }

    // Retrieve necessary variable to add the item to the playlist:
    try {
        $user_id = retrieve_uid($conn, $_COOKIE['checker']);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);

        $conn->rollback();
    }
    if (!$user_id) { exit("No user found."); }

    // Add the playlist to the database and retrieve the corresponding playlist id:
    try {
        $playlist_id = retrieve_pid($conn, $user_id, $playlist);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);

        $conn->rollback();
    }

    // Check if the item doesn't already exist in another playlist:
    try {
        update_playlists($conn, $user_id, $item_id, $playlist_id);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);

        $conn->rollback();
    }

    // Finally add the item to the playlist:
    try {
        add_item_to_playlist($conn, $playlist_id, $item_id, $service_url);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);

        $conn->rollback();
    }

    /* If code reaches this point without errors then commit the data in the database */
    $conn->commit();

    // The code has successfully executed. Close the connection to the database and
    // exit with succes code '0':
    if (is_resource($conn)) { mysqli_close($conn); }
    exit(0);
}




////////////////////////////////////////////////////////////////////////////////
// Add item to playlist:
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
$poster = $data["moviePoster"];


if($data["prime"]) {
    $service_url = $data["prime"];
    addToPlaylist($title, $poster, $service_url, "prime", $playlist);
}
if($data["netflix"]) {
    $service_url = $data["netflix"];
    addToPlaylist($title, $poster, $service_url, "netflix", $playlist);
}
if($data["disney"]) {
    $service_url = $data["disney"];
    addToPlaylist($title, $poster, $service_url, "disney", $playlist);
}
if($data["hbo"]) {
    $service_url = $data["hbo"];
    addToPlaylist($title, $poster, $service_url, "hbo", $playlist);
}
if($data["hulu"]) {
    $service_url = $data["hulu"];
    addToPlaylist($title, $poster, $service_url, "hulu", $playlist);
}
if($data["apple"]) {
    $service_url = $data["apple"];
    addToPlaylist($title, $poster, $service_url, "apple", $playlist);
}

?>