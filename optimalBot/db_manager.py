from database_servers.mysql import MySQL
from database_servers.oracle import Oracle


class DBManager:
    def __init__(self, user, password, host, database, server='mysql'):
        if server == 'mysql':
            self.db = MySQL(user, password, host, database)
        elif server == 'oracle':
            self.db = Oracle(user, password, host, database)

    def get_tables(self):
        self.db.show_('tables')
        tables = self.db.fetch_all_()
        tables = list(zip(*tables))
        return tables

    def get_table_data(self, table_name):
        self.db.select_('*')
        self.db.from_(table_name)
        return self.db.fetch_all_()

    def authenticate_user(self, token):
        self.db.select_('*')
        self.db.from_('company')
        self.db.where_("token='" + token + "'")
        company = self.db.fetch_all_()
        if company:
            # Retrieve Bot Name, server, name, username, password, driver
            return (company[0][15], company[0][4], company[0][5],
                    company[0][6], company[0][7], company[0][8])
        return False
