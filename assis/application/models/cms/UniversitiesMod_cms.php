<?php

class UniversitiesMod_cms extends CI_Model {

    public function getAllUniversities() {
        $this->db->select('*');

        $this->db->from('universities');

        $query = $this->db->get();

        if ($query) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function add_university($data){
        $this->db->insert('universities', $data);
    }

    public function toggleActive($active, $id) {
        $this->db->where('id', $id);
        $this->db->update('universities', array("active" => $active));
    }

    public function deleteUniversity($id) {
        $this->db->delete('universities', array('id' => $id));
    }

}
