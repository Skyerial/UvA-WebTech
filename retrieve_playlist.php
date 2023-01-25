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
    if (!$result) { 
        ?>
            <div class="banner">
                <img src="streaming_img/watchlist.png">
                <h3> Your current watchlist is emtpy, please click the "icon" to add to your current watchlist. <h3>
            </div>
    <?php
    $retrieve->close(); 
    return;
    }

    while ($row = $result->fetch_assoc()) {
        // Note: the variables are arrays, so title = $row['title'];
        // Note: $row['streaming_service'] is all lowercase (e.g. netflix);
        // Note: after the close-statement, the variables no longer exist!
        //echo $row['title'] . " " . $row['picture_url'] . " " . $row['streaming_service'];
        ?>
            <div class="card" id="card" style="opacity: 1 !important;">
                <div class="imagebox">
                    <img class="poster" id="poster${i}" value="${moviePoster}" src="<?= $row['picture_url']?>"/>
                    <div class="streamingservicebox">
                        <a href="https://www.netflix.com/" class="streamingservice">
                            <img src="streaming_img/<?=$row['streaming_service']?>.png">
                        </a>
                    </div>
                </div>
                <div>
                    <h3 id="title${i}" value="${movieTitle}"><?= $row['title']?></h3>
                </div>
                <div class="hover-content">
                    <?php if ($name == "future watching") {
                    ?>
                    <a href="javascript:void(0)" onclick="" class="cardbutton">Current</a>
                    <a href="javascript:void(0)" onclick="" class="cardbutton">Watched</a>
                    <a href="javascript:void(0)" onclick="" class="cardbutton">Delete</a>
                    <?php
                    } else if ($name == "currently watching") {
                    ?>
                    <a href="javascript:void(0)" onclick="" class="cardbutton">Future</a>
                    <a href="javascript:void(0)" onclick="" class="cardbutton">Watched</a>
                    <a href="javascript:void(0)" onclick="" class="cardbutton">Delete</a>
                    <?php
                    } else if ($name == "finished watching") {
                    ?>
                    <a href="javascript:void(0)" onclick="" class="cardbutton">Future</a>
                    <a href="javascript:void(0)" onclick="" class="cardbutton">Current</a>
                    <a href="javascript:void(0)" onclick="" class="cardbutton">Delete</a>
                    <?php
                    }
                    ?>
                </div>
            </div>
        <?php
    }

    $retrieve->close();
}

?>