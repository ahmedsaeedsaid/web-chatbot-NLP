<?php

class DepartmentsMod_cms extends CI_Model {

    public function getAllDepartments() {
        $this->db->select('departments.*, faculties.name as f_name, universities.name as u_name');

        $this->db->from('departments');

        $this->db->join('faculties', 'faculties.id = departments.faculty_id');

        $this->db->join('universities', 'universities.id = departments.university_id');


        $query = $this->db->get();

        if ($query) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function add_department($data){
        $this->db->insert('departments', $data);
    }

    public function toggleActive($active, $id) {
        $this->db->where('id', $id);
        $this->db->update('departments', array("active" => $active));
    }

    public function deleteDepartment($id) {
        $this->db->delete('departments', array('id' => $id));
    }

}
