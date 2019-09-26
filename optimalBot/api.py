from flask import Flask
from flask_restful import Api
from flask_cors import CORS
from settings import *
from apiBot import ApiBot
from requirementData import RequirementData
app = Flask(__name__)
CORS(app)
api = Api(app)

glove = RequirementData.loadGlove()
tags = RequirementData.loadTags()


@app.route('/', methods=['POST', 'OPTIONS'])
def root():
    api_bot = ApiBot(glove, tags)
    return api_bot.processApi()


if __name__ == '__main__':
    #context = ('C:\wamp\www\web-assistant\optimalBot\key\certificate.crt', 'C:\wamp\www\web-assistant\optimalBot\key\private.key') # certificate and key files
    app.run(host=HOST, port=PORT)
