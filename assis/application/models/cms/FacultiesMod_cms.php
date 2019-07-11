<?php

class FacultiesMod_cms extends CI_Model {

    public function getAllFaculties() {
        $this->db->select('faculties.* , universities.name as uniName');

        $this->db->from('faculties');

        $this->db->join('universities', 'universities.id = faculties.UniversityID');

        $query = $this->db->get();

        if ($query) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function getAllUniversities() {
        $this->db->select('*');

        $this->db->from('universities');

        $query = $this->db->get();

        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function add_faculty($data){
        $this->db->insert('faculties', $data);
    }

    public function toggleActive($active, $id) {
        $this->db->where('id', $id);
        $this->db->update('faculties', array("active" => $active));
    }

    public function deleteFaculty($id) {
        $this->db->delete('faculties', array('id' => $id));
    }

}
