<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "account_verification/basic_error_checks.php";
require_once "account_verification/close_connection.php";
require_once "account_verification/csrf.php";
require_once "/../../../conn/db.php";

////////////////////////////////////////////////////////////////////////////////
// Start error definement:
////////////////////////////////////////////////////////////////////////////////
$errors = [
    'pw_error' => false, // Password error.
    'csrf_error' => false,
];

// Define log file:
define("ERROR_LOG_FILE", "errorLog/error.txt");

////////////////////////////////////////////////////////////////////////////////
// Functions:
////////////////////////////////////////////////////////////////////////////////

// reset_password changes the password in the database corresponding to
// $token to $hashed_password.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//
// Output: None
function reset_password($conn, $hashed_password, $token) {
    $reset_pw = $conn->prepare("UPDATE user
                                SET password = ?
                                WHERE activation_code = ?");

    if (!$reset_pw->bind_param("ss", $hashed_password, $token)) {
        throw new Exception ("[reset_password] Could not bind parameters.");
    }
    if (!$reset_pw->execute()) {
        throw new Exception ("[reset_password] Could not execute query.");
    }

    $reset_pw->execute();
    $reset_pw->close();
}

////////////////////////////////////////////////////////////////////////////////
// Start session:
////////////////////////////////////////////////////////////////////////////////
if(!isset($_SESSION)) { session_start(); }
if(isset($_GET['token'])){ $_SESSION['token'] = $_GET['token']; }

////////////////////////////////////////////////////////////////////////////////
// Handling reset_password POST request:
////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['reset_password'])) {
    // Retrieve information from html form:
    $password = mysqli_real_escape_string($conn,
                                       htmlspecialchars($_POST['password']));

    // Error checking: password.
    basic_password_error($password, $errors);

    // Check if the correct CSRF token is used:
    $csrf_token_from_form = $_POST['csrf_token'];
    $csrf_token_from_db = retrieve_csrf($conn);

    if ($csrf_token_from_form != $csrf_token_from_db) {
        setError($errors, 'csrf_error');
    }

    // If no errors occured, change the password from in the database:
    if (!(in_array(true, $errors))) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        try {
            reset_password($conn, $hashed_password, $_SESSION['token']);
        } catch (Exception $err) {
            $err_file = fopen(ERROR_LOG_FILE, "a");
            fwrite($file, $err->getMessage() . "\n");
            fclose($err_file);
        }

        close_connection($conn);
        session_destroy();
        unset($_SESSION['token']);
        header("Location: login.php?success=true");
        exit(0);
    }

    close_connection($conn);
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Login form</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/form.css">
        <link rel="stylesheet" href="styles/nav.css">
        <script src = "https://www.google.com/recaptcha/api.js" asyncdefer>
        </script>
    </head>

    <body>
        <?php include 'nav.php'; ?>
        <div class="content">
            <!-- The main container for the reset password form -->
            <div class="reg-form">
                <!-- The header with the title -->
                <div class="reg-header">
                    <h2>Reset password</h2>
                </div>

                <form method = "post" action = "reset_password.php"
                autocomplete="off" novalidate>

                    <!-- Enter the (correct) generated CSRF token: -->
                    <input type="hidden" name="csrf_token"
                    value="<?=retrieve_csrf($conn)?>">

                    <!-- The password input box -->
                    <div class="input-box">
                        <label>Password</label>
                        <input type="password" name="password"
                        class="form-control" value="" maxlength="255"
                        required="">
                    </div>

                    <input type="submit" class="form-btn" name="reset_password"
                    value="Submit">

                    <!-- The error messages -->
                    <div class="form-sub-message">
                        <?php if($errors['pw_error']): ?>
                            <div class="error-message">
                                <p>
                                Please enter a valid password with a minimum of
                                6 characters.
                                </p>
                            </div>
                        <?php endif; ?>
                        <?php if($errors['csrf_error']): ?>
                            <div class="error-message">
                                <p>
                                    Invalid/Expired CSRF token!
                                    Please refresh the page.
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>