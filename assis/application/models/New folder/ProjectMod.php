<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProjectMod extends CI_Model {

    function insert_data($data) {
        $this->db->insert("projects", $data);
    }

    function delete_data($id) {
        $this->db->where("id", $id);
        $this->db->delete("projects");
    }

}
