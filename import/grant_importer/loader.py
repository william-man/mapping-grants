import pymysql

class Loader:
    def __init__(self, host, user, password, database):
        self.connection = pymysql.connect(
            host=host,
            user=user,
            password=password,
            database=database
        )

    def load_from_transform(self, data):
        self.load_table('funding_organisations', list(data['funding_organisations'].values()))
        self.load_table('recipient_organisations', list(data['recipient_organisations'].values()))
        self.load_table('recipient_individuals', list(data['recipient_individuals'].values()))
        self.load_table('grants', data['grants'])

    def load_table(self, table_name, rows, chunk_size=200):
        if not rows:
            return

        columns = list(rows[0].keys())
        sql = self.build_insert_sql(table_name, columns)
        self.insert_chunks(sql, columns, rows, chunk_size)

    def build_insert_sql(self, table_name, columns):
        placeholders = ', '.join(['%s'] * len(columns))
        column_list = ', '.join(columns)
        return f"INSERT IGNORE INTO {table_name} ({column_list}) VALUES ({placeholders})"

    def chunked(self, data, size):
        for i in range(0, len(data), size):
            yield data[i:i + size]

    def insert_chunks(self, sql, columns, data, chunk_size):
        with self.connection.cursor() as cursor:
            for chunk in self.chunked(data, chunk_size):
                values = [
                    tuple(row[col] if row.get(col) != '' else None for col in columns)
                    for row in chunk
                ]
                try:
                    cursor.executemany(sql, values)
                    self.connection.commit()
                except Exception as error:
                    print(f"Error loading chunk (first row: {chunk[0]}): {error}")

    def close(self):
        self.connection.close()