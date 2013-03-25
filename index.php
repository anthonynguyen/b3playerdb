<?php
require("config.php");

session_start();
if (isset($_SESSION["logged_in"]) && $_SESSION["logged_in"]) {
?>
<!DOCTYPE html>
<html>
<head>
	<title>B3 Player DB</title>
	<link rel="stylesheet" href="assets/style.css" />
	<script type="text/javascript" src="assets/jquery-1.8.1.min.js"></script>
	<script type="text/javascript" src="assets/jquery.collapse.js"></script>
	<script type="text/javascript" src="assets/jquery.collapse_storage.js"></script>
	<script type="text/javascript" src="assets/jquery.collapse_cookie_storage.js"></script>
	<script>
	$(document).ready(function() {
		window.lastli = $(document);
		$("#resultlist").on("click", "li", function() {
			window.lastli.removeClass("active");
			$(this).addClass("active");
			window.lastli = $(this);
			$.get("playerinfo.php", {id: $(".rlistpid", this).text().slice(2, -1)}, function(data) {
				$("#pinfocont").html(data);
				$("#pinfocont div").collapse({persist: true});
			});
		});

		$("#search").submit(function(e) {
			e.preventDefault();
			$.get("search.php", $(this).serialize(), function(data) {
				$("#resultlist").html(data);
				$("#searchresults h1").text("Search Results ("+$("#resultlist li").length+")");
			});
		});
	});
	</script>
</head>
<body>
	<div id="container">
		<div id="leftbar">
			<div id="logo">b3 player db<div class="ver">v1.0</div></div>
			<div id="searchform">
				<form id="search" method="post">
					<label class="pid" for="pid">Player ID</label>
					<input type="text" id="pid" name="id" />
					<label class="pname" for="pname">Player Name</label>
					<input type="text" id="pname" name="name" />
					<div class="aliases"><label id="aliasesl"><input type="checkbox" id="aliases" name="aliases" value="yes" />Include aliases?</label></div>
					<label class="plevel">Level</label><br />
					<select name="lvlop">
						<option value="eqlt">Equal to or lower than</option>
						<option value="lt">Lower than</option>
						<option value="eq">Equal to</option>
						<option value="gt">Higher than</option>
						<option value="eqgt" selected>Equal to or higher than</option>
					</select>
					<select name="lvl">
						<option value="0" selected>Guest</option>
						<option value="1">User</option>
						<option value="2">Regular</option>
						<option value="8">Moderator</option>
						<option value="16">Admin</option>
						<option value="32">Full Admin</option>
						<option value="64">Senior Admin</option>
						<option value="128">Super Admin</option>
					</select>
					<input type="reset" value="Reset" /><input type="submit" value="Submit" />
				</form>
			</div>
		</div>

		<div id="searchresults">
			<h1>Search Results</h1>
			<ul id="resultlist">
			</ul>
		</div>

		<div id="playerinfo">
			<div id="pinfocont">
			</div>
		</div>
	</div>
</body>
</html>
<?php
} else {
	header("Location: login.php");
}
?>