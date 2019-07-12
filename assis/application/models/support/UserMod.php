<?php

class UserMod extends CI_Model {

    public function getAllUsers() {
        $this->db->select('*');

        $this->db->from('users');

        $query = $this->db->get();

        if ($query) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function toggleActive($active, $id) {
        $this->db->where('id', $id);
        $this->db->update('users', array("active" => $active));
    }

    public function deleteUser($id) {
        $this->db->delete('users', array('id' => $id));
    }

}
