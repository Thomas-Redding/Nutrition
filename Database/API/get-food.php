<?php

class IndexLenPair {
	public $index;
	public $len;
	function __construct($i, $l) {
		$this->index = (int) $i;
		$this->len = (int) $l;
	}
}

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

function get_atomic_food($index) {
	$path = "";
	if ($index < 10000000)
		$path = "../Database-Files/Food-Data/" . $index . ".txt";
	else if ($index < 20000000)
		$path = "../Database-Files/User-Foods/" . $index . ".txt";
	if ($path == "")
		return NULL;
	return read_file($path);
}

function get_composite_food($index) {
	$path = "../Database-Files/Composite-Food/" . $index . ".txt";
	return read_file($path);
}

function main() {
	$index = $_GET["index"];
	$json = get_atomic_food($index);

	if ($json == NULL) {
		$json = get_composite_food($index);
		if ($json == NULL) {
			echo "{}";
			return;
		}
		$json = json_decode($json);
		foreach ($json["components"] as $pair) {
			$atomic_json = get_atomic_food($pair["index"]);
			if ($atomic_json == NULL) {
				echo "{}";
				return;
			}
			foreach ($atomic_json["nutrients"] as $key => $value) {
				if (array_key_exists($key, $json["nutrients"])) {
					$json["nutrients"][$key] += $pair["weight"] / 100 * $value;
				}
				else {
					$json["nutrients"][$key] = $pair["weight"] / 100 * $value;
				}
			}
			echo json_encode($json);
			return;
		}
	}
	else {
		echo $json;
	}
}

main();

?>