<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$url = rtrim(__DIR__, 'controllers');
require_once $url . 'classes/user.php';

class Subscribe extends CI_Controller {

    private $data;
    private $db_drivers;

    function __construct() {
        parent::__construct();
        // loading models
        $this->load->model('subscribeFormMod');
        $this->data = NULL;
        $this->db_drivers = array('mysqli');
    }
    
    private function testConnection($drivers, $config){
        $driver = '';
        if($drivers){
            $driver = $drivers[0];
            $config['dbdriver'] = $driver;
            $db = @$this->load->database($config, TRUE);
            if($db->conn_id) {
                return $driver;
            } else {
                array_shift($drivers);
                $driver = $this->testConnection($drivers, $config);
            }
        }
        return $driver;
    }

    public function index() {
        $data['title'] = 'Subscribe ChatBot';
        $data['package_id'] = '1';
        $data['platforms'] = $this->subscribeFormMod->loadPlatforms();
        $data['websiteTypes'] = $this->subscribeFormMod->loadWebsiteTypes();

        $this->load->view('header', $data);
        $this->load->view('subscribeForm', $data);
        $this->load->view('footer');
    }
    
    public function submitSubscription()
    {
        $config = array(
            "hostname" => $this->input->post('server'),
            "username" => $this->input->post('username'),
            "password" => $this->input->post('password'),
            "database" => $this->input->post('DB_name'),
            "dbdriver" => "",
            "db_debug" => false
        );
        $db_driver = $this->testConnection($this->db_drivers, $config);
        if($db_driver == ''){
            $this->session->set_flashdata('db_connection', 'failed');
            redirect(base_url('subscribe'));
        }
        
        $data_client = array(
            'name' => $this->input->post('name'),
            'email'  => $this->input->post('email'),
            'phone'  => $this->input->post('phone')
        );
        
        $insert_id = $this->subscribeFormMod->addClient($data_client);
        $need_support = 0;
        // Platform is native
        if($this->input->post('platform') == 5){
            $need_support = 1;
        }
        
        // The next id for company
        $new_company_id = $this->subscribeFormMod->getLastComapnyId() + 1;
        
        $token = password_hash($insert_id . $this->input->post('company') . $this->input->post('bot_name') , PASSWORD_DEFAULT);
        //$this->load->library('encrypt');
        // Encrypt bot name
        //$bot_name = $this->encrypt->encode($this->input->post('bot_name') . "_" . $new_company_id);
        $bot_name = $this->input->post('bot_name') . "_" . $new_company_id;
        $data_company= array(
            'client_id' => $insert_id,
            'name'  => $this->input->post('company'),
            'db_server'  => $this->input->post('server'),
            'db_name'  => $this->input->post('DB_name'),
            'db_username'  => $this->input->post('username'),
            'db_password'  => $this->input->post('password'),
            'db_driver'  => $db_driver,
            'platform_id'  => $this->input->post('platform'),
            'domain'  => $this->input->post('domain'),
            'type_id'  => $this->input->post('website_type'),
            'support'  => $need_support,
            'bot_name'  => $bot_name,
            'token'  => $token,
            'status'  => 'pending'
        );
        
        $this->subscribeFormMod->addCompany($data_company);
        
        $data_subscriptions= array(
            'client_id' => $insert_id,
            'package_id'  => $this->input->post('package_id'),
            'from_date'  => date("m/d/Y", time()),
            'payment_id'  => '',
            'payment_status'  => 'pending',
            'status'  => 'pending'
        ); 
    
        $insert_id = $this->subscribeFormMod->addSubscription($data_subscriptions);
        $this->sendFirstStepEmail($bot_name, $data_client['email'], $data_client['name']);
        $data['order_id']=$insert_id;
        $data['description_order']='Bot chat';
        $data['price']=50;
        $data['currency']='USD';
        $this->load->view('paymentGetaway', $data);
    }
    
    public function validateDomain() {
        $url_http = 'http://'. $_POST['domain'].'/';
        $url_https= 'https://'. $_POST['domain'].'/';
        $ch = curl_init($url_http);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch); 
        if($httpcode>=200 && $httpcode<300){
            echo 'yes';
        } else {
            $ch = curl_init($url_https);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch); 
            if($httpcode>=200 && $httpcode<300){
                echo 'yes';
            }else{
                echo 'no';
            }
        }
    }

    public function successOrder()
    {
        if (isset($_POST['transaction_id']) && isset($_POST['status']) && isset($_POST['order_id']))
        {
            if($_POST['status']=='success')
            {
                $this->session->set_flashdata('payment_status', 'success');

                    $subscripe_data = array(
                        'payment_status' => 'success'
                    );
                
                    $this->subscribeFormMod->UpdateSubscripe($subscripe_data,$_POST['order_id']);
            }
            else
            {
                $this->session->set_flashdata('payment_status', 'failed');

                    $subscripe_data = array(
                        'payment_status' => 'failed'
                    );
                
                    $this->subscribeFormMod->UpdateSubscripe($subscripe_data,$_POST['order_id']);
            }
        }
        redirect(base_url());
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
    
    public function sendFirstStepEmail($bot_name, $email, $username){
        $info = array("bot_name" => $bot_name, "username" => $username, "email" => $email);
        $data = $this->load->view('emailTemplates/db_verification', $info, TRUE);
        $this->sendEmail('Welcome to Optimal Bot', $data, $email);
    } 
    
    public function downloadScript($bot_name){
        $this->load->helper('download');
        $bot_name = $this->input->post('bot_name');
        $company = $this->subscribeFormMod->getCompanyByBotName($bot_name);
        $info = array('company' => $company);
        $data = $this->load->view('verificationScripts/db_verification', $info, TRUE);
        $name = 'db_verification.php';
        force_download($name, $data);
    }

}
