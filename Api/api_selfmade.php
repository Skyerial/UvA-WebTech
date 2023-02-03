<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "/../../../../conn/db.php";

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

$api_key = retrieve_api($conn, $_COOKIE['checker']);
