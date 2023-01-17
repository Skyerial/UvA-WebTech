<?php

require_once "temp/db.php";

if(isset($_GET['email_token'])){
    $email_token = $_GET['email_token'];
    $query = 'UPDATE user SET status = 1
              WHERE activation_code="'.$email_token.'"';
    if($conn->query($query)){
        if (is_resource($conn)) {
            mysqli_close($conn);
        }
        header("Location:code_received.php");
        exit();
    }
}

?>