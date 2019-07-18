from flask import Flask, request, abort, make_response
from flask_restful import Resource, Api
from flask_jsonpify import jsonify
from flask_cors import CORS
from db_manager import DBManager
import time
import gensim
import numpy as np
from sentence_classification import *
from chatterbot import ChatBot
import chatterbot.comparisons as comp
import chatterbot.response_selection as resp
import json
import re

db = DBManager(user='root',
               password='',
               host='127.0.0.1',
               database='assis')

def authorize(user_token):
    # Contain User Verification Logic
    # Return Bot name associated with user
    authorized = db.authenticate_user(user_token)
    if authorized:
        return authorized
    else:
        abort(403)

app = Flask(__name__)
CORS(app)
api = Api(app)

@app.route('/')
def api_root():
    return 'Welcome'


@app.route('/askBot', methods=['GET', 'POST'])
def api_askBot():
    if request.method == 'POST':
        args = request.form
    elif request.method == 'GET':
        args = request.args
    if 'token' in args and 'query' in args:
        print(args['token'])
        bot_information = authorize(args['token'])
        bot_name, db_server, db_name, db_username, db_password, db_driver = bot_information
        if db_driver == 'mysqli' or db_driver == 'mysql':
            uri = "mysql://" + db_username + "@" + db_server + ":3306/" + db_name
            chatbot = ChatBot(name=bot_name,
                              storage_adapter="chatterbot.storage.SQLStorageAdapter",
                              database_uri=uri,
                              logic_adapters=
                              [{
                                  "import_path": "chatterbot.logic.BestMatch",
                                  "statement_comparison_function": comp.SpacySimilarity,
                                  "response_selection_method": resp.get_most_frequent_response
                              }])
            # Filter User Query
            cleaned_query = re.sub('[^ a-zA-Z0-9]', ' ', args['query'])
            cleaned_query = " ".join(nltk.word_tokenize(cleaned_query))
            response = chatbot.get_response(cleaned_query)
            data = dict()
            data['bot_reply'] = str(response)
            return jsonify(data)
        else:
            abort(403)
    else:
        abort(403)

if __name__ == '__main__':
    app.run(host="localhost", port='5002')
