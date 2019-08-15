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

        bot_name, db_server, db_name, db_username, db_password, db_driver, _, domain, db_verified, first_train = self.bot_information

        if not db_verified:
            return WS.Response.throwError(HTTP_FORBIDDEN_RESPONSE, "Sorry, Database is not verified yet.")

        if not first_train:
            return WS.Response.throwError(HTTP_FORBIDDEN_RESPONSE, "Please train the bot at least one time using our customer portal.")

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
            print(Story_ID)

            return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, {'bot_reply': str(response), 'story_id': Story_ID})
        else:
            return WS.Response.throwError(DATABASE_TYPE_ERROR, "Database type is not supported.")
        #except:
             #return WS.Response.throwError(JWT_PROCESSING_ERROR, "Sorry, Server is down, please contact the administrators")

    def createBot(self):
        try:
            bot_name, db_server, db_name, db_username, db_password, db_driver, _, domain, db_verified, first_train = self.bot_information
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

                return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, 'success')
            else:
                return WS.Response.throwError(DATABASE_TYPE_ERROR, "Database type is not supported.")
        except:
             return WS.Response.throwError(JWT_PROCESSING_ERROR, "Sorry, Server is down, please contact the administrators")

    def checkMetaValidity(self):
        try:
            content = WS.Validation.validateParameter('content', self.param['content'], STRING)
            if content['valid']:
                content = content ['data']
            else:
                return content['data']

            status = self.db.verify_meta(content)
            return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, {'status': str(status)})
        except:
             return WS.Response.throwError(JWT_PROCESSING_ERROR, "Sorry, Server is down, please contact the administrators")

    def validateDatabase(self):
        try:
            status = self.db.validate_db(self.token)
            return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, {'status': str(status)})
        except:
             return WS.Response.throwError(JWT_PROCESSING_ERROR, "Sorry, Server is down, please contact the administrators")
