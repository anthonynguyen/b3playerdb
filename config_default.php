<?php
define("PLAYERDB_PASSWORD", "");

define("MYSQL_HOST", "");
define("MYSQL_USER", "");
define("MYSQL_PASSWORD", "");
define("MYSQL_DBNAME", "");

$conn = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);

if (!$conn) {
	die("Could not connect to the MySQL server.");
}

$select_db = mysql_select_db(MYSQL_DBNAME);

if (!$select_db) {
	die("Could not select database " . MYSQL_DBNAME . ".");
}
?>