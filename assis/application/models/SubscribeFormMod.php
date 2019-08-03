<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class subscribeFormMod extends CI_Model {

    public function loadPlatforms() {
        $query = $this->db->get('platform');
        $platforms=array();
        $i=0;
        foreach ($query->result() as $row)
        {
            $platforms[]=$row;
        }
        return $platforms;
    }

    public function loadWebsiteTypes() {
        $query = $this->db->get('website_type');
        $website_types=array();
        $i=0;
        foreach ($query->result() as $row)
        {
            $website_types[]=$row;
        }
        return $website_types;
    }

    public function getSubscription($indicator) {
        $query = $this->db->get_where('payment_det', array('indicator' => $indicator));
        if($query->num_rows() > 0){
            $payment = $query->row();
            return $payment->subscription_id;
        }else{
            return '';
        }
        
    }

    public function addClient($data_client) {
        $this->db->insert('client', $data_client);
        return $this->db->insert_id();
    }

    public function addCompany($data_company) {
        $this->db->insert('company', $data_company);
        return $this->db->insert_id();
    }

    public function addSubscription($data_subscriptions) {
        $this->db->insert('subscriptions', $data_subscriptions);
        return $this->db->insert_id();
    }

    public function addPayment($data_payment) {
        $this->db->insert('payment_det', $data_payment);
        return $this->db->insert_id();
    }

    public function UpdateSubscripe($subscripe_data,$id) {
        $this->db->where('id',$id);
        $this->db->update('subscriptions', $subscripe_data);
    }

    public function getLastComapnyId() {
        $this->db->select('id');
        $this->db->from('company');
        $this->db->order_by('id', 'desc');
        $result = $this->db->get();
        if($result->row()){
            return $result->row()->id;
        }
        return 0;
    }
    
    public function getCompanyByBotName ($bot_name) {
        $this->db->select('*');
        $this->db->from('company');
        $this->db->where('bot_name', $bot_name);
        $result = $this->db->get();
        return $result->row();
    }
    
    public function getCompanyById ($id) {
        $this->db->select('*');
        $this->db->from('company');
        $this->db->where('id', $id);
        $result = $this->db->get();
        return $result->row();
    }
    
    public function getClientById ($id) {
        $this->db->select('*');
        $this->db->from('client');
        $this->db->where('id', $id);
        $result = $this->db->get();
        return $result->row();
    }

    

}
