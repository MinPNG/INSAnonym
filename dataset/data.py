import gpxpy
import pandas as pd
import os
from os.path import isfile

id = 0
list_user = []
# Đường dẫn tới file GPX
for gpx_file_path  in os.listdir():
    if  isfile(gpx_file_path) and gpx_file_path.endswith(".gpx"):
        with open(gpx_file_path, 'r') as gpx_file:
            gpx = gpxpy.parse(gpx_file)

        id += 1


        data = []
        for track in gpx.tracks:
            for segment in track.segments:
                for point in segment.points:
                    data.append({
                        'id':id,
                        'time':point.time,
                        'longitude': point.longitude,
                        'latitude': point.latitude
                    })

        # Chuyển sang DataFrame
        df = pd.DataFrame(data)
        
        #file CSV
        output_csv_path = 'processed_gps_data.csv'
        df.to_csv(output_csv_path, date_format="%Y-%m-%d %H:%M:%S" ,header=False, sep='\t', index=False,mode='a')
        print(f"File CSV đã được lưu tại: {output_csv_path}")
        gpx_file.close()