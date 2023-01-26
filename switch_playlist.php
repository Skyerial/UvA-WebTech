<!-- https://phppot.com/php/php-curl-post-json/ -->
<?php
require_once "account_verification/session_token.php";
require_once "/../../../conn/db.php";

function addToPlaylist($movieTitle, $moviePoster, $service_url, $service, $playlist) {
    $url = 'https://webtech-in01.webtech-uva.nl/~marcob/add_to_playlist.php';

    $movie = array( 'movieTitle' => "$movieTitle",
        'moviePoster' => "$moviePoster",
        'service' => "$service",
        'service_url' => "$service_url",
        'playlist' => "$playlist"
    );
    // encoding the request data as JSON which will be sent in POST
    $encodedData = json_encode($movie);
    // initiate curl with the url to send request
    $curl = curl_init($url);

    // return CURL response
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Send request data using POST method
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

    // Data conent-type is sent as JSON
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type:application/json'
    ));
    curl_setopt($curl, CURLOPT_POST, true);

    // Curl POST the JSON data to send the request
    curl_setopt($curl, CURLOPT_POSTFIELDS, $encodedData);

    // execute the curl POST request and send data
    $result = curl_exec($curl);
    curl_close($curl);

    exit("$result");
    // if required print the curl response
}


$body = file_get_contents('php://input');
$json = json_decode($body);
$id = $json->id;
$playlist = $json->playlist;


if(!isset($_SESSION)) { session_start(); }

// Check if the user is logged in, if not redirect the user to the login page.
if (isset($_COOKIE['login']) && isset($_COOKIE['checker'])) {
    if (!check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])) {
        if (is_resource($conn)) { mysqli_close($conn); }
        header("Location: login.php");
        exit("conn");
    }
} else {
    if (is_resource($conn)) { mysqli_close($conn); }
    header("Location: login.php");
    exit("cookie");
}

$data = $_SESSION['displayed_cards'][$id];
//var_dump($data["movieTitle"]);
$title = $data["movieTitle"];
$poster = $data["moviePoster"];

if($data["prime"]) {
    $service_url = $data["prime"];
    addToPlaylist($title, $poster, $service_url, "prime", $playlist);
}
sleep(3);
if($data["netflix"]) {
    $service_url = $data["netflix"];
    addToPlaylist($title, $poster, $service_url, "netflix", $playlist);
}
sleep(3);
if($data["disney"]) {
    $service_url = $data["disney"];
    addToPlaylist($title, $poster, $service_url, "disney", $playlist);
}
sleep(3);
if($data["hbo"]) {
    $service_url = $data["hbo"];
    addToPlaylist($title, $poster, $service_url, "hbo", $playlist);
}
sleep(3);
if($data["hulu"]) {
    $service_url = $data["hulu"];
    addToPlaylist($title, $poster, $service_url, "hulu", $playlist);
}
sleep(3);
if($data["apple"]) {
    $service_url = $data["apple"];
    addToPlaylist($title, $poster, $service_url, "apple", $playlist);
}

?>