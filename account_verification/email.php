<?php

// retrieve_username retrieves the username corresponding to $email in the DB.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email whose corresponding username must be retrieved.
//
// Output: The retrieved username.
function retrieve_username($conn, $email) {
    $fetch_user = $conn->prepare("SELECT username FROM user WHERE email = ?");

    if (!$fetch_user->bind_param("s", $email)) {
        throw new Exception ("[retrieve_username] Could not bind parameters.");
    }
    if (!$fetch_user->execute()) {
        throw new Exception ("[retrieve_username] Could not execute query.");
    }

    $fetch_user->bind_result($username);
    $fetch_user->fetch();
    $fetch_user->close();

    return $username;
}

// replace_token replaces the current token in the database corresponding to
// $email with $email_token.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email whose corresponding username must be retrieved.
//  $email_token: The token to be set.
//
// Output: None.
function replace_token($conn, $email, $email_token) {
    $rep_token = $conn->prepare("UPDATE user
                                 SET activation_code = ?
                                 WHERE email = ?");

    if (!$rep_token->bind_param("ss", $email_token, $email)) {
        throw new Exception ("[replace_token] Could not bind parameters.");
    }
    if (!$rep_token->execute()) {
        throw new Exception ("[replace_token] Could not execute query.");
    }

    $rep_token->close();
}

// generate_email generates an email and sends it to $email.
//
// Input:
//  $username: The username of the user to which the email is send.
//  $email: The email address to which the email must the send.
//  $email_token: The token with which the system can determine the user.
//  $act: True, if the email is going to be an activation email,
//        false otherwise.
//
// Output: None.
function generate_email($username, $email, $email_link, $act) {
    // Create a html email:
    $headers  = "From: noreply@WhereToWatch2.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if ($act) {
        $subject = "Email activation";
        $email_message = file_get_contents(
            'email_templates/email_activation.html'
        );
    } else {
        $subject = "Reset password";
        $email_message = file_get_contents(
            'email_templates/email_reset.html'
        );
    }

    $email_message =  str_replace(
        "{{USERNAME}}",
        htmlspecialchars($username),
        $email_message
    );
    $email_message =  str_replace(
        "{{ACTIVATION_LINK}}",
        htmlspecialchars($email_link),
        $email_message
    );

    // Send the activation mail:
    mail($email, $subject, $email_message, $headers);
}

?>