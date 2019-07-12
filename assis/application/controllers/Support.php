<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$url = rtrim(__DIR__, 'controllers');
require_once $url . 'classes/user.php';

class Support extends CI_Controller {

    private $data = NULL;

    function __construct() {
        parent::__construct();
        // check if user has access permission
        $this->load->library('authentication');
        // loading models
        $this->load->model('support/LogMod', 'loginMod');
    }

    public function index() {
        // check loggedIn
        $this->authentication->IsLoggedIn('login');
        $config['title'] = 'Login';
        $this->load->view('support/login', $this->data, $config);
    }

    public function login() {
        $usr = new User();
        $usr->name = $this->input->post('dusername');
        $usr->pass = strip_tags(addslashes(stripslashes($this->input->post('dpass'))));
        $res = $this->loginMod->authenticateCMSUser($usr);
        if ($res) {
            redirect('support/main');
        } else {
            $this->data['err'] = 'normal_login_auth_error';
            $this->index();
        }
    }

    public function logout() {
        $this->session->unset_userdata('assis_userid');
        $this->session->unset_userdata('assis_username');
        $this->session->unset_userdata('assis_name');
        $this->session->unset_userdata('assis_job');
        $this->session->unset_userdata('assis_brief');
        $this->session->unset_userdata('assis_email');
        $this->session->unset_userdata('assis_facebook');
        $this->session->unset_userdata('assis_role');
        redirect('support');
    }

    public function main() {
        // check loggedIn
        $this->authentication->IsLoggedIn('any');
        $this->load->model('support/ClientMod', 'clientMod');
        $client_stats = $this->clientMod->getClientStats();
        $this->data['client_stats'] = $client_stats;
        $config['title'] = 'Administration';
        $this->load->view('support/common', $config);
        $this->load->view('support/main', $this->data);
    }

}
