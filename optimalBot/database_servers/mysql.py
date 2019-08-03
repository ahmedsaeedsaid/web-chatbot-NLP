import mysql.connector


class MySQL:
    def __init__(self, user, password, host, database):
        self.query = ''
        self.prev_query = ''
        self.user = user
        self.password = password
        self.host = host
        self.database = database

    def connection(self):
        con = mysql.connector.connect(user=self.user,
                                      password=self.password,
                                      host=self.host,
                                      database=self.database)
        return con

    def select_(self, select):
        self.query += 'select ' + select + ' '

    def delete_(self):
        self.query += 'delete '

    def from_(self, table):
        self.query += 'from ' + table + ' '

    def where_(self, where):
        self.query += 'where ' + where

    def show_(self, entity):
        self.query += 'show ' + entity

    def insert_(self, table_name, data):
        self.query += 'insert into ' + table_name + ' values('
        self.query += ",".join(dict.keys()) + ") " + ",".join(dict.values())
        return self.query

    def update_(self, table_name, data, where):
        self.query += 'update ' + table_name + ' set '
        keys = list(data.keys())
        for key, value in data.items():
            self.query += str(key) + "=" + str(value)
            if keys[-1] != key:
                self.query += ","
        self.where_(where)
        cr = self.__execute()
        return cr.rowcount

    def fetch_all_(self):
        con = self.connection()
        cr = con.cursor()
        cr.execute(self.query)
        self._save_last_query()
        self._reset_buffer()
        return cr.fetchall()
    # TODO: Merge __execute with commit__
    def __execute(self):
        con = self.connection()
        cr = con.cursor(buffered=True)
        cr.execute(self.query)
        con.commit()
        self._save_last_query()
        self._reset_buffer()
        return cr

    def commit_(self):
        con = self.connection()
        cr = con.cursor(buffered=True)
        try:
            cr.execute(self.query)
            cr.commit()
            self._save_last_query()
            self._reset_buffer()
            return True
        finally:
            self._save_last_query()
            self._reset_buffer()
            return False

    def last_query(self):
        return self.prev_query

    def _save_last_query(self):
        self.prev_query = self.query

    def _reset_buffer(self):
        self.query = ''
