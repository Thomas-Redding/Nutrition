# -*- coding: latin-1 -*-


# reads the given file from Raw-Data-Cleaners
# @param name - name of file
# @returns lines of file in list
def readFile(name):
    f = open("../Raw-Data/USDA/" + name, "r")
    return f.read().splitlines()

# creates/overwrites the given contents to a new file in Database Files
# @param name - name of file to create/overwrite
# @param contents - new contents of file
def writeFile(name, contents):
    f = open("../Database-Files/Food-Data/" + name, "w+")
    f.write(contents)

class Nutrition_Data:
    def __init__(self, line):
        line = line.split("~^")
        self.index = line[0][1:]
        self.nutrient = line[1][1:]
        self.value = line[2]
        for j in range(0, len(self.value)):
            if self.value[j] == '^':
                self.value = self.value[0:j]
                break
    def __str__(self):
        return "(" + self.index + ", " + self.nutrient + ", " + self.value + ")"
nutrition_data = readFile("NUT_DATA.txt")
for i in range(0, len(nutrition_data)):
    nutrition_data[i] = Nutrition_Data(nutrition_data[i])
index_to_nutrition_data = {}
for i in range(0, len(nutrition_data)):
    if nutrition_data[i].index not in index_to_nutrition_data:
        index_to_nutrition_data[nutrition_data[i].index] = {}
    index_to_nutrition_data[nutrition_data[i].index][nutrition_data[i].nutrient] = nutrition_data[i]


class Food_Description:
    def __init__(self, line):
        line = line.split("~^~")
        self.index = line[0][1:]
        self.group = line[1]
        self.name1 = line[2]
        self.name2 = line[3]
food_descriptions = readFile("FOOD_DES.txt")
for i in range(0, len(food_descriptions)):
    food_descriptions[i] = Food_Description(food_descriptions[i])
    
class Nutrient_Definition:
    def __init__(self, line):
        line = line.split("~^~")
        self.index = line[0][1:]
        self.units = line[1]
        self.abbreviation = line[2]
        self.name = line[3]
nutrient_definitions = readFile("NUTR_DEF.txt")
for i in range(0, len(nutrient_definitions)):
    nutrient_definitions[i] = Nutrient_Definition(nutrient_definitions[i])
index_to_nutrient_definition = {}
for i in range(0, len(nutrient_definitions)):
    index_to_nutrient_definition[nutrient_definitions[i].index] = nutrient_definitions[i]

    
class Weight:
    def __init__(self, line):
        line = line.split("~")
        self.index = line[1]
        self.name = line[3]
        self.grams = line[4][1:]
        self.grams = self.grams[:-2]
        for j in range(0, len(self.grams)):
        	if self.grams[j] == '^':
        		self.grams = self.grams[0:j]
        		break
        
weights = readFile("WEIGHT.txt")
for i in range(0, len(weights)):
    weights[i] = Weight(weights[i])
index_to_weight = {}
for i in range(0, len(weights)):
    if weights[i].index not in index_to_weight:
        index_to_weight[weights[i].index] = {}
    index_to_weight[weights[i].index][weights[i].name] = weights[i].grams

index_to_group_name = {}
group_data = readFile("FD_GROUP.txt")
for i in range(0, len(group_data)):
    group_data[i] = group_data[i].split("~^~")
    index_to_group_name[group_data[i][0][1:]] = group_data[i][1][:-1]

def sanitized_string(string):
	return string.replace("\\", "\\\\").replace("\"", "\\\"")

print len(food_descriptions)
for i in range(0, len(food_descriptions)):
    index = food_descriptions[i].index
    output = "{\"food_id\": " + str(i) + ",\n"
    output += "\"short_name\": \"" + sanitized_string(food_descriptions[i].name1) + "\",\n"
    output += "\"long_name\": \"" + sanitized_string(food_descriptions[i].name2) + "\",\n"
    output += "\"category\": \"" + index_to_group_name[food_descriptions[i].group] + "\",\n"
    output += "\"nutrients\": {"
    nutrientDic = index_to_nutrition_data[index]
    counter = 0
    for key in nutrientDic:
        nutrient_name = index_to_nutrient_definition[key].name
        nutrient_units = index_to_nutrient_definition[key].units
        nutrient_value = float(nutrientDic[key].value)
        if nutrient_units == "g":
            nutrient_value *= 1000
        elif nutrient_units == "mg":
            nutrient_value *= 1
        elif nutrient_units == "kcal":
            nutrient_value *= 1
        elif nutrient_units == "kJ":
            nutrient_value *= 0.239006
        elif nutrient_units == "IU":
            continue
        else:
            nutrient_value /= 1000 #micro-grams
        if counter > 0:
            output += ",\n"
        counter = counter + 1
        output += "\"" + nutrient_name + "\": " + str(nutrient_value)
    output += "},\n"
    output += "\"weights\": {"
    if index in index_to_weight:
        weightDic = index_to_weight[index]
        counter = 0
        for key in weightDic:
            if counter > 0:
                output += ",\n"
            counter = counter + 1
            label = sanitized_string(key)
            # label = label.replace("é", "e")
            output += "\"" + label + "\": " + weightDic[key]
    output += "}}"
    writeFile(str(i)+".txt", output)
    if i%100 == 0:
        print i