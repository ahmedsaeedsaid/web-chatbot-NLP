<?php

class UsersMod extends CI_Model {

    function getUData() {
        $this->db->select('id,name');
        $this->db->from('users');
        return $this->db->get();
    }
    function getUserByID($user_id){
        $this->db->select('*');
        $this->db->where('id',$user_id);
        $this->db->from('users');
        return $this->db->get();    
    }

}

