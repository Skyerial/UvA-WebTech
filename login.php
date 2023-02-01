<?php
////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "account_verification/basic_error_checks.php";
require_once "account_verification/close_connection.php";
require_once "account_verification/csrf.php";
require_once "account_verification/email.php";
require_once "account_verification/recaptcha.php";
require_once "account_verification/session_token.php";
require_once "/../../../conn/db.php";

////////////////////////////////////////////////////////////////////////////////
// Functions:
////////////////////////////////////////////////////////////////////////////////
// retrieve_password retrieves the password corresponding to $email from the DB.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email of the user, whose password must be retrieved.
//
// Output: The password corresponding to $email.
function retrieve_password($conn, $email) {
    $pw_query = $conn->prepare("SELECT password FROM user WHERE email = ?");

    if (!$pw_query->bind_param("s", $email)) {
        throw new Exception ("[retrieve_password] Could not bind parameters.");
    }
    if (!$pw_query->execute()) {
        throw new Exception ("[retrieve_password] Could not execute query.");
    }
    // re_password means retrieved password.
    $pw_query->bind_result($re_password);
    $pw_query->fetch();
    $pw_query->close();

    return $re_password;
}

// retrieve_status retrieves the status corresponding to $email from the DB.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email of the user, whose password must be retrieved.
//
// Output: The status corresponding to $email.
function retrieve_status($conn, $email) {
    $st_query = $conn->prepare("SELECT status
                                FROM user
                                WHERE email = ?");

    if (!$st_query->bind_param("s", $email)) {
        throw new Exception ("[retrieve_status] Could not bind parameters.");
    }
    if (!$st_query->execute()) {
        throw new Exception ("[retrieve_status] Could not execute query.");
    }

    $st_query->bind_result($status);
    $st_query->fetch();
    $st_query->close();

    return $status;
}

////////////////////////////////////////////////////////////////////////////////
// Miscellaneous: 
////////////////////////////////////////////////////////////////////////////////
// Check if the user is logged in, if yes redirect the user to the index page.
if (isset($_COOKIE['login']) && isset($_COOKIE['checker'])) {
    if (!check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])) {
        if (is_resource($conn)) { mysqli_close($conn); }
        header("Location: index.php");
        exit(0);
    }
}

// A pop up message that notifies the user a email has been sent:
$email_sent = false;

////////////////////////////////////////////////////////////////////////////////
// Password reset:
////////////////////////////////////////////////////////////////////////////////
$password_reset = isset($_GET['success']);

////////////////////////////////////////////////////////////////////////////////
// Account activation:
////////////////////////////////////////////////////////////////////////////////
$account_activated = false;
if (isset($_GET['token'])) {
    $activate_account = $conn->prepare("UPDATE user
                                        SET status = 1
                                        WHERE activation_code= ?");

    if (!$activate_account->bind_param("s", $_GET['token'])) {
        exit("Could not bind parameters.");
    }
    if (!$activate_account->execute()) {
        exit("Could not execute query.");
    }

    $activate_account->close();

    $account_activated = true;
}

////////////////////////////////////////////////////////////////////////////////
// Start error definement:
////////////////////////////////////////////////////////////////////////////////

// The following variables are used to show different error messages in html.
// An specific error message is shown if the variable is true.
$errors = [
    'captcha_empty' => false, // The user has not done the captcha test.
    'captcha_error' => false,
    'csrf_error' => false,
    'mail_error' => false,
    'main_error' => false, // The combination mail and password is incorrect.
    'pw_error' => false, // Password error.
    'status_error' => false // The user has not yet activated it's account.
];

// Define log file:
define("ERROR_LOG_FILE", "errorLog/error.txt");

////////////////////////////////////////////////////////////////////////////////
// Handling login form:
////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['login'])) {
    captcha_check($errors);

    // Check if the correct CSRF token is used:
    $csrf_token_from_form = $_POST['csrf_token'];
    $csrf_token_from_db = retrieve_csrf($conn);

    if ($csrf_token_from_form != $csrf_token_from_db) {
        set_error($errors, 'csrf_error');
    }

    // Retrieve information from html form:
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Error checking: email.
    if (basic_mail_error($email, $errors)) {
        try {
            mail_dup_error($conn, $email, $errors, false);
        } catch (Exception $err) {
            $err_file = fopen(ERROR_LOG_FILE, "a");
            fwrite($err_file, $err->getMessage() . "\n");
            fclose($err_file);
        }
    }

    // Error checking: password.
    basic_password_error($password, $errors);

    // If no errors have been found, create a login session for the
    // specific user:
    if (!(in_array(true, $errors))) {
        // To check if the entered password corresponds to the password in the
        // database, we first retrieve the password from the database:
        try {
            $re_password = retrieve_password($conn, $email);
        } catch (Exception $err) {
            $err_file = fopen(ERROR_LOG_FILE, "a");
            fwrite($err_file, $err->getMessage() . "\n");
            fclose($err_file);
        }

        // Check if the entered password is correct:
        if (password_verify($password, $re_password)) {
            // If the correct password has been entered, check if the account is
            // not blocked. The account can be blocked, because the account is
            // not verified or because the account has exceeded the maximum
            // number of login attempts.
            try {
                $status = retrieve_status($conn, $email);
            } catch (Exception $err) {
                $err_file = fopen(ERROR_LOG_FILE, "a");
                fwrite($err_file, $err->getMessage() . "\n");
                fclose($err_file);
            }

            if ($status == 1) {
                // The account is not blocked, the user can login.
                // Create session token:
                $session_token = bin2hex(random_bytes(32));
                // Set the cookies to expiry in one day.
                // Secure flag and httponly flag are set to true.
                setcookie("login", $session_token, time() + (86400),
                    "/", "", true, true);
                setcookie("checker", $email, time() + (86400),
                    "/", "", true, true);

                // Add the session token to the DB:
                try {
                    add_session_token($conn, $session_token, $email);
                } catch (Exception $err) {
                    $err_file = fopen(ERROR_LOG_FILE, "a");
                    fwrite($err_file, $err->getMessage() . "\n");
                    fclose($err_file);
                };

                // Close the connection to the database:
                close_connection($conn);

                header("location: index.php");
                exit(0);
            } else {
                // The account is blocked, the user cannot login;
                $errors['status_error'] = true;
            }
        } else {
            // The wrong password has been entered:
            $errors['main_error'] = true;
            add_login_attempt($conn, $email);
        }
    } else {
        // Seperate closing connection to the database for errors. Because if an
        // error occurred, the php code could not be exited.
        close_connection($conn);
    }
}

////////////////////////////////////////////////////////////////////////////////
// Handling reset password form:
////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['reset_password'])) {
    // Retrieve information from html form:
    $email = $_POST['email'];

    // Check if the correct CSRF token is used:
    $csrf_token_from_form = $_POST['csrf_token'];
    $csrf_token_from_db = retrieve_csrf($conn);

    if ($csrf_token_from_form != $csrf_token_from_db) {
        set_error($errors, 'csrf_error');
    }

    // Error checking: email.
    if (basic_mail_error($email, $errors)) {
        try {
            mail_dup_error($conn, $email, $errors, false);
        } catch (Exception $err) {
            $err_file = fopen(ERROR_LOG_FILE, "a");
            fwrite($err_file, $err->getMessage() . "\n");
            fclose($err_file);
        }
    }

    if (!$errors['csrf_error'] && !$errors['mail_error']
        && !$errors['main_error']) {
        // Check if the account is not blocked:
        try {
            $status = retrieve_status($conn, $email);
        } catch (Exception $err) {
            $err_file = fopen(ERROR_LOG_FILE, "a");
            fwrite($err_file, $err->getMessage() . "\n");
            fclose($err_file);
        }

        if ($status != 1) {
            $errors['main_error'] = true;
        }
    }

    // If no error has occured, send an email with which the user can reset
    // it's password:
    if (!$errors['csrf_error'] &&  !$errors['mail_error']
        && !$errors['main_error'] && !$errors['status_error']) {
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
        $email_link = "https://webtech-in01.webtech-uva.nl/" .
        "reset_password.php?token=$email_token";
        generate_email($username, $email, $email_link, false);
        $email_sent = true;
    }

    close_connection($conn);
}

if (isset($_POST['to_register'])) {
    header("location: registration.php");
    close_connection($conn);
    exit(0);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once("headtags.php") ?>
    <link rel="stylesheet" href="styles/form.css">
    <title>Login</title>
    <script type="text/javascript" src="menuScript.js" defer></script>
    <script src="https://www.google.com/recaptcha/api.js" asyncdefer>
    </script>
</head>

<body>
    <?php require_once("nav.php") ?>

    <main id="mainID" class="reg-form">
        <div class="reg-header">
            <h2>Login Form</h2>
            <p>Please fill all fields in the form</p>
        </div>

        <!-- Start confirmation messages -->

        <div id="reset_pw" class="email-confirmation">
            <p class="confirmation-message">
                We sent you an email, with which you can reset your password.
                Please check your email. If you receive no mail, please check
                your spam.
            </p>
        </div>

        <?php if ($email_sent) {
            echo "
                <script type='text/javascript'>
                document.getElementById('reset_pw').style.display = 'block';
                </script>
                ";
        }
        ?>

        <div id="acc_ac" class="email-confirmation">
            <p class="confirmation-message">Your account has been
                activated, you can now log in.</p>
        </div>

        <?php if ($account_activated) {
            echo "
                <script type='text/javascript'>
                document.getElementById('acc_ac').style.display = 'block';
                </script>
                ";
        }
        ?>

        <div id="pw_reset" class="email-confirmation">
            <p class="confirmation-message">Your password has been
                successfully reset.</p>
        </div>

        <?php if ($password_reset) {
            echo "
                <script type='text/javascript'>
                document.getElementById('pw_reset').style.display = 'block';
                </script>
                ";
        }
        ?>

        <!-- End confirmation messages -->
        <!-- Start login form -->

        <form method="post" action="login.php" autocomplete="off" novalidate>

            <!-- Enter the (correct) generated CSRF token: -->
            <input type="hidden" name="csrf_token"
            value="<?=retrieve_csrf($conn)?>">

            <div class="input-box">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value=""
                maxlength="255" required="">
                <?php if ($errors['mail_error']) : ?>
                    <div class="error-message">
                        <p>Please enter a valid email.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="input-box">
                <label>Password</label>
                <input type="password" name="password" class="form-control"
                value="" maxlength="255" required="">
                <?php if ($errors['pw_error']) : ?>
                    <div class="error-message">
                        <p>
                            Please enter a valid password with a minimum of 6
                            characters.
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="g-recaptcha"
                data-sitekey="6LeFivEjAAAAAMCHBdjCxO4-TQ-IHxfMFJF3hWom">
            </div>

            <div class="form-sub-message">
                <?php if ($errors['captcha_empty']) : ?>
                    <div class="error-message loose">
                        <p> Please check the the captcha form. </p>
                    </div>
                <?php elseif ($errors['captcha_error']) : ?>
                    <div class="error-message loose">
                        <p> You did not pass the captcha test, please try
                            again. </p>
                    </div>
                <?php endif; ?>
            </div>

            <input type="submit" class="form-btn" name="login" value="Submit">

            <div class="form-sub-message">
                <?php if ($errors['csrf_error']) : ?>
                    <div class="error-message">
                        <p> Invalid/Expired CSRF token!
                            Please refresh the page. </p>
                    </div>
                <?php endif; ?>
                <?php if ($errors['status_error']) : ?>
                    <div class="error-message">
                        <p>
                            You have not yet verificated your email adress.
                            Please check your inbox.
                        </p>
                    </div>
                <?php endif; ?>
                <?php if ($errors['main_error']) : ?>
                    <div class="error-message">
                        <p> Incorrect email or password! </p>
                    </div>
                <?php endif; ?>
            </div>

            <br>

            <div class="left-float">
                <p class="form-sub-message">Forgot password?</p>
                <input type="submit" class="form-btn" name="reset_password"
                value="Reset password">
            </div>
            <div class="left-float">
                <p class="form-sub-message">You don't have an account?</p>
                <input type="submit" class="form-btn" name="to_register"
                value="Register here">
            </div>

        </form>

                </main>

        <?php require_once("footer.php")?>
    </body>
</html>