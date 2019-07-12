<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Home extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $config['url'] = 'home';
        $config['title'] = 'Home';
        $this->load->view('header', $config);
        $this->load->view('home');
        $this->load->view('footer');
    }

}
