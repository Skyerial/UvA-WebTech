<?php

if(!isset($_SESSION)) { session_start(); }

require_once "temp/db.php";
$pw_error = false;

if(isset($_GET['email_token'])){
    $email_token = $_GET['email_token'];
    $_SESSION['email_token'] = $email_token;
}

if (isset($_POST['reset_password'])) {
    $password = mysqli_real_escape_string($conn,
                                       htmlspecialchars($_POST['password']));

    // Password error checking:
    if (empty($password) || strlen($password) < 6) {
        $pw_error = true;
        if (is_resource($conn)) {
            mysqli_close($conn);
        }
    }

    if (!$pw_error) {
        $rs_query = $conn->prepare("UPDATE user SET password = ?
                                    WHERE activation_code = ?");
        if (!$rs_query) {
            // TODO: ERROR HANDLING
        }
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $email_token = $_SESSION['email_token'];

        $rs_query->bind_param("ss", $hashed_password, $email_token);
        $rs_query->execute();
        $rs_query->close();

        if (is_resource($conn)) {
            mysqli_close($conn);
        }
        unset($_SESSION['email_token']);
        header("Location: login.php?success=true");
        exit(0);
    }
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

                <form method = "post" action = "reset_password.php"
                autocomplete="off" novalidate>

                    <div class="input-box">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control"
                        value="" maxlength="255" required="">
                        <?php if($pw_error): ?>
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
        </div>
    </body>
</html>