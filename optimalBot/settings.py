import os
from dotenv import load_dotenv, find_dotenv

# Load .env File.

load_dotenv(find_dotenv())


'''foundStart = False
for key in os.environ.keys():
    if key == "STARTVAR":
        foundStart = True
        continue
    if  foundStart:
        globals()[key] = os.getenv(key)
'''

HOST = os.environ['HOST']
PORT = os.environ['PORT']

DB_SERVER = os.environ['DB_SERVER']
DB_NAME = os.environ['DB_NAME']
DB_USERNAME = os.environ['DB_USERNAME']
DB_PASSWORD = os.environ['DB_PASSWORD']

TABLE_BOT_1 = os.environ['TABLE_BOT_1']
TABLE_BOT_2 = os.environ['TABLE_BOT_2']
TABLE_BOT_3 = os.environ['TABLE_BOT_3']
FAQ_TABLE_NAME = os.environ['FAQ_TABLE_NAME']

STORY_ID_COLUMN = os.environ['STORY_ID_COLUMN']
QUESTION_SUBJECT_COLUMN = os.environ['QUESTION_SUBJECT_COLUMN']
QUESTION_ID_COLUMN = os.environ['QUESTION_ID_COLUMN']
PARENT_ID_COLUMN = os.environ['PARENT_ID_COLUMN']

DEFAULT_STORY_ID = os.environ['DEFAULT_STORY_ID']

BOOLEAN = os.environ['BOOLEAN']
INTEGER = os.environ['INTEGER']
STRING = os.environ['STRING']

REQUEST_METHOD_NOT_VALID = os.environ['REQUEST_METHOD_NOT_VALID']
REQUEST_CONTENTTYPE_NOT_VALID = os.environ['REQUEST_CONTENTTYPE_NOT_VALID']
VALIDATE_PARAMETER_REQUIRED = os.environ['VALIDATE_PARAMETER_REQUIRED']
VALIDATE_PARAMETER_DATATYPE = os.environ['VALIDATE_PARAMETER_DATATYPE']
API_NAME_REQUIRED = os.environ['API_NAME_REQUIRED']
API_PARAM_REQUIRED = os.environ['API_PARAM_REQUIRED']
API_DOST_NOT_EXIST = os.environ['API_DOST_NOT_EXIST']
USER_NOT_ACTIVE = os.environ['USER_NOT_ACTIVE']

HTTP_SUCCESS_RESPONSE = os.environ['HTTP_SUCCESS_RESPONSE']
HTTP_FORBIDDEN_RESPONSE = os.environ['HTTP_FORBIDDEN_RESPONSE']

HTTP_FORBIDDEN_RESPONSE_MSG = os.environ['HTTP_FORBIDDEN_RESPONSE_MSG']

JWT_PROCESSING_ERROR = os.environ['JWT_PROCESSING_ERROR']
ATHORIZATION_HEADER_NOT_FOUND = os.environ['ATHORIZATION_HEADER_NOT_FOUND']
ACCESS_TOKEN_ERRORS = os.environ['ACCESS_TOKEN_ERRORS']
FAILED_PROCESSING = os.environ['FAILED_PROCESSING']
ACCESS_DOMAIN_ERRORS = os.environ['ACCESS_DOMAIN_ERRORS']
