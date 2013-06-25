<?php
require("config.php");

session_start();
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) {

// Code goes here

} else {
	header("Location: login.php");
}
?>