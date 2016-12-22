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

function ageToIndex($age) {
	if ($age < 0.5)
		return 0;
	else if ($age < 1)
		return 1;
	else if ($age < 3)
		return 2;
	else if ($age < 8)
		return 3;
	else if ($age < 13)
		return 4;
	else if ($age < 18)
		return 5;
	else if ($age < 30)
		return 6;
	else if ($age < 50)
		return 7;
	else if ($age < 70)
		return 8;
	else
		return 9;
}


$username = $_GET["username"];
$token = $_GET["token"];

$path = "../Users/tokens/" . $username . ".txt";
if (read_file($path) == $token || true) {
	$path = "../Users/demographic-info/" . $username . ".txt";
	$json = read_file($path);
	if ($json == NULL)
		echo "{}";
	else {
		$json = (array) json_decode($json);
		$lifestyle = $json["lifestyle"];
		$age = $json["age"];
		$weight = $json["weight"];
		$calorie_goal = $json["calorie_goal"];
		$protein_goal = $json["protein_goal"];
		$lifestyle = $lifestyle[0];
		$age_index = ageToIndex($age);
		$path = "../Database-Files/Daily-Recommended-Intakes/" . $lifestyle . $age_index . ".txt";
		$json = read_file($path);
		$json = (array) json_decode($json);
		foreach ($json["per_kilogram"] as $key => $value) {
			$json[$key] = $value * $weight;
		}
		unset($json["per_kilogram"]);
		$json["calorie_goal"] = $calorie_goal;
		$json["protein_goal"] = $protein_goal;
		echo json_encode($json);
	}
}
else {
	echo "{\"error\": \"invalid token\"}";
}

?>
