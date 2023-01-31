<?php
$q = $_POST['movieTitle'];

require_once "../account_verification/session_token.php";
require_once "../../../../conn/db.php";

function check_mail($conn, $email) {
    $check_mail = $conn->prepare(
        "SELECT email FROM user WHERE email = ?"
    );

    if (!$check_mail->bind_param("s", $email)) {
        throw new Exception ("[check_mail] Could not bind parameters.");
    }
    if (!$check_mail->execute()) {
        throw new Exception ("[check_mail] Could not execute query.");
    }

    $check_mail->store_result();
    if ($check_mail->num_rows === 0) {
        $check_mail->close();
        return false;
    } else {
        $check_mail->close();
        return true;
    }
}

function retrieve_short($conn, $email) {
    $retrieve_short = $conn->prepare(
        "SELECT region.short_region
        FROM user
        LEFT JOIN region ON user.rid = region.rid
        WHERE email = ?"
    );

    if (!$retrieve_short->bind_param("s", $email)) {
        throw new Exception ("[retrieve_short] Could not bind parameters.");
    }
    if (!$retrieve_short->execute()) {
        throw new Exception ("[retrieve_short] Could not execute query.");
    }

    $retrieving_result = $retrieve_short->get_result();
    $retrieving_row = $retrieving_result->fetch_assoc();

    if(isset($retrieving_row['short_region'])) {
        // var_dump($retrieving_row['short_region']);
        return $retrieving_row['short_region'];
    } else {
        return 'nl';
    }
}

// Get the current region or take nl as default.
if (isset($_COOKIE['login']) && isset($_COOKIE['checker'])) {
    if (!check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])) {
        $country = 'nl';
    } else {
        if (check_mail($conn, $_COOKIE['checker'])) {
            $country = retrieve_short($conn, $_COOKIE['checker']);
        }
    }
} else {
    $country = 'nl';
}

// title needs to be checked before it is used anywhere...
function buildUrl($title, $conn, $country) {
    if(!preg_match('/^\w+( \w+)*$/', $title)) {
        return NULL;
    }
    $title = preg_replace('/\s+/', '%20', $title);
    $startUrl = 'https://streaming-availability.p.rapidapi.com/v2/search/title?title=';
    // maybe change these to constants
    // country, type and language settings
    $countrySetting = '&country=';
    $typeSetting = '&type=';
    $languageSetting = '&output_language=';

    // $country = 'us';

    $type = 'all';
    $language = 'en';
    return "{$startUrl}{$title}{$countrySetting}{$country}{$typeSetting}{$type}{$languageSetting}{$language}";
}

// 4a90c0cc84mshe4455be523837acp163521jsnc5e366760b07
// This code is taken from the rapid-api website
// They provide code snippets for the apis available on their website
// basically their way of documentation since it isnt always available
function apiCall($apiUrl) {
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $apiUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: streaming-availability.p.rapidapi.com",
            "X-RapidAPI-Key: cf82865596msh45d9207f056c08dp141eedjsn61b1f53b4bd3"
        ],
    ]);

    //"X-RapidAPI-Key: 4a90c0cc84mshe4455be523837acp163521jsnc5e366760b07"

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return $response;
    }
}

/**
 * 1. turn json string in php object
 * 2. get relevant data per movie
 * 3. add relevant movie data to new php object
 * 4. return new object once all relevant movie data is added for all the
 *      movies
 */

class movieDetails {
    var $id;
    var $movieTitle;
    var $moviePoster;
    var $prime;
    var $netflix;
    var $disney;
    var $hbo;
    var $hulu;
    var $apple;
}

function condenseData($response, $country) {
    // $country = "us";
    $obj = json_decode($response);
    $result = array();
    $i = 0;

    foreach ($obj->result as $data) {
        $movie = new movieDetails;
        $movie->movieTitle = $data->title;
        if(!empty($data->posterURLs->{"500"})) {
            $movie->moviePoster = $data->posterURLs->{"500"};
        } else {
            continue;
        }
        if(!empty($data->streamingInfo->{$country})) {
            if(!empty($data->streamingInfo->{$country}->prime)) {
                $movie->prime = $data->streamingInfo->{$country}->prime[0]->link;
            }
            if(!empty($data->streamingInfo->{$country}->netflix)) {
                $movie->netflix = $data->streamingInfo->{$country}->netflix[0]->link;
            }
            if(!empty($data->streamingInfo->{$country}->disney)) {
                $movie->disney = $data->streamingInfo->{$country}->disney[0]->link;
            }
            if(!empty($data->streamingInfo->{$country}->hbo)) {
                $movie->hbo = $data->streamingInfo->{$country}->hbo[0]->link;
            }
            if(!empty($data->streamingInfo->{$country}->hulu)) {
                $movie->hulu = $data->streamingInfo->{$country}->hulu[0]->link;
            }
            if(!empty($data->streamingInfo->{$country}->apple)) {
                $movie->apple = $data->streamingInfo->{$country}->apple[0]->link;
            }
        } else {
            continue;
        }
        $movie->id = $i;
        $result[$i] = $movie;
        $i++;
    }
    // var_dump($result);
    return $result;
}

$actualDataToSend = condenseData(apiCall(buildUrl($q, $conn, $country)), $country);
if(!isset($_SESSION)) { session_start(); }
$_SESSION['displayed_cards'][0] = $actualDataToSend;

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($actualDataToSend);
?>
