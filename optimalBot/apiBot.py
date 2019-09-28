import web_services as WS
import chatBot_tags as CT
import chatterbot.comparisons as comp
import optimal_chatterbot.response_selection as resp
import re
from optimal_chatterbot.chatbot import chatBot as optimalbot
from optimal_chatterbot.sentence_classification import *
from optimal_chatterbot.Filters import *
from optimal_chatterbot.trainer import ListTrainerOverridden,ChatterBotCorpusTrainerOverridden
from db_manager import DBManager
from settings import *
from DataCleaning import DataCleaning


class ApiBot(WS.Rest):
    def askBot(self):
        #try:
            query = WS.Validation.validateParameter('query', self.param['query'], STRING)
            if query['valid']:
                query = query['data']
            else:
                return query['data']
            Story_ID = WS.Validation.validateParameter('story_id', self.param['story_id'], INTEGER)
            if Story_ID['valid']:
                Story_ID = Story_ID['data']
            else:
                return Story_ID['data']

            bot_name, db_server, db_name, db_username, db_password, db_driver, client_id, domain, db_verified, first_train = self.bot_information

            if not db_verified:
                return WS.Response.throwError(HTTP_FORBIDDEN_RESPONSE, "Sorry, Database is not verified yet.")

            if not first_train:
                return WS.Response.throwError(HTTP_FORBIDDEN_RESPONSE, "Please train the bot at least one time using our customer portal.")

            if db_driver == 'mysqli' or db_driver == 'mysql':
                db = DBManager(user=DB_USERNAME,
                            password=DB_PASSWORD,
                            host=DB_SERVER,
                            database=DB_NAME)
                # TODO: configure db_port

                uri = "mysql://" + DB_USERNAME + ":" + DB_PASSWORD + "@" + DB_SERVER + ":3306/" + DB_NAME
                chatbot = optimalbot(name=bot_name,
                                     storage_adapter="optimal_chatterbot.SQLStorageAdapter",
                                     database_uri=uri,
                                     read_only=True,
                                     logic_adapters=
                                     [{
                                         "import_path": "optimal_chatterbot.FlowAdapter.FlowAdapter",
                                         "statement_comparison_function": comp.SentimentComparison,
                                         "response_selection_method": resp.get_flow_response,
                                         "maximum_similarity_threshold":0.45
                                     }],
                                     filters=[get_recent_repeated_responsesCustomized],
                                     Story_ID=Story_ID,
                                     bot_information=self.bot_information,
                                     glove = self.glove,
                                     tags = self.tags
                                     )

                # Filter User Query
                dt = DataCleaning()
                cleaned_query = dt.clean(query)
                #cleaned_query = re.sub('[^ a-zA-Z0-9]', ' ', query)
                #escaped_query = re.escape(query)
                #tokenized_query = " ".join(nltk.word_tokenize(escaped_query))
                #cleaned_query = re.sub(u"(\u2018|\u2019)", "'", tokenized_query)
                response, Story_ID, children_questions, means_questions,FAQ_simarities = chatbot.get_response(cleaned_query)
                # Get suggested Text for question
                suggested_text = []
                for child in children_questions:
                    suggested_text.append(db.get_value(table_name=FAQ_TABLE_NAME, column_name=QUESTION_SUGGESTED_TEXT_COLUMN, conditions={QUESTION_SUBJECT_COLUMN: child[0]}))
                return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, {'bot_reply': str(response), 'story_id': Story_ID, 'suggested_actions': children_questions,'means_questions': means_questions, 'suggested_text': suggested_text,'FAQ_simarities':FAQ_simarities})
            else:
                return WS.Response.throwError(DATABASE_TYPE_ERROR, "Database type is not supported.")
        #except:
            #return WS.Response.throwError(JWT_PROCESSING_ERROR, "Sorry, Server is down, please contact the administrators")

    def createBot(self):
     #   try:
            # client_id: is actually company id but name is used to avoid doing huge changes in code
            bot_name, db_server, db_name, db_username, db_password, db_driver, client_id, domain, db_verified, first_train = self.bot_information
            if db_driver == 'mysqli' or db_driver == 'mysql':
                db = DBManager(user=DB_USERNAME,
                            password=DB_PASSWORD,
                            host=DB_SERVER,
                            database=DB_NAME)

                uri = "mysql://" + DB_USERNAME + ":" + DB_PASSWORD + "@" + DB_SERVER + ":3306/" + DB_NAME
                chatbot = optimalbot(name=bot_name,
                                    storage_adapter="optimal_chatterbot.SQLStorageAdapter",
                                    database_uri=uri,
                                    read_only=True)

                #db.change_column_datatype('statement', 'text', 'text')
                db.change_column_datatype('statement', 'search_text', 'text')
                db.change_column_datatype('statement', 'in_response_to', 'text')
                db.change_column_datatype('statement', 'search_in_response_to', 'text')


                tables = [TABLE_BOT_1, TABLE_BOT_2, TABLE_BOT_3]
                for table in tables:
                    db.delete_table_data(table, conditions={CLIENT_ID_COLUMN: str(client_id)})

                faq_table_name = FAQ_TABLE_NAME
                Q_A = get_faq_Q_A_Pairs(faq_table_name, db, client_id)

                dt = DataCleaning()

                conversation = list()
                for key, value in Q_A.items():
                    conversation.append(dt.clean(key))
                    conversation.append(dt.clean(value))
                '''trainer = ChatterBotCorpusTrainerOverridden(chatbot)
                trainer.train(
                    "chatterbot.corpus.english.greetings",
                    "chatterbot.corpus.english.conversations"
                )'''

                trainer = ListTrainerOverridden(chatbot)
                trainer.train({'conversation': conversation, 'client_id': client_id})

                return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, 'success')
            else:
                return WS.Response.throwError(DATABASE_TYPE_ERROR, "Database type is not supported.")
       # except:
        #     return WS.Response.throwError(JWT_PROCESSING_ERROR, "Sorry, Server is down, please contact the administrators")

    def checkMetaValidity(self):
        try:
            content = WS.Validation.validateParameter('content', self.param['content'], STRING)
            if content['valid']:
                content = content['data']
            else:
                return content['data']

            status = self.db.verify_meta(content)
            return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, {'status': str(status)})
        except:
             return WS.Response.throwError(JWT_PROCESSING_ERROR, "Sorry, Server is down, please contact the administrators")

    def validateDatabase(self):
        try:
            driver = WS.Validation.validateParameter('driver', self.param['driver'], STRING)
            if driver['valid']:
                driver = driver['data']
            else:
                return driver['data']
            if driver:
                status = self.db.validate_db(self.token, driver)
                return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, {'status': str(status)})
            else:
                return WS.Response.throwError(DATABASE_TYPE_ERROR, "Sorry, We couldn't verify your database, please check with our support")
        except:
             return WS.Response.throwError(JWT_PROCESSING_ERROR, "Sorry, Server is down, please contact the administrators")

    def suggestionTags(self):
        try:
            statement = WS.Validation.validateParameter('statement', self.param['statement'], STRING)
            if statement['valid']:
                statement = statement['data']
            else:
                return statement['data']

            similarity = CT.Similarity(self.glove,self.tags)
            statement_tags, statement_keywords = similarity.get_tags(statement)

            return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, {'tags': list(set(statement_tags + statement_keywords))})

        except:
             return WS.Response.throwError(JWT_PROCESSING_ERROR, "Sorry, Server is down, please contact the administrators")

    def saveLog(self):
        # try:
        user_query = WS.Validation.validateParameter('user_query', self.param['user_query'], STRING)
        bot_reply = WS.Validation.validateParameter('bot_reply', self.param['bot_reply'], STRING)
        user_email = WS.Validation.validateParameter('user_email', self.param['user_email'], STRING)
        user_phone = WS.Validation.validateParameter('user_phone', self.param['user_phone'], STRING)
        date = WS.Validation.validateParameter('date', self.param['date'], STRING)
        _, _, _, _, _, _, companyId, _, _, _ = self.bot_information

        if user_query['valid']:
            user_query = user_query['data']
        else:
            return user_query['data']

        if bot_reply['valid']:
            bot_reply = bot_reply['data']
        else:
            return bot_reply['data']

        if user_email['valid']:
            user_email = user_email['data']
        else:
            return user_email['data']

        if user_phone['valid']:
            user_phone = user_phone['data']
        else:
            return user_phone['data']

        if date['valid']:
            date = date['data']
        else:
            return date['data']

        db = DBManager(user=DB_USERNAME,
                       password=DB_PASSWORD,
                       host=DB_SERVER,
                       database=DB_NAME)

        db.saveLog(user_query, bot_reply, user_email, user_phone, date, companyId)
        return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, 'success')
        # except:
        # return WS.Response.throwError(JWT_PROCESSING_ERROR, "Sorry, Server is down, please contact the administrators")

    def getAccuracyOfQuestions(self):
        try:
            query = WS.Validation.validateParameter('query', self.param['query'], STRING)
            if query['valid']:
                query = query['data']
            else:
                return query['data']

            bot_name, db_server, db_name, db_username, db_password, db_driver, client_id, domain, db_verified, first_train = self.bot_information

            if not db_verified:
                return WS.Response.throwError(HTTP_FORBIDDEN_RESPONSE, "Sorry, Database is not verified yet.")

            if not first_train:
                return WS.Response.throwError(HTTP_FORBIDDEN_RESPONSE, "Please train the bot at least one time using our customer portal.")

            if db_driver == 'mysqli' or db_driver == 'mysql':
                db = DBManager(user=DB_USERNAME,
                            password=DB_PASSWORD,
                            host=DB_SERVER,
                            database=DB_NAME)
                # TODO: configure db_port

                uri = "mysql://" + DB_USERNAME + ":" + DB_PASSWORD + "@" + DB_SERVER + ":3306/" + DB_NAME
                chatbot = optimalbot(name=bot_name,
                                     storage_adapter="optimal_chatterbot.SQLStorageAdapter",
                                     database_uri=uri,
                                     read_only=True,
                                     logic_adapters=
                                     [{
                                         "import_path": "optimal_chatterbot.FlowAdapter.FlowAdapter",
                                         "statement_comparison_function": comp.SentimentComparison,
                                         "response_selection_method": resp.get_flow_response,
                                         "maximum_similarity_threshold":0.45
                                     }],
                                     filters=[get_recent_repeated_responsesCustomized],
                                     Story_ID=0,
                                     bot_information=self.bot_information,
                                     glove = self.glove,
                                     tags = self.tags
                                     )

                # Filter User Query
                dt = DataCleaning()
                cleaned_query = dt.clean(query)

                FAQ_simarities = chatbot.getAccuracyOfQuestions(cleaned_query)

                return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, {'FAQ_simarities':FAQ_simarities})
            else:
                return WS.Response.throwError(DATABASE_TYPE_ERROR, "Database type is not supported.")

        except:
             return WS.Response.throwError(JWT_PROCESSING_ERROR, "Sorry, Server is down, please contact the administrators")
