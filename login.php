<?php
require("config.php");

if (isset($_POST["password"]) && $_POST["password"] == PLAYERDB_PASSWORD) {
	session_start();
	$_SESSION["logged_in"] = TRUE;
	header("Location: index.php");
} elseif (isset($_POST["password"]) && $_POST["password"] != PLAYERDB_PASSWORD) {
	$message = "<div id=\"pwincorrect\">Password incorrect.<div>";
}
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
				<input type="password" name="password" id="login_pass" autofocus="autofocus" />
				<input type="submit" value="Log in" />
			</form>
		</div>
	</div>
</body>
</html>