import optimalBot.web_services as WS
from optimalBot.chatbot import chatBot as optimalbot
import chatterbot.comparisons as comp
import optimalBot.response_selection as resp
import re
from optimalBot.sentence_classification import *
from optimalBot.settings import *
from optimalBot.Filters import *


class ApiBot(WS.Rest):
    def askBot(self):
        Story_ID = DEFAULT_STORY_ID
        WS.Validation.validateParameter('query', self.param['query'], STRING)
        if self.bot_information:
            bot_name, db_server, db_name, db_username, db_password, db_driver, _, domain = self.bot_information
            if WS.Validation.verifyDomain(domain):
                if db_driver == 'mysqli' or db_driver == 'mysql':
                    # TODO: configure db_port
                    uri = "mysql://" + db_username + ":" + db_password + "@" + db_server + ":3306/" + db_name
                    chatbot = optimalbot(name=bot_name,
                                         storage_adapter="chatterbot.storage.SQLStorageAdapter",
                                         database_uri=uri,
                                         logic_adapters=
                                         [{
                                             "import_path": "FlowAdapter.FlowAdapter",
                                             "statement_comparison_function": comp.SentimentComparison,
                                             "response_selection_method": resp.get_flow_response
                                         }],
                                         filters=[get_recent_repeated_responsesCustomized],
                                         Story_ID=Story_ID,
                                         bot_information=self.bot_information)
                    # Filter User Query
                    cleaned_query = re.sub('[^ a-zA-Z0-9]', ' ', self.param['query'])
                    cleaned_query = " ".join(nltk.word_tokenize(cleaned_query))
                    print(cleaned_query)
                    response, Story_ID = chatbot.get_response(cleaned_query)
                    data = dict()
                    data['bot_reply'] = str(response)
                    return WS.Response.returnResponse(HTTP_SUCCESS_RESPONSE, data)
                else:
                    return WS.Response.throwError(HTTP_FORBIDDEN_RESPONSE, HTTP_FORBIDDEN_RESPONSE_MSG)
            else:
                return WS.Response.throwError(HTTP_FORBIDDEN_RESPONSE, HTTP_FORBIDDEN_RESPONSE_MSG)
        else:
            return WS.Response.throwError(HTTP_FORBIDDEN_RESPONSE, HTTP_FORBIDDEN_RESPONSE_MSG)
