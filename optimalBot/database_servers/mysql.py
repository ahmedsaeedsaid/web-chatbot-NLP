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

    def from_(self, table):
        self.query += 'from ' + table + ' '

    def where_(self, where):
        self.query += 'where ' + where

    def show_(self, entity):
        self.query += 'show ' + entity

    def fetch_all_(self):
        con = self.connection()
        cr = con.cursor()
        cr.execute(self.query)
        self._save_last_query()
        self._reset_buffer()
        return cr.fetchall()

    def last_query(self):
        return self.prev_query

    def _save_last_query(self):
        self.prev_query = self.query

    def _reset_buffer(self):
        self.query = ''
