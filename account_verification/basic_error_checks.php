<?php

// setError sets an error value in an error array to true.
//
// Input:
//  &$errors: An array containing possible errors.
//            '&' is used to modify global array, not only the local array.
//  $errortype: A specific error in the error array.
//
// Output: None.
function setError(&$errors, $errorType) { $errors[$errorType] = true; }

// basic_username_error checks if the username variable is not empty and if the
// username does not contain spaces.
//
// Input:
//  $username: The username to be checked.
//  &$errors: An array containing possible errors.
//
// Output: None.
function basic_username_error($username, &$errors) {
    if (empty($username) || (strpos($username, " ") !== false)) {
        setError($errors, 'username_error');
    }
}

// basic_mail_error checks if the email variable is not empty and if the email
// variable contains a valid email address.
//
// Input:
//  $email: The email to be checked.
//  &$errors: An array containing possible errors.
//
// Output:
//  True if the email is valid, false otherwise.
function basic_mail_error($email, &$errors) {
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setError($errors, 'mail_error');
        return false;
    }
    return true;
}

// mail_dup_error checks if the email is not already in use.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email to be checked.
//  &$errors: An array containing possible errors.
//  $dup: True, if function needs to check on duplicate, false otherwise.
//
// Output: None.
function mail_dup_error($conn, $email, &$errors, $dup) {
    $check_dup_email = $conn->prepare("SELECT email
                                       FROM user
                                       WHERE email = ?");

    if (!$check_dup_email->bind_param("s", $email)) {
        throw new Exception ("[mail_dup_error] Could not bind parameters.");
    }
    if (!$check_dup_email->execute()) {
        throw new Exception ("[mail_dup_error] Could not execute query.");
    }

    $check_dup_email->store_result();

    if ($check_dup_email->num_rows > 0) {
        if ($dup) { setError($errors, 'mail_dup_error'); }
    } else {
        if (!$dup) { setError($errors, 'main_error'); }
    }

    $check_dup_email->close();
}

// basic_password_error checks if the password is not empty and if the password
// contains at least six characters.
//
// Input:
//  $password: The password to be checked.
//  &$errors: An array containing possible errors.
//
// Output: None.
function basic_password_error($password, &$errors) {
    if (empty($password) || strlen($password) < 6) {
        setError($errors, 'pw_error');
    }
}

?>