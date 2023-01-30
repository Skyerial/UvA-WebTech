<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "email.php";

////////////////////////////////////////////////////////////////////////////////
// Functions:
////////////////////////////////////////////////////////////////////////////////

// add_login_attempt changes the value of the row 'login_attempt' by +1 of
// the user with $email.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email of the user whose login_attempt value must increase.
//
// Output: None.
function add_login_attempt($conn, $email) {
    // Prepare SQL statement to add '1' to the value of the row login_attempt:
    $add_attempt = $conn->prepare("UPDATE user
        SET login_attempt = login_attempt + 1 WHERE email = ?");

    if (!$add_attempt->bind_param("s", $email)) {
        exit("Could not bind parameters.");
    }
    if (!$add_attempt->execute()) { exit("Could not execute query."); }

    $add_attempt->close();
}

// reset_login_attempt sets the value of the row 'login_attempt' to 0 of
// the user with $email.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email of the user whose login_attempt value must increase.
//
// Output: None.
function reset_login_attempt($conn, $email) {
    // Prepare SQL statement to set the value of the row login_attempt to '0':
    $reset_attempt = $conn->prepare("UPDATE user
        SET login_attempt = 0 WHERE email = ?");

    if (!$reset_attempt->bind_param("s", $email)) {
        exit("Could not bind parameters.");
    }
    if (!$reset_attempt->execute()) { exit("Could not execute query."); }

    $reset_attempt->close();
}

// block_account blocks the account with email $email.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email of the user whose account must be blocked.
//
// Output: None.
function block_account($conn, $email) {
    // Prepare SQL statement to change the values of 'status' and
    // 'login_attempt' to to '0' of the account with email $email:
    $block_account = $conn->prepare("UPDATE user
        SET status = 0, login_attempt = 0 WHERE email = ?");

    if (!$block_account->bind_param("s", $email)) {
        exit("Could not bind parameters.");
    }
    if (!$block_account->execute()) { exit("Could not execute query."); }

    $block_account->close();

    // Send an email to give the user the ability to change unblock
    // its account.

    // Retrieve the username:
    try {
        $username = retrieve_username($conn, $email);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);
    }

    // Generate email token and replace the current one in the database:
    try {
        $email_token = bin2hex(random_bytes(32));
        replace_token($conn, $email, $email_token);
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);
    }

    // Generate and send the email:
    $email_link = "https://webtech-in01.webtech-uva.nl/~tijnk/pages/" .
    "unblock_account.php?token=$email_token";
    unblock_mail($username, $email, $email_link);
}

// login_attempt_check checks if the user with $email still has available
// login attempts.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email of the user whose login_attempt value must be checked.
//
// Output: True if the login_attempt value is above 10, false otherwise.
function login_attempt_check($conn, $email) {
    // Prepare SQL statement to retrieve the value of the row 'login_attempt':
    $check_attempt = $conn->prepare("SELECT login_attempt FROM user
                                     WHERE email = ?");

    if (!$check_attempt->bind_param("s", $email)) {
        exit("Could not bind parameters.");
    }
    if (!$check_attempt->execute()) { exit("Could not execute query."); }

    $attempts = $check_attempt->get_result();

    if ($attempts->num_rows > 0) {
        $row = $attempts->fetch_assoc();
        if ($row['login_attempt'] >= 10) {
            block_account($conn, $email);
            $check_attempt->close();
            return true;
        }
    }

    $check_attempt->close();
    return false;
}

?>