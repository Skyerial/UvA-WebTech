<?php

function close_connection($conn) {
    if (is_resource($conn)) {
        mysqli_close($conn);
    }
}

?>