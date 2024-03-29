<?php

////////////////////////////////////////////////////////////////////////////////
// Imported files:
////////////////////////////////////////////////////////////////////////////////
require_once "../account_verification/basic_error_checks.php";
require_once "../account_verification/close_connection.php";
require_once "../account_verification/csrf.php";
require_once "../account_verification/session_token.php";
require_once "../../../../conn/db.php";

////////////////////////////////////////////////////////////////////////////////
// Check session:
////////////////////////////////////////////////////////////////////////////////

// Check if the user is logged in, if not redirect the user to the login page.
if (isset($_COOKIE['login']) && isset($_COOKIE['checker'])) {
    if (!check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])) {
        close_connection($conn);
        header("Location: ../page_login/login.php");
        exit(0);
    }
} else {
    close_connection($conn);
    header("Location: ../page_login/login.php");
    exit(0);
}

// Set confirm messages:
$changed_username = false;
$changed_password = false;

////////////////////////////////////////////////////////////////////////////////
// Functions:
////////////////////////////////////////////////////////////////////////////////

// retrieve_region retrieves the region variable corresponding to $email.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email who must be region variable must be retrieved.
//
// Output:
//  If the user had a corresponding region variable, this value.
//  Otherwise "Select region".
function retrieve_region($conn, $email) {
    $retrieve_region = $conn->prepare(
        "SELECT region.region
        FROM user
        LEFT JOIN region ON user.rid = region.rid
        WHERE email = ?"
    );

    if (!$retrieve_region->bind_param("s", $email)) {
        throw new Exception ("[retrieve_region] Could not bind parameters.");
    }
    if (!$retrieve_region->execute()) {
        throw new Exception ("[retrieve_region] Could not execute query.");
    }

    $retrieve_result = $retrieve_region->get_result();
    $retrieve_row = $retrieve_result->fetch_assoc();

    if(isset($retrieve_row['region'])){
        return $retrieve_row['region'];
    } else {
        return "Select region";
    }
}

// check_mail checks if the email $email exists in the database.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email who must be checked.
//
// Output: If the email exists true, false otherwise.
function check_mail($conn, $email) {
    $check_mail = $conn->prepare(
        "SELECT email FROM user WHERE email = ?"
    );

    if (!$check_mail->bind_param("s", $email)) {
        throw new Exception ("[check_mail] Could not bind parameters.");
    }
    if (!$check_mail->execute()) {
        throw new Exception ("[check_mail] Could not execute query.");
    }

    $check_mail->store_result();
    if ($check_mail->num_rows === 0) {
        $check_mail->close();
        return false;
    } else {
        $check_mail->close();
        return true;
    }
}

// change_username changes the username of the user corresponding to $email.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email corresponding to the user whose username must be changed.
//  $username: The new username value.
//
// Output: None.
function change_username($conn, $email, $username) {
    // Check if the email exists:
    try {
        if (check_mail($conn, $email)) {
            // The email exists, continue executing:
            $change_username = $conn->prepare(
                "UPDATE user SET username = ?
                WHERE email = ?"
            );

            if (!$change_username->bind_param("ss", $username, $email)) {
                throw new Exception (
                    "[change_username] Could not bind parameters."
                );
            }
            if (!$change_username->execute()) {
                throw new Exception (
                    "[change_username] Could not execute query."
                );
            }
        }
    } catch (Exception $err) {
        $err_file = fopen(ERROR_LOG_FILE, "a");
        fwrite($err_file, $err->getMessage() . "\n");
        fclose($err_file);
    }
}

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

// change_password changes the password of the user corresponding to $email.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//  $email: The email corresponding to the user whose username must be changed.
//  $password: The new password value.
//
// Output: None.
function change_password($conn, $email, $password) {
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $change_password = $conn->prepare(
        "UPDATE user SET password = ?
        WHERE email = ?"
    );

    if (!$change_password->bind_param("ss", $hashed_password, $email)) {
        throw new Exception (
            "[change_password] Could not bind parameters."
        );
    }
    if (!$change_password->execute()) {
        throw new Exception (
            "[change_password] Could not execute query."
        );
    }
}

////////////////////////////////////////////////////////////////////////////////
// Start error definement:
////////////////////////////////////////////////////////////////////////////////

// The following variables are used to show different error messages in html.
// An specific error message is shown if the variable is true.
$errors = [
    'csrf_error' => false,
    'curpw_error' => false, // Current password error.
    'pw_error' => false, // Password error.
    'username_error' => false
];

// Define log file:
define("ERROR_LOG_FILE", "../errorLog/error.txt");

////////////////////////////////////////////////////////////////////////////////
// Retrieve region information:
////////////////////////////////////////////////////////////////////////////////
$region = htmlspecialchars(retrieve_region($conn, $_COOKIE['checker']));

////////////////////////////////////////////////////////////////////////////////
// Handling reset username:
////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['change_username'])) {
    // Retrieve information from html form:
    $username = $_POST['username'];

    // Check if the correct CSRF token is used:
    $csrf_token_from_form = $_POST['csrf_token'];
    $csrf_token_from_db = retrieve_csrf($conn);

    if ($csrf_token_from_form != $csrf_token_from_db) {
        setError($errors, 'csrf_error');
    }

    // Error checking: username.
    basic_username_error($username, $errors);

    if (!(in_array(true, $errors))) {
        try {
            change_username($conn, $_COOKIE['checker'], $username);
            $changed_username = true;
        } catch (Exception $err) {
            $err_file = fopen(ERROR_LOG_FILE, "a");
            fwrite($err_file, $err->getMessage() . "\n");
            fclose($err_file);
        }
        close_connection($conn);
    } else {
        close_connection($conn);
    }
}

////////////////////////////////////////////////////////////////////////////////
// Handling reset password:
////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['change_password'])) {
    // Retrieve information from html form:
    $cur_password = $_POST['cur_password'];
    $new_password = $_POST['new_password'];

    // Check if the correct CSRF token is used:
    $csrf_token_from_form = $_POST['csrf_token'];
    $csrf_token_from_db = retrieve_csrf($conn);

    if ($csrf_token_from_form != $csrf_token_from_db) {
        setError($errors, 'csrf_error');
    }

    // Error checking: password.
    basic_password_error($cur_password, $errors);
    basic_password_error($new_password, $errors);

    if (!(in_array(true, $errors))) {
        // To check if the entered password corresponds to the password in the
        // database, we first retrieve the password from the database:
        try {
            $re_password = retrieve_password($conn, $_COOKIE['checker']);
        } catch (Exception $err) {
            $err_file = fopen(ERROR_LOG_FILE, "a");
            fwrite($err_file, $err->getMessage() . "\n");
            fclose($err_file);
        }

        // Check if the entered password is correct:
        if (password_verify($cur_password, $re_password)) {
            try {
                change_password($conn, $_COOKIE['checker'], $new_password);
                $changed_password = true;
            } catch (Exception $err) {
                $err_file = fopen(ERROR_LOG_FILE, "a");
                fwrite($err_file, $err->getMessage() . "\n");
                fclose($err_file);
            }
        } else {
            setError($errors, 'curpw_error');
        }
        close_connection($conn);
    } else {
        close_connection($conn);
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <?php require_once("../page_include/headtags.php") ?>
        <?php require_once("../Api/api_selfmade.php");?>
        <title>Settings</title>
        <link rel="stylesheet" href="../styles/settings.css">
        <link rel="stylesheet" href="../styles/form.css">
        <script type="text/javascript" src="../page_home/scripts.js"></script>
        <script type="text/javascript" src="settings.js"></script>
        <script type="text/javascript" src="../page_include/menuScript.js" defer></script>
    </head>
    <body>
        <!-- insert the nav bar -->
        <?php require_once("../page_include/nav.php") ?>
        
        <main id="mainID">
        <p></p>
        <div class = "border">
            <div class="reg-header">
                <h2>Settings</h2>
                <p>Change your settings.</p>
            </div>

            <h3 class="settings_text">Region</h3>
            <?php
                    $region_query = "SELECT * FROM `region`";
                    $receive = mysqli_query($conn, $region_query);
            ?>

            <div class = "dropdown">
                <!-- Current region saved in database. -->
                <p class="settings_text">Change your region:</p>
                <div class = "region" id = "selected-region"
                onclick = "dropdown()">
                    <?= $region?>
                </div>

                <div class = "view">
                    <!-- Search bar to look up regions. -->
                    <div>
                        <input id = "search-input" onkeyup = "search()"
                        type = "text" placeholder = "Search region...">
                    </div>

                    <!-- Get all regions from database and put them in a dropdown -->
                    <div id = "regions" class = "all-options">
                        <?php while ($line = mysqli_fetch_array($receive)):;?>
                        <div class = "option" onclick = "newRegion('<?php echo $line[1];?>',
                        '<?php echo $_COOKIE['checker'] ?>')">
                            <input type = "radio" class = "input_php">
                            <label><?php echo $line[1];?></label>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <div>
                <!-- Option to change password -->
                <h3 class="settings_text">User settings</h3>

                <div>
                    <!-- Start confirmation messages -->

                    <div id="un-con" class="email-confirmation">
                        <p class="confirmation-message">
                            Your username has been successfully changed!
                        </p>
                    </div>


                    <?php if($changed_username) {
                        echo "
                        <script type='text/javascript'>
                        document.getElementById('un-con').style.display =
                            'block';
                        </script>
                        ";
                        }
                    ?>

                    <div id="pw-con" class="email-confirmation">
                        <p class="confirmation-message">
                            Your password has been successfully changed!
                        </p>
                    </div>

                    <?php if($changed_password) {
                        echo "
                        <script type='text/javascript'>
                        document.getElementById('pw-con').style.display =
                            'block';
                        </script>
                        ";
                        }
                    ?>
                    <!-- End confirmation messages -->

                    <!-- Make the forms appear with buttons. -->
                    <div class = "container-buttons">
                        <button class = "buttons passbutton" onclick = input_pass()>Change password</button>
                        &emsp;&emsp;&emsp;
                        <button class = "buttons userbutton" onclick = input_user()>Change username</button>
                    </div>

                    <form method = "post" action = "settings.php"
                    autocomplete="off" novalidate>
                        <input type="hidden" name="csrf_token"
                        value="<?=retrieve_csrf($conn)?>">
                        &nbsp;
                        <div class = "table">
                            <div class="one" id="passID">
                                <div class = "column left passchange text">
                                    <label>Current password</label>
                                </div>
                                <div class = "column left passchange">
                                    <input type="password" name="cur_password" class="#"
                                    value="" maxlength="255" required="">
                                </div>
                                <div class = "column left passchange text">
                                    <label>New Password</label>
                                </div>
                                <div class = "column left passchange">
                                    <input type="password" name="new_password" class="#"
                                    value="" maxlength="255" required="">
                                </div>
                                <input type="submit" class="hidden_pass buttons"
                                name="change_password" value="Submit password">
                            </div>

                            <div class="two" id="userID">
                                <div class = "column right userchange text">
                                    <label class="userLabel">Username</label>
                                </div>
                                <div class = "column right userchange">
                                    <input type="text" name="username" class="#"
                                    value="" maxlength="30" required="">
                                </div>
                                <input type="submit" class="hidden_user buttons"
                                name="change_username" value="Submit username">

                            </div>

                            </div>

                            <!-- Submit buttons. -->

                            <?php if($errors['username_error']): ?>
                                <div class="error-message">
                                    <p>
                                        Please enter a valid username. A username
                                        cannot contain spaces.
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if($errors['curpw_error']): ?>
                                <div class="error-message">
                                    <p>
                                        Please enter your current password.
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if($errors['pw_error']): ?>
                                <div class="error-message">
                                    <p>
                                        Please enter a valid password with a
                                        minimum of 6 characters.
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
                    </form>
                </div>
            </div>
            <div class="apikey">
                <h3 class="settings_text">Your API key</h3>
                <button class="buttons" onclick="copy('<?=$api_key?>'); return false">Click to copy key</button>
                <p id="APIkey"></p>
            </div>
        </div>

        </main>
        <?php require_once("../page_include/footer.php")?>

    </body>
</html>
