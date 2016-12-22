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


$username = $_GET["username"];
$day = $_GET["day"];
$token = $_GET["token"];

$path = "../Users/tokens/" . $username . ".txt";
if (read_file($path) == $token) {
	$path = "../Users/diaries/" . $username . "*" . $day . ".txt";
	$json = read_file($path);
	if ($json == NULL)
		echo "{}";
	else
		echo $json;
}
else {
	echo "{\"error\": \"invalid token\"}";
}

?>
