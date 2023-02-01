<?php
// title needs to be checked before it is used anywhere...
function build_url($title, $conn, $country) {
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
function api_call($apiUrl) {
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
    // cf82865596msh45d9207f056c08dp141eedjsn61b1f53b4bd3

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

class movie_details {
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

function condense_data($response, $country) {
    $obj = json_decode($response);
    $result = array();
    $i = 0;

    foreach ($obj->result as $data) {
        $movie = new movie_details;
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
?>
