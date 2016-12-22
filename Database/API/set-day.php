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
	fwrite($f, $str);
	fclose($f);
}


$username = $_GET["username"];
$day = $_GET["day"];
$token = $_GET["token"];
$json = $_GET["json"];

$path = "../Users/tokens/" . $username . ".txt";
if (read_file($path) == $token) {
	$path = "../Users/diaries/" . $username . "*" . $day . ".txt";
	$success = write_file($path, $json);
	echo "{}";
}
else {
	echo "{\"error\": \"invalid token\"}";
}

?>
