import pymysql
import csv

class Loader:
    def __init__(self, host, user, password, database):
        self.connection = pymysql.connect(
            host=host,
            user=user,
            password=password,
            database=database
        )

    def load_data(self, table_name, file_path, chunk_size=200):
        data = self.read_csv(file_path)
        if not data:
            return

        columns = list(data[0].keys())
        sql = self.build_insert_sql(table_name, columns)
        self.insert_chunks(sql, columns, data, chunk_size)

    def read_csv(self, file_path):
        with open(file_path, "r") as file:
            reader = csv.DictReader(file)
            return list(reader)
    
    def build_insert_sql(self, table_name, columns):
        placeholders = ', '.join(['%s'] * len(columns))
        column_list = ', '.join(columns)
        return f"INSERT INTO {table_name} ({column_list}) VALUES ({placeholders})"

    def chunked(self, data, size):
        for i in range(0, len(data), size):
            yield data[i:i + size]

    def insert_chunks(self, sql, columns, data, chunk_size):
        with self.connection.cursor() as cursor:
            for chunk in self.chunked(data, chunk_size):
                values = [
                    tuple(row[col] if row[col] != '' else None for col in columns)
                    for row in chunk
                ]
                try:
                    cursor.executemany(sql, values)
                    self.connection.commit()
                except Exception as error:
                    print(f"Error loading chunk (first grantsID: {chunk[0].get('grantsID')}): {error}")

    def close(self):
        self.connection.close()