from datetime import date
import csv
from Utils import separator

def main(nona, anon, parameters={}):
    total = 0
    filesize = 0
    fd_nona_file = open(nona, "r")
    fd_anon_file = open(anon, "r")
    nona_reader = csv.reader(fd_nona_file, delimiter=separator)
    anon_reader = csv.reader(fd_anon_file, delimiter=separator)
    date_change = True
    for row1, row2 in zip(nona_reader, anon_reader):
        score = 1
        filesize += 1
                    
        if row2[0]=="DEL":
            continue
        if date_change:
            min_hour_na = int(row1[1][11:13])
            min_hour_an = int(row2[2][11:13])
        if len(row2[1]) > 10 and len(row2[0]):
            current_day = int(row1[1][7:10])
            
        if len(row2[1]) > 10 and len(row2[0]):
            year_na, month_na, day_na = row1[1][0:10].split("-")
            year_an, month_an, day_an = row2[1][0:10].split("-")
            try :
                #Uses the ISO calendar to get both week and day number
                dateanon = date(int(year_an), int(month_an), int(day_an)).isocalendar()
                datenona = date(int(year_na), int(month_na), int(day_na)).isocalendar()
            except: return (-1, filesize)

            if datenona[1] == current_day:
                date_change = False
            else: 
                date_change = True
                period_nona = int(row1[1][11:13]) - min_hour_na
                period_anon = int(row2[2][11:13]) - min_hour_an
                score -= abs(period_anon - period_nona)/3
        else: return (-1, filesize)
        total += max(0, score) if row2[0] != "DEL" else 0
        return total/filesize