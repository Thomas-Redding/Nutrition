<?php

class IndexLenPair {
	public $index;
	public $relevance;
	public $popularity;
	function __construct($i, $l, $p) {
		$this->index = (int) $i;
		$this->relevance = (int) $l;
		$this->popularity = (int) $p;
	}
}

function getFoodData($index) {
	$path = "../Database-Files/Food-Data/" . $index . ".txt";
	if (file_exists($path)) {
		$f = fopen($path, "r");
		$file_str = fread($f, filesize($path));
		fclose($f);
		return $file_str;
	}
	else
		return NULL;
}

function getFoodsWithTrichar($trichar) {
	$path = "../Database-Files/Trichars-To-Index/" . $trichar . ".txt";
	if (file_exists($path)) {
		$f = fopen($path, "r");
		$file_str = fread($f, filesize($path));
		$matches = explode(";", $file_str);
		for ($k = 0; $k < count($matches); ++$k) {
			$temp = explode(",", $matches[$k]);
			if (sizeof($temp) == 2)
				$matches[$k] = new IndexLenPair($temp[0], $temp[1], 0);
			else
				$matches[$k] = new IndexLenPair($temp[0], $temp[1], $temp[2]);
		}
		fclose($f);
		return $matches;
	}
	else
		return NULL;
}


$search_str = $_GET["str"];
$max_results_to_return = 50;

$search_str = preg_replace("/[^A-Za-z0-9 ]/", '', $search_str);
$search_arr = split(" ", strtolower($search_str));

$trichars = array();
for ($i = 0; $i < count($search_arr); ++$i) {
	for ($j = 0; $j < strlen($search_arr[$i])-2; ++$j) {
		$current_trichar = substr($search_arr[$i], $j, 3);
		$trichars[$current_trichar] = 1;
	}
}

$results = array();
$popularity = array();

foreach ($trichars as $trichar => $value) {
	$trichar_matches = getFoodsWithTrichar($trichar);
	if ($trichar_matches == NULL)
		continue;
	foreach ($trichar_matches as &$pair) {
		$pair->index = $pair->index . "";
		if (array_key_exists($pair->index, $results))
			$results[$pair->index] = $results[$pair->index] + $pair->relevance;
		else {
			$results[$pair->index] = $pair->relevance;
			$popularity[$pair->index] = $pair->popularity;
		}
	}
}

$results_list = [];
foreach ($results as $index => $val)
	array_push($results_list, [(int) $index, $results[$index] * ($popularity[$index] + 1)]);

function foo($bar, $baz) {
	return $bar[1] < $baz[1] ? 1 : -1;
}

usort($results_list, "foo");

$output = "[";
for ($i = 0; $i < $max_results_to_return; ++$i) {
	if ($i >= count($results_list))
		break;
	if ($i != 0)
		$output .= ",";
	$food_info = getFoodData($results_list[$i][0]);
	$output .= "{\"index\": " . $results_list[$i][0] . ", \"info\": " . $food_info . ", \"popularity\": " . $popularity[$results_list[$i][0]] . "}";
}
$output .= "]";

echo $output;
?>