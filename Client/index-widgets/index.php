<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0">
<title>Nutrition</title>
<script src="scripts/jquery.js"></script>
<script src="scripts/cache.js"></script>
<script src="scripts/shared-gui.js"></script>
<style>
body {
	margin: 0;
	font-family: sans-serif;
	background-color: hsl(0, 0%, 20%);
}
</style>
</head>
<body>
<?php
include "scripts/api.html";
include "shared-widgets/menu-widget.html";
include "index-widgets/search-widget.html";
include "index-widgets/food-chooser-widget.html";
include "index-widgets/food-adder-widget.html";
include "index-widgets/day-widget.html";
include "index-widgets/day-nutrient-summary-widget.html";
include "shared-widgets/footer.html";
?>
</body>
</html>
