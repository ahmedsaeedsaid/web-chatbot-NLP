<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';
use Restserver\Libraries\REST_Controller;

class BotAPI extends REST_Controller {
 
    function DBConnect_post() {
        $this->load->model('questionnaire_mod');
        $user_id = $this->input->post('user_id');
        $user = $this->questionnaire_mod->getUser($user_id);
        if($user) {
            // success and user found
            $this->response($user, 200);
        } else {
            // user not found
            $this->response(NULL, 404);
        }
    }
    
    
}
