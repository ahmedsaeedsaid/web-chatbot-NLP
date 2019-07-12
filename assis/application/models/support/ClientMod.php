<?php

class ClientMod extends CI_Model {

    public function getAllClients() {
        $this->db->select('client.name as cname, client.email as cemail, company.*, platform.name as pname, website_type.name as wname');

        $this->db->from('client');
        
        $this->db->join('company', 'client.id=company.client_id');
        
        $this->db->join('platform', 'company.platform_id=platform.id');
        
        $this->db->join('website_type', 'company.type_id=website_type.id');

        $query = $this->db->get();

        if ($query) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function getClientStats() {
        $data = array();
        
        $this->db->select('count(*) as pending_count');

        $this->db->from('company');
        
        $this->db->where('status', 'Pending');

        $query = $this->db->get();
        
        $data['pending_count'] = $query->row()->pending_count;
        
        
        $this->db->select('count(*) as support_count');

        $this->db->from('company');
        
        $this->db->where('status', 'Support');

        $query = $this->db->get();
        
        $data['support_count'] = $query->row()->support_count;

        if ($data) {
            return $data;
        } else {
            return false;
        }
    }

    public function toggleActive($active, $id) {
        $this->db->where('id', $id);
        $this->db->update('company', array("active" => $active));
    }

    public function deleteClient($id) {
        $this->db->delete('company', array('client_id' => $id));
        $this->db->delete('client', array('id' => $id));
    }

}
