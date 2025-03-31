import numpy as np
import csv
import random
import time
import hmac
import hashlib

separator = '\t'
SECRET_KEY = b'super_secret_key_123456'  # Secret key for HMAC


# Helper functions
def read(file):
    file = open(file, "r")
    reader = csv.reader(file, delimiter=separator)
    return reader


def write(data, name):
    with open(name, 'w', newline='') as file:
        writer = csv.writer(file, delimiter=separator)
        for line in data:
            writer.writerow(line)


def anonymise(input):
    data = []
    for line in input:
        week = get_week(line[1])
        line[0] = anonym_id(line[0], week)
        line[1] = anonym_time(line[1])
        line[2], line[3] = anonym_coords(line[2], line[3])
        data.append(line)
    return data


# Anonymization functions
def anonym_id(user_id, week):
    """HMAC pour anonymiser les identifiants des utilisateurs avec cohérence des semaine"""
    anonymized = hmac.new(SECRET_KEY, f"{user_id}{week}".encode('utf-8'), hashlib.sha256).hexdigest()
    return anonymized[:10]  # Truncate for brevity


def anonym_time(date_str):
    """Randomisez le temps"""
    base_time = string_to_time(date_str)
    week_start = time_to_epoch(base_time) - (base_time.tm_wday * 86400)  # Start of the week
    random_offset = random.randint(0, 7 * 86400 - 1)  # Random time within the week
    randomized_time = epoch_to_time(week_start + random_offset)
    return time_to_string(randomized_time)


def anonym_coords(lat, lon):
    """Ajoutez du bruit gaussien et randomisez le GPS dans une zone de délimitation"""
    lat = float(lat)
    lon = float(lon)

    # Random radius and angle to create a bounding box
    radius = abs(np.random.normal(0, 0.01))  # Gaussian noise with std dev of ~1km
    angle = random.uniform(0, 2 * np.pi)

    # Convert radius and angle to lat/lon offset
    lat_offset = radius * np.cos(angle)
    lon_offset = radius * np.sin(angle)

    new_lat = lat + lat_offset
    new_lon = lon + lon_offset

    return f"{new_lat:.6f}", f"{new_lon:.6f}"


# Time helper functions
def get_week(date_str):
    struct_time = string_to_time(date_str)
    return time.strftime("%U", struct_time)


def string_to_time(date_str):
    return time.strptime(date_str, "%Y-%m-%d %H:%M:%S")


def time_to_string(time_struct):
    return time.strftime("%Y-%m-%d %H:%M:%S", time_struct)


def time_to_epoch(time_struct):
    return time.mktime(time_struct)


def epoch_to_time(epoch_time):
    return time.localtime(epoch_time)


# Main script
if __name__ == "__main__":
    input_data = read("processed_gps_data.csv")
    anonymized_data = anonymise(input_data)
    write(anonymized_data, "processed_gps_anonym.csv")
