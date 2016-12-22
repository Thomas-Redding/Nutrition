import sys
import json
import re
import os
import glob
import math

def read_file(path):
	f = open(path, "r")
	rtn = json.loads(f.read())
	f.close()
	return rtn

# get usage stats
dic = {}
diaries_path = "../Users/diaries/"
os.chdir(diaries_path)
for file_path in glob.glob("*.txt"):
	list_of_foods = read_file(file_path)["foods"]
	for i in range(0, len(list_of_foods)):
		if list_of_foods[i]["id"] not in dic:
			dic[list_of_foods[i]["id"]] = 0
		dic[list_of_foods[i]["id"]] += 1


# reads the given file from Raw-Data-Cleaners
# @param name - name of file
# @returns lines of file in list
def readFile(path):
	f = open(path, "r")
	return f.read().splitlines()

# creates/overwrites the given contents to a new file in Database Files
# @param name - name of file to create/overwrite
# @param contents - new contents of file
def writeFile(name, contents):
	f = open("../../Database-Files/Trichars-To-Index/" + name, "w+")
	f.write(contents)

class Food_Description:
	def __init__(self, line):
		line = line.split("~^~")
		self.index = line[0][1:]
		self.group = line[1]
		self.name1 = line[2]
		self.name2 = line[3]
		self.name = self.name1.lower().split(' ')
		self.count = 0
		# http://stackoverflow.com/questions/1276764/stripping-everything-but-alphanumeric-chars-from-a-string-in-python
		for i in range(0, len(self.name)):
			self.name[i] = re.sub('\W+', '', self.name[i])
food_descriptions = readFile("../../Raw-Data/USDA/FOOD_DES.txt")
for i in range(0, len(food_descriptions)):
	food_descriptions[i] = Food_Description(food_descriptions[i])

test = "abcdefghijkl"

output_dic = {}

for i in range(0, len(food_descriptions)):
	for j in range(0, len(food_descriptions[i].name)):
		for k in range(0, len(food_descriptions[i].name[j])-2):
			food_descriptions[i].count = food_descriptions[i].count + 1
			trichar = food_descriptions[i].name[j][k:k+3]
			if trichar not in output_dic:
				output_dic[trichar] = {}
			output_dic[trichar][i] = 1

print len(output_dic)
foo = 0
for trichar in output_dic:
	foo = foo + 1
	output = ""
	count = 0
	for index in output_dic[trichar]:
		popularity = 0
		if index in dic:
			popularity = dic[index]
		else:
			popularity = 0
		score = 1.0 / math.sqrt(food_descriptions[index].count)

		if count > 0:
			output += ";"
		output += str(index)
		output += ","
		output += str(int(100*score))
		if popularity != 0:
			output += ","
			output += str(popularity)
		count = count + 1
	writeFile(trichar + ".txt", output)
	if foo%100 == 0:
		print foo
