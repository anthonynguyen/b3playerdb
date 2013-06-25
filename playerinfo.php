<?php
require("config.php");

session_start();
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) {
	if(isset($_GET["id"]) && is_numeric($_GET["id"])) {
		$result = mysql_query("SELECT * FROM `clients` WHERE `id`=".$_GET["id"]);
		$client = mysql_fetch_assoc($result);

		$banned = false;
		$q = mysql_query("SELECT * FROM  `penalties` WHERE  `type` IN ('Ban',  'TempBan') AND `client_id` =". strval($client["id"]) . ";");
		while ($p = mysql_fetch_assoc($q)) {
			if ($p["type"] == "Ban" && $p["inactive"] == 0) {
				$banned = true;
				break;
			} elseif ($p["type"] == "TempBan" && $p["inactive"] == 0 && $p["time_expire"] > time()) {
				$banned = true;
				break;
			}
		}

		$result = mysql_query("SELECT `name` FROM `groups` WHERE `id`=".$client["group_bits"]);
		$levelstr = mysql_fetch_assoc($result);
		$levelstr = $levelstr["name"];

		if ($banned == true) {
			echo("<h1>${client['name']} (@${client['id']}) - <span class=\"banned\">BANNED</span></h1>");
			if ($_SESSION["is_admin"]) {echo("<a href=\"#ubdiag\" class=\"banbutt\" id=\"unbanbutt\">Unban</a><br /><br />");}
		} else {
			echo("<h1>${client['name']} (@${client['id']})</h1>");
			if ($_SESSION["is_admin"]) {echo("<a href=\"#pbdiag\" class=\"banbutt\" id=\"permbanbutt\">Permban</a><a href=\"#tbdiag\" class=\"banbutt\" id=\"tempbanbutt\" href=\"#\">Tempban</a><br /><br />");}
		}
		
		echo("<div id=\"player_id\" style=\"display:none\">" . $client["id"] . "</div>");
		echo("<div id=\"player_name\" style=\"display:none\">" . $client["name"] . "</div>");
		echo("<div id=\"infodiv\"><h2>Player Info</h2>");
		echo("<table cellspacing=\"0\" id=\"infotable\"><tbody>");

		echo("<tr><td>ID</td><td>${client['id']}</tr>");
		echo("<tr><td>Level</td><td>${levelstr}</tr>");
		echo("<tr><td>GUID</td><td>${client['guid']}</tr>");
		echo("<tr><td>Latest IP</td><td>${client['ip']}</tr>");
		echo("<tr><td>Connections</td><td>${client['connections']}</tr>");
		echo("<tr><td>Greeting</td><td>" . (empty($client["greeting"]) ? "None" : $client["greeting"]) . "</tr>");
		echo("<tr><td>First Seen</td><td>" . date("M j, Y", $client["time_add"]) . "</tr>");
		echo("<tr><td>Last Seen</td><td>" . date("M j, Y", $client["time_edit"]) . "</tr>");

		echo("</tbody></table></div>");

		echo("<div id=\"xlrdiv\"><h2>XLRstats</h2>");
		$result = mysql_query("SELECT * FROM `xlr_playerstats` WHERE `client_id`=" . $client["id"]);
		if (mysql_num_rows($result) == 0) {
			echo("<h3>No xlrstats.</h3>");
		} else {
			echo("<table cellspacing=\"0\" id=\"xlrtable\"><tbody>");
			$xlrstats = mysql_fetch_assoc($result);
			foreach($xlrstats as $attr => $value) {
				echo("<tr><td>${attr}</td><td>${value}</tr>");
			}
			echo("</tbody></table>");
		}
		echo("</div>");

		$result = mysql_query("SELECT * FROM `aliases` WHERE `client_id`=" . $client["id"] . " ORDER BY  `num_used` DESC");
		$num_rows = mysql_num_rows($result);
		echo("<div id=\"aliasdiv\"><h2>Aliases" . " ($num_rows)" . "</h2>");
		if ($num_rows == 0) {
			echo("<h3>No aliases.</h3>");
		} else {
			echo("<table cellspacing=\"0\" id=\"aliastable\"><thead><tr><th>Alias</th><th>Times Used</th><th>Last Used</th></tr></thead><tbody>");
			while ($alias = mysql_fetch_assoc($result)) {
				echo("<tr><td>" . $alias["alias"] . "</td><td>" . $alias["num_used"] . "</td><td>" . date("M j, Y", $alias["time_edit"]) . "</td></tr>");
			}
			echo("</tbody></table>");
		}
		echo("</div>");

		$result = mysql_query("SELECT * FROM `ipaliases` WHERE `client_id`=" . $client["id"] . " ORDER BY  `num_used` DESC");
		$num_rows = mysql_num_rows($result);
		echo("<div id=\"ipaliasdiv\"><h2>IP address aliases" . " ($num_rows)" . "</h2>");
		if ($num_rows == 0) {
			echo("<h3>No IP address aliases</h3>");
		} else {
			echo("<table cellspacing=\"0\" id=\"ipaliastable\"><thead><tr><th>Address</th><th>Times Used</th><th>Last Used</th></tr></thead><tbody>");
			while ($ip = mysql_fetch_assoc($result)) {
				echo("<tr><td>" . $ip["ip"] . "</td><td>" . $ip["num_used"] . "</td><td>" . date("M j, Y", $ip["time_edit"]) . "</td></tr>");
			}
			echo("</tbody></table>");
		}
		echo("</div>");

		$result = mysql_query("SELECT * FROM `penalties` WHERE `client_id`=" . $client["id"] . " ORDER BY  `id` DESC");
		$num_rows = mysql_num_rows($result);
		echo("<div id=\"penaltydiv\"><h2>Penalties" . " ($num_rows)" . "</h2>");
		if ($num_rows == 0) {
			echo("<h3>No penalties.</h3>");
		} else {
			echo("<table cellspacing=\"0\" id=\"penaltytable\"><thead><tr><th>Type</th><th>By</th><th>Reason</th><th>Made</th><th>Expires</th></tr></thead><tbody>");

			$penalties = array();
			while ($penalty = mysql_fetch_assoc($result)) {
				$penalties[] = $penalty;
			}

			foreach ($penalties as $penalty) {
				$result = mysql_query("SELECT `name` FROM `clients` WHERE `id`=".$penalty["admin_id"]);
				$adminrow = mysql_fetch_assoc($result);
				$admin = $adminrow["name"];
				echo("<tr><td>" . $penalty["type"] . "</td><td>" . $admin . "</td><td>" . preg_replace('/\^\d/', "", $penalty["reason"]) . "</td><td>" . date("M j, Y", $penalty["time_add"]) . "</td><td>" . ($penalty["time_expire"] == -1 ? "Never" : ($penalty["time_expire"] == 0 ? "n/a" : date("M j, Y", $penalty["time_expire"]))) . "</td></tr>");
			}
			echo("</tbody></table>");
		}
		echo("</div>");

		$result = mysql_query("SELECT * FROM `penalties` WHERE `admin_id`=" . $client["id"] . " ORDER BY  `id` DESC");
		$num_rows = mysql_num_rows($result);
		echo("<div id=\"penaltiesgivendiv\"><h2>Penalties Assigned" . " ($num_rows)" . "</h2>");
		if ($num_rows == 0) {
			echo("<h3>No penalties assigned.</h3>");
		} else {
			echo("<table cellspacing=\"0\" id=\"penaltyassignedtable\"><thead><tr><th>Type</th><th>To</th><th>Reason</th><th>Made</th><th>Expires</th></tr></thead><tbody>");

			$penalties = array();
			while ($penalty = mysql_fetch_assoc($result)) {
				$penalties[] = $penalty;
			}

			foreach ($penalties as $penalty) {
				$result = mysql_query("SELECT `name` FROM `clients` WHERE `id`=".$penalty["client_id"]);
				$adminrow = mysql_fetch_assoc($result);
				$admin = $adminrow["name"];
				echo("<tr><td>" . $penalty["type"] . "</td><td>" . $admin . "</td><td>" . preg_replace('/\^\d/', "", $penalty["reason"]) . "</td><td>" . date("M j, Y", $penalty["time_add"]) . "</td><td>" . ($penalty["time_expire"] == -1 ? "Never" : ($penalty["time_expire"] == 0 ? "n/a" : date("M j, Y", $penalty["time_expire"]))) . "</td></tr>");
			}
			echo("</tbody></table>");
		}
		echo("</div>");
	} else {
		echo("Invalid");
	}
} else {
	header("Location: login.php");
}
?>