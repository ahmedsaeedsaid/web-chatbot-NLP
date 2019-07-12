<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {

    function __construct() {
        parent::__construct();
        // check if user has access permission
        $this->load->library('authentication');
        // check loggedIn
        $this->authentication->IsLoggedIn('any');
        
        // load models
        $this->load->model('support/UserMod', 'CMSCM');
    }

    public function index() {
        $this->list();
    }

    public function list() {
        $users = $this->CMSCM->getAllusers();
        $data['users'] = $users;
        $config['title'] = 'Users List';
        $this->load->view('support/common', $config);
        $this->load->view('support/users_list', $data);
    }

    public function toggleActive() {
        $active = $this->input->post('active');
        $id = $this->input->post('id');
        $this->CMSCM->toggleActive($active, $id);
    }

    public function deleteUser() {
        $id = $this->input->post('id');
        $this->CMSCM->deleteUser($id);
    }

}
