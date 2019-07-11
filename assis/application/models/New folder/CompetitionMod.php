<?php

/* * To change this license header, choose License Headers in Project Properties. * To change this template file, choose Tools | Templates * and open the template in the editor. 
 */

class CompetitionMod extends CI_Model {


    public function selectedType($selected, $facultyID) {
        if ($selected == 'lvl1')
            $query = $this->db->get_where('courses', array('faculty_level' => 1 , 'facultyID' => $facultyID));
        else if ($selected == 'lvl2')
            $query = $this->db->get_where('courses', array('faculty_level' => 2 , 'facultyID' => $facultyID));
        else if ($selected == 'lvl3')
            $query = $this->db->get_where('courses', array('faculty_level' => 3 , 'facultyID' => $facultyID));
        else if ($selected == 'lvl4')
            $query = $this->db->get_where('courses', array('faculty_level' => 4 , 'facultyID' => $facultyID));
        else if ($selected == 'registered') {
            $course = $this->db->get_where('users_courses', array('courses_users_ID' => $this->session->userdata('userid')));

            if ($course->num_rows() > 0) {
                $cours = $course->result_array();
                $c_info = array();
                for ($i = 0; $i < sizeof($cours); $i++) {
                    $c_info[$i] = $cours[$i]['users_courses_ID'];
                }
                $this->db->select('*');
                $this->db->where_in('c_id', $c_info);
                $this->db->from('courses');
                $query = $this->db->get();
            }else{
                return false;
            }

            
        } else if ($selected == 'all')
            $query = $this->db->get('courses');
        else
            $query = $this->db->get('courses');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }


    public function getQuestion($offset, $cid, $lecid) {
        $this->db->select('mcq_question.question, mcq_question.id, mcq_question.img, mcq_choices.ch1, mcq_choices.ch2, mcq_choices.ch3, mcq_choices.ch4, mcq_choices.answer, mcq_question_type.qt_id');
        $this->db->from('mcq_question');
        $this->db->join('mcq_choices', 'mcq_question.id = mcq_choices.q_id');
        $this->db->join('mcq_question_type', 'mcq_question_type.qt_id = mcq_question.question_type');
        $this->db->where("course_id = $cid and lectNum = $lecid and mcq_question.active = 1");
        if ($offset) {
            $this->db->limit(1, $offset);
        } else {
            $this->db->limit(1);
        }
        $query = $this->db->get();
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function numOfQues($cid, $lecid) {
        $this->db->where("course_id = $cid and lectNum = $lecid and active = 1");
        $this->db->from('mcq_question');
        return $this->db->count_all_results();
    }

    public function getCompetitons($facultyID, $faculty_level) {
        $this->db->select('*');
        $this->db->from('competitons');
        $query = $this->db->get();
        $compts = $query->result();
        $courses = array();
        foreach ($compts as $compt) {
            $crid = $compt->crid;
            $this->db->select('*');
            $this->db->from('courses');
            $this->db->where("c_id = $crid and facultyID = $facultyID and faculty_level = $faculty_level");
            $query = $this->db->get();
            if ($query->row()) {
                array_push($courses, $query->row());
            }
        }
        if ($query) {
            return $courses;
        } else {
            return false;
        }
    }

    public function setScore($id, $score) {
        $this->db->where('userid', $id);
        $this->db->update('students', $score);
    }

    public function getScore($id) {
        $this->db->select('score');
        $this->db->from('students');
        $this->db->where('userid', $id);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $score = $query->row();
            return $score;
        }
    }

    public function getRank() {
        $this->db->select('*');
        $this->db->from('rank');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $rank = $query->result_array();
            return $rank;
        }
    }

    public function getUser($id) {

        $this->db->select('score');
        $this->db->where('userid', $id);
        $this->db->from('students');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $score = $query->result();
            $sc = $query->result_array();
            $names = array();

            $this->db->select('name , img , username');
            $this->db->where('id', $id);
            $this->db->from('users');
            $query1 = $this->db->get();
            array_push($names, $query1->result_array());
        }

        $name = array();
        for ($i = 0; $i < sizeof($names); $i++) {
            $name[$i] = $names[$i][0]['name'];
        }

        $scor = array();
        for ($i = 0; $i < sizeof($sc); $i++) {
            $scor[$i] = $sc[$i]['score'];
        }

        $img = array();
        for ($i = 0; $i < sizeof($names); $i++) {
            $img[$i] = $names[$i][0]['img'];
        }

        $username = array();
        for ($i = 0; $i < sizeof($names); $i++) {
            $username[$i] = $names[$i][0]['username'];
        }

        $all = array();
        $j = 0;
        for ($i = 0; $i < sizeof($sc); $i++) {
            $all[$j] = $name[$i];
            $all[$j + 1] = $scor[$i];
            $all[$j + 2] = $img[$i];
            $all[$j + 3] = $username[$i];
            $j += 4;
        }

        return $all;
    }


    public function getOwnRank($id){

        $this->db->select('score , userid');
        $this->db->from('students');
        $this->db->order_by('score', 'desc');


        $query = $this->db->get();

        if($query->num_rows() > 0){

            $data = $query->result();
            $i = 1;
            foreach ($data as $da) {
                $userid = $da->userid;

                if($userid == $id){
                    $rank = $i . '/' . $query->num_rows();
                    return $rank;
                }

                $i++;
            }

        }
    }


    public function getTop5() {

        $this->db->select('score , userid , level');
        $this->db->from('students');
        $this->db->order_by('score', 'desc');
        $this->db->limit(5);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $score = $query->result();
            $sc = $query->result_array();
            $names = array();
            foreach ($score as $id) {
                $userid = $id->userid;
                $this->db->select('users.name , users.img , users.username , faculties.name as faculty');
                $this->db->from('users');
                $this->db->join('faculties', 'faculties.id=users.facID');
                $this->db->where('users.id', $userid);
                $query1 = $this->db->get();
                array_push($names, $query1->result_array());
            }
        

            $name = array();
            $img = array();
            $username = array();
            $faculty = array();
            for ($i = 0; $i < sizeof($names); $i++) {
                $name[$i] = $names[$i][0]['name'];
                $img[$i] = $names[$i][0]['img'];
                $username[$i] = $names[$i][0]['username'];
                $faculty[$i] = $names[$i][0]['faculty'];
            }

            $scor = array();
            $level = array();
            $id = array();
            for ($i = 0; $i < sizeof($sc); $i++) {
                $scor[$i] = $sc[$i]['score'];
                $level[$i] = $sc[$i]['level'];
                $id[$i] = $sc[$i]['userid'];
            }

            $all = array();
            $j = 0;
            for ($i = 0; $i < sizeof($sc); $i++) {
                $all[$j] = $name[$i];
                $all[$j + 1] = $scor[$i];
                $all[$j + 2] = $img[$i];
                $all[$j + 3] = $username[$i];
                $all[$j + 4] = $level[$i];
                $all[$j + 5] = $faculty[$i];
                $all[$j + 6] = $id[$i];
                $j += 7;
            }
            return $all;
        }else{
            return false;
        }

    }

    public function getUserInfo($usid, $crid) {
        $this->db->select('solved_phases');
        $this->db->from('user_competitions');
        $this->db->where("user_id = $usid AND cr_id = $crid");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result()[0]->solved_phases;
        } else {
            $data = array(
                'user_id' => $usid,
                'cr_id' => $crid,
                'solved_phases' => 0
            );
            $this->db->insert('user_competitions', $data);
            return 0;
        }
    }

    public function UpdateSolvedPhases($crid, $usid, $curr_phase) {
        $data = array(
            'solved_phases' => $curr_phase
        );
        $this->db->where("user_id = $usid AND cr_id = $crid");
        $this->db->update('user_competitions', $data);
    }

    public function getAllQuestions($crid, $lecid) {
        $this->db->select('*');
        $this->db->from('mcq_question');
        $this->db->where("course_id = $crid AND lectNum = $lecid AND active = 1");
        $query = $this->db->get();
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function deleteCompetitonQues($id) {
        $data = array(
            'active' => 0
        );
        $this->db->where("id", $id);
        $this->db->update('mcq_question', $data);
    }

    public function updateCompetitonQues($id, $ques) {
        $data = array(
            'question' => $ques
        );
        $this->db->where("id", $id);
        $this->db->update('mcq_question', $data);
    }

    public function getAllFaculties() {
        $this->db->select('*');
        $this->db->from('faculties');
        $query = $this->db->get();
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }
    
    public function getUserFacultyAndLevel($userID){
        $this->db->select('users.facID, students.level');
        $this->db->from('users');
        $this->db->join('students', 'users.id=students.userid');
        $query = $this->db->get();
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }
    
    public function getAlluniversities() {
        $this->db->select('*');
        $this->db->from('universities');
        $query = $this->db->get();
        if ($query) {
            return $query->result();
        } else {
            return false;
        }
    }

}
