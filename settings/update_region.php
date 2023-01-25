<?php 
require_once "../../../../conn/db.php";
$body = file_get_contents('php://input');
$json = json_decode($body);
$email = $json->email;
$region = $json->region;
$update_region = $conn->prepare("UPDATE user SET rid = (SELECT rid FROM region WHERE region = ?) WHERE email = ?");
if (!$update_region->bind_param("ss", $region, $email)) {
    exit("Could not bind parameters.");
}
if (!$update_region->execute()) {
    exit("Could not execute query.");
}
?>