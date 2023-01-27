<?php

// The reCAPTCHA verification code is not written by myself, but by SoCix.
// The code was retrieved from: https://stackoverflow.com/questions/
// 27274157/how-to-validate-google-recaptcha-v3-on-server-side. The site
// was last visited on 13-01-2023.
//
// The code has been slightly modified to fit the purpose of my program.
function captcha_check(&$errors) {
    $captcha = $_POST['g-recaptcha-response'] ?? NULL;

    if (!$captcha){
        setError($errors, 'captcha_empty');
    } else {
        $response = json_decode(file_get_contents("https://www.google.com/" .
        "recaptcha/api/siteverify?secret=6LeFivEjAAAAAJt8rR6WIOdokUvPM" .
        "_mWDkcimV34&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']),
        true);

        if($response['success'] == false) {
            setError($errors, 'captcha_error');
        }
    }
}

?>