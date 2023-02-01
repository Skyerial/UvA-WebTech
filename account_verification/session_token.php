<?php

// add_session_token adds the $session_token to the database by the
// user corresponding to $email.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $session_token: The session token to be added.
//  $email: To the corresponding user of this email, the session token must
//          be added.
//
// Output: None
function add_session_token($conn, $session_token, $email) {
    // Hash the session token:
    $hashed_token = password_hash($session_token, PASSWORD_BCRYPT);

    $add_session_token = $conn->prepare(
        "UPDATE user SET session_token = ? WHERE email = ?"
    );

    if (!$add_session_token->bind_param("ss", $hashed_token, $email)) {
        throw new Exception ("[add_token] Could not bind parameters.");
    }
    if (!$add_session_token->execute()) {
        throw new Exception ("[add_token] Could not execute query.");
    }

    $add_session_token->close();
}

// retrieve_token retrieves the session token of the user corresponding
// to $email.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email whose corresponding session token must be retrieved.
//
// Output: False if the email doesn't exist, none otherwise.
function retrieve_token($conn, $email) {
    // Prepare SQL statement to check if the email exists:
    $mail_query = $conn->prepare(
        "SELECT email FROM user WHERE email = ?"
    );

    if (!$mail_query->bind_param("s", $email)) {
        throw new Exception ("[retrieve_token] Could not bind parameters.");
    }
    if (!$mail_query->execute()) {
        throw new Exception ("[retrieve_token] Could not execute query.");
    }

    $mail_query->store_result();
    if ($mail_query->num_rows === 0) {
        $mail_query->close();
        return false;
    }
    $mail_query->close();

    // The email exists, retrieve the corresponding session token:
    $token_query = $conn->prepare(
        "SELECT session_token FROM user WHERE email = ?"
    );

    if (!$token_query->bind_param("s", $email)) {
        throw new Exception ("[retrieve_token] Could not bind parameters.");
    }
    if (!$token_query->execute()) {
        throw new Exception ("[retrieve_token] Could not execute query.");
    }
    // Note: re_token means retrieved token.
    $token_query->bind_result($re_token);
    $token_query->fetch();
    $token_query->close();

    return $re_token;
}

// retrieve_token retrieves the session token of the user corresponding
// to $email.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email whose corresponding session token must be checked.
//  $session_token: The session token which must be checked.
//
// Output: True if the token is valid, false otherwise.
function check_token($conn, $email, $session_token) {
    // Retrieve token corresponding to email from the database:
    $re_token = retrieve_token($conn, $email);
    if ($re_token === false) { return false; }

    // Check if the session token corresponds to the token in the database:
    if (password_verify($session_token, $re_token)) {
        // The session token is correct:
        return true;
    } else {
        return false;
    }
}

?>