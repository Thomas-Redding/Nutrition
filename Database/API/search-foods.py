import os
import sys
import re

# python search-foods.py <search string> <maximum number of results>

class IndexLenPair:
	def __init__(self, i, l):
		self.index = int(i)
		self.len = int(l)


def getFoodsWithTrichar(trichar):
	path = "../Database-Files/Trichars-To-Index/" + trichar + ".txt"
	if os.path.isfile(path):
		f = open(path, "r")
		matches = f.read().split(";")
		for i in range(0, len(matches)):
			temp = matches[i].split(",")
			matches[i] = IndexLenPair(temp[0], temp[1])
		f.close()
		return set(matches)
	else:
		return None


search_str = ""
max_results_to_return = 20
if len(sys.argv) == 3:
	search_str = sys.argv[1]
	try:
		max_results_to_return = int(sys.argv[2])
	except ValueError:
		print "Error: second argument must be an integer"
		sys.exit(0)
elif len(sys.argv) == 2:
	search_str = sys.argv[1]
elif len(sys.argv) == 1:
	print "Error: No search string given"
	sys.exit(0)
else:
	print "Error: Too many arguments"
	sys.exit(0)

search_str = re.sub('\W+', '', search_str)
search_arr = search_str.lower().split(" ")

trichars = {}
for i in range(0, len(search_arr)):
	for j in range(0, len(search_arr[i])-2):
		current_trichar = search_arr[i][j:j+3]
		trichars[current_trichar] = 1

results = {}

for trichar in trichars:
	trichar_matches = getFoodsWithTrichar(trichar)
	if trichar_matches == None:
		continue
	for pair in trichar_matches:
		if pair.index in results:
			results[pair.index] = results[pair.index] + 1.0/pair.len
		else:
			results[pair.index] = 1.0/pair.len
			

results_list = []
for index in results:
	results_list.append([int(index), results[index]])


def foo(bar):
	return -1 * bar[1]

results_list = sorted(results_list, key = foo)

output = ""
for i in range(0, max_results_to_return):
	if i >= len(results_list):
		break
	if i != 0:
		output += ","
	output += str(results_list[i][0])
print output
