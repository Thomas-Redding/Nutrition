<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0">
<title>Nutrition</title>
<script src="scripts/jquery.js"></script>
<script src="shared-widgets/cache.js"></script>
<script src="shared-widgets/shared-gui.js"></script>
<style>
body {
	margin: 0;
	font-family: sans-serif;
}

#master-table {
	width: 100%;
	border-collapse: collapse;
	min-height: 100vh;
	display: block;
}

#master-table > tbody > tr > td {
	padding: 0;
}

#master-table > tbody > tr:first-child > td {
	height: 1.95em;
}

#master-table > tbody > tr:last-child > td {
	height: 4em;
}

</style>
</head>
<body>
<?php
include "shared-widgets/api.html";
?>
<table id="master-table"><tbody>
	<tr><td>
		<?php
			include "shared-widgets/menu-widget.html";
		?>
	</td></tr>
	<tr><td>
		<?php
			include "index-widgets/search-widget.html";
			include "index-widgets/food-chooser-widget.html";
			include "index-widgets/food-adder-widget.html";
			include "index-widgets/food-editor-widget.html";
			include "shared-widgets/day-widget.html";
			include "index-widgets/day-nutrient-summary-widget.html";
		?>
	</td></tr>
	<tr><td>
		<?php
			include "shared-widgets/footer.html";
		?>
	</td></tr>
</tbody>
</table>
</body>
</html>
