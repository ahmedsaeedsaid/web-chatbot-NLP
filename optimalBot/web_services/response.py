from flask_jsonpify import jsonify

# Class handle response

class Response :
    @staticmethod
    def throwError(code , message):
        return jsonify({'error': {'code' : code , 'message' : message}})

    @staticmethod
    def returnResponse(code , data):
        return jsonify({'response': {'code' : code , 'result' : data}})
