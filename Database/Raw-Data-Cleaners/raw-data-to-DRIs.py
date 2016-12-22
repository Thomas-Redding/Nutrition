import sys

def read_file(path):
	f = open(path, "r")
	rtn = f.read()
	f.close()
	return rtn

def writeFile(path, contents):
	f = open(path, "w+")
	f.write(contents)
	f.close()

def life_age_to_row(life, age):
	if age < 0.5:
		return 1
	elif age < 1:
		return 2
	elif age < 3:
		return 3
	elif age < 8:
		return 4
	elif life == "male":
		if age < 13:
			return 5
		elif age < 18:
			return 6
		elif age < 30:
			return 7
		elif age < 50:
			return 8
		elif age < 70:
			return 9
		else:
			return 10
	elif life == "female":
		if age < 13:
			return 11
		elif age < 18:
			return 12
		elif age < 30:
			return 13
		elif age < 50:
			return 14
		elif age < 70:
			return 15
		else:
			return 16
	elif life == "pregnant":
		if 14 < age < 18:
			return 17
		elif age < 30:
			return 18
		elif age < 50:
			return 19
	elif life == "lactating":
		if 14 < age < 18:
			return 20
		elif age < 30:
			return 21
		elif age < 50:
			return 22
	return None


def ageToIndex(age):
	if age < 0.5:
		return 0
	elif age < 1:
		return 1
	elif age < 3:
		return 2
	elif age < 8:
		return 3
	elif age < 13:
		return 4
	elif age < 18:
		return 5
	elif age < 30:
		return 6
	elif age < 50:
		return 7
	elif age < 70:
		return 8
	else:
		return 9

# https://en.wikipedia.org/wiki/Essential_amino_acid#Minimum_daily_intake
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

def makeJSONFile(lifestyle, age):
	row = life_age_to_row(lifestyle, age)
	if row == None:
		print "Error: age=" + str(age) + ", lifestyle=" + lifestyle
	else:
		age_index = ageToIndex(age)
		path = "../Database-Files/Daily-Recommended-Intakes/" + lifestyle[0] + str(age_index) + ".txt"
		contents = "{"
		contents += "\"lifestyle\": \"" + lifestyle + "\""
		contents += ",\n\"min-age\": " + str(min_ages[age_index])
		contents += ",\n\"max-age\": " + str(max_ages[age_index])
		for col in range(2, len(table[0])):
			contents += ",\n\"" + table[0][col] + "\": "  + table[row][col]

		contents += ",\n\"per_kilogram\": {"
		counter = 0
		for key in amino_acids:
			if counter > 0:
				contents += ","
			contents += "\n\"" + key + "\": "  + str(amino_acids[key])
			counter = counter + 1
		contents += "}\n}"
		writeFile(path, contents)

table = read_file("../Database-Files/Daily-Recommended-Intakes/DRIs.txt").splitlines()
for i in range(0, len(table)):
	table[i] = table[i].split("\t")

lifestyles = {"male", "female", "pregnant", "lactating"}
ages = {0.25, 0.75, 2, 6, 11, 16, 24.5, 40.5, 60.5, 80}
min_ages = [0, 0.5, 1, 4, 9, 14, 19, 31, 51, 70]
max_ages = [0.5, 1, 3, 8, 13, 18, 30, 50, 70, 1000]
pregnant_ages = {16, 24.5, 40.5}

for lifestyle in lifestyles:
	if lifestyle in {"male", "female"}:
		for age in ages:
			makeJSONFile(lifestyle, age)
	else:
		for age in pregnant_ages:
			makeJSONFile(lifestyle, age)




























