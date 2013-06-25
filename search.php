<?php
require("config.php");

session_start();
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) {
	$qparts = array();

	$lvlops = array("eq" => "=", "gt" => ">", "lt" => "<", "eqgt" => ">=", "eqlt" => "<=");
	$lvls = array("0", "1", "2", "8", "16", "32", "64", "128");

	$mplyrs = array();

	if (isset($_GET["aliases"]) && $_GET["aliases"] == "yes" && isset($_GET["name"]) && is_string($_GET["name"])) {
		$result = mysql_query("SELECT * FROM `aliases` WHERE `alias` LIKE \"%" . mysql_real_escape_string($_GET["name"]) . "%\" ORDER BY `client_id` ASC");
		$numrows = mysql_num_rows($result);
		$tplyrs = array();
		if ($numrows > 0) {
			while ($ret = mysql_fetch_assoc($result)) {
				$tplyrs[] = $ret;
			}
		} else {
			die("No players found.");
		}

		foreach ($tplyrs as $plyr) {
			if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
				if (intval($_GET["id"]) != $plyr["client_id"]) {
					continue;
				}
			}

			if (isset($_GET["lvlop"]) && array_key_exists($_GET["lvlop"], $lvlops)) {
				$result = mysql_query("SELECT * FROM `clients` WHERE `id`=" . $plyr["client_id"] . " AND `group_bits`" . $lvlops[$_GET["lvlop"]] . $_GET["lvl"]);
				$tf = mysql_num_rows($result);
				if ($tf == 0) {
					continue;
				} else {
					$p = mysql_fetch_assoc($result);
				}
			}

			$mplyrs[] = $p;
		}

		// $mplyrs = array_unique($mplyrs);
		// $ids = array();
		// foreach ($mplyrs as $plyr) {
		// 	if (!in_array($plyr["id"], $ids)) {
		// 		echo("<li><span class=\"rlistpid\">(@" . $plyr["id"] . ")</span>" . $plyr["name"] . "</li>");
		// 		$ids[] = $plyr["id"];
		// 	}
		// }
	}

	if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
		$qparts[] = "`id`=" . $_GET["id"];
	}

	if (isset($_GET["name"]) && is_string($_GET["name"])) {
		$qparts[] = "`name` LIKE \"%" . mysql_real_escape_string($_GET["name"]) . "%\"";
	}

	if (isset($_GET["lvlop"]) && array_key_exists($_GET["lvlop"], $lvlops)) {
		if (isset($_GET["lvl"]) && in_array($_GET["lvl"], $lvls)) {
			$qparts[] = "`group_bits`" . $lvlops[$_GET["lvlop"]] . $_GET["lvl"];
		}
	}

	$beenbanned = true;
	$notbanned = true;
	if (isset($_GET["banstatus"]) && $_GET["banstatus"] == "1") {
		$notbanned = false;
	} else if (isset($_GET["banstatus"]) && $_GET["banstatus"] == "2") {
		$beenbanned = false;
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
	sort($mplyrs);
	if (!empty($mplyrs)) {
		foreach ($mplyrs as $plyr) {
			if (!in_array($plyr["id"], $ids)) {
				$banned = false;
				$pbeenbanned = false;
				$q = mysql_query("SELECT * FROM  `penalties` WHERE  `type` IN ('Ban',  'TempBan') AND `client_id` =". strval($plyr["id"]) . ";");
				while ($p = mysql_fetch_assoc($q)) {
					$pbeenbanned = true;
					if ($p["type"] == "Ban" && $p["inactive"] == 0) {
						$banned = true;
						break;
					} elseif ($p["type"] == "TempBan" && $p["inactive"] == 0 && $p["time_expire"] > time()) {
						$banned = true;
						break;
					}
				}

				if (($beenbanned && $pbeenbanned) || ($notbanned && !$pbeenbanned)) {
					if ($banned == true) {
						echo("<li><span class=\"rlistpid\">(@" . $plyr["id"] . ")</span>" . $plyr["name"] . " - <span class=\"banned\">BANNED</span></li>");
					} else {
						echo("<li><span class=\"rlistpid\">(@" . $plyr["id"] . ")</span>" . $plyr["name"] . "</li>");
					}
				}

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