from database_servers.mysql import MySQL
from database_servers.oracle import Oracle
from settings import *


class DBManager:
    def __init__(self, user, password, host, database, server='mysql'):
        if server == 'mysql':
            self.db = MySQL(user, password, host, database)
        elif server == 'oracle':
            self.db = Oracle(user, password, host, database)

    def __build_query_condition(self, conditions, like=False):
        where = ''
        if conditions:
            for key, value in conditions.items():
                if isinstance(value, str):
                    value = value.replace('"', '\\"')
                if like:
                    where += key + " like '%" + value + "%' and  "
                else:
                    where += key + " = \"" + value + "\" and "
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

    def get_table_data(self, table_name, client_id):
        self.db.select_('*')
        self.db.from_(table_name)
        self.db.where_('client_id=' + str(client_id))
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
            if multiple_values:
                return []
            return 0

    def authenticate_user(self, token):
        self.db.select_('*')
        self.db.from_(COMPANY_TABLE_NAME)
        self.db.where_("token='" + token + "'")
        company = self.db.fetch_all_()
        if company:
            # Retrieve Bot Name, server, name, username, password, driver, client_id , domain
            return (company[0][15], company[0][4], company[0][5], company[0][6],
                    company[0][7], company[0][8], company[0][0],
                    company[0][10], company[0][16], company[0][17])
        return False

    def verify_meta(self, content):
        self.db.select_('*')
        self.db.from_(COMPANY_TABLE_NAME)
        self.db.where_("token='" + content + "'")
        company = self.db.fetch_all_()
        if company:
            return 'success'
        return 'failure'

    def validate_db(self, token, driver):
        data = dict()
        data['db_verified'] = 1
        data['db_driver'] = driver
        status = self.db.update_(COMPANY_TABLE_NAME, data, "token='" + token + "'")
        if status:
            return 'success'
        return 'failure'

    def change_column_datatype(self, table, column, datatype):
        self.db.alter_(table, column, datatype)

    def saveLog(self, user_query, bot_reply, user_email, user_phone, date, companyId):
        # Get Company User Id
        userId = self.get_value(table_name=COMAPNY_USERS_TABLE_NAME, column_name='id',
                     conditions={COMAPNY_USERS_EMAIL_COLUMN_NAME: str(user_email)})
        if userId == 0:
            #Create User and Return newly inserted Id
            user_data = dict()
            user_data['email'] = str(user_email)
            user_data['phone'] = str(user_phone)
            user_data['companyId'] = str(companyId)
            userId = self.db.insert_('company_users', user_data)
        data = dict()
        data['user_query'] = user_query
        data['bot_reply'] = bot_reply
        data['msgdatetime'] = date
        data['company_userId'] = str(userId)
        self.db.insert_('logs', data)
