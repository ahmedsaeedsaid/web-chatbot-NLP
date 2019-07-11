<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class contactMod extends CI_Model {

    public function sendInfo($data){

    	$this->db->insert("contact_us",$data);

    }

}
