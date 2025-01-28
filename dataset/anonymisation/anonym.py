import numpy as np
import csv
import random
import time
from datetime import date
from Utils import separator,zip_outfileShuffle
import os
import hashlib

file_in = "big_survey_results"
file_out = "big_survey_results_anonym"
users = []
#open the files:
def read(file):
    file = open(file, "r")
    reader = csv.reader(file, delimiter=separator)
    return reader

def write(data,name):
    with open(name,'w',newline='') as file:
        write = csv.writer(file,delimiter=separator)
        for line in data:
            write.writerow(line)
    
def anonymise(input):
    data = []
    i = 0
    for line in input:
        week = get_week(line[1])
        line[0] = anonym_id(line[0],week)
        try:
            line[1] = anonym_time(line[1])
        except Exception:
            line[0] = 'DEL'
            line[1] = line[1]
        line[2] = anonym_loca(line[2])
        line[3] = anonym_loca(line[3])
        

        data.append(line)
    return data

def anonym_id(id,week):
    week = str(week)
    #10% chance de supprimer ce ligne
    if random.randint(1,100) % 10 == 0:
        return 'DEL'
    masked_data = hashlib.pbkdf2_hmac('sha256',id.encode('utf-8'),week.encode('utf-8'),32)
    return masked_data
    

def anonym_time(time_nona):
    base_time = string_to_time(time_nona)
    try:
        epoch_time = time_to_epoch(base_time)
    except OverflowError:
        raise Exception
    subtract = [-2,-1,0,1,2]
    
    #Make sure week is the same
    
    week = get_week(time_nona)
    
    base_time = epoch_to_time(epoch_time + 86400*subtract[random.randint(0,4)])

    if get_week(time_to_string(base_time)) != week:
        while (get_week(time_to_string(base_time)) != week):
            try:
                base_time = epoch_to_time(epoch_time + 86400*subtract[random.randint(0,4)])
            except:
                print("Something wrong here")
                return time_to_string(epoch_to_time(epoch_time))
    
    return time_to_string(base_time)

def anonym_loca(location):
    x  = float(location) + get_noise(0.8)
    return x.astype(str)

def get_noise(privat_coefficent):
    return np.random.laplace(0,privat_coefficent,1)[0]

def get_week(str):
    return date.fromisoformat(str[0:10]).isocalendar().week

def string_to_time(str):
    return time.strptime(str,"%Y-%m-%d %H:%M:%S")

def time_to_string(input):
    return time.strftime("%Y-%m-%d %H:%M:%S",input)

def time_to_epoch(input):
    return time.mktime(input)

def epoch_to_time(input):
    return time.gmtime(input)

if __name__ == "__main__":
    input = read(file_in + ".csv")
    # write(anonymise(input),file_out + ".csv")
    # zip_outfileShuffle(file_out + ".csv")

    #id printing
    # id_output = []
    # id_masked = set()
    # for line in input:
    #     id = str(line[0])
    #     temp = line[1][0:10]
    #     week = str(get_week(line[1]))
    #     masked_data = hashlib.pbkdf2_hmac('sha256',id.encode('utf-8'),week.encode('utf-8'),32)
    #     if masked_data not in id_masked:
    #         id_masked.add(masked_data)
    #         id_output.append([id+ '- \'' + temp +'\'',masked_data])
    # write(id_output,"id_anonym.csv")

    #Time printing
    # time_output = []
    # for line in input:
    #     time_anonym = anonym_time(line[1])
    #     time_output.append((line[1],time_anonym))
    # write(time_output,'time_anonym.csv')

    #GPS printing
    # cordinate_ouput = []
    # for line in input:
    #     (lat_anon,long_anon) = (anonym_loca(line[2]),anonym_loca(line[3]))
    #     cordinate_ouput.append((line[2],line[3],lat_anon,long_anon))
    # write(cordinate_ouput,'gps_anonym.csv')