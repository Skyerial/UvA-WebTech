<?php
$q = $_POST['movieTitle'];

require_once "movieApiCall.php";
require_once "../account_verification/session_token.php";
require_once "/../../../../conn/db.php";

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

function retrieve_short($conn, $email) {
    $retrieve_short = $conn->prepare(
        "SELECT region.short_region
        FROM user
        LEFT JOIN region ON user.rid = region.rid
        WHERE email = ?"
    );

    if (!$retrieve_short->bind_param("s", $email)) {
        throw new Exception ("[retrieve_short] Could not bind parameters.");
    }
    if (!$retrieve_short->execute()) {
        throw new Exception ("[retrieve_short] Could not execute query.");
    }

    $retrieving_result = $retrieve_short->get_result();
    $retrieving_row = $retrieving_result->fetch_assoc();

    if(isset($retrieving_row['short_region'])) {
        // var_dump($retrieving_row['short_region']);
        return $retrieving_row['short_region'];
    } else {
        return 'nl';
    }
}

// Get the current region or take nl as default.
if (isset($_COOKIE['login']) && isset($_COOKIE['checker'])) {
    if (!check_token($conn, $_COOKIE['checker'], $_COOKIE['login'])) {
        $country = 'nl';
    } else {
        if (check_mail($conn, $_COOKIE['checker'])) {
            $country = retrieve_short($conn, $_COOKIE['checker']);
        }
    }
} else {
    $country = 'nl';
}

$actualDataToSend = condense_data(api_call(build_url($q, $conn, $country)), $country);
if(!isset($_SESSION)) { session_start(); }
$_SESSION['displayed_cards'][0] = $actualDataToSend;

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($actualDataToSend);
?>
