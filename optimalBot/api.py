from flask import Flask, request, abort, make_response
from flask_restful import Resource, Api
from flask_jsonpify import jsonify
from flask_cors import CORS
from db_manager import DBManager
import time
import gensim
import numpy as np
from sentence_classification import *
from optimalBot.chatbot import chatBot as optimalbot
from chatterbot import filters
import chatterbot.comparisons as comp
import optimalBot.response_selection as resp
import json
import re
import chatterbot.logic.best_match
from chatterbot import ChatBot
from optimalBot.trainer import ListTrainerOverridden,ChatterBotCorpusTrainerOverridden
from sentence_classification import *
import os
from os.path import join, dirname
from dotenv import load_dotenv

dotenv_path = join(dirname(__file__), '../.env')
load_dotenv(dotenv_path)

# Accessing variables.
HOST = os.getenv('HOST')
PORT = os.getenv('PORT')
DB_SERVER = os.getenv('DB_SERVER')
DB_NAME = os.getenv('DB_NAME')
DB_USERNAME = os.getenv('DB_USERNAME')
DB_PASSWORD = os.getenv('DB_PASSWORD')
TABLE_BOT_1 = os.getenv('TABLE_BOT_1')
TABLE_BOT_2 = os.getenv('TABLE_BOT_2')
TABLE_BOT_3 = os.getenv('TABLE_BOT_3')
print(TABLE_BOT_1)
FAQ_TABLE_NAME = os.getenv('FAQ_TABLE_NAME')
DEFAULT_STORY_ID = os.getenv('DEFAULT_STORY_ID')

db = DBManager(user=DB_USERNAME,
               password=DB_PASSWORD,
               host=DB_SERVER,
               database=DB_NAME)

app = Flask(__name__)
global Story_ID
Story_ID = DEFAULT_STORY_ID
CORS(app)
api = Api(app)


def authorize(user_token):
    # Contain User Verification Logic
    # Return Bot name associated with user
    authorized = db.authenticate_user(user_token)
    if authorized:
        return authorized
    else:
        abort(403)


def verifyMetaTag(content):
    # Contain Meta Verification Logic
    # Return Success if meta content is not fake
    status = db.verify_meta(content)
    return status


def validateDatabase(token):
    # Contain Meta Verification Logic
    # Return Success if meta content is not fake
    status = db.validate_db(token)
    return status

@app.route('/askBot', methods=['GET', 'POST'])
def api_askBot():
    global Story_ID
    if request.method == 'POST':
        args = request.form
    elif request.method == 'GET':
        args = request.args
    if 'token' in args and 'query' in args:
        bot_information = authorize(args['token'])
        bot_name, db_server, db_name, db_username, db_password, db_driver, _ = bot_information
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
                                 filters=[filters.get_recent_repeated_responses],
                                 Story_ID=Story_ID,
                                 bot_information=bot_information)
            # Filter User Query
            cleaned_query = re.sub('[^ a-zA-Z0-9]', ' ', args['query'])
            cleaned_query = " ".join(nltk.word_tokenize(cleaned_query))
            response, Story_ID = chatbot.get_response(cleaned_query)
            data = dict()
            data['bot_reply'] = str(response)
            return jsonify(data)
        else:
            abort(403)
    else:
        abort(403)


@app.route('/create', methods=['GET', 'POST'])
def api_create():
    if request.method == 'POST':
        args = request.form
    elif request.method == 'GET':
        args = request.args
    if 'token' in args:
        bot_information = authorize(args['token'])
        bot_name, db_server, db_name, db_username, db_password, db_driver, client_id = bot_information
        if db_driver == 'mysqli' or db_driver == 'mysql':
            uri = "mysql://" + db_username + ":" + db_password + "@" + db_server + ":3306/" + db_name
            chatbot = ChatBot(bot_name,
                              storage_adapter="chatterbot.storage.SQLStorageAdapter",
                              database_uri=uri)
            db = DBManager(user=db_username,
                           password=db_password,
                           host=db_server,
                           database=db_name)

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
            return jsonify('success')


@app.route('/checkMetaValidity', methods=['POST'])
def api_checkMetaValidity():
    args = request.form
    if 'content' in args:
        status = verifyMetaTag(args['content'])
        data = dict()
        data['status'] = str(status)
        return jsonify(data)
    else:
        abort(403)



@app.route('/validateDatabase', methods=['POST'])
def api_validateDatabase():
    args = request.form
    if 'token' in args:
        status = validateDatabase(args['token'])
        if status:
            status = 'success'
        else:
            status = 'failure'
        data = dict()
        data['status'] = str(status)
        return jsonify(data)
    else:
        abort(403)


if __name__ == '__main__':
    app.run(host=HOST, port=PORT)
