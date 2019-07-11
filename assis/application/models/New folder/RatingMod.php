<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ratingMod extends CI_Model {

    public function getuserRateInfo($id) {
        $this->db->select(array('users_rates', 'rate'));
        $this->db->from('courses');
        $this->db->where('c_id = ' . $id);
        $query = $this->db->get();
        return $query->result();
    }

}
