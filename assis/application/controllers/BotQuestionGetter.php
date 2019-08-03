<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$url = rtrim(__DIR__, 'controllers');
require_once $url . 'classes/user.php';

class BotQuestionGetter extends CI_Controller {

    function __construct() {
        parent::__construct();
        // check if user has access permission
        $this->load->library('authentication');
        // loading models
        $this->load->model('BotQuestionGetterMod');
    }

    public function index() {
    }

}
