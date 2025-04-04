import json
import csv
from threading import Thread
from collections import defaultdict
from Utils import *
from datetime import date
from anonym import get_week     

file_origin = "big_survey_results.csv"
file_anonym = "big_survey_results_anonym.csv"
file_json = "big_survey_attack_results.json"
#################################
#      Attack Thread      #
#################################
class Attack(Thread):
    def __init__(self, original_file, anonym_file, anwser_JSON):
        Thread.__init__(self)
        self.original_file = original_file
        self.anonym_file = anonym_file
        self.anwser_JSON = anwser_JSON
        # self.dbconn = dbconn
        self.score = -1

    # Génère un dictionaire ayant pour chaque mois la somme des coordonnées GPS
    def generateSumGPS(self, file):
        dictsumGPS = defaultdict(list_struct)
        dictnomGPS = defaultdict(int)
        with open(file, newline='') as csvfile:
            spamreader = csv.reader(csvfile, delimiter=separator)
            size = spamreader.line_num
            for row in spamreader:
                if row[0]!="DEL":
                    # Pour chaque ligne
                    year  = date.fromisoformat(row[1][0:10]).isocalendar().year
                    week  = date.fromisoformat(row[1][0:10]).isocalendar().week
                    #calendar = date.fromisoformat(row[1][0:10]).isocalendar()
                    id_date = f"{row[0]}.{year}-{week}"
                    dictsumGPS[id_date][0] += round(float(row[-2]),2)
                    dictsumGPS[id_date][1] += round(float(row[-1]),2)
        csvfile.close()      
        return dictsumGPS

    def run(self):
        self.anonym_dict = self.generateSumGPS(self.anonym_file)
        self.original_dict = self.generateSumGPS(self.original_file)

        # Parcours le tableau et détermine l'ID le plus probable
        sol = defaultdict(dict)
        for key in self.original_dict:
            orginal_gps = self.original_dict[key]
            minimum = float('inf')
            minimum_key = ""
            for key2 in self.anonym_dict:
                difference = abs(orginal_gps[0] - self.anonym_dict[key2][0]) + abs(
                    orginal_gps[1] - self.anonym_dict[key2][1])
                if difference < minimum:
                    minimum = difference
                    minimum_key = key2
            sol[key.split(".")[0]][key.split(".")[1]] = [minimum_key.split(".")[0]]

        #écire le resultat
        json_object = json.dumps(sol)
        with open(self.anwser_JSON,"w") as json_file:
            json_file.write(json_object)
        json_file.close()

        # Détermine le score
        with open(self.anwser_JSON) as json_file:
            data = json.load(json_file)
            # Nombre d'ID à déterminer
            size = sum((len(data[tab]) for tab in data))
            score = 0

            for tab in data:
                for month in data[tab]:
                    if data[tab][month][0] == sol[tab][month][0]:
                        score += 1
            self.score = score / size

        #Update the database
        # self.dbconn.cursor().execute(f"UPDATE anonymisation \
        #                                set status='Attaque naïve terminée' \
        #                                where fileLink='{self.anonym_file.split('/')[1]}'")
        # self.dbconn.commit()

    def result(self):
        return self.score
    
if __name__ == "__main__":
    attacker = Attack(file_origin,file_anonym,file_json)
    attacker.run()
    print("Score attack :")
    print(attacker.result())
    