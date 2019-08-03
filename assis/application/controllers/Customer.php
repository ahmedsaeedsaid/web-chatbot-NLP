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
        $this->load->view('customer/common', $config);
        $this->load->view('customer/main', $this->data);
    }
    
    public function questions($scenario_id){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $config['title'] = 'Bot Question ChatBot';
        $data['scenario_id'] = $scenario_id;
        $this->load->view('customer/common', $config);
        $this->load->view('BotQuestionGetterForm', $data);
    }
    
    public function scenariosList(){
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
    }
    
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
        $data = array(
            "name" => $name,
            "companyId" => $this->session->userdata('assis_companyid')
        );
        $this->CM->addScenario($data);
        redirect('customer/scenariosList');
    }
    
    public function toggleScenarioActive(){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $active = $this->input->post('active');
        $id = $this->input->post('id');
        $this->CM->toggleActive($active, $id);
    }
    
    public function saveQASC(){
        // check loggedIn
        $this->authentication->IsLoggedInCustomer('any');
        $Questions_generated = $this->input->post('Questions_generated');
        $this->CM->saveQASC($Questions_generated);
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
