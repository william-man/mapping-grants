import csv
import os

from grant_importer.loader import Loader

def main():
    SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
    csv_path = os.path.join(SCRIPT_DIR, "..", "data", "postcodes", "ukpostcodes.csv")
    csv_path = os.path.normpath(csv_path)

    loader = Loader('127.0.0.1', 'sail', 'password', 'laravel')

    with open(csv_path, 'r') as file:
        reader = csv.DictReader(file)
        rows = [
            {
                'postcode': row['postcode'],
                'latitude': row['latitude'] or None,
                'longitude': row['longitude'] or None
            }
            for row in reader
        ]

    loader.load_table('postcodes', rows, chunk_size=500)
    loader.close()

if __name__ == "__main__":
    main()