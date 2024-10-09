from dataclasses import dataclass
import logging
from typing import List
from statistics import mean, median
import os
import json
import importlib
from sqlite3.dbapi2 import Connection
from scripts.src.utils import RETURN_MESSAGES, UserException, select_in_aggregation
from pandas.core.frame import DataFrame

@dataclass
class Runner:
    score_list = []

    def run_utilities(self, scripts, df_anon, df_origin):
        nb_lines_df_origin = len(df_origin)
        df = df_anon.loc[df_anon['id'] != "DEL"]
        df_origin_del = df_origin.loc[df.index]

        for script,params in scripts:
            try:
                logging.debug(f"Starting utility {script}")
                exec = importlib.import_module("scripts.metrics." + script)
                score = 0
                if script in ["utility_tuile", "utility_POI", "utility_meet"]:
                    """
                    Utilities that require original df but only DEL anon
                    """
                    score = exec.main(df, df_origin, json.loads(params), nb_lines_df_origin)
                else:
                    score = exec.main(df, df_origin_del, json.loads(params), nb_lines_df_origin)
                self.score_list.append(score)
                logging.debug(f"Score for { script }: { score }")
            except Exception as e:
                logging.error(e)
                raise UserException(RETURN_MESSAGES.ERROR_IN_UTILITY.value + script)
        print(f"Utility: {self.score_list}")

    def aggregate(self, scores, conn: Connection) -> float:
        # Return the final utility score according to the aggregating function
        if len(scores) == 0:
            raise Exception("No values in score_list")
        agg = select_in_aggregation(conn)
        if agg == "mean":
            return mean(scores)
        elif agg == "median":
            return median(scores)
        elif agg == "max":
            return max(scores)
        elif agg == "min":
            return min(scores)
        raise Exception(f"Aggregation selected not supported: {agg}")

    def run_attack(self, footprint: DataFrame, df_anon, df_orig):
        score_list = []
        for script in os.listdir(os.path.dirname('scripts/attack/')):
            if script[-3:] != '.py': continue
            try:
                logging.debug(f"Starting attack {script}")
                exec = importlib.import_module("scripts.attack." + script[:-3])
                score = exec.main(df_anon, df_orig, footprint)
                score_list.append(score)
            except Exception as e:
                logging.error(e)
                raise UserException(RETURN_MESSAGES.ERROR_IN_ATTACK.value + script[:-3])
        print(f"Attack: {score_list}")
        return score_list


