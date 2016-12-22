<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1.0, minimum-scale=1.0, maximum-scale=1.0">
<title>Energy</title>
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
			include "shared-widgets/day-widget.html";
		?>
	</td></tr>
	<tr><td style="vertical-align: top;">
		<table id="main-table"><thead>
			<tr>
				<td onclick="weight_widget.unfocus(); exercise_widget.focus();" id="exercise-button">Exercise</td>
				<td onclick="exercise_widget.unfocus(); weight_widget.focus();" id="weight-button">Weight</td></tr>
			</thead><tbody>
			<tr><td colspan="2">x</td></tr>
			<tr><td colspan="2">x</td></tr>
		</tbody></table>
		<style>
			#main-table {
				margin: 0 auto;
				border-collapse: collapse;
				border: 1px solid hsl(0, 0%, 90%);
			}

			#main-table td {
				padding: 0.5em;
				width: 50%;
			}

			#main-table > thead {
				font-weight: bold;
				text-align: center;
			}

			#main-table > thead > tr > td {
				cursor: pointer;
				background-color: hsl(0, 0%, 90%);
			}

			#main-table > tbody > tr:last-child > td {
				border-top: 1px solid hsl(0, 0%, 90%);
			}

			#main-table input[type=number] {
				width: 4em;
			}

			#main-table .remove_icon {
				height: 1.2em;
				cursor: pointer;
				vertical-align: middle;
			}
		</style>
		<script>
			function Exercise_Widget(d) {
				var self = this;
				self.div = d;

				self.adder_div = self.div.find("tbody > tr:first-child > td");
				self.history_div = self.div.find("tbody > tr:last-child > td");

				self.exercise_forms = {
					"Cardio": "<td>Heart Rate</td><td><input type=\"number\" onchange=\"exercise_widget.update_calories_burned()\" onkeyup=\"exercise_widget.update_calories_burned()\"/></td>",
					"Running": "<td>Miles</td><td><input type=\"number\" onchange=\"exercise_widget.update_calories_burned()\" onkeyup=\"exercise_widget.update_calories_burned()\"/></td>",
					"Elliptical": "<td colspan=\"2\"></td>",
					"Biking": "<td>Miles</td><td><input type=\"number\" onchange=\"exercise_widget.update_calories_burned()\" onkeyup=\"exercise_widget.update_calories_burned()\"/></td>"
				}

				self.exercise_functions = {
					"Cardio": function(time, heart_rate, weight, age, is_male) {
						/*
						 * time - minutes
						 * heart_rate - beats per minute
						 * weight - kilograms
						 * age - years
						 * is_male - boolean
						 * src - http://fitnowtraining.com/2012/01/formula-for-calories-burned/
						 */
						if (isNaN(heart_rate))
							return 0;
						if (is_male) {
							return (age * 0.2017 - weight * 0.09036 + heart_rate * 0.6309 - 55.0969) * time / 4.184;
						}
						else {
							return (age * 0.074 - weight * 0.05741 + heart_rate * 0.4472 - 20.4022) * time / 4.184;
						}
					},
					"Running": function(time, distance, weight, age, is_male) {
						/*
						 * distance - in miles
						 * time - in minutes
						 * weight - in kilograms
						 * src - http://fitness.stackexchange.com/questions/15608/energy-expenditure-calories-burned-equation-for-Running
						 */
						if (isNaN(distance))
							return 0;
						var km = distance * 1.6;
						var hours = time / 60;
						var kph = km / hours;
						var VO2 = 2.209 + 3.1633 * kph;
						var kcal_per_min = 4.86 * weight * VO2 / 1000
						return kcal_per_min * time;
					},
					"Elliptical": function(time, ignore, weight, age, is_male) {
						/*
						 * time - in minutes
						 * weight - in kilograms
						 * src - http://www.webmd.com/fitness-exercise/healthtool-exercise-calculator
						 */
						return weight * time / 10.905125409;
					},
					"Biking": function(time, distance, weight, age, is_male) {
						/*
						 * distance - in miles
						 * time - in minutes
						 * weight - in kilograms
						 * src - http://www.ilovebicycling.com/how-many-calories-do-you-burn-when-cycling/
						 */
						if (isNaN(distance))
							return 0;
						var speed = distance / time * 60;
						var energy_per_minute = 0.453658537 * (speed - 4.98924732);
						return time * weight * energy_per_minute / 27.272727269;
					},
				}

				self.focus = function() {
					$("#exercise-button").css("background-color", "hsl(0, 0%, 80%)");

					var html = "<table><tbody><tr><td>Exercise</td><td><select onchange=\"exercise_widget.exercise_changed();\">";
					for (key in self.exercise_forms) {
						html += "<option value=\"" + key + "\">";
						html += key.replace(/-/g,' ')
						html += "</option>";
					}
					html += "</select></td></tr>";
					html += "<tr id=\"exercise-minutes\"><td>Minutes</td><td><input type=\"number\" onchange=\"exercise_widget.update_calories_burned()\" onkeyup=\"exercise_widget.update_calories_burned()\" /></td></tr>";
					html += "<tr id=\"exercise-details\"><td></td><td></td></tr>";
					html += "<tr id=\"calories-burned\"><td colspan=\"2\" style=\"text-align: center;\"></td></tr>";
					html += "<tr><td colspan=\"2\" style=\"text-align: center;\"><input type=\"button\" value=\"Submit\" onclick=\"exercise_widget.add_exercise();\"/></td></tr>";
					html += "</tbody></table>";
					self.adder_div.html(html);
					self.history_div.html("");
					self.exercise_changed();
					self.update_exercise_history();
				}
				self.unfocus = function() {
					$("#exercise-button").css("background-color", "hsl(0, 0%, 90%)");
				}
				self.exercise_changed = function() {
					var exercise_type = self.adder_div.find("select").val();
					$("#exercise-details").html(self.exercise_forms[exercise_type]);
					self.update_calories_burned();
				}
				self.update_calories_burned = function() {
					var exercise_type = self.adder_div.find("select").val();
					var minutes = $("#exercise-minutes input").val();
					var details = parseFloat($("#exercise-details input").val());
					if (minutes <= 0 || details <= 0 || isNaN(minutes)) {
						$("#calories-burned > td").html("0 Calories");
					}
					else {
						var burn = self.exercise_functions[exercise_type](minutes, details, 71.3, 21, true);
						burn = Math.max(burn, 0);
						$("#calories-burned > td").html(Math.round(burn) + " Calories");
					}
				}
				self.exercise_history = [
					{
						"Type": "Running",
						"Time": 8,
						"Distance": 1,
						"Weight": 71,
						"Calories": 111
					}
				]
				self.update_exercise_history = function() {
					var html = "<table style=\"width: 100%;\"><tbody>";
					html += "<tr><td><b>Exercise</b></td><td><b>Cal</b></td></tr>";
					var total_burn = 0;
					var counter = 0;
					for (key in self.exercise_history) {
						html += "<tr><td>" + self.exercise_history[key].Type + "</td><td>" + self.exercise_history[key].Calories + "</td><td><img onclick=\"exercise_widget.remove_exercise(" + counter + ")\" class=\"remove_icon\" src=\"images/close.svg\"></td></tr>";
						total_burn += self.exercise_history[key].Calories;
						++counter;
					}
					html += "<tr><td><b>Total</b></td><td><b>" + total_burn + "</b></td></tr>";
					html += "</tbody></table>";
					self.history_div.html(html);
				}

				self.add_exercise = function() {
					var exercise_type = self.adder_div.find("select").val();
					var min = parseFloat($("#exercise-minutes input").val());
					var details_type = self.label_to_details_type($("#exercise-details td:first-child").html());
					var details = parseFloat($("#exercise-details input").val());
					var burn = parseInt($("#calories-burned > td").html());
					self.exercise_history.push({
						"Type": exercise_type,
						"Time": min,
						"Calories": burn
					});
					self.exercise_history[self.exercise_history.length - 1][details_type] = details;
					// todo: call api to update server
				}

				self.remove_exercise = function(index) {
					self.exercise_history.splice(index, 1);
					self.update_exercise_history();
					// todo: call api to update server
				}

				self.label_to_details_type = function(str) {
					return str.replace(/ /g, '_');
				}
			}

			function Weight_Widget(d) {
				var self = this;
				self.div = d;
				self.adder_div = self.div.find("tbody > tr:first-child > td");
				self.history_div = self.div.find("tbody > tr:last-child > td");

				self.todays_weight = null;

				self.focus = function() {
					$("#weight-button").css("background-color", "hsl(0, 0%, 80%)");
					var html = "<table style=\"min-width: 100%;\"><tbody>";
					html += "<tr><td style=\"text-align: right; padding-right: 0; width: 50%;\"><input type=\"number\" onchange=\"exercise_widget.update_calories_burned()\" onkeyup=\"exercise_widget.update_calories_burned()\" /></td>";
					html += "<td style=\"padding-left: 0; width: 50%;\"><select><option>lb</option><option>kg</option></select></td></tr>";
					html += "<tr><td colspan=\"2\" style=\"text-align: center;\"><input type=\"button\" value=\"Submit\" onclick=\"weight_widget.add_weight();\"/></td></tr>";
					html += "</tbody></table>";
					self.adder_div.html(html);
					self.update_history();
				}

				self.unfocus = function() {
					$("#weight-button").css("background-color", "hsl(0, 0%, 90%)");
				}

				self.add_weight = function() {
					var weight = self.adder_div.find("input[type=number]").val();
					var units = self.adder_div.find("select").val();
					if (units == "lb")
						self.todays_weight = weight;
					else
						self.todays_weight = 2.2 * weight;
					self.update_history();
				}

				self.update_history = function() {
					if (self.todays_weight == null) {
						self.history_div.html("You haven't entered a weight today.");
					}
					else {
						self.history_div.html("You weighed in at " + Math.round(self.todays_weight * 10) / 10 + " pounds today. <img src=\"images/close.svg\" class=\"remove_icon\" onclick=\"weight_widget.remove_weight();\">");
					}
				}

				self.remove_weight = function() {
					self.todays_weight = null;
					self.update_history();
					// call api to update weight
				}
			}

			

			var exercise_widget = new Exercise_Widget($("#main-table"));
			var weight_widget = new Weight_Widget($("#main-table"));
			weight_widget.unfocus();
			exercise_widget.focus();

			function get_days_weight_and_exercises() {
				// todo: call api to get weight and exercises
			}

			get_days_weight_and_exercises();
			day_widget.date_changed_callback = get_days_weight_and_exercises;
		</script>
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
