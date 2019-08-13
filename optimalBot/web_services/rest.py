from ..settings import *
from .response import *
from .validation import *
from ..db_manager import DBManager


class Rest:
    def __init__(self):
        self.error = ""
        self.serviceName = ""
        self.param = ""
        self.token = ""
        self.bot_information = ""
        self.flagError = False
        self.db = DBManager(user=DB_USERNAME,
                            password=DB_PASSWORD,
                            host=DB_SERVER,
                            database=DB_NAME)

        request_result = Validation.validateRequest()
        if not request_result['valid']:
            self.error = request_result['data']
            self.flagError = True
        else:
            self.serviceName = request_result['data']['name']
            self.param = request_result['data']['param']

        if not self.flagError:
            request_token = Validation.validateToken()
            if not request_token['valid']:
                self.error = request_token['data']
                self.flagError = True
            else:
                self.token = request_token['data']

        if not self.flagError:
            self.bot_information = self.db.authenticate_user(self.token)
            if not self.bot_information:
                self.flagError = True
                self.error = Response.throwError(ACCESS_TOKEN_ERRORS, "This token not valid.")

        if not self.flagError:
            if not Validation.verifyDomain(self.bot_information[7]):
                self.flagError = True
                self.error = Response.throwError(ACCESS_DOMAIN_ERRORS, "This Domain has been used, restricted access.")

    def __api_not_exists(self):
        return Response.throwError(API_DOST_NOT_EXIST, "API does Not exist.")

    def processApi(self):
        if self.flagError:
            return self.error
        rMethod = getattr(self, self.serviceName, self.__api_not_exists)
        return rMethod()
