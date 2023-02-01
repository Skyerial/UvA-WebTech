<?php
// Builds the request url which can be used to ask data from the api
//
// IN:
//  $title: title of the film or serie
//  $conn: connection to the database
//  $country: country for the services
// OUT:
//  the url needed for the request to check where the film or serie is watchable 
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

// This code is taken from the rapid-api website 
// https://rapidapi.com/movie-of-the-night-movie-of-the-night-default/api/streaming-availability
// They provide code snippets for the apis available on their website
// basically their way of documentation since it isnt always available
//
// Call the api and return all the movie data that the api provides
//
// IN:
//  $apiUrl: url needed for the request to check where the film or serie is watchable
// OUT:
//  json string with all movie or series information from the api
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
            "X-RapidAPI-Key: cf82865596msh45d9207f056c08dp141eedjsn61b1f53b4bd3"
        ],
    ]);

    // different API keys to use if calls on one are empty for the day
    // 4a90c0cc84mshe4455be523837acp163521jsnc5e366760b07"
    // cf82865596msh45d9207f056c08dp141eedjsn61b1f53b4bd3
    // 1f4f387fb7msh9026d962fb7b4d6p161790jsn6a3628cbcf9b

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return $response;
    }
}

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

// Takes json string and takes out the useful information and outputs it as json
//
// IN:
//  $response: json string with all available information
//  $country: country of where the the movie or series is available on what services
// OUT:
//  condensed form of the json string with only the relevant information as json
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
    // print_r($result);
    return $result;
}
?>
