<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication {

    protected $CI;

    public function __construct() {
        // Assign the CodeIgniter super-object
        $this->CI = & get_instance();

        // load models
        $this->CI->load->model('support/authMod');
        $this->CI->load->model('support/profileMod');
    }

    public function IsLoggedIn($mode='any') {
        if($mode == 'any'){
            if (!$this->CI->session->userdata('assis_userid')) {
                redirect('support');
                exit;
            }
        } else if($mode == 'login') {
           if ($this->CI->session->userdata('assis_userid')) {
                redirect('support/main');
                exit;
            }
        }
        return true;
    }

}
