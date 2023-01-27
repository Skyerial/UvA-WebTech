<?php

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

function retrieve_token($conn, $email) {
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

function check_token($conn, $email, $session_token) {
    // Retrieve token corresponding to email from the database:
    $re_token = retrieve_token($conn, $email);

    // Check if the session token corresponds to the token in the database:
    if (password_verify($session_token, $re_token)) {
        // The session token is correct:
        return true;
    } else {
        return false;
    }
}

?>