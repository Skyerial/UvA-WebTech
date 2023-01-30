<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "/../../../../conn/db.php";

////////////////////////////////////////////////////////////////////////////////
// Functions:
////////////////////////////////////////////////////////////////////////////////

// generate_csrf generates a new csrf token and adds it to the database.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//
// Side effect: A new csrf token is added to the table csrf_token.
function generate_csrf($conn) {
    $csrf_token = bin2hex(random_bytes(32));

    $add_csrf = $conn->prepare(
        "INSERT INTO csrf_token(csrf_token, time) VALUES (?, NOW())"
    );

    if (!$add_csrf->bind_param("s", $csrf_token)) {
        throw new Exception ("[generate_csrf] Could not bind parameters.");
    }
    if (!$add_csrf->execute()) {
        throw new Exception ("[generate_csrf] Could not execute query.");
    }

    $add_csrf->close();
}

// retrieve_csrf retrieves the most recent csrf token. If the token is older
// than one hour, a new token is generated and that one is retrieved instead.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//
// Output: The most recent csrf token.
function retrieve_csrf($conn) {
    $retrieve_csrf = $conn->prepare(
        "SELECT csrf_token, time
        FROM csrf_token
        ORDER BY time
        DESC LIMIT 1"
    );

    if (!$retrieve_csrf->execute()) {
        throw new Exception ("[retrieve_csrf] Could not execute query.");
    }

    $retrieve_csrf_result = $retrieve_csrf->get_result();
    if ($retrieve_csrf_result->num_rows > 0) {
        $row = $retrieve_csrf_result->fetch_assoc();
        $csrf_token = $row['csrf_token'];
        $time = $row['time'];

        // Check if the csrf token is older than 1 hour:
        $time_diff = time() - strtotime($time);
        if ($time_diff > 3600) {
            // The csrf token is older than 1 hour, generate a new one:
            generate_csrf($conn);
            retrieve_csrf($conn);
        } else {
            // The csrf token is valid, return the token:
            $retrieve_csrf->close();
            return $csrf_token;
        }
    } else {
        // No csrf token exists yet, generate a token:
        generate_csrf($conn);
        retrieve_csrf($conn);
    }

    $retrieve_csrf->close();
}

?>