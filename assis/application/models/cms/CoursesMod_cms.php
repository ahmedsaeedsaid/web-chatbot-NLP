<?php

class CoursesMod_cms extends CI_Model {

    public function getfacultiesinfo() {
        // SELECT `id`, `name`, `number_of_levels`, `active` FROM `faculties` WHERE 1

        $types = $this->courses_type();

        $this->db->select('*');

        $this->db->from('faculties');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $i = 0;
            foreach ($query->result() as $row) {
                $data['id'][$i] = $row->id;
                $data['name'][$i] = $row->name;
                $data['level'][$i] = $row->number_of_levels;

                $i++;
            }
        }

        $i = 0;
        foreach ($types as $t) {
            $data['c_id'][$i] = $t->id;
            $data['c_type'][$i] = $t->type;
            $i++;
        }
        

        if (isset($data)) {
            return $data;
        } else {
            return null;
        }
    }

    public function getfacultyid($Fname) {

        $this->db->where('name', $Fname);

        $this->db->select('id');

        $this->db->from('faculties');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                return $row->id;
            }
        }
        return null;
    }

    /*
      SELECT `id`, `name`, `number_of_levels`, `active` FROM `faculties` WHERE 1

      SELECT `c_name`, `c_id`, `c_picture`, `c_description`, `users_rates`, `rate`, `type`, `facultyID`, `faculty_level` FROM `courses` WHERE 1

      $Cname => Course name  => FCIH_OS
      $Fname => faculties name

     */

    public function courseisExiste($Cname, $Fname) {
        $id = $this->getfacultyid($Fname);

        $this->db->where('facultyID', $id);

        $this->db->where('c_name', $Fname . '_' . $Cname);

        $this->db->select('c_id');

        $this->db->from('courses');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function addCourse($data) {
        $this->db->insert('courses', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    public function courses_type(){
        $this->db->select('*');
        $this->db->from('courses_types');
        $query = $this->db->get();

        if($query->num_rows() > 0){
            return $query->result();
        }else{
            return false;
        }
    }

    public function getAllCourses() {
        $this->db->select('courses.*, faculties.name'); // , courses_types.type

        $this->db->from('courses');

        $this->db->join('faculties', 'faculties.id = courses.facultyID');

        //$this->db->join('courses_types', 'courses_types.id = courses.type');

        $query = $this->db->get();

        if ($query) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function toggleActive($active, $id) {
        $this->db->where('c_id', $id);
        $this->db->update('courses', array("active" => $active));
    }

    public function deleteCourse($id) {
        $this->db->delete('courses', array('c_id' => $id));
    }

    public function edit_course($id , $courseArray){
        $this->db->where('c_id',$id);
        $this->db->update('courses',$courseArray);
        
    }
    
    public function toggleActiveQuestion($active, $id) {
        $this->db->where('id', $id);
        $this->db->update('mcq_question', array("active" => $active));
    }
    
    public function deleteQuestion($id) {
        $this->db->delete('mcq_question', array('id' => $id));
    }

    public function mcq_type(){
        $this->db->select('*');
        $this->db->from('mcq_question_type');
        $this->db->where('active = 1');
        $query = $this->db->get();

        if($query->num_rows() > 0){
            return $query->result();
        }else{
            return false;
        }
    }

    public function add_questions($data){
        $this->db->insert('mcq_question',$data);
        $id = $this->db->insert_id();
        return $id;
    }

    public function add_answers($data){
        $this->db->insert('mcq_choices',$data);
    }
    
    public function course_questions($id){
        $this->db->select('*');
        $this->db->where('course_id',$id);
        $this->db->from('mcq_question');
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return false;
        }
    }
    
    public function questions_answers(){
        $this->db->select('*');
        $this->db->from('mcq_choices');
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return false;
        }
    }
    
    public function edit_question($id , $questionArray , $answerArray){
        $this->db->where('id',$id);
        $this->db->update('mcq_question',$questionArray);
        
        $this->db->where('q_id',$id);
        $this->db->update('mcq_choices',$answerArray);
    }
    
    public function course_Q_and_A($id){
        $this->db->select('*');
        $this->db->where('courseID',$id);
        $this->db->from('questions_answers');
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return false;
        }
    }
    
    public function toggleActiveQ_A($active, $id) {
        $this->db->where('id', $id);
        $this->db->update('questions_answers', array("active" => $active));
    }
    
    public function deleteQ_A($id) {
        $this->db->delete('questions_answers', array('id' => $id));
    }
    
    public function edit_Q_A($id , $questionArray){
        $this->db->where('id',$id);
        $this->db->update('questions_answers',$questionArray);
        
    }
    
    public function question_type(){
        $this->db->select('*');
        $this->db->from('questions_answers_types');
        $this->db->where('active = 1');
        $query = $this->db->get();

        if($query->num_rows() > 0){
            return $query->result();
        }else{
            return false;
        }
    }
    
    public function code_type(){
        $this->db->select('*');
        $this->db->from('questions_answers_code');
        $query = $this->db->get();

        if($query->num_rows() > 0){
            return $query->result();
        }else{
            return false;
        }
    }
    
    public function add_Q_A($data){
        $this->db->insert('questions_answers',$data);
    }

}
