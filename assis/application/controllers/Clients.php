<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends CI_Controller {

    function __construct() {
        parent::__construct();
        // check if user has access permission
        $this->load->library('authentication');
        // check loggedIn
        $this->authentication->IsLoggedIn('any');
        
        // load models
        $this->load->model('support/ClientMod', 'CMSCM');
    }

    public function index() {
        $this->list();
    }

    public function list() {
        $users = $this->CMSCM->getAllClients();
        $data['clients'] = $users;
        $config['title'] = 'Clients List';
        $this->load->view('support/common', $config);
        $this->load->view('support/clients_list', $data);
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
