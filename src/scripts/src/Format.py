import csv
import pandas as pd
import logging
from .utils import RETURN_MESSAGES, UserException

def validator_first_row(filepath):
    logging.debug("Validating first Row")
    with open(filepath, newline='') as csvfile:
        spamreader = csv.reader(csvfile, delimiter='\n', quotechar="|")
        first_row = next(spamreader)
        if first_row[0].split('\t')[0] == "":
            raise UserException(RETURN_MESSAGES.ERROR_VALIDATOR_COLUMNS.value)
        if len(first_row[0].split('\t')) != 4:
            raise UserException(RETURN_MESSAGES.ERROR_VALIDATOR_COLUMNS.value)


def validator_rows(df):
    logging.debug("Validating all rows")
    if df.shape[1] != 4:
        raise UserException(RETURN_MESSAGES.ERROR_VALIDATOR_COLUMNS.value)

def add_columns(df):
    df.columns = ['id', 'date', 'latitude', 'longitude']
    return df
