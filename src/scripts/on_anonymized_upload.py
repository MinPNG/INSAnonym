import argparse
import pandas as pd
import logging
from os import path
import multiprocessing
from scripts.src.Footprint import Footprint
from scripts.src.utils import *
from scripts.src.Format import *
from scripts.src.Runner import *
from scripts.src.Shuffle import *
import sqlite3

def unzip_files(args):
    """
    Try to unzip original file, and unzip anonymized file.
    """
    if not path.exists(args.full_path_original): 
        logging.debug("Unzipping original file")
        try: 
            unzip_file(args.full_path_original)
        except Exception as e: 
            logging.error(str(e))
            raise UserException(RETURN_MESSAGES.ERROR_ZIP_ADMIN.value)
    try: 
        logging.debug("Unzipping anonymized file")
        unzip_file(args.full_path_anonymized)
    except Exception as e:
        logging.error(str(e))
        raise UserException(RETURN_MESSAGES.ERROR_ZIP_ANON.value)

def run_utilities(runner: Runner, conn, df_anon, df_origin):
    scripts = select_in_metrics(conn)
    logging.debug("Starting utility calculus")
    scripts = filter(lambda x: x[0] != "utility_POI_perWeek", scripts) # Remove perWeek utility
    runner.run_utilities(scripts, df_anon, df_origin)
    scores = runner.aggregate(runner.score_list, conn)
    return scores

def run_attack(runner: Runner, footprint: pd.DataFrame, df_anon, df_origin):
    logging.debug("Starting Attack algorithms")
    scores = runner.run_attack(footprint, df_anon, df_origin)
    score = runner.aggregate(scores, conn)
    return score

def generate_footprint(footprint_file, df_anon, df_origin):
    footprint = Footprint(footprint_file)
    logging.debug("Starting Footprint generation")
    return footprint.run(df_anon, df_origin)

def generate_shuffled(shuffled_file, df_anon):
    logging.debug("Starting shuffle")
    shuffled = shuffleDataFrame(df_anon)
    generated_shuffled = shuffled.to_csv(header=False, index=False)
    zipFile(generated_shuffled, shuffled_file, 'csv')

if __name__ == "__main__":
    # Arguments
    parser = argparse.ArgumentParser()
    parser.add_argument("anonymized", help="Anonymized Dataframe filename")
    parser.add_argument("--db", help="Full path to database")
    parser.add_argument("--delimiter", help="Delimiter to use in tables", nargs='?', default='\t')
    args = parser.parse_args()
    conn = sqlite3.connect(args.db or 'database/tables.sqlite3', check_same_thread=False)

    #Inputs
    args.full_path_anonymized = "uploads/" + args.anonymized
    args.full_path_original = "files/c3465dad3864bb1e373891fdcfbfcca5f974db6a9e0b646584e07c5f554d7df7" 
    args.full_path_shuffled = "files/S_" + args.anonymized 
    args.full_path_footprint = "files/F_" + args.anonymized 

    # If you want to see all messages, change level field with logging.DEBUG.
    # For only starting/finished/error messages, logging.INFO.
    # For only error and warnings, logging.
    logging.basicConfig(
        format= f"{args.anonymized} - PID %(process)d - %(asctime)s - %(levelname)s - %(pathname)s.(%(lineno)d) - %(message)s", 
        filename='logs/on_anonymized_upload.log', 
        level = logging.DEBUG,
        datefmt='%d/%m/%Y %H:%M:%S'
    )

    try:
        logging.info(f"Starting to process file {args.anonymized}")
        unzip_files(args)
        validator_first_row(args.full_path_anonymized)

        def open_dataframe(arg):
            logging.debug(f"Opening file {arg}")
            file = open(arg)
            df = pd.read_csv(file, delimiter=args.delimiter, header=None,
                dtype={ 'id': 'category', 'latitude': 'category', 'longitude': 'category' },
                names=['id', 'date', 'latitude', 'longitude' ]
            )
            validator_rows(df)
            # Change types
            logging.debug("Reducing dtypes")
            df['date'] = pd.to_datetime(df['date'])
            return df
        
        with multiprocessing.Pool() as p:
            df_anon, df_origin = p.map(open_dataframe, [args.full_path_anonymized, args.full_path_original])


        scores = Scores()


        def run_pool(option):
            if option == 'utility':
                runner = Runner()
                return run_utilities(runner, conn, df_anon, df_origin)
            elif option == 'attack':
                runner = Runner()
                return run_attack(
                    runner, 
                    generate_footprint(args.full_path_footprint, df_anon, df_origin), 
                    df_anon, 
                    df_origin
                )
            elif option == 'shuffle':
                generate_shuffled(args.full_path_shuffled, df_anon)

        with multiprocessing.Pool() as p:
            results = p.map(run_pool, ['utility', 'attack', 'shuffle'])


        scores.utility = results[0]
        scores.naive = results[1]
        update_db(conn, RETURN_MESSAGES.UTILITY_DONE.value, args.anonymized, scores=scores, is_processed=1)
        logging.info("Process exit")

    except UserException as e:
        logging.error(str(e))
        update_db(conn, str(e), args.anonymized, is_processed=1)

    except Exception as e:
        logging.error(str(e))
        update_db(conn, RETURN_MESSAGES.UNKNOWN_ERROR.value, args.anonymized, is_processed=1)
    finally:
        conn.close()
