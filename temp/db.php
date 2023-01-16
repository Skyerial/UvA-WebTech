<?php

$servername = 'localhost';
$username = 'tijnk';
$password = 'DIQrFzgZrSxzUbMERkxwLKoTbgTBunHX';
$database = "main";

$conn = mysqli_connect($servername, $username, $password, "$database");
if (!$conn) {
    die('Could not connect to the server:' .mysql_error());
}

?>