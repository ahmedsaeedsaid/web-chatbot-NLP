<?php

class UsersMod_cms extends CI_Model {

    public function getAllUsers() {
        $this->db->select('users.* , faculties.name as facName , universities.name as uniName');

        $this->db->from('users');

        $this->db->join('faculties', 'faculties.id = users.facID');

        $this->db->join('universities', 'universities.id = users.UniversityID');

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
