<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$url = rtrim(__DIR__, 'controllers');
require_once $url . 'classes/user.php';

class userStory extends CI_Controller {

    private $data;
    private $db_drivers;

    function __construct() {
        parent::__construct();
        // check if user has access permission
        $this->load->library('authentication');
        // loading models
        $this->data = NULL;
        
    }
    
    

    public function index() {
        $data['title'] = 'Bot Stories';
        

        $this->load->view('header', $data);
        $this->load->view('userStory', $data);
        $this->load->view('footer');
    }
    
    
    
    

    

}
