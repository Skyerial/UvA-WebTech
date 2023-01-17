<?php

if(!isset($_SESSION)) { session_start(); }

require_once "temp/db.php"; // Do NOT put .php files in root!!!

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

// A pop up message that notifies the user a email has been sent:
$email_sent = false;

// The user cannot register, if the user is already logged in:
if (isset($_SESSION['login'])) {
	header("Location: index.php");
    exit(0);
}

// Create a CSRF token, used for protection against CSRF vulnerabilities:
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['register'])) {
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
	$username = mysqli_real_escape_string($conn,
                                        htmlspecialchars($_POST['username']));
    $email = mysqli_real_escape_string($conn,
                                        htmlspecialchars($_POST['email']));
    $password = mysqli_real_escape_string($conn,
                                        htmlspecialchars($_POST['password']));
    $cpassword = mysqli_real_escape_string($conn,
                                        htmlspecialchars($_POST['cpassword']));

    // Check if any username has been entered:
    if (empty($username) || (strpos($username, " ") !== false)) {
        $errors['username_error'] = true;
    }
    // Check if something has been entered and if it suffices as an email:
	if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['mail_error'] = true;
    // Check if the email has already been used:
    }  else {
        $check_dup_email = $conn->prepare("SELECT email
                                           FROM user WHERE email = ?");

        if (!$check_dup_email->bind_param("s", $email)) {
            exit("Could not bind parameters.");
        }
        if (!$check_dup_email->execute()) { exit("Could not execute query."); }
        $check_dup_email->store_result();

        if ($check_dup_email->num_rows > 0) {
            $errors['mail_dup_error'] = true;
        }

        $check_dup_email->close();
    }

    if (empty($password) || strlen($password) < 6 ||
        (strpos($password, " ") !== false)) {
        $errors['pw_error'] = true;
    }
    if ($password != $cpassword) { $errors['pwc_error'] = true; }

    if (!(in_array(true, $errors))) {
        // Generate a email verification token:
        $email_token = bin2hex(random_bytes(32));
        $email_link = "http://webtech-in01.webtech-uva.nl/~marcob/" .
        "login.php?token=$email_token";

        // Hash the users password with PASSWORD_BCRYPT and a salt:
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Add the new user to the data base with its own email
        // verification token:
        $add_user = $conn->prepare("INSERT INTO user(username, email, password,
        activation_code) VALUES (?, ?, ?, ?)");

        if (!$add_user->bind_param("ssss", $username, $email, $hashed_password,
        $email_token)) { exit("Could not bind parameters."); }
        if (!$add_user->execute()) { exit("Could not execute query."); }
        $add_user->close();

        // Create a html email:
        $headers  = "From: noreply@Where2Watch.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $email_message = file_get_contents('email_activation.html');
        $email_message =  str_replace("{{USERNAME}}", $username,
        $email_message);
        $email_message =  str_replace("{{ACTIVATION_LINK}}", $email_link,
        $email_message);

        // Send the activation mail:
        mail($email, 'Email activation', $email_message, $headers);

        // Change the CSRF token:
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

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
        <title>Registration form</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="styles/form.css">
        <link rel="stylesheet" href="styles/nav.css">
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <script src = "https://www.google.com/recaptcha/api.js" asyncdefer>
        </script>
    </head>
    <body>
        <?php include 'nav.php'; ?>
        <div class="content">
            <div class="reg-form">
                <div class="reg-header">
                    <h2>Registration Form</h2>
                    <p>Please fill all fields in the form</p>
                </div>

                <div id="e-con" class="email-confirmation">
                    <p class="confirmation-message">We sent you an activation code.
                        Check your email and click on the link to verify.</p>
                </div>

                <?php if($email_sent) {
                    echo "
                    <script type='text/javascript'>
                    document.getElementById('e-con').style.display = 'block';
                    console.log(10);
                    </script>
                    ";
                    }
                ?>

                <form method = "post" action = "registration.php"
                autocomplete="off" novalidate>

                    <!-- Enter the (correct) generated CSRF token: -->
                    <input type = "hidden" name = "csrf_token"
                    value = "<?= $_SESSION['csrf_token'] ?>">

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

                    <div class = "recaptcha">
                        <div class = "g-recaptcha" data-sitekey =
                        "6LeFivEjAAAAAMCHBdjCxO4-TQ-IHxfMFJF3hWom"></div>
                    </div>

                    <div class="form-sub-message">
                        <?php if($errors['csrf_error']): ?>
                            <div class="error-message">
                                <p> Invalid CSRF token! </p>
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

                    <br><br><br><br>
                    <p class="form-sub-message">Already have an account?</p>
                    <a href="login.php" class="form-btn">Login</a>
                </form>


                <!-- <p class="form-sub-message">Already have an account?</p>
                <a href="login.php" class="form-btn">Login</a> -->
            </div>
        </div>
    </body>
</html>