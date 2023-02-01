<?php
require_once "api_selfmade.php";
require_once "../APICall/movie_api_call.php";

// url for using our movie finder should come in like:
// header= "apiKey:<your api key findable in settings"
// webtech-uva.nl/v1/movie?title=<title>%26country=<country(optional)>
// EXAMPLE
// curl -v -H "apiKey:822d9746853668f907a5cd20eba5215d3a6b72c4a9452dfac502bc44e3951b2f" 
// https://webtech-in01.webtech-uva.nl/~danielo/v1/movie?title=spiderman%26country=nl

// url for getting a playlist from database should look like:
// webtech-uva.nl/v1/playlist?name="<name of playlist>"

$parts = explode('/', $_SERVER["REQUEST_URI"]);

// if first part isnt ~danielo give 404
// this needs to be changed if this goes live on main
if ($parts[1] != "v1") {
    http_response_code(404);
    exit;
}
// if ($parts[1] != "~danielo") {
//     http_response_code(404);
//     exit;
// }

// get headers
$headers = array();
foreach (getallheaders() as $name => $value) {
    $headers[$name] = $value;
}
var_dump($headers);
if (check_api($conn, $headers['apiKey'])) {
} else {
    var_dump("And you are? (API key does not exist in our database)");
    exit;
}

$api_query = $parts[2] ??  null;
$query_parts = explode('?', $api_query);
$query_options = explode('%26', $query_parts[1]);

$option = $query_parts[0];
$title = explode('title=', $query_options[0]);
$country = explode('country=', $query_options[1]);

switch ($query_parts[0]) {
    case "movie":
        $response_data = condense_data(api_call(build_url($title[1], $conn, $country[1])), $country[1]);
        echo json_encode($response_data);
        break;
    case "current":
        // retrieve current
        break;
    case "future":
        // retrieve future
        break;
    case "finished":
        // retrieve finished
        break;
    default:
        var_dump("not a correct option. Choose movie for finding where you can watch a movie and current, future, finished for retrieving a playlist");
        break;
}
?>