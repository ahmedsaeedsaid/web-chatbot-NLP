import json
from flask import request
from .response import *
import re
from urllib.parse import urlparse
import sys

sys.path.append("..")
from settings import *


# Class handle Validation Request
class Validation:
    @staticmethod
    def validateRequest():
        if request.method == 'OPTIONS':
            return {'data': jsonify({'Access-Control-Allow-Origin': '*',
                                     'Access-Control-Allow-Methods': 'POST, OPTIONS',
                                     'Access-Control-Max-Age': 1000,
                                     'Access-Control-Allow-Headers': 'origin, x-csrf-token, content-type, accept'}), 'valid': False}

        if request.content_type != 'application/json':
            return {'data': Response.throwError(REQUEST_CONTENTTYPE_NOT_VALID,
                                                'Request content type is not valid'), 'valid': False}

        data = request.get_json()

        api_name_not_found_error = {'data': Response.throwError(API_NAME_REQUIRED,
                                                                "API name is required."), 'valid': False}
        api_param_not_found_error = {'data': Response.throwError(API_PARAM_REQUIRED,
                                                                 "API PARAM is required."), 'valid': False}

        if 'name' in data.keys():
            if data['name'] == "":
                return api_name_not_found_error
        else:
            return api_name_not_found_error

        if 'param' in data.keys():
            if not isinstance(data['param'], list):
                 api_param_not_found_error
        else:
            return api_param_not_found_error

        return {'data': data,'valid':True}

    @staticmethod
    def validateToken():
        # get the auth token
        auth_header = request.headers.get('Authorization')
        if auth_header:
            token = re.findall('Bearer\s(\S+)', auth_header)
            if token:
                return {'data': token[0], 'valid': True}

        return {'data': Response.throwError(ATHORIZATION_HEADER_NOT_FOUND, "Access Token Not found."), 'valid': False}

    @staticmethod
    def validateParameter(fieldName, value, dataType, required=True):
        if required and not value and str(value) != "0":
            return {'data': Response.throwError(VALIDATE_PARAMETER_REQUIRED,
                                                fieldName + " parameter is required."), 'valid': False}

        if dataType == INTEGER:
            if isinstance(value, int):
                return {'data': value, 'valid': True}
            else:
                return {'data': Response.throwError(VALIDATE_PARAMETER_REQUIRED, "Data type is not valid for "
                                                    + fieldName + " It should be Numeric."), 'valid': False}
        elif dataType == STRING:
            if isinstance(value, str):
                return {'data': value, 'valid': True}
            else:
                return {'data': Response.throwError(VALIDATE_PARAMETER_REQUIRED, "Data type is not valid for "
                                                    + fieldName + " It should be String."), 'valid': False}
        elif dataType == BOOLEAN:
            if str(value) == "0":
                return {'data': False, 'valid': True}
            elif str(value) == "1":
                return {'data': True, 'valid': True}
            else:
                return {'data': Response.throwError(VALIDATE_PARAMETER_REQUIRED, "Data type is not valid for "
                                                    + fieldName + " It should be Boolean."), 'valid': False}
        else:
            return {'data': Response.throwError(VALIDATE_PARAMETER_REQUIRED,
                                                "Data type is not valid for " + fieldName), 'valid': False}

    @staticmethod
    def verifyDomain(domain):
        requested_domain = request.headers.get("Referer")
        if requested_domain:
            parsed_uri = urlparse(requested_domain)
            requested_domain = '{uri.netloc}'.format(uri=parsed_uri)
            return (domain == requested_domain) or (requested_domain == HOST)
        return True














