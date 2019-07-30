<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$url = rtrim(__DIR__, 'controllers');
require_once $url . 'classes/user.php';

class BotQuestionGetter extends CI_Controller {

    private $data;
    private $db_drivers;

    function __construct() {
        parent::__construct();
        // check if user has access permission
        $this->load->library('authentication');
        // loading models
        $this->load->model('BotQuestionGetterMod');
        $this->data = NULL;
        
    }
    
    

    public function index() {
        $data['title'] = 'Bot Question ChatBot';
        

        $this->load->view('header', $data);
        $this->load->view('BotQuestionGetterForm', $data);
        $this->load->view('footer');
    }
    
    
    
    

    

}
