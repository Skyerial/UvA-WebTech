<?php

// Delete the login cookies by setting its expiration time to a past date:
setcookie("login", "", time() - 3600, "/", "", false, true);
setcookie("checker", "", time() - 3600, "/", "", false, true);
header("Location: ../index.php");

?>