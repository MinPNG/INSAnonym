import zipfile
import logging
from typing import List
import sqlite3
from dataclasses import dataclass
from enum import Enum
from os import rename, rmdir

class UserException(Exception):
    pass

def unzip_file(filename):
    filenamezip = filename+".zip"
    directoryname = filename+"_directory"

    with zipfile.ZipFile(filenamezip, 'r') as zip_ref:
        #Raise if the file is bigger than 10Go
        if zip_ref.infolist()[0].file_size > 10000000000:
            logging.error("Bombzip file?")
            raise SystemExit('File to unzip too big (BombZip?)')
        zip_ref.extract(zip_ref.infolist()[0], path=directoryname)
    rename(directoryname+"/"+zip_ref.infolist()[0].filename, filename)
    rmdir(directoryname)

class RETURN_MESSAGES(Enum):
    INVALID_ORIGIN = "Invalid original file"
    ERROR_ZIP_ADMIN = "Error in unzipping original file. Please report this issue to the admin"
    ERROR_ZIP_ANON = "Error in unzipping anonymized file. Please verify your upload"
    ERROR_VALIDATOR_COLUMNS = "Wrong number of columns"
    ERROR_IN_UTILITY = "Error in processing utility script: "
    ERROR_IN_ATTACK = "Error in processing attack script: "
    UTILITY_DONE = "Utility calculus successfuly processed"
    UNKNOWN_ERROR = "Please report the error to the administrator, by sharing the file name."
    ERROR_WEEKS_DIFFER = "Weeks differ between the anonymized file and the original one. Eaxh row needs to have the same week."

@dataclass
class Scores:
    naive: float = -1
    utility: float = -1

def update_db(conn: sqlite3.Connection, status: str, anonymized_file: str, scores: Scores=Scores(), is_processed = 0):
    print(f"Writing on DB: {status}")
    conn.cursor().execute(f"UPDATE anonymisation \
                          set naiveAttack='{scores.naive}', \
                          utility='{scores.utility}', \
                          status='{status}', \
                          isProcessed='{is_processed}' \
                          where fileLink='{anonymized_file}'")
    conn.commit()

def select_in_aggregation(conn: sqlite3.Connection):
    cursor = conn.cursor()
    cursor.execute(f"SELECT * FROM aggregation")
    return cursor.fetchall()[0][0]

def select_in_metrics(conn: sqlite3.Connection):
    cursor = conn.cursor()
    cursor.execute(f"SELECT * FROM METRIC")
    return [(el[0][:-3], el[1]) for el in cursor.fetchall()]

def zipFile(data, fullPath: str, fileType: str):
    zipFile = fullPath + '.zip'
    fileName = fullPath.split('/')[-1] + '.' + fileType
    zf = zipfile.ZipFile(zipFile, mode="w", compression=zipfile.ZIP_DEFLATED)
    zf.writestr(fileName, data)
    zf.close()
