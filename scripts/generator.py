import pygame
import pandas as pd
import csv
from datetime import datetime, timedelta
import math
import numpy as np
import gpxpy


# Initialize Pygame
pygame.init()

# Constants
WIDTH, HEIGHT = 680, 600
MAP_IMAGE = "france_map.png"  # Replace with your map file
FONT_SIZE = 12
TEXT_COLOR = (150, 0, 0)  # red color for text
PIN_RADIUS = 2
FONT = pygame.font.Font(None, FONT_SIZE)
SPEED_KMPH = 0.3  # Speed in km/h
TIME_INTERVAL_MIN = 20  # Time interval for pin generation

# Load the map
screen = pygame.display.set_mode((WIDTH, HEIGHT))
map_image = pygame.image.load(MAP_IMAGE)
map_image = pygame.transform.scale(map_image, (WIDTH, HEIGHT))

# Parse the GPX file to extract waypoints
def parse_gpx(file_path):
    with open(file_path, "r") as gpx_file:
        gpx = gpxpy.parse(gpx_file)
        waypoints = [(point.latitude, point.longitude) for track in gpx.tracks for segment in track.segments for point in segment.points]
    return waypoints

# Rotate and scale the GPX path while keeping start and end points fixed
def align_gpx_path(gpx_path, start_lat, start_lon, end_lat, end_lon):
    # Convert lat/lon to Cartesian coordinates (relative to the first point of the GPX path)
    original_start = np.array(gpx_path[0])
    original_end = np.array(gpx_path[-1])
    gpx_coords = np.array(gpx_path)
    gpx_coords -= original_start  # Shift to origin

    # Calculate the original path's vector and the desired vector
    original_vector = original_end - original_start
    desired_vector = np.array([end_lat - start_lat, end_lon - start_lon])

    # Calculate scaling factor
    scale = np.linalg.norm(desired_vector) / np.linalg.norm(original_vector)

    # Normalize vectors for rotation calculation
    original_vector_unit = original_vector / np.linalg.norm(original_vector)
    desired_vector_unit = desired_vector / np.linalg.norm(desired_vector)

    # Calculate the rotation angle (signed)
    dot_product = np.dot(original_vector_unit, desired_vector_unit)
    cross_product = np.cross(original_vector_unit, desired_vector_unit)
    angle = np.arccos(np.clip(dot_product, -1.0, 1.0))
    if cross_product < 0:  # Handle direction of rotation
        angle = -angle

    # Apply scaling and rotation
    rotation_matrix = np.array([[np.cos(angle), -np.sin(angle)], [np.sin(angle), np.cos(angle)]])
    transformed_coords = scale * (rotation_matrix @ gpx_coords.T).T

    # Shift the transformed path to match the new start point
    transformed_coords += np.array([start_lat, start_lon])

    return transformed_coords.tolist()

# Interpolate pins along the transformed path
def interpolate_path(path, num_intervals):
    pins = []
    for i in range(num_intervals + 1):
        fraction = i / num_intervals
        index = fraction * (len(path) - 1)
        lower_idx = int(index)
        upper_idx = min(lower_idx + 1, len(path) - 1)
        sub_fraction = index - lower_idx
        lat = path[lower_idx][0] + sub_fraction * (path[upper_idx][0] - path[lower_idx][0])
        lon = path[lower_idx][1] + sub_fraction * (path[upper_idx][1] - path[lower_idx][1])
        pins.append((lat, lon))
    return pins


# France's geographical boundaries
FRANCE_LAT_MIN = 38.7
FRANCE_LAT_MAX = 52.5
FRANCE_LON_MIN = -9.1
FRANCE_LON_MAX = 13.4

# Latitude and Longitude to screen coordinates
def lat_to_y(lat):
    """Convert latitude to Y-coordinate on the France map."""
    return HEIGHT * (FRANCE_LAT_MAX - lat) / (FRANCE_LAT_MAX - FRANCE_LAT_MIN)

def lon_to_x(lon):
    """Convert longitude to X-coordinate on the France map."""
    return WIDTH * (lon - FRANCE_LON_MIN) / (FRANCE_LON_MAX - FRANCE_LON_MIN)


# Haversine formula to calculate distance between two points in km
def haversine(lat1, lon1, lat2, lon2):
    R = 6371  # Earth's radius in km
    phi1, phi2 = math.radians(lat1), math.radians(lat2)
    delta_phi = math.radians(lat2 - lat1)
    delta_lambda = math.radians(lon2 - lon1)
    a = math.sin(delta_phi / 2) ** 2 + math.cos(phi1) * math.cos(phi2) * math.sin(delta_lambda / 2) ** 2
    c = 2 * math.atan2(math.sqrt(a), math.sqrt(1 - a))
    return R * c


# Calculate travel time in hours
def calculate_travel_time(distance):
    return distance / SPEED_KMPH


# Linear interpolation to find a point along the path
def interpolate(lat1, lon1, lat2, lon2, fraction):
    lat = lat1 + fraction * (lat2 - lat1)
    lon = lon1 + fraction * (lon2 - lon1)
    return lat, lon


# Select the appropriate GPX file based on start_time's seconds
def select_gpx_file(start_time):
    gpx_files = ["trail1.gpx", "trail2.gpx", "trail3.gpx", "trail4.gpx", "trail5.gpx"]
    last_digit = int(str(start_time.second)[-1])  # Get the last digit of the seconds
    gpx_index = (last_digit - 1) % len(gpx_files)  # Map 1-5 to 0-4 (handle modulo for 6-0 as well)
    return gpx_files[gpx_index]

# Generate pins with GPX file selection based on start_time
def generate_pins():
    capitals = pd.read_csv("capitals.csv", header=0, names=["Latitude", "Longitude"])
    centers = pd.read_csv("centers.csv", header=0, names=["Latitude", "Longitude"])

    pins = []
    current_time = datetime.now()

    for idx, (center_row, capital_row) in enumerate(zip(centers.itertuples(), capitals.itertuples()), start=1):
        center_lat, center_lon = center_row.Latitude, center_row.Longitude
        capital_lat, capital_lon = capital_row.Latitude, capital_row.Longitud
        # Determine start time for this ID
        distance = haversine(center_lat, center_lon, capital_lat, capital_lon)
        travel_time = calculate_travel_time(distance)  # in hours
        start_time = current_time - timedelta(hours=travel_time)
        # Select GPX file based on the last digit of the start time's seconds
        gpx_file = select_gpx_file(start_time)
        # Parse the selected GPX file
        gpx_path = parse_gpx(gpx_file)
        # Align the GPX path to this ID's start and end points
        aligned_path = align_gpx_path(gpx_path, center_lat, center_lon, capital_lat, capital_lon)
        # Calculate total intervals for pin generation
        total_intervals = max(1, int(travel_time * 60 / TIME_INTERVAL_MIN))
        # Generate pins along the path
        interpolated_pins = interpolate_path(aligned_path, total_intervals)
        for interval, (lat, lon) in enumerate(interpolated_pins):
            timestamp = start_time + timedelta(minutes=interval * TIME_INTERVAL_MIN)
            pins.append((idx, timestamp.strftime("%Y-%m-%d %H:%M:%S"), lon, lat))

    return pins

# Export pins to a CSV file
def export_pins_to_csv(filename, pins):
    with open(filename, "w", newline="") as file:
        writer = csv.writer(file, delimiter="\t")
        for pin in pins:
            writer.writerow(pin)


# Main loop
def main():
    running = True
    pins = generate_pins()
    export_pins_to_csv("data.csv", pins)

    while running:
        screen.blit(map_image, (0, 0))

        # Group pins by their IDs
        from collections import defaultdict

        # Organize pins by ID for efficient lookup
        pins_by_id = defaultdict(list)
        for pin in pins:
            pin_id, timestamp, lon, lat = pin
            pins_by_id[pin_id].append(pin)

        # Draw pins
        for pin_id, pin_list in pins_by_id.items():
            for idx, pin in enumerate(pin_list):
                _, timestamp, lon, lat = pin
                pin_colors = [int(char) * 28 for char in str(pin_id).zfill(3)]
                x = lon_to_x(lon)
                y = lat_to_y(lat)
                pygame.draw.circle(screen, (pin_colors[1], pin_colors[0], pin_colors[2]), (int(x), int(y)), PIN_RADIUS)

                # Display timestamp only for the first and last pins
                if idx == 0 or idx == len(pin_list) - 1:  # First or last pin
                    text_surface = FONT.render(f"{timestamp}", True, TEXT_COLOR)
                    screen.blit(text_surface, (int(x), int(y) + PIN_RADIUS + 1))

        for event in pygame.event.get():
            if event.type == pygame.QUIT:
                running = False

        pygame.display.flip()

    pygame.quit()

#Noice
if __name__ == "__main__":
    main()
