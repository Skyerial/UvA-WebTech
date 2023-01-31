<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "account_verification/close_connection.php";
require_once "account_verification/session_token.php";
require_once "/../../../conn/db.php";

////////////////////////////////////////////////////////////////////////////////
// Check session:
////////////////////////////////////////////////////////////////////////////////

// Check if the user is logged in, if not redirect the user to the login page.
if (isset($_COOKIE['login']) && isset($_COOKIE['checker'])) {
    if (!check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])) {
        close_connection($conn);
        header("Location: login.php");
        exit(0);
    }
} else {
    close_connection($conn);
    header("Location: login.php");
    exit(0);
}

////////////////////////////////////////////////////////////////////////////////
// Functions:
////////////////////////////////////////////////////////////////////////////////

// retrieve_api retrieves the API key corresponding to the user with $email.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email of the user, whose API key must be retrieved.
//
// Output: An API key.
function retrieve_api($conn, $email) {
    $retrieve_api = $conn->prepare(
        "SELECT api_key FROM user WHERE email = ?"
    );

    if (!$retrieve_api->bind_param("s", $email)) {
        throw new Exception ("[retrieve_api] Could not bind parameters.");
    }
    if (!$retrieve_api->execute()) {
        throw new Exception ("[retrieve_api] Could not execute query.");
    }

    $retrieve_api->bind_result($api_key);
    if (!$retrieve_api->fetch()) {
        throw new Exception ("[retrieve_api] Could not fetch result.");
    }

    $retrieve_api->close();

    return $api_key;
}

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

    if (!$retrieve_api->bind_param("s", $api_key)) {
        throw new Exception ("[check_api] Could not bind parameters.");
    }
    if (!$retrieve_api->execute()) {
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

////////////////////////////////////////////////////////////////////////////////
// Retrieve API key:
////////////////////////////////////////////////////////////////////////////////
