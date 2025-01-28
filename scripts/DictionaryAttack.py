import csv
import hashlib
import time

# Function to hash an ID with a given week number
def hash_id(id, week):
    return hashlib.pbkdf2_hmac('sha256', id.encode('utf-8'), week.encode('utf-8'), 32)

# Function to reverse anonymize the IDs
def reverse_anonymize(input_file, output_file):
    # Read the anonymized data
    with open(input_file, 'r') as infile:
        reader = csv.reader(infile, delimiter='\t')
        anonymized_data = list(reader)

    # Prepare a list to store the de-anonymized data
    deanon_data = []

    # Iterate over each row in the anonymized data
    for row in anonymized_data:
        anonymized_id = eval(row[0])  # Convert the string representation of bytes back to bytes
        timestamp = row[1]
        longitude = row[2]
        latitude = row[3]

        # Iterate over all possible week numbers (0-53)
        for week in range(54):
            week_str = f"{week:02d}"  # Format week number as two digits

            # Iterate over possible original IDs (assuming IDs are integers)
            for original_id in range(1, 1000):  # Adjust the range as needed
                original_id_str = str(original_id)
                hashed_id = hash_id(original_id_str, week_str)

                # Compare the hashed ID with the anonymized ID
                if hashed_id == anonymized_id:
                    # If a match is found, replace the anonymized ID with the original ID
                    deanon_data.append([original_id_str, timestamp, longitude, latitude])
                    break
            else:
                continue
            break
        else:
            # If no match is found, keep the anonymized ID
            deanon_data.append([anonymized_id, timestamp, longitude, latitude])

    # Write the de-anonymized data to a new file
    with open(output_file, 'w', newline='') as outfile:
        writer = csv.writer(outfile, delimiter='\t')
        writer.writerows(deanon_data)

# Main execution
if __name__ == "__main__":
    input_file = "processed_gps_anonym.csv"
    output_file = "attacked_gps_anonym.csv"
    reverse_anonymize(input_file, output_file)
    print(f"De-anonymized data has been written to {output_file}")

