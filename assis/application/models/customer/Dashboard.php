<?php

class Dashboard extends CI_Model {
    
    public function getChatStartEndTime() {
        $this->db->select('*');
        $this->db->from('company_users');
        $this->db->where('companyId', $this->session->userdata('assis_companyid'));
        $res = $this->db->get();
        $users = $res->result_array();
        $logs = array();
        foreach($users as $user){
            $yesterday = date('d', strtotime("-1 days"));
            $lastweekday = date('d', strtotime("-7 days"));
            $this->db->select('*');
            $this->db->from('logs');
            $this->db->where('company_userId', $user['id']);
            $this->db->where('DAY(insertdatetime) <=', $yesterday);
            $this->db->where('DAY(insertdatetime) >', $lastweekday);
            $this->db->order_by('insertdatetime', 'asc');
            $res = $this->db->get();
            $chat_log = $res->result_array();
            $logs [] = $chat_log;
        }
        return $logs;
    }
}
