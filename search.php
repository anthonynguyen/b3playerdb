<?php
require("config.php");

session_start();
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) {
	$qparts = array();

	$lvlops = array("eq" => "=", "gt" => ">", "lt" => "<", "eqgt" => ">=", "eqlt" => "<=");
	$lvls = array("0", "1", "2", "8", "16", "32", "64", "128");

	$mplyrs = array();

	if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
		$qparts[] = "`id`=" . $_GET["id"];
	}

	if (isset($_GET["name"]) && is_string($_GET["name"])) {
		if (isset($_GET["aliases"]) && $_GET["aliases"] == "yes") {
			$qparts[] = "(`id` IN (SELECT `client_id` FROM `aliases` WHERE `alias` LIKE \"%" . mysql_real_escape_string($_GET["name"]) . "%\") OR `name` LIKE \"%" . mysql_real_escape_string($_GET["name"]) . "%\")";
		} else {
			$qparts[] = "`name` LIKE \"%" . mysql_real_escape_string($_GET["name"]) . "%\"";
		}
	}

	if (isset($_GET["lvlop"]) && array_key_exists($_GET["lvlop"], $lvlops)) {
		if (isset($_GET["lvl"]) && in_array($_GET["lvl"], $lvls)) {
			$qparts[] = "`group_bits`" . $lvlops[$_GET["lvlop"]] . $_GET["lvl"];
		}
	}

	$query = "SELECT * FROM `clients` WHERE " . implode(" AND ", $qparts) . " ORDER BY `id` ASC";
	$result = mysql_query($query);
	$numrows = mysql_num_rows($result);
	if ($numrows > 0) {
		while ($ret = mysql_fetch_assoc($result)) {
			$mplyrs[] = $ret;
			// echo("<li><span class=\"rlistpid\">(@" . $ret["id"] . ")</span>" . $ret["name"] . "</li>");
		}
	}

	$ids = array();
	if (!empty($mplyrs)) {
		sort($mplyrs);

		foreach ($mplyrs as $plyr) {
			if (!in_array($plyr["id"], $ids)) {
				echo("<li><span class=\"rlistpid\">(@" . $plyr["id"] . ")</span>" . $plyr["name"] . "</li>");
				$ids[] = $plyr["id"];
			}
		}
	} else {
		echo("No players found.");
	}
	
} else {
	header("Location: login.php");
}
?>