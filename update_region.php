<!-- <?php 
// require_once "../../../../conn/db.php";
// $body = file_get_contents('php://input');
// $json = json_decode($body);
// $email = $json->email;
// $region = $json->region;
// $update_region = $conn->prepare("UPDATE user SET rid = (SELECT rid FROM region WHERE region = ?) WHERE email = ?");
// if (!$update_region->bind_param("ss", $region, $email)) {
//     exit("Could not bind parameters.");
// }
// if (!$update_region->execute()) {
//     exit("Could not execute query.");
// }
?> -->

<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "/../../../../conn/db.php";

// Define log file:
define("ERROR_LOG_FILE", "pages/errorLog/error.txt");

////////////////////////////////////////////////////////////////////////////////
// Functions:
////////////////////////////////////////////////////////////////////////////////

// check_mail checks if the email $email exists in the database.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email who must be checked.
//
// Output: If the email exists true, false otherwise.
function check_mail($conn, $email) {
    $check_mail = $conn->prepare(
        "SELECT email FROM user WHERE email = ?"
    );

    if (!$check_mail->bind_param("s", $email)) {
        throw new Exception ("[check_mail] Could not bind parameters.");
    }
    if (!$check_mail->execute()) {
        throw new Exception ("[check_mail] Could not execute query.");
    }

    $check_mail->store_result();
    if ($check_mail->num_rows === 0) {
        $check_mail->close();
        return false;
    } else {
        $check_mail->close();
        return true;
    }
}

// check_region checks if the region $region exists in the database.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $region: The region who must be checked.
//
// Output: If the region exists true, false otherwise.
function check_region($conn, $region) {
    $check_region = $conn->prepare(
        "SELECT region FROM region WHERE region = ?"
    );

    if (!$check_region->bind_param("s", $region)) {
        throw new Exception ("[check_region] Could not bind parameters.");
    }
    if (!$check_region->execute()) {
        throw new Exception ("[check_region] Could not execute query.");
    }

    $check_region->store_result();
    if ($check_region->num_rows === 0) {
        $check_region->close();
        return false;
    } else {
        $check_region->close();
        return true;
    }
}

// update_region changes the region setting of the user with $email.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email of the user whose region setting must be changed.
//  $region: The new region value.
//
// Output: None.
function update_region($conn, $email, $region) {
    // Check if the entered values exist:
    try {
        if (check_mail($conn, $email)) {
            try {
                if (check_region($conn, $region)) {
                    // The entered values exist, update the region:
                    $update_region = $conn->prepare(
                        "UPDATE user
                        SET rid = (SELECT rid FROM region WHERE region = ?)
                        WHERE email = ?");

                    if (!$update_region->bind_param("ss", $region, $email)) {
                        throw new Exception (
                            "[update_region] Could not bind parameters."
                        );
                    }
                    if (!$update_region->execute()) {
                        throw new Exception (
                            "[update_region] Could not execute query."
                        );
                    }
                }
            } catch (Exception $err) {
                $err_file = fopen(ERROR_LOG_FILE, "a");
                fwrite($err_file, $err->getMessage() . "\n");
                fclose($err_file);
            }
        }
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);
    }
}

////////////////////////////////////////////////////////////////////////////////
// Update region:
////////////////////////////////////////////////////////////////////////////////
$body = file_get_contents('php://input');
$json = json_decode($body);

try {
    update_region($conn, $json->email, $json->region);
} catch (Exception $err) {
    $err_file = fopen(ERROR_LOG_FILE, "a");
    fwrite($err_file, $err->getMessage() . "\n");
    fclose($err_file);
}

?>