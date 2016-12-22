
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
    f = open("../Database-Files/Index-To-Name/" + name, "w+")
    f.write(contents)

class Food_Description:
    def __init__(self, line):
        line = line.split("~^~")
        self.index = line[0][1:]
        self.group = line[1]
        self.name1 = line[2]
        self.name2 = line[3]


def sanitized_string(string):
	return string.replace("\\", "\\\\").replace("\"", "\\\"")

food_descriptions = readFile("../Raw-Data/USDA/FOOD_DES.txt")
for i in range(0, len(food_descriptions)):
    food_descriptions[i] = Food_Description(food_descriptions[i])
    output = "{\"name\": \"" + sanitized_string(food_descriptions[i].name1) + "\","
    output += "\"index\": " + str(i) + "}"
    writeFile(str(i) + ".txt", output)
