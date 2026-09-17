import glob
import os
import time

from grant_importer.transformer import Transformer
from grant_importer.loader import Loader

def main():
    start_time = time.perf_counter()

    SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
    json_files = [
        os.path.normpath(path)
        for path in glob.glob(os.path.join(SCRIPT_DIR, "..", "data", "json_all", "*.json"))
    ]

    transformer = Transformer()
    loader = Loader('127.0.0.1', 'sail', 'password', 'laravel')

    for i, file in enumerate(json_files, start=1):
        try:
            data = transformer.process_file(file)
            loader.load_from_transform(data)
        except Exception as error:
            print(f"FAILED on {file}: {error}")

        if i % 20 == 0:
            elapsed = time.perf_counter() - start_time
            print(f"  {i}/{len(json_files)} files done ({elapsed:.1f}s elapsed)")

    loader.close()

if __name__ == "__main__":
    main()
    print("Processing complete.")