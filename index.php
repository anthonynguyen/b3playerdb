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
	<link rel="stylesheet" href="assets/jquery.fancybox.css" />
	<script type="text/javascript" src="assets/jquery-2.0.2.min.js"></script>
	<script type="text/javascript" src="assets/jquery.collapse.js"></script>
	<script type="text/javascript" src="assets/jquery.collapse_storage.js"></script>
	<script type="text/javascript" src="assets/jquery.collapse_cookie_storage.js"></script>
	<script type="text/javascript" src="assets/jquery.fancybox.js"></script>
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
				$(".banbutt").fancybox({autoSize: false, width: 500, height: 300});
				document.uid = $("#player_id").html();
				document.uname = $("#player_name").html();
			});
		});

		$("#search").submit(function(e) {
			e.preventDefault();
			$.get("search.php", $(this).serialize(), function(data) {
				$("#resultlist").html(data);
				$("#searchresults h1").text("Search Results ("+$("#resultlist li").length+")");
			});
		});

		$(document).on("click", "#permbanbutt", function(e) {
			e.preventDefault();
			$("#pbdiag h1").html("Permbanning " + document.uname + " (@" + document.uid + ")");
		});
	});
	</script>
</head>
<body>
	<div id="pbdiag" style="display:none">
		<h1></h1>
		<form id="pbform">
			<label for="length">Length:</label><br />
			<input type="number" name="length">
			<select name="unit">
				<option value="minutes">Minutes</option>
				<option value="hours">Hours</option>
				<option value="days">Days</option>
				<option value="weeks">Weeks</option>
				<option value="months">Months</option>
				<option value="years">Years</option>
			</select><br /><br />
			<label for="reason">Reason (link to forum post if possible):</label><br />
			<input type="text" name="reason" placeholder="Reason" /><br /><br />
			<input type="submit" name="go" value="Ban!" />
		</form>
	</div>
	<div id="ubdiag" style="display:none">
		asdasd
	</div>
	<div id="tbdiag" style="display:none">
		asdasd
	</div>
	<div id="container">
		<div id="leftbar">
			<div id="userinfo">Hello, <?php echo($_SESSION["username"]); if($_SESSION["is_admin"]) {echo(" - [A]");} ?></div>
			<div id="logo">b3 player db<div class="ver">v2.0</div></div>
			<div id="searchform">
				<form id="search" method="post">
					<label for="pid">Player ID</label>
					<input type="text" id="pid" name="id" autofocus />
					<label for="pname">Player Name</label>
					<input type="text" id="pname" name="name" />
					<div class="aliases"><label id="aliasesl"><input type="checkbox" id="aliases" name="aliases" value="yes" />Include aliases?</label></div>
					<label>Level</label><br />
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
					<label for="bans">Ban Status</label><br />
					<input type="radio" name="banstatus" value="1">Has past or active bans</input><br />
					<input type="radio" name="banstatus" value="2">Has no past or active bans</input><br />
					<input type="radio" name="banstatus" value="3" checked>Doesn't matter</input><br /><br />
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