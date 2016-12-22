# -*- coding: latin-1 -*-

import os
import json
import math

def read_food_data(file_name):
	f = open("../Database-Files/Food-Data/" + file_name, "r")
	file_string = unicode(f.read(), 'latin-1')
	return json.loads(file_string)

def write_food_data(file_name, obj):
	f = open("../Database-Files/Food-Data/" + file_name, "w")
	f.write(json.dumps(obj))

def read_food_name(file_name):
	f = open("../Database-Files/Index-To-Name/" + file_name, "r")
	file_string = unicode(f.read(), 'latin-1')
	return json.loads(file_string)

def write_food_name(file_name, obj):
	f = open("../Database-Files/Index-To-Name/" + file_name, "w")
	f.write(json.dumps(obj))

f = open("../Database-Files/Daily-Recommended-Intakes/DRIs.txt", "r")
dris = f.read().splitlines()
for i in range(0, len(dris)):
	dris[i] = dris[i].split('\t')
	if i == 0:
		continue
	for j in range(0, len(dris[i])):
		if j == 1:
			continue
		dris[i][j] = float(dris[i][j])


template = {
	"male": [0, 0.5, 1, 3, 8, 13, 18, 30, 50, 70],
	"female": [0, 0.5, 1, 3, 8, 13, 18, 30, 50, 70],
	"pregnant": [13, 18, 30, 50],
	"lactating": [13, 18, 30, 50]
}

nutrient_to_col = {}
for i in range(2, len(dris[0])):
	nutrient_to_col[dris[0][i]] = i

def lifestyle_age_to_row(lifestyle, age):
	if lifestyle == "male":
		if age <= 0.5:
			return 1
		elif age <= 1:
			return 2
		elif age <= 3:
			return 3
		elif age <= 8:
			return 4
		elif age <= 13:
			return 5
		elif age <= 18:
			return 6
		elif age <= 30:
			return 7
		elif age <= 50:
			return 8
		elif age <= 70:
			return 9
		else:
			return 10
	elif lifestyle == "female":
		if age <= 0.5:
			return 1
		elif age <= 1:
			return 2
		elif age <= 3:
			return 3
		elif age <= 8:
			return 4
		elif age <= 13:
			return 11
		elif age <= 18:
			return 12
		elif age <= 30:
			return 13
		elif age <= 50:
			return 14
		elif age <= 70:
			return 15
		else:
			return 16
	elif lifestyle == "pregnant":
		if age <= 13:
			return -1
		elif age <= 18:
			return 17
		elif age <= 30:
			return 18
		elif age <= 50:
			return 19
		else:
			return -1
	elif lifestyle == "pregnant":
		if age <= 13:
			return -1
		elif age <= 18:
			return 20
		elif age <= 30:
			return 21
		elif age <= 50:
			return 22
		else:
			return -1
	return -1

nutrients_to_consider = [
	"Calcium, Ca",
	"Copper, Cu",
	"Fiber, total dietary",
	"Iron, Fe",
	"Isoleucine",
	"Leucine",
	"Lysine",
	"Magnesium, Mg",
	"Manganese, Mn",
	"Methionine",
	"Niacin",
	"Pantothenic acid",
	"Phenylalanine",
	"Phosphorus, P",
	"Riboflavin",
	"Selenium, Se",
	"Threonine",
	"Tryptophan",
	"Valine",
	"Vitamin A, RAE",
	"Vitamin B-12",
	"Vitamin B-6",
	"Vitamin C, total ascorbic acid",
	"Vitamin D (D2 + D3)",
	"Vitamin E (alpha-tocopherol)",
	"Vitamin K (phylloquinone)",
	"Zinc, Zn"
]

amino_acids = {
	"Histidine": 10,
	"Isoleucine": 10,
	"Leucine": 39,
	"Lysine": 30,
	"Methionine": 10.4,
	"Cystine": 4.1,
	"Phenylalanine": 12.5,
	"Tyrosine": 12.5,
	"Threonine": 15,
	"Tryptophan": 4,
	"Valine": 26
}

# https://health.gov/dietaryguidelines/2015/guidelines/appendix-2/
# http://fittobepregnant.com/pregnancy-breastfeeding-calorie-calculator/
calorie_reqs = {
	"male": [1000, 1000, 1000, 1300, 1700, 2300, 2400, 2300, 2100],
	"female": [1000, 1000, 1000, 1200, 1500, 1800, 2000, 1800, 1600],
	"pregnant": [2100, 2300, 2100],
	"lactating": [2100, 2300, 2100]
}

# https://www.ncbi.nlm.nih.gov/pmc/articles/PMC1622953/?page=3
avg_weights = {
	"male": [30, 30, 30, 40, 74, 125, 157, 167, 161, 155],
	"female": [28, 28, 28, 41, 73, 115, 125, 135, 147, 138],
	"pregnant": [115, 125, 135],
	"lactating": [115, 125, 135],
}


directory = "../Database-Files/Food-Data/"
counter = 0
calorie_normalization = 300.0

for filename in os.listdir(directory):
	if not filename.endswith(".txt"):
		continue
	if counter % 100 == 0:
		print counter
	obj = read_food_data(filename)
	obj["healthiness"] = {}
	for lifestyle in template:
		obj["healthiness"][lifestyle] = []
		for i in range(1, len(template[lifestyle])):
			min_age = template[lifestyle][i-1]
			max_age = template[lifestyle][i]
			healthiness = 0
			for nutrient in nutrients_to_consider:
				if nutrient in obj["nutrients"]:
					if nutrient in nutrient_to_col:
						row = lifestyle_age_to_row(lifestyle, (min_age + max_age) / 2)
						col = nutrient_to_col[nutrient]
						ratio = 0
						# infants don't need fiber or something
						if dris[row][col] != 0:
							ratio = obj["nutrients"][nutrient] / dris[row][col]
					else:
						req = amino_acids[nutrient] * avg_weights[lifestyle][i-1] / 2.2
						ratio = obj["nutrients"][nutrient] / req

					if obj["nutrients"]["Energy"] == 0:
						healthiness = "-"
					else:
						ratio *= calorie_normalization / obj["nutrients"]["Energy"]
						if ratio > 1:
							ratio = 1
						healthiness += ratio
			if healthiness == "-":
				obj["healthiness"][lifestyle].append("-")
			else:
				healthiness /= 27
				score = healthiness - calorie_normalization / calorie_reqs[lifestyle][i-1]
				# -inf to 1

				score = int(round(100 * score))
				obj["healthiness"][lifestyle].append(score)
	write_food_data(filename, obj)
	obj_b = read_food_name(filename)
	obj_b["healthiness"] = obj["healthiness"]
	write_food_name(filename, obj_b)
	counter += 1
