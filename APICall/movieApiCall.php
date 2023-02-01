<?php
$q = $_POST['movieTitle'];

// title needs to be checked before it is used anywhere...
function buildUrl($title) {
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
    $country = 'nl';
    $type = 'all';
    $language = 'en';
    return "{$startUrl}{$title}{$countrySetting}{$country}{$typeSetting}{$type}{$languageSetting}{$language}";
}

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
            "X-RapidAPI-Key: 4a90c0cc84mshe4455be523837acp163521jsnc5e366760b07"
        ],
    ]);

    //"X-RapidAPI-Key: 4a90c0cc84mshe4455be523837acp163521jsnc5e366760b07"

    //"X-RapidAPI-Key: cf82865596msh45d9207f056c08dp141eedjsn61b1f53b4bd3"

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

function condenseData($response) {
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
        if(!empty($data->streamingInfo->nl)) {
            if(!empty($data->streamingInfo->nl->prime)) {
                $movie->prime = $data->streamingInfo->nl->prime[0]->link;
            }
            if(!empty($data->streamingInfo->nl->netflix)) {
                $movie->netflix = $data->streamingInfo->nl->netflix[0]->link;
            }
            if(!empty($data->streamingInfo->nl->disney)) {
                $movie->disney = $data->streamingInfo->nl->disney[0]->link;
            }
            if(!empty($data->streamingInfo->nl->hbo)) {
                $movie->hbo = $data->streamingInfo->nl->hbo[0]->link;
            }
            if(!empty($data->streamingInfo->nl->hulu)) {
                $movie->hulu = $data->streamingInfo->nl->hulu[0]->link;
            }
            if(!empty($data->streamingInfo->nl->apple)) {
                $movie->apple = $data->streamingInfo->nl->apple[0]->link;
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

$actualDataToSend = condenseData(apiCall(buildUrl($q)));

if(!isset($_SESSION)) { session_start(); }
$_SESSION['displayed_cards'][0] = $actualDataToSend;

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($actualDataToSend);
?>
