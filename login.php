<?php

require_once "temp/db.php";

$password_reset = false;
if(isset($_GET['success'])){
    $password_reset = true;
}

$account_activated = false;
if(isset($_GET['token'])){
    $email_token = $_GET['token'];
    $query = 'UPDATE user SET status = 1
              WHERE activation_code="'.$email_token.'"';
    if($conn->query($query)){
        if (is_resource($conn)) {
            mysqli_close($conn);
        }
        $account_activated = true;
    }
}

if(!isset($_SESSION)) { session_start(); }

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

// A pop up message that notifies the user a email has been sent:
$email_sent = false;

if (isset($_SESSION['login'])) {
	header("Location: index.php");
    exit(0);
}

// Create a CSRF token, used for protection against CSRF vulnerabilities:
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['login'])) {
    // The reCAPTCHA verification code is not written by myself, but by SoCix.
    // The code was retrieved from: https://stackoverflow.com/questions/
    // 27274157/how-to-validate-google-recaptcha-v3-on-server-side. The site
    // was last visited on 13-01-2023.
    //
    // The code has been slightly modified to fit the purpose of my program.
    $captcha = $_POST['g-recaptcha-response'] ?? NULL;

    if (!$captcha){
        $errors['captcha_empty'] = true;
    } else {
        $response = json_decode(file_get_contents("https://www.google.com/" .
        "recaptcha/api/siteverify?secret=6LeFivEjAAAAAJt8rR6WIOdokUvPM" .
        "_mWDkcimV34&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']),
        true);

        if($response['success'] == false) { $errors['captcha_error'] = true; }
    }
    // End reCAPTCHA verification code.

    // Check if the correct CSRF token is used:
    if (!empty($_POST['csrf_token'])) {
        if (!(hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']))) {
            $errors['csrf_error'] = true;
        }
    }

    // Retrieve information from html form:
	$email = mysqli_real_escape_string($conn,
                                       htmlspecialchars($_POST['email']));
	$password = mysqli_real_escape_string($conn,
                                       htmlspecialchars($_POST['password']));

    // Check if something has been entered and if it suffices as an email:
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['mail_error'] = true;
    } else {
        // Check if the entered email exists:
        $e_query = $conn->prepare("SELECT password FROM user WHERE email = ?");

        if (!$e_query->bind_param("s", $email)) {
            exit("Could not bind parameters.");
        }
        if (!$e_query->execute()) { exit("Could not execute query."); }
        $e_query->store_result();

        if (!(($e_query->num_rows) > 0)) { $errors['main_error'] = true; }

        $e_query->close();
    }

    if (empty($password) || strlen($password) < 6) {
        $errors['pw_error'] = true;
    }

    // If no errors have been found, create a login session for the
    // specific user:
    if (!(in_array(true, $errors))) {
        // Check if the entered password corresponds to the password in the DB:
        $pw_query = $conn->prepare("SELECT password FROM user WHERE email = ?");

        if (!$pw_query->bind_param("s", $email)) {
            exit("Could not bind parameters.");
        }
        if (!$pw_query->execute()) { exit("Could not execute query."); }
        // re_password means retrieved password.
        $pw_query->bind_result($re_password);
        $pw_query->fetch();
        $pw_query->close();

        // If the correct password has been entered, check if the email is
        // verificated:
        if (password_verify($password, $re_password)) {
            $st_query = $conn->prepare("SELECT status
                                        FROM user WHERE email = ?");

            if (!$st_query->bind_param("s", $email)) {
                exit("Could not bind parameters.");
            }
            if (!$st_query->execute()) { exit("Could not execute query."); }

            $st_query->bind_result($status);
            $st_query->fetch();
            $st_query->close();

            if ($status == 1) {
                $_SESSION['login'] = $email;

                // Change the CSRF token:
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                if (is_resource($conn)) {
                    mysqli_close($conn);
                }

                header("location: index.php");
                exit(0);
            } else { $errors['status_error'] = true; }
        } else { $errors['main_error'] = true;}
    }

    // Seperate closing connection to the DB for errors. Because if an error
    // occurred, the php code could not be exited.
    if (in_array(true, $errors)) {
        if (is_resource($conn)) {
            mysqli_close($conn);
        }
    }
}

if (isset($_POST['reset_password'])) {
    // Retrieve information from html form:
    $email = mysqli_real_escape_string($conn,
                                       htmlspecialchars($_POST['email']));

    // Check if something has been entered and if it suffices as an email:
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['mail_error'] = true;
    } else {
        // Check if the entered email exists:
        $e_query = $conn->prepare("SELECT email FROM user WHERE email = ?");

        if (!$e_query->bind_param("s", $email)) {
            exit("Could not bind parameters.");
        }
        if (!$e_query->execute()) { exit("Could not execute query."); }
        $e_query->store_result();

        if (!($e_query->num_rows > 0)) {
            $errors['main_error'] = true;
        }

        $e_query->close();
    }

    if (!$errors['mail_error'] && !$errors['main_error']) {
        // Check if the email is verificated:
        $st_query = $conn->prepare("SELECT status FROM user WHERE email = ?");

        if (!$st_query->bind_param("s", $email)) {
            exit("Could not bind parameters.");
        }
        if (!$st_query->execute()) { exit("Could not execute query."); }
        $st_query->bind_result($status);
        $st_query->fetch();
        $st_query->close();

        if ($status != 1) { $errors['main_error'] = true; }
    }

    // If no error has occured, send an email with which the user can reset
    // it's password:
    if (!$errors['mail_error'] && !$errors['main_error']
        && !$errors['status_error']) {
        // Fetch the username:
        $u_query = $conn->prepare("SELECT username FROM user WHERE email = ?");
        if (!$u_query->bind_param("s", $email)) {
            exit("Could not bind parameters.");
        }
        if (!$u_query->execute()) { exit("Could not execute query."); }
        $u_query->bind_result($username);
        $u_query->fetch();
        $u_query->close();

        // Generate email token and replace the current one in the database:
        $email_token = bin2hex(random_bytes(32));
        $et_query = $conn->prepare("UPDATE user SET activation_code = ?
                                    WHERE email = ?");
        if (!$et_query->bind_param("ss", $email_token, $email)) {
            exit("Could not bind parameters.");
        }
        if (!$et_query->execute()) { exit("Could not execute query."); }
        $et_query->close();

        // Set the email_link:
        $email_link = "http://webtech-in01.webtech-uva.nl/~marcob/" .
        "reset_password.php?email_token=$email_token";

        // Create a html email:
        $headers  = "From: noreply@WhereToWatch2.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $email_message = file_get_contents('email_reset.html');
        $email_message =  str_replace("{{USERNAME}}", $username,
        $email_message);
        $email_message =  str_replace("{{ACTIVATION_LINK}}", $email_link,
        $email_message);

        // Send the activation mail:
        mail($email, 'Reset password', $email_message, $headers);

        if (is_resource($conn)) {
            mysqli_close($conn);
        }

        $email_sent = true;
    } else {
        if (is_resource($conn)) {
            mysqli_close($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Login form</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/nav.css">
        <link rel="stylesheet" href="styles/form.css">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <script src = "https://www.google.com/recaptcha/api.js" asyncdefer>
        </script>
    </head>

    <body>
        <?php include 'nav.php'; ?>
        <div class="content">
            <div class="reg-form">
                <div class="reg-header">
                    <h2>Login Form</h2>
                    <p>Please fill all fields in the form</p>
                </div>

                <div id="reset_pw" class="email-confirmation">
                    <p class="confirmation-message">We sent you an email, with
                        which you can reset your password. Check your email.</p>
                </div>

                <?php if($email_sent) {
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

                <?php if($account_activated) {
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

                <?php if($password_reset) {
                    echo "
                    <script type='text/javascript'>
                    document.getElementById('pw_reset').style.display = 'block';
                    </script>
                    ";
                    }
                ?>

                <form method = "post" action = "login.php"
                autocomplete="off" novalidate>

                    <!-- Enter the (correct) generated CSRF token: -->
                    <input type = "hidden" name = "csrf_token"
                    value = "<?= $_SESSION['csrf_token'] ?>">

                    <div class="input-box">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control"
                        value="" maxlength="255" required="">
                        <?php if($errors['mail_error']): ?>
                            <div class="error-message">
                                <p>Please enter a valid email.</p>
                            </div>
                        <?php endif; ?>
                    </div>

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

                    <div class = "recaptcha">
                        <div class = "g-recaptcha" data-sitekey =
                        "6LeFivEjAAAAAMCHBdjCxO4-TQ-IHxfMFJF3hWom"></div>
                    </div>

                    <div class="form-sub-message">
                        <?php if($errors['captcha_empty']): ?>
                            <div class="error-message loose">
                                <p> Please check the the captcha form. </p>
                            </div>
                        <?php elseif($errors['captcha_error']): ?>
                            <div class="error-message loose">
                                <p> You did not pass the captcha test, please try
                                    again. </p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <input type="submit" class="form-btn" name="login"
                    value="Submit">

                    <div class="form-sub-message">
                        <?php if($errors['csrf_error']): ?>
                            <div class="error-message">
                                <p> Invalid CSRF token! </p>
                            </div>
                        <?php endif; ?>
                        <?php if($errors['status_error']): ?>
                            <div class="error-message">
                                <p> You have not yet verificated your email adress.
                                    Please check your inbox. </p>
                            </div>
                        <?php endif; ?>
                        <?php if($errors['main_error']): ?>
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
                    <a href="registration.php" class="form-btn">Register here</a>
                    </div>
                </form>

            </div>
        </div>
    </body>
</html>