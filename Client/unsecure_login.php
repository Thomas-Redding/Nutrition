<html>
<head>
<script src="shared-widgets/cache.js"></script>
</head>
<body>
<?php

function read_file($path) {
	if (file_exists($path)) {
		$f = fopen($path, "r");
		$file_str = fread($f, filesize($path));
		fclose($f);
		return $file_str;
	}
	else
		return NULL;
}

function write_file($path, $str) {
	$f = fopen($path, "w+");
	$file_str = fwrite($f, $str);
	fclose($f);
}

$username = $_GET["username"];
$password = $_GET["password"];
$path = "../Database/Users/passwords/" . $username . ".txt";
$true_password = read_file($path);

if ($true_password == $password) {
	$token = rand(0, 1000000000);
	$path = "../Database/Users/tokens/" . $username . ".txt";
	write_file($path, $token);
	echo "<span id=\"token\">" . $token . "</span>";
	echo "<span id=\"username\">" . $username . "</span>";
}
else {
	echo "<span id=\"token\">error</span>";
}


?>
<script>
var token = document.getElementById("token").innerHTML;
if (token == "error") {
	window.location.href = "login.php?login=failed";
}
else {
	var username = document.getElementById("username").innerHTML;
	if (cache.isCachingAvailable()) {
		cache.setSecurityToken(parseInt(token));
		cache.setUsername(username);
		window.location.href = "index.php";
	}
	else {
		alert("Please enable caching");
	}
}
</script>
</body>
</html>