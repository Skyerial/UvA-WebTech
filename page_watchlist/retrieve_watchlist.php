<?php

////////////////////////////////////////////////////////////////////////////////
// Globals:
////////////////////////////////////////////////////////////////////////////////
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

$movie = new movie_details;
$card_counter = 0;
$displayed_cards = array();

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "../account_verification/session_token.php";
require_once "../../../../conn/db.php";

////////////////////////////////////////////////////////////////////////////////
// Error log file definement:
////////////////////////////////////////////////////////////////////////////////
define("ERROR_LOG_FILE", "../errorLog/error.txt");

////////////////////////////////////////////////////////////////////////////////
// Functions:
////////////////////////////////////////////////////////////////////////////////

// Collects the data for a card and dynamically stores it in a global.
// Displays the card if all data is collected.
function fill_class($movieTitle, $moviePoster, $ssLink, $service, $watchlist) {
    global $movie;

    // Create new card if all data for prev. card is collected.
    if ($movie->moviePoster != $moviePoster) {
        if ($movie->moviePoster) {
            display_card($movie, $watchlist);
            $movie = new movie_details;
        }
        $movie->movieTitle = $movieTitle;
        $movie->moviePoster = $moviePoster;
    }

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

// display_card pushes a card into html and stores the data in a global array.
function display_card($movie, $watchlist) {
    global $card_counter;
    global $displayed_cards;

    //add card to backend cards array
    $new_card = clone($movie);
    $displayed_cards[] = $new_card;

    //sanitize before pushing to html
    foreach($movie as &$value) {
        $value = htmlspecialchars($value);
    }

    $current = "currently watching";
    $future = "future watching";
    $finished = "finished watching";
    ?>
        <div class="card" id="card<?=$card_counter?>" style="opacity: 1 !important;">
            <div class="imagebox">
                <img class="poster" src="<?=$movie->moviePoster?>"/>
                <div class="streamingservicebox">
                    <?php if($movie->prime) {?>
                        <a href="<?=$movie->prime?>" target="_blank" class="streamingservice">
                            <img src="../streaming_img/prime.png">
                        </a>
                    <?php } if($movie->netflix) {?>
                        <a href="<?=$movie->netflix?>" target="_blank" class="streamingservice">
                            <img src="../streaming_img/netflix.png">
                        </a>
                    <?php } if($movie->disney) {?>
                        <a href="<?=$movie->disney?>" target="_blank" class="streamingservice">
                            <img src="../streaming_img/disney.png">
                        </a>
                    <?php } if($movie->hbo) {?>
                        <a href="<?=$movie->hbo?>" target="_blank" class="streamingservice">
                            <img src="../streaming_img/hbo.png">
                        </a>
                    <?php } if($movie->hulu) {?>
                        <a href="<?=$movie->hulu?>" target="_blank" class="streamingservice">
                            <img src="../streaming_img/hulu.png">
                        </a>
                    <?php } if($movie->apple) {?>
                        <a href="<?=$movie->apple?>" target="_blank" class="streamingservice">
                            <img src="../streaming_img/apple.png">
                        </a>
                    <?php }?>
                </div>
            </div>
            <div>
                <h3><?=$movie->movieTitle?><span class="tooltiptext"><?=$movie->movieTitle?></span></h3>
            </div>
            <div class="hover-content">
                <?php if ($watchlist == "future watching") { ?>
                    <a href="javascript:void(0)" onclick="curWatching(<?=$card_counter?>); return false;" class="cardbutton">
                        <i class="fa-solid fa-eye"></i>
                        <span class="tooltiptext">Currently Watching</span>
                    </a>
                    <a href="javascript:void(0)" onclick="watched(<?=$card_counter?>); return false;" class="cardbutton">
                        <i class="fa-solid fa-eye-slash"></i>
                        <span class="tooltiptext">Finished Watching</span>
                    </a>
                    <a href="javascript:void(0)" onclick="deleteItem('<?=$card_counter?>' , '<?=$future?>'); return false;" class="cardbutton">
                        <i style = "color: red;" class="fa-solid fa-trash"></i>
                        <span class="tooltiptext">Delete</span>
                    </a>
                <?php } else if ($watchlist == "currently watching") { ?>
                    <a href="javascript:void(0)" onclick="toWatch(<?=$card_counter?>); return false;" class="cardbutton">
                        <i class="fa-solid fa-clock"></i>
                        <span class="tooltiptext">Future Watching</span>
                    </a>
                    <a href="javascript:void(0)" onclick="watched(<?=$card_counter?>); return false;" class="cardbutton">
                        <i class="fa-solid fa-eye-slash"></i>
                        <span class="tooltiptext">Finished Watching</span>
                    </a>
                    <a href="javascript:void(0)" onclick="deleteItem('<?=$card_counter?>' , '<?=$current?>'); return false;" class="cardbutton">
                        <i style = "color: red;" class="fa-solid fa-trash"></i>
                        <span class="tooltiptext">Delete</span>
                    </a>
                <?php } else if ($watchlist == "finished watching") { ?>
                    <a href="javascript:void(0)" onclick="toWatch(<?=$card_counter?>); return false;" class="cardbutton">
                        <i class="fa-solid fa-clock"></i>
                        <span class="tooltiptext">Future Watching</span>
                    </a>
                    <a href="javascript:void(0)" onclick="curWatching(<?=$card_counter?>); return false;" class="cardbutton">
                        <i class="fa-solid fa-eye"></i>
                        <span class="tooltiptext">Currently Watching</span>
                    </a>
                    <a href="javascript:void(0)" onclick="deleteItem('<?=$card_counter?>','<?=$finished?>'); return false;" class="cardbutton">
                        <i style = "color: red;" class="fa-solid fa-trash"></i>
                        <span class="tooltiptext">Delete</span>
                    </a>
                <?php } ?>
            </div>
        </div>
    <?php
    $card_counter++;
}

// retrieve_watchlist retrieves the title, picture URL and streaming service
// from a watchlist named $name from a user with $uid.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $user_id: The id of the user, whose watchlist must be retrieved.
//  $name: The name of the watchlist to be retrieved.
//
// Side effect:
//  All items of the watchlist are pushed to HTML.
function retrieve_watchlist($conn, $email, $watchlist) {
    // Prepare SQL statement to retrieve all items with their characteristics:
    $retrieve = "SELECT DISTINCT
                item.title, item.picture_url, item_ssid.ss_link,
                streaming_service.streaming_service
                FROM item
                JOIN item_ssid ON item.iid = item_ssid.iid
                JOIN streaming_service ON item_ssid.ssid = streaming_service.ssid
                JOIN watchlist_item ON item.iid = watchlist_item.iid
                JOIN watchlist_name ON watchlist_item.wid = watchlist_name.wid
                JOIN watchlist_user ON watchlist_name.wid = watchlist_user.wid
                JOIN user ON watchlist_user.uid = user.uid
                WHERE user.email = ? AND watchlist_name.name = ?";

    $retrieve = $conn->prepare($retrieve);

    if (!$retrieve->bind_param("ss", $email, $watchlist)) {
    throw new Exception ("[retrieve_watchlist] Could not bind parameters.");
    }
    if (!$retrieve->execute()) {
    throw new Exception ("[retrieve_watchlist] Could not execute query.");
    }
    $result = $retrieve->get_result();
    while ($row = $result->fetch_assoc()) {
        //print_r($row['title'] );
        fill_class($row['title'], $row['picture_url'], $row['ss_link'], $row['streaming_service'], $watchlist);
    }

    //display last card if no more data is coming in.
    global $movie;
    if($movie->moviePoster) {
        display_card($movie, $watchlist);
    }
    $movie = new movie_details;
}

// Push watchlist cards to html and save the data in backend session.
function display_watchlist($conn, $watchlist) {
    try {
        retrieve_watchlist($conn, $_COOKIE['checker'], $watchlist);
        global $displayed_cards;
        if (!$displayed_cards) {
            return 1;
        }
        $_SESSION['displayed_cards'][1] = $displayed_cards;
        return 0;
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);
        return 0;
    }
}
?>
