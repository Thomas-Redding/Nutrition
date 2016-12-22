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

$token = read_file("../Users/tokens/" . $_GET["username"] . ".txt");

if ($token == $_GET["token"]) {
	echo "{\"response\": 1}";
}
else {
	echo "{\"response\": 0}";
}

?>