<?php

if(!isset($_SESSION)) { session_start(); }

if (isset($_SESSION['login'])) {
	session_destroy();
	unset($_SESSION['login']);
	header("Location: index.php");
} else {
	header("Location: index.php");
}

?>