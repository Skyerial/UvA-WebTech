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

class movieDetails {
    var $movieTitle;
    var $moviePoster;
    var $prime;
    var $netflix;
    var $disney;
    var $hbo;
    var $hulu;
    var $apple;
}

$movie = new movieDetails;
$card_counter = 0;
$displayed_cards = array();

function get_data() {
    global $displayed_cards;
    var_dump($displayed_cards);
    return $displayed_cards;
}
function fill_class($movieTitle, $moviePoster, $ssLink, $service, $playlist) {
    global $movie;

    // new card
    if ($movie->moviePoster != $moviePoster) {
        if ($movie->moviePoster) {
            display_card($movie, $playlist);
            $movie = new movieDetails;
        }
        $movie->movieTitle = htmlspecialchars($movieTitle);
        $movie->moviePoster = htmlspecialchars($moviePoster);
    }
    $ssLink = htmlspecialchars($ssLink);

    if ($service == "prime") {
        $movie->prime = $ssLink;
    } else if ($service == "netflix") {
        $movie->netflix = $ssLink;
    } else if ($service == "disney") {
        $movie->disney = $ssLink;
    } else if ($service == "hbo") {
        $movie->hbo = $ssLink;
    } else if ($service == "hulu") {
        $movie->hulu = $ssLink;
    } else if ($service == "apple") {
        $movie->apple = $ssLink;
    }
}

function display_card($movie, $playlist) {
    global $card_counter;
    global $displayed_cards;
    $displayed_cards[] = array('id' => "$card_counter",
                                'movieTitle' => "$movie->movieTitle",
                                'moviePoster' => "$movie->moviePoster",
                                'prime' => "$movie->prime",
                                'netflix' => "$movie->netflix",
                                'disney' => "$movie->disney",
                                'hbo' => "$movie->hbo",
                                'hulu' => "$movie->hulu",
                                'apple' => "$movie->apple",
                                );
                                
    $current = "currently watching";
    $future = "future watching";
    $finished = "finished watching";
    //var_dump($displayed_cards);
    ?>
        <div class="card" id="card<?=$card_counter?>" style="opacity: 1 !important;">
            <div class="imagebox">
                <img class="poster" src="<?=$movie->moviePoster?>"/>
                <div class="streamingservicebox">
                    <?php if($movie->prime) {?>
                        <a href="<?=$movie->prime?>" target="_blank" class="streamingservice">
                            <img src="streaming_img/prime.png">
                        </a>
                    <?php } if($movie->netflix) {?>
                        <a href="<?=$movie->netflix?>" target="_blank" class="streamingservice">
                            <img src="streaming_img/netflix.png">
                        </a>
                    <?php } if($movie->disney) {?>
                        <a href="<?=$movie->disney?>" target="_blank" class="streamingservice">
                            <img src="streaming_img/disney.png">
                        </a>
                    <?php } if($movie->hbo) {?>
                        <a href="<?=$movie->hbo?>" target="_blank" class="streamingservice">
                            <img src="streaming_img/hbo.png">
                        </a>
                    <?php } if($movie->hulu) {?>
                        <a href="<?=$movie->hulu?>" target="_blank" class="streamingservice">
                            <img src="streaming_img/hulu.png">
                        </a>
                    <?php } if($movie->apple) {?>
                        <a href="<?=$movie->prime?>" target="_blank" class="streamingservice">
                            <img src="streaming_img/apple.png">
                        </a>
                    <?php }?>
                </div>
            </div>
            <div>
                <h3><?=$movie->movieTitle?></h3>
            </div>
            <div class="hover-content">
                <?php if ($playlist == "future watching") {
                ?>
                <a href="javascript:void(0)" onclick="cur_watching(<?=$card_counter?>); return false;" class="cardbutton">Current</a>
                <a href="javascript:void(0)" onclick="watched(<?=$card_counter?>); return false;" class="cardbutton">Watched</a>
                <a href="javascript:void(0)" onclick="delete_item('<?=$card_counter?>' , '<?=$future?>'); return false;" class="cardbutton">Delete</a>
                <?php
                } else if ($playlist == "currently watching") {
                ?>
                <a href="javascript:void(0)" onclick="to_watch(<?=$card_counter?>); return false;" class="cardbutton">Future</a>
                <a href="javascript:void(0)" onclick="watched(<?=$card_counter?>); return false;" class="cardbutton">Watched</a>
                <a href="javascript:void(0)" onclick="delete_item('<?=$card_counter?>' , '<?=$current?>'); return false;" class="cardbutton">Delete</a>
                <?php
                } else if ($playlist == "finished watching") {
                ?>
                <a href="javascript:void(0)" onclick="to_watch(<?=$card_counter?>); return false;" class="cardbutton">Future</a>
                <a href="javascript:void(0)" onclick="cur_watching(<?=$card_counter?>); return false;" class="cardbutton">Current</a>
                <a href="javascript:void(0)" onclick="delete_item('<?=$card_counter?>','<?=$finished?>'); return false;" class="cardbutton">Delete</a>
                <?php
                }
                ?>
            </div>
        </div>
    <?php
    $card_counter++;
}

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
function retrieve_playlist($conn, $email, $playlist) {
    // Prepare SQL statement to retrieve all items with their characteristics:
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

    if (!$retrieve->bind_param("ss", $email, $playlist)) {
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
        //echo $row['title'] . " " . $row['streaming_service'];
        fill_class($row['title'], $row['picture_url'], $row['ss_link'], $row['streaming_service'], $playlist);
    }
    //display last card
    global $movie;
    if($movie->moviePoster) {
        display_card($movie, $playlist);
    }
    $movie = new MovieDetails;
}
?>

