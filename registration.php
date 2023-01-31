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
function add_user($conn, $username, $email, $hashed_password, $email_token) {

    // Generate a private API key:
    $api_key = bin2hex(random_bytes(32));

    // Prepare a SQL query to add the user to the database:
    $add_user = $conn->prepare("INSERT INTO user(username, email, password,
        activation_code, api_key) VALUES (?, ?, ?, ?, ?)");

    // Start a loop to regenerate the API key when the key is not unique:
    $result = false;
    while (!$result) {
        if (!$add_user->bind_param("sssss", $username, $email, $hashed_password,
            $email_token, $api_key)) {
            throw new Exception ("[add_user] Could not bind parameters.");
        }

        $result = $add_user->execute();
        // Note: Error 1062 is the MYSQLI duplicate error.
        if ($result === false && $conn->errno == 1062) {
            $api_key = bin2hex(random_bytes(32));
        } else if (!$result) {
            throw new Exception ("[add_user] Could not execute query.");
        }
    }

    $add_user->close();
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
// Start error definement:
////////////////////////////////////////////////////////////////////////////////

// The following variables are used to show different error messages in html.
// An specific error message is shown if the variable is true.
$errors = [
    'captcha_empty' => false, // The user has not done the captcha test.
    'captcha_error' => false,
    'csrf_error' => false,
    'mail_dup_error' => false, // Email already exist error.
    'mail_error' => false,
    'pw_error' => false, // Password error.
    'pwc_error' => false, // Password and confirm password don't match error.
    'username_error' => false
];

// Define log file:
define("ERROR_LOG_FILE", "errorLog/error.txt");

////////////////////////////////////////////////////////////////////////////////
// Handling register form:
////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['register'])) {
    captcha_check($errors);

    // Check if the correct CSRF token is used:
    $csrf_token_from_form = $_POST['csrf_token'];
    $csrf_token_from_db = retrieve_csrf($conn);

    if ($csrf_token_from_form != $csrf_token_from_db) {
        setError($errors, 'csrf_error');
    }

    // Retrieve information from html form:
	$username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Error checking: username.
    basic_username_error($username, $errors);

    // Error checking: email.
    if (basic_mail_error($email, $errors)) {
        try {
            mail_dup_error($conn, $email, $errors, true);
        } catch (Exception $err) {
            $err_file = fopen(ERROR_LOG_FILE, "a");
            fwrite($err_file, $err->getMessage() . "\n");
            fclose($err_file);
        }
    }

    // Error checking: password.
    basic_password_error($password, $errors);

    // Error checking: confirm password.
    if ($password != $cpassword) { $errors['pwc_error'] = true; }

    if (!(in_array(true, $errors))) {
        // Generate a email verification token:
        $email_token = bin2hex(random_bytes(32));
        $email_link = "https://webtech-in01.webtech-uva.nl/login.php?token=$email_token";

        // Hash the users password:
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Add the new user:
        try {
            add_user($conn, $username, $email, $hashed_password, $email_token);
        } catch (Exception $err) {
            $err_file = fopen(ERROR_LOG_FILE, "a");
            fwrite($err_file, $err->getMessage() . "\n");
            fclose($err_file);
        }

        // Create a html email:
        generate_email($username, $email, $email_link, true);
        $email_sent = true;

        close_connection($conn);
    } else {
        close_connection($conn);
    }
}

if (isset($_POST['to_login'])) {
    header("location: login.php");
    close_connection($conn);
    exit(0);
}

?>

<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Registration form</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/form.css">
        <link rel="stylesheet" href="styles/nav.css">
        <script type="text/javascript" src="menuScript.js"></script>
        <script src="https://www.google.com/recaptcha/api.js" asyncdefer>
        </script>
    </head>
    <body onload="showFooter()">
        <?php require_once("nav.php");?>

        <main id="mainID" class="reg-form">
            <div class="reg-header">
                <h2>Registration Form</h2>
                <p>Please fill all fields in the form</p>
            </div>

            <!-- Start confirmation messages -->

            <div id="e-con" class="email-confirmation">
                <p class="confirmation-message">
                    We sent you an activation code. Check your email and click
                    on the link to verify. If you receive no mail, please check
                    your spam.
                </p>
            </div>

            <?php if($email_sent) {
                echo "
                <script type='text/javascript'>
                document.getElementById('e-con').style.display = 'block';
                </script>
                ";
                }
            ?>

            <!-- End confirmation messages -->
            <!-- Start registration form -->

            <form method = "post" action = "registration.php"
            autocomplete="off" novalidate>

                <!-- Enter the (correct) generated CSRF token: -->
                <input type="hidden" name="csrf_token"
                value="<?=retrieve_csrf($conn)?>">

                <div class="input-box">
                    <label>Username</label><br>
                    <input type="text" name="username" class="form-control"
                    value="" maxlength="30" required="">
                    <?php if($errors['username_error']): ?>
                        <div class="error-message">
                            <p>Please enter a valid username. A username cannot
                               contain spaces.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="input-box">
                    <label>Email</label><br>
                    <input type="email" name="email" class="form-control"
                    value="" maxlength="255" required="">
                    <?php if($errors['mail_error']): ?>
                        <div class="error-message">
                            <p>Please enter a valid email.</p>
                        </div>
                    <?php endif; ?>
                    <?php if($errors['mail_dup_error']): ?>
                        <div class="error-message">
                            <p>This email is already in use.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="input-box">
                    <label>Password</label><br>
                    <input type="password" name="password" class="form-control"
                    value="" maxlength="255" required="">
                    <?php if($errors['pw_error']): ?>
                        <div class="error-message">
                            <p>
                                Please enter a valid password with a minimum of
                                6 characters.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="input-box">
                    <label>Confirm Password</label><br>
                    <input type="password" name="cpassword"
                    class="form-control" value="" maxlength="255" required="">
                    <?php if($errors['pwc_error']): ?>
                        <div class="error-message">
                            <p>
                                The fields 'Password' and 'Confirm Password'
                                don't match.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="g-recaptcha"
                    data-sitekey="6LeFivEjAAAAAMCHBdjCxO4-TQ-IHxfMFJF3hWom">
                </div>

                <div class="form-sub-message">
                    <?php if($errors['csrf_error']): ?>
                        <div class="error-message">
                            <p> Invalid/Expired CSRF token!
                                Please refresh the page. </p>
                        </div>
                    <?php endif; ?>
                    <?php if($errors['captcha_empty']): ?>
                        <div class="error-message">
                            <p> Please check the the captcha form. </p>
                        </div>
                    <?php elseif($errors['captcha_error']): ?>
                        <div class="error-message">
                            <p> You did not pass the captcha test, please try
                                again. </p>
                        </div>
                    <?php endif; ?>
                </div>

                <input type="submit" class="form-btn" name="register"
                value="Submit">

                <br><br>

                <div class="left-float">
                    <p class="form-sub-message">Already have an account?</p>
                    <input type="submit" class="form-btn" name="to_login"
                    value="Login here">
                </div>
            </form>
            <!-- End registration form -->
        </main>

        <?php require_once("footer.php");?>
    </body>
</html>