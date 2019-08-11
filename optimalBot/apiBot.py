import optimalBot.web_services as WS
from optimalBot.settings import *
class ApiBot(WS.Rest):
    def test(self):
        return WS.Response.returnResponse(SUCCESS_RESPONSE,[1,2,3])
