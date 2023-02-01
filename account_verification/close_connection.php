<?php

// close_connection checks if there is a connection with the database and if
// there is a connection, it is closed.
//
// Input:
//  $conn: Variable, with which connection can be laid with the database.
//
// Output: None
function close_connection($conn) {
    if (is_resource($conn)) {
        mysqli_close($conn);
    }
}

?>