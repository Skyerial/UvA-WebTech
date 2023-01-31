<?php
require_once("api_selfmade.php");

$parts = explode('/', $_SERVER["REQUEST_URI"]);

// if first part isnt ~danielo give 404
if ($parts[1] != "v1") {
    http_response_code(404);
    exit;
}

// set to id if available otherwise null
$id = $parts[2] ?? null;

var_dump($id);
?>