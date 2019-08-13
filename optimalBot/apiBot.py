import optimalBot.web_services as WS
import chatterbot.comparisons as comp
import optimalBot.optimal_chatterbot.response_selection as resp
import re
from optimalBot.optimal_chatterbot.chatbot import chatBot as optimalbot
from optimalBot.optimal_chatterbot.sentence_classification import *
from optimalBot.optimal_chatterbot.Filters import *
from optimalBot.optimal_chatterbot.trainer import ListTrainerOverridden,ChatterBotCorpusTrainerOverridden
from .db_manager import DBManager
from .settings import *
class ApiBot(WS.Rest):
    def askBot(self):
        try:
            query = WS.Validation.validateParameter('query', self.param['query'], STRING)
            if query['valid']:
                query = query ['data']
            else:
                return query['data']
            startChat = WS.Validation.validateParameter('startChat', self.param['startChat'], BOOLEAN)
            if startChat['valid']:
                startChat = startChat ['data']
            else:
                return startChat['data']

            bot_name, db_server, db_name, db_username, db_password, db_driver, _, domain , company_id= self.bot_information

            if startChat:
                self.db.update_story_id(DEFAULT_STORY_ID,company_id)

            Story_ID = self.db.get_value(COMPANY_TABLE_NAME,CURRENT_STORY_ID_COLUMN,{'id':str(company_id)})

            if db_driver == 'mysqli' or db_driver == 'mysql':
                # TODO: configure db_port

                uri = "mysql://" + db_username + ":" + db_password + "@" + db_server + ":3306/" + db_name
                chatbot = optimalbot(name=bot_name,
                                     storage_adapter="chatterbot.storage.SQLStorageAdapter",
                                     database_uri=uri,
                                     logic_adapters=
                                     [{
                                         "import_path": "optimalBot.optimal_chatterbot.FlowAdapter.FlowAdapter",
                                         "statement_comparison_function": comp.SentimentComparison,
                                         "response_selection_method": resp.get_flow_response
                                     }],
                                     filters=[get_recent_repeated_responsesCustomized],
                                     Story_ID=Story_ID,
                                     bot_information=self.bot_information)

                # Filter User Query
                cleaned_query = re.sub('[^ a-zA-Z0-9]', ' ', query)
                cleaned_query = " ".join(nltk.word_tokenize(cleaned_query))
                response, Story_ID = chatbot.get_response(cleaned_query)

                self.db.update_story_id(Story_ID,company_id)
                return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, {'bot_reply':str(response)})
            else:
                return WS.Response.throwError(DATABASE_TYPE_ERROR, "this Database type not supported.")
        except:
             return WS.Response.throwError(JWT_PROCESSING_ERROR, "An exception occurred.")

    def createBot(self):
        try:
            bot_name, db_server, db_name, db_username, db_password, db_driver, _, domain , company_id= self.bot_information
            if db_driver == 'mysqli' or db_driver == 'mysql':
                db = DBManager(user=db_username,
                            password=db_password,
                            host=db_server,
                            database=db_name)

                uri = "mysql://" + db_username + ":" + db_password + "@" + db_server + ":3306/" + db_name
                chatbot = optimalbot(name=bot_name,
                                    storage_adapter="chatterbot.storage.SQLStorageAdapter",
                                    database_uri=uri,)


                tables = [TABLE_BOT_1, TABLE_BOT_2, TABLE_BOT_3]
                for table in tables:
                    db.delete_table_data(table)

                faq_table_name = FAQ_TABLE_NAME
                Q_A = get_faq_Q_A_Pairs(faq_table_name, db)

                conversation = list()
                for key, value in Q_A.items():
                    conversation.append(key)
                    conversation.append(value)

                trainer = ChatterBotCorpusTrainerOverridden(chatbot)
                trainer.train(
                    "chatterbot.corpus.english.greetings",
                    "chatterbot.corpus.english.conversations"
                )

                trainer = ListTrainerOverridden(chatbot)
                trainer.train(conversation)

                self.db.update_story_id(DEFAULT_STORY_ID,company_id)
                return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, 'success')
            else:
                return WS.Response.throwError(DATABASE_TYPE_ERROR, "this Database type not supported.")
        except:
             return WS.Response.throwError(JWT_PROCESSING_ERROR, "An exception occurred.")

    def checkMetaValidity(self):
        try:
            content = WS.Validation.validateParameter('content', self.param['content'], STRING)
            if content['valid']:
                content = content ['data']
            else:
                return content['data']

            status = self.db.verify_meta(content)
            return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, {'status':str(status)})
        except:
             return WS.Response.throwError(JWT_PROCESSING_ERROR, "An exception occurred.")

    def validateDatabase(self):
        try:
            status = self.db.validateDatabase(self.token)
            return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, {'status':str(status)})
        except:
             return WS.Response.throwError(JWT_PROCESSING_ERROR, "An exception occurred.")

