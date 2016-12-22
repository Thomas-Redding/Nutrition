import sys
import json
import re
import os

# python create-food.py <json>
# prints the index of the new food or an error message

def claimIndex():
	path = "../Database-Files/Meta-Files/composite-food-index-count.txt"
	f = open(path, "r+")
	rtn = int(f.read())
	f.seek(0)
	f.truncate()
	f.write(str(rtn+1))
	f.close()
	return rtn


def createNewFood(fileName, contents):
	f = open("../Database-Files/Composite-Food/" + str(fileName) + ".txt", "w+")
	f.write(contents)
	f.close()


def addFoodToTricharFile(trichar, food_id, num_of_trichars):
	path = "../Database-Files/Trichars-To-Index/" + trichar + ".txt"
	str_to_insert = str(food_id) + "," + str(num_of_trichars)
	if os.path.isfile(path):
		f = open(path, "a")
		f.write(";"+str_to_insert)
		f.close()
	else:
		f = open(path, "w+")
		f.write(str_to_insert)
		f.close()


def addFoodToIndex(food_id, food_name):
	food_name = re.sub('\W+', '', food_name)
	food_arr = food_name.lower().split(" ")
	num_of_trichars = 0
	trichars_added = {}
	for word in food_arr:
		for i in range(0, len(word)-2):
			num_of_trichars = num_of_trichars + 1
	for word in food_arr:
		for i in range(0, len(word)-2):
			trichar = word[i:i+3]
			if trichar not in trichars_added:
				addFoodToTricharFile(trichar, food_id, num_of_trichars)
				trichars_added[trichar] = 1


data = None
if len(sys.argv) == 2:
	try:
		data = json.loads(sys.argv[1])
	except:
		print "Error: argument is not a valid json string"
		sys.exit(0)
elif len(sys.argv) == 1:
	print "Error: No food data (json) given"
	sys.exit(0)
else:
	print "Error: Too many arguments"
	sys.exit(0)


if "short_name" not in data:
	print "Error: JSON doen't contain \"short_name\""
	sys.exit(0)
if "components" not in data:
	print "Error: JSON doen't contain \"components\""
	sys.exit(0)

index = claimIndex()
createNewFood(index, json.dumps(data))
addFoodToIndex(index, data["short_name"])
print index
