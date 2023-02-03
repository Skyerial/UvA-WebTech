<?php
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

// retrieve_wid finds the corresponding watchlist id to $user_id and $watchlist
// from the database.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $user_id: The id of the user whose watchlist is to be found
//  $watchlist: Name of the watchlist to be found
//
// Output:
//  The id of the watchlist if it exists, otherwise a new watchlist is created
//  with the provided name and the id of the new watchlist is returned.
function retrieve_wid($conn, $user_id, $watchlist) {
    // Prepare SQL statement to retrieve the watchlist id:
    $list_exist = $conn->prepare(
       "SELECT watchlist_user.wid FROM watchlist_user
        JOIN watchlist_name ON watchlist_name.wid = watchlist_user.wid
        WHERE watchlist_user.uid = ? AND watchlist_name.name = ?"
    );

    if (!$list_exist->bind_param("is", $user_id, $watchlist)) {
        throw new Exception ("[list_exist] Could not bind parameters.");
    }
    if (!$list_exist->execute()) {
        throw new Exception ("[list_exist] Could not execute query.");
    }

    $list_exist->store_result();
    if ($list_exist->num_rows > 0) {
        // watchlist already exists. Retrieve watchlist id:
        $list_exist->bind_result($watchlist_id);
        $list_exist->fetch();
        $list_exist->close();

        return $watchlist_id;
    } else {
        // No watchlist exists, create a new one:
        $add_watchlist = $conn->prepare("INSERT INTO watchlist_user(uid)
                                        VALUES (?)");

        if (!$add_watchlist->bind_param("i", $user_id)) {
            throw new Exception ("[add_watchlist] Could not bind parameters.");
        }
        if (!$add_watchlist->execute()) {
            throw new Exception ("[add_watchlist] Could not execute query.");
        }

        $watchlist_id = $conn->insert_id;
        $add_watchlist->close();

        // Give the watchlist a name:
        $watchlist_name = $conn->prepare("INSERT INTO watchlist_name(wid, name)
                                         VALUES (?, ?)");

        if (!$watchlist_name->bind_param("is", $watchlist_id, $watchlist)) {
            throw new Exception ("[watchlist_name] Could not bind parameters.");
        }
        if (!$watchlist_name->execute()) {
            throw new Exception ("[watchlist_name] Could not execute query.");
        }

        $watchlist_name->close();
    }

    return $watchlist_id;
}

// update_watchlists deletes all occurences where the item is added to other
// watchlists, than the watchlist where the item needs to be added.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $user_id: The id of the user whose watchlists will be updated.
//  $item_id: The id of the item to be deleted from the watchlists.
//  $watchlist_id: The id of the watchlist that the item should not be deleted
//                from
//
// Output: None
function update_watchlists($conn, $user_id, $item_id, $watchlist_id) {
    // Prepare SQL statement to delete the item in all other watchlists:
    $delete_item = $conn->prepare(
        "DELETE watchlist_item FROM watchlist_item
        JOIN watchlist_name ON watchlist_item.wid = watchlist_name.wid
        JOIN watchlist_user ON watchlist_name.wid = watchlist_user.wid
        WHERE watchlist_user.uid = ? AND watchlist_item.iid = ?
        AND watchlist_item.wid != ?"
    );

    if (!$delete_item->bind_param("iii", $user_id, $item_id, $watchlist_id)) {
        throw new Exception ("[delete_item] Could not bind parameters.");
    }
    if (!$delete_item->execute()) {
        throw new Exception ("[delete_item] Could not execute query.");
    }

    $delete_item->close();
}

// add_item_to_watchlist adds an item to the watchlist with id: $watchlist_id.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $watchlist_id: The watchlist to which the item should be added.
//  $item_id: The id of the item to be added.
// Output:
//  None
function add_item_to_watchlist($conn, $watchlist_id, $item_id, $ss_link) {
    // Prepare SQL statement to check if the item is already in the watchlist:
    $check_item = $conn->prepare(
        "SELECT wiid FROM watchlist_item
        JOIN item_ssid ON watchlist_item.iid = item_ssid.iid
        WHERE watchlist_item.wid = ? and item_ssid.ss_link = ?"
    );

    if (!$check_item->bind_param("is", $watchlist_id, $ss_link)) {
        throw new Exception ("[check_item] Could not bind parameters.");
    }
    if (!$check_item->execute()) {
        throw new Exception ("[check_item] Could not execute query.");
    }

    $check_item->store_result();
    if ($check_item->num_rows > 0) {
        // The item is already in the watchlist:
        $check_item->free_result();
        $check_item->close();
    } else {
        // The item is not in the watchlist:
        $check_item->free_result();
        $check_item->close();

        // Prepare SQL statement to add the item to the watchlist:
        $add_item = $conn->prepare("INSERT INTO watchlist_item(wid, iid)
                                    VALUES (?, ?)");

        if (!$add_item->bind_param("ii", $watchlist_id, $item_id)) {
            throw new Exception ("[add_item] Could not bind parameters.");
        }
        if (!$add_item->execute()) {
            throw new Exception ("[add_item] Could not execute query.");
        }

        $add_item->close();
    }
}

// remove_item removes a row from watchlist_item.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $title: The title of the item to be removed.
//  $picture_url: The picture URL of the item to be removed.
//  $ss_link: The link to the streaming service of the item to be removed.
//  $email: The email of the user.
//  $name: The name of the watchlist to be removed.
//
// Output: None.
function remove_item($conn, $title, $picture_url, $ss_link, $email, $name) {
    // Prepare SQL statement to remove an item id and its corresponding
    // watchlist id from the table watchlist_item.
    $remove_item =
       "DELETE watchlist_item FROM watchlist_item
        JOIN item ON item.iid = watchlist_item.iid
        JOIN item_ssid ON item_ssid.iid = item.iid
        JOIN watchlist_name ON watchlist_name.wid = watchlist_item.wid
        JOIN watchlist_user ON watchlist_user.wid = watchlist_name.wid
        JOIN user ON user.uid = watchlist_user.uid
        WHERE item.title = ? AND item.picture_url = ? AND item_ssid.ss_link = ?
        AND user.email = ? AND watchlist_name.name = ?";

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

// calls the right function to execute item removal
function remove_from_watchlist($title, $picture, $service_url, $watchlist) {
    global $conn;
    if (!($watchlist == "future watching" || $watchlist == "currently watching" || $watchlist == "finished watching")) {
        exit("Invalid watchlist");
    }
    try {
        remove_item(
            $conn, $title, $picture, $service_url, $_COOKIE['checker'], $watchlist
        );
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);
    }
}

// calls the right functions to add an item to the watchlist
//
// in: $title: Title of the movie/serie.
//     $picture: Picture url.
//     $service_url: Url to streaming service.
//     $service: Name of the streaming service.
//     $watchlist: Name of the watchlist where item should be added.
function add_to_watchlist($conn, $title, $picture, $service_url, $service, $watchlist) {
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

    // Retrieve necessary variable to add the item to the watchlist:
    try {
        $user_id = retrieve_uid($conn, $_COOKIE['checker']);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);

        $conn->rollback();
    }
    if (!$user_id) { exit("No user found."); }

    // Add the watchlist to the database and retrieve the corresponding watchlist id:
    try {
        $watchlist_id = retrieve_wid($conn, $user_id, $watchlist);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);

        $conn->rollback();
    }

    // Check if the item doesn't already exist in another watchlist:
    try {
        update_watchlists($conn, $user_id, $item_id, $watchlist_id);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);

        $conn->rollback();
    }

    // Finally add the item to the watchlist:
    try {
        add_item_to_watchlist($conn, $watchlist_id, $item_id, $service_url);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);

        $conn->rollback();
    }

    // If code reaches this point without errors then commit the data in the database
    $conn->commit();
    return;
}
