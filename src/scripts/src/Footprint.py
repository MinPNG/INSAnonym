from dataclasses import dataclass
import zipfile
from textwrap import indent
import pandas as pd
from scripts.src.utils import RETURN_MESSAGES, UserException, zipFile

@dataclass
class Footprint:
    footprint_file: str

    def run(self, anon, orig):
        # Convert to iso datetime
        df_orig = orig.copy()
        df_anon = anon.copy()

        df_orig = df_orig.drop(['latitude', 'longitude'], axis=1)
        df_anon = df_anon.drop(['latitude', 'longitude'], axis=1)
        df_orig = df_orig.astype({ 'id': 'string' })
        df_anon = df_anon.astype({ 'id': 'string' })

        df_orig['date'] = df_orig['date'].dt.year.astype(str) + "-" + df_orig['date'].dt.week.astype(str)
        df_anon['date'] = df_anon['date'].dt.year.astype(str) + "-" + df_anon['date'].dt.week.astype(str)

        if not (df_orig['date'] == df_anon['date']).all():
            raise UserException(RETURN_MESSAGES.ERROR_WEEKS_DIFFER.value)
        
        # Construct footprint 
        df = pd.merge(df_orig, df_anon, left_index=True, right_index=True) 
        df = df.drop(['date_y'], axis=1)
        df = df.groupby(['date_x', 'id_x'])['id_y'].unique().apply(list)
        weeks = df.reset_index()['date_x'].unique().tolist()

        df = df.reset_index().set_index(['id_x', 'date_x']).unstack(['date_x'])
        df.columns = weeks


        df.to_json(path_or_buf=self.footprint_file, orient='index', indent=4)
        return df
