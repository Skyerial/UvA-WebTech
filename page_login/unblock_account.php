<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "../account_verification/basic_error_checks.php";
require_once "../account_verification/close_connection.php";
require_once "../../../../conn/db.php";

////////////////////////////////////////////////////////////////////////////////
// Start error definement:
////////////////////////////////////////////////////////////////////////////////
$errors = [
    'pw_error' => false, // Password error.
];

// Define log file:
define("ERROR_LOG_FILE", "../errorLog/error.txt");

////////////////////////////////////////////////////////////////////////////////
// Functions:
////////////////////////////////////////////////////////////////////////////////
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

function activate_account($conn) {
    $activate_account = $conn->prepare(
        "UPDATE user SET status = '0' WHERE activation_code = ?"
    );

    if (!$activate_account->bind_param("s", $_SESSION['token'])) {
        throw new Exception ("[activate_account] Could not bind parameters.");
    }
    if (!$activate_account->execute()) {
        throw new Exception ("[activate_account] Could not execute query.");
    }

    $activate_account->close();
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

        try {
            activate_account($conn);
        } catch (Exception $err) {
            $err_file = fopen(ERROR_LOG_FILE, "a");
            fwrite($file, $err->getMessage() . "\n");
            fclose($err_file);
        }

        close_connection($conn);
        session_destroy();
        unset($_SESSION['token']);
        header("Location: ../page_login/login.php?success=true");
        exit(0);
    }

    close_connection($conn);
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require_once("../page_include/headtags.php") ?>
        <title>Unblock account</title>
        <link rel="stylesheet" href="../styles/form.css">
        <script src = "https://www.google.com/recaptcha/api.js" asyncdefer>
        </script>
    </head>

    <body>
        <?php require_once("../page_include/nav.php") ?>

        <div class="reg-form">
            <div class="reg-header">
                <h2>Unblock account</h2>
                <p>Enter a new password to unblock your account.</p>
            </div>

            <form method = "post" action = "reset_password.php"
            autocomplete="off" novalidate>

                <div class="input-box">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control"
                    value="" maxlength="255" required="">
                    <?php if($errors['pw_error']): ?>
                        <div class="error-message">
                            <p>
                            Please enter a valid password with a minimum of 6
                            characters.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <input type="submit" class="form-btn" name="reset_password"
                value="Submit">
            </form>
        </div>
    </body>
</html>