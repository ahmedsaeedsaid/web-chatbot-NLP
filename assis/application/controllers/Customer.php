<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$url = rtrim(__DIR__, 'controllers');
require_once $url . 'classes/customer.php';

class Customer extends CI_Controller {

    private $data = NULL;

    function __construct() {
        parent::__construct();
        // check if user has access permission
        $this->load->library('authentication');
        // loading models
        $this->load->model('customer/LogMod', 'loginMod');
        $this->load->model('customer/CustomerMod', 'CM');
    }

    public function index() {
        // check loggedIn
        
        $this->authentication->IsLoggedInCustomer('login');
        $config['title'] = 'Login';
        $this->load->view('customer/login', $config);
    }

    public function login() {
        $cust = new Customer_obj();
        $cust->email = $this->input->post('demail');
        $cust->password = strip_tags(addslashes(stripslashes($this->input->post('dpass'))));
        $res = $this->loginMod->authenticateCustomer($cust);
        if ($res) {
            redirect('customer/main');
        } else {
            $this->data['err'] = 'normal_login_auth_error';
            $this->index();
        }
    }

    public function logout() {
        $this->session->unset_userdata('assis_customerid');
        $this->session->unset_userdata('assis_customername');
        $this->session->unset_userdata('assis_customeremail');
        redirect('customer');
    }

    public function main() {
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $config['title'] = 'Administration';
        $config['page'] = 'main';
        $this->load->view('customer/common', $config);
        $this->load->view('customer/main', $this->data);
    }
    
    public function questions(){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $config['title'] = 'Bot Question ChatBot';
        $data['tree_nodes'] = $this->CM->getQASC();
        $this->load->model('subscribeFormMod');
        $company = $this->subscribeFormMod->getCompanyById($this->session->userdata('assis_companyid'));
        $data['token'] = $company->token;
        $this->load->view('customer/common', $config);
        $this->load->view('customer/qa', $data);
    }
    
    /*public function scenariosList(){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $scenarios = $this->CM->getAllScenarios();
        $data['scenarios'] = $scenarios;
        $this->load->model('subscribeFormMod');
        $company = $this->subscribeFormMod->getCompanyById($this->session->userdata('assis_companyid'));
        $data['token'] = $company->token;
        $config['title'] = 'Bot Scenarios';
        $this->load->view('customer/common', $config);
        $this->load->view('customer/scenarios_list', $data);
    }*/
    
    public function addScenario(){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $config['title'] = 'Add Scenario';
        $this->load->view('customer/common', $config);
        $this->load->view('customer/add_scenario');
    }
    
    public function saveScenario(){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $name = $this->input->post('name');
        $action = $this->input->post('sc-action');
        $scenario_id = $this->input->post('scenario');
        $data = array(
            "name" => $name
        );
        $this->CM->saveScenario($data, $scenario_id, $action);
        redirect('customer/questions');
    }
    
    /*public function toggleScenarioActive(){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $active = $this->input->post('active');
        $id = $this->input->post('id');
        $this->CM->toggleActive($active, $id);
    }*/
    
    public function deleteScenario(){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $id = $this->input->post('id');
        $this->CM->deleteScenario($id);
    }
    
    public function saveQASC(){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $Question = $this->input->post('Question');
        $answer = $this->input->post('Answer');
        $tags = $this->input->post('Tags');
        if(!$tags){
            $tags = array();
        }
        $scenario = $this->input->post('scenario');
        $parent = $this->input->post('parent');
        $action = $this->input->post('action');
        $question_id = $this->input->post('question_id');
        $data = array(
            "question" => $Question,
            "answer" => $answer,
            "parent" => $parent,
            "scenario" => $scenario
        );
        $this->CM->saveQASC($data, $scenario ,$tags, $action, $question_id);
    }
    
    public function deleteQA(){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $parent = $this->input->post('parent');
        $current_question_id = $this->input->post('current_question_id');
        $childs_question_ids = $this->input->post('childs_question_ids');
        if(!$childs_question_ids){
            $childs_question_ids = array();
        }
        $this->CM->deleteQA($parent, $current_question_id, $childs_question_ids);
    }
    
    public function getQA(){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $id = $this->input->post('id');
        $ques = $this->CM->getQA($id);
        echo json_encode($ques);
    }
    
    private function sendEmail ($subject, $body, $to){
        $this->load->library('email');
        // Also, for getting full html you may use the following internal method:
        //$body = $this->email->full_html($subject, $message);

        $result = $this->email
            ->from('optimal.bot.service@gmail.com', 'Optimal AI Support')
            ->to($to)
            ->subject($subject)
            ->message($body)
            ->send();
    }
    
    public function sendBotScriptEmail(){
        $this->load->model('subscribeFormMod');
        $trained = $this->CM->checkIfTrainedFirstTime($this->session->userdata('assis_companyid'));
        if(!$trained){
            $company = $this->subscribeFormMod->getCompanyById($this->session->userdata('assis_companyid'));
            $client = $this->subscribeFormMod->getClientById($this->session->userdata('assis_customerid'));
            $info = array("username" => $client->name, 'token' => $company->token);
            $data = $this->load->view('emailTemplates/bot_script', $info, TRUE);
            $this->sendEmail('Optimal Bot Deployment', $data, $client->email);
            $this->CM->finishedTrainingFirstTime($this->session->userdata('assis_companyid'));
        }
        redirect('customer');
    } 

}
