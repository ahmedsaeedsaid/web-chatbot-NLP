from optimalBot.database_servers.mysql import MySQL
from optimalBot.database_servers.oracle import Oracle
from .settings import *


class DBManager:
    def __init__(self, user, password, host, database, server='mysql'):
        if server == 'mysql':
            self.db = MySQL(user, password, host, database)
        elif server == 'oracle':
            self.db = Oracle(user, password, host, database)

    def __build_query_condition(self, conditions, like=False):
        where = ''
        if conditions :
            for key, value in conditions.items():
                if like:
                    where += key + " like '%" + value + "%' and  "
                else:
                    where += key + " = '" + value + "' and "
        where += " 1"
        return where

    def get_tables(self):
        self.db.show_('tables')
        tables = self.db.fetch_all_()
        tables = list(zip(*tables))
        return tables

    def delete_table_data(self, table_name, conditions={}, like=False):
        self.db.delete_()
        self.db.from_(table_name)
        where = self.__build_query_condition(conditions, like)
        self.db.where_(where)
        return self.db.commit_()

    def get_table_data(self, table_name):
        self.db.select_('*')
        self.db.from_(table_name)
        return self.db.fetch_all_()

    def get_value(self, table_name, column_name, conditions={}, like=False, multiple_values=False):
        self.db.select_(column_name)
        self.db.from_(table_name)
        where = self.__build_query_condition(conditions, like)
        self.db.where_(where)
        results = self.db.fetch_all_()
        if results and not multiple_values:
            return results[0][0]
        elif results:
            return results
        else:
            return 0

    def authenticate_user(self, token):
        self.db.select_('*')
        self.db.from_(COMPANY_TABLE_NAME)
        self.db.where_("token='" + token + "'")
        company = self.db.fetch_all_()
        if company:
            # Retrieve Bot Name, server, name, username, password, driver, client_id , domain , company_id
            return (company[0][15], company[0][4], company[0][5],
                    company[0][6], company[0][7], company[0][8], company[0][1] , company[0][10] , company[0][0] )
        return False

    def verify_meta(self, content):
        self.db.select_('*')
        self.db.from_(COMPANY_TABLE_NAME)
        self.db.where_("token='" + content + "'")
        company = self.db.fetch_all_()
        if company:
            return 'success'
        return 'failure'

    def validate_db(self, token):
        data = dict()
        data['db_verified'] = 1
        status = self.db.update_(COMPANY_TABLE_NAME, data, "token='" + token + "'")
        return status

    def update_story_id(self, story_id, company_id):
        status = self.db.update_(COMPANY_TABLE_NAME, {CURRENT_STORY_ID_COLUMN:story_id}, "id=" + str(company_id))
        return status
