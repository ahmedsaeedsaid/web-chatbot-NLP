<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$url = rtrim(__DIR__, 'controllers');
require_once $url . 'classes/user.php';

class Subscribe extends CI_Controller {

    private $data = NULL;

    function __construct() {
        parent::__construct();
        // check if user has access permission
        $this->load->library('authentication');
        // loading models
        $this->load->model('subscribeFormMod');
    }

    public function index() {
        // check loggedIn
        
        $data['title'] = 'Subscribe ChatBot';
        $data['package_id'] = '1';
        $data['platforms'] = $this->subscribeFormMod->loadPlatforms();
        $data['websiteTypes'] = $this->subscribeFormMod->loadWebsiteTypes();
        $this->load->view('subscribeForm', $data);
    }
    public function submitSubscription()
    {
        $data_client = array(
            'name' => $this->input->post('name'),
            'email'  => $this->input->post('email'),
            'phone'  => $this->input->post('phone')
        );
        $this->db->insert('client', $data_client);
        $insert_id = $this->db->insert_id();

        $data_company= array(
            'client_id' => $insert_id,
            'name'  => $this->input->post('company'),
            'db_server'  => $this->input->post('server'),
            'db_name'  => $this->input->post('DB_name'),
            'db_username'  => $this->input->post('username'),
            'db_password'  => $this->input->post('password'),
            'platform_id'  => $this->input->post('platform'),
            'domain'  => $this->input->post('domain'),
            'type_id'  => $this->input->post('website_type'),
            'status'  => 'pending',

        );
        $this->db->insert('company', $data_company);
        $data_subscriptions= array(
            'client_id' => $insert_id,
            'package_id'  => $this->input->post('package_id'),
            'from_date'  => date("m/d/Y", time()),
            'payment_id'  => '',
            'payment_status'  => 'pending',
            'status'  => 'pending',

        );
    
        return $this->db->insert('subscriptions', $data_subscriptions);
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

    

}
