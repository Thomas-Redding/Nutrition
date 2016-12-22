<html>
<head>
</head>
<body>
<?php

function write_file($path, $str) {
	$f = fopen($path, "w+");
	$file_str = fwrite($f, $str);
	fclose($f);
}

$username = $_GET["username"];
$path = "../Database/Users/tokens/" . $username . ".txt";
write_file($path, -1);
?>
<script>
window.location.href = "login.php";
</script>
</body>
</html>