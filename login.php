<?php
require("config.php");

if (isset($_POST["b3id"]) && is_numeric($_POST["b3id"])) {
	$q = mysql_query("SELECT * FROM `clients` WHERE `id`=".$_POST["b3id"]);
	$r = mysql_fetch_assoc($q);
	$pw = $r["password"];
	if (isset($_POST["password"]) && (hash("md5", $_POST["password"]) == $pw)) {
		session_start();
		$_SESSION["logged_in"] = TRUE;
		$_SESSION["username"] = $r["name"];
		$_SESSION["is_admin"] = FALSE;
		if ($r["group_bits"] == 128) {
			$_SESSION["is_admin"] = TRUE;
		}
		header("Location: index.php");
	} else {
		$message = "<div id=\"pwincorrect\">Password incorrect.<div>";
	}
	
} elseif (isset($_POST["b3id"])) {
	$message = "<div id=\"pwincorrect\">Invalid ID.<div>";
}


// if (isset($_POST["password"]) && $_POST["password"] == PLAYERDB_PASSWORD) {
// 	session_start();
// 	$_SESSION["logged_in"] = TRUE;
// 	header("Location: index.php");
// } elseif (isset($_POST["password"]) && $_POST["password"] != PLAYERDB_PASSWORD) {
// 	$message = "<div id=\"pwincorrect\">Password incorrect.<div>";
// }
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="assets/style.css" />
</head>
<body>
	<div id="loginpage">
		<div id="login">
			<?php if(isset($message)) {echo $message;} ?>
			<form action="login.php" method="post">
				<input type="text" name="b3id" id="login_b3id" placeholder="B3 ID" autofocus />
				<input type="password" name="password" id="login_pass" placeholder="Password" />
				<input type="submit" value="Log in" />
			</form>
		</div>
	</div>
</body>
</html>