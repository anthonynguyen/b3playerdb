<?php
define("SERVER_BANLIST", "/path/to/banlist.txt")

define("MYSQL_HOST", "host.of.mysql.db");
define("MYSQL_USER", "mysql_user");
define("MYSQL_PASSWORD", "mysq_password");
define("MYSQL_DBNAME", "b3_database_name");

$conn = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);

if (!$conn) {
	die("Could not connect to the MySQL server.");
}

$select_db = mysql_select_db(MYSQL_DBNAME);

if (!$select_db) {
	die("Could not select database " . MYSQL_DBNAME . ".");
}
?>