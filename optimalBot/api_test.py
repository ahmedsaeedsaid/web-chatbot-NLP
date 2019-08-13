from flask import Flask
from flask_restful import Api
from flask_cors import CORS
from optimalBot.settings import *
from optimalBot.apiBot import ApiBot

app = Flask(__name__)
CORS(app)
api = Api(app)


@app.route('/', methods=['POST', 'OPTIONS'])
def root():
    api_bot = ApiBot()
    return api_bot.processApi()


if __name__ == '__main__':
    app.run(host=HOST, port=PORT)
