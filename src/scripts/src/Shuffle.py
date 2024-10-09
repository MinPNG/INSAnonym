from pandas.core.frame import DataFrame
import logging

def shuffleDataFrame(_df: DataFrame):
    df = _df
    df = df.astype({'longitude': 'float64', 'latitude': 'float64', 'id': 'string', 'date': 'datetime64[ns]'})
    shuffled = df.sample(n=len(df))
    logging.debug("Shuffle finished")
    return shuffled
