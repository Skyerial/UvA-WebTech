<?php
////////////////////////////////////////////////////////////////////////////////
// this is the api endpoint. It handles the requests that start with v1 after
// the website url. Calling this endpoint gives access to the api we use for
// getting the movie data and can return that same data to the caller.
////////////////////////////////////////////////////////////////////////////////

require_once "../APICall/movie_api_call.php";

// url for using our movie finder should come in like:
// header= "apiKey:<your api key findable in settings>"
// webtech-uva.nl/v1/movie?title=<title>%26country=<country(optional)>
// EXAMPLE
// curl -v -H "apiKey:822d9746853668f907a5cd20eba5215d3a6b72c4a9452dfac502bc44e3951b2f" 
// https://webtech-in01.webtech-uva.nl/v1/movie?title=spiderman%26country=nl

// url for getting a playlist from database should look like:
// webtech-uva.nl/v1/playlist?name="<name of playlist>"

// check_api checks if an API key exists in the database.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $api_key: The key which existence should be checked.
//
// Output: True if the API key exists, false otherwise.
function check_api($conn, $api_key) {
    $check_api = $conn->prepare(
        "SELECT api_key FROM user WHERE api_key = ?"
    );

    if (!$check_api->bind_param("s", $api_key)) {
        throw new Exception ("[check_api] Could not bind parameters.");
    }
    if (!$check_api->execute()) {
        throw new Exception ("[check_api] Could not execute query.");
    }

    $result = $check_api->get_result();
    if ($result->num_rows > 0) {
        $check_api->close();
        return true;
    } else {
        $check_api->close();
        return false;
    }
}

$parts = explode('/', $_SERVER["REQUEST_URI"]);
if ($parts[1] != "v1") {
    http_response_code(404);
    exit;
}

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
$country = explode('country=', $query_options[1]) ?? 'nl';

// based on the first part of query a function gets called to give the appropriate
// data as a response
// DISCLAIMER! 
// at the moment only the api we use can be called to get the same results as shown on the homepage
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
