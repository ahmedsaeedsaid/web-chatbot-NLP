<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class coursesMod extends CI_Model {

    public function loadcourses($offset, $search_st) {
        $this->db->select("*");
        $this->db->from('courses');
        $this->db->where('active=1');
        $this->db->limit(12, $offset);
        if ($search_st) {
            $search_st = urldecode($search_st);
            $this->db->like('c_name', $search_st);
        }
        $query = $this->db->get();
        if ($query) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function facultyCourses() {
        $this->db->select('*');
        $this->db->where(array('type' => 1, 'active' => 1));
        $this->db->from('courses');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function getLectNum($crid) {
        $this->db->distinct();
        $this->db->select('lectNum');
        $this->db->where("course_id = $crid");
        $this->db->order_by("lectNum", "asc");
        $query = $this->db->get('mcq_question');

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    /* public function SearchCourse($offset, $search_st) {
      $search_st = urldecode($search_st);
      $this->db->select('*');
      $this->db->from('courses');
      if ($offset) {
      $this->db->limit(12);
      } else {
      $this->db->limit(12, $offset);
      }
      $this->db->like('c_name', $search_st);
      $query = $this->db->get();
      if ($query) {
      return $query->result_array();
      } else {
      return false;
      }
      } */

    public function numofcourses($search_st, $type, $offset) {
        $num = "";
        if ($search_st) {
            $search_st = urldecode($search_st);
            $this->db->like('c_name', $search_st);
            $this->db->from('courses');
            $num = $this->db->count_all_results();
        } else {
            $num = $this->selectedType($type, $offset, 'count');
        }
        return $num;
    }

    public function getcourse($courseid) {
        $query = $this->db->get_where('courses', array('c_id' => $courseid, 'active' => 1));
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function haveCourses($id){
        $query = $this->db->get_where('users_courses', array('courses_users_ID' => $id));
        if($query->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

    public function selectedType($selected, $offset, $mode='normal') {
        if($mode != 'count'){
            $this->db->limit(12, $offset);
        }
        if ($selected == 'faculty') {
            $query = $this->db->get_where('courses', array('type' => 1));
        }
        else if ($selected == 'da7i7a') {
            $query = $this->db->get_where('courses', array('type' => 2));
        }
        else if ($selected == 'external') {
            $query = $this->db->get_where('courses', array('type' => 3));
        }
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
            } else {
                return false;
            }
        } else {
            $query = $this->db->get('courses');
        }

        if ($query->num_rows() > 0) {
            if($mode == 'count'){
                return $query->num_rows();
            }
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function numofselected($search_st, $selected) {
        $num = "";
        if ($selected == 'faculty') {
            if ($search_st) {
                $search_st = urldecode($search_st);
                $this->db->like('c_name', $search_st);
                $this->db->from('courses');
                $this->db->where('type', 1);
                $num = $this->db->count_all_results();
            } else {
                $this->db->where('type', 1);
                $num = $this->db->count_all_results('courses');
            }
        } else if ($selected == 'da7i7a') {
            if ($search_st) {
                $search_st = urldecode($search_st);
                $this->db->like('c_name', $search_st);
                $this->db->from('courses');
                $this->db->where('type', 2);
                $num = $this->db->count_all_results();
            } else {
                $this->db->where('type', 2);
                $num = $this->db->count_all_results('courses');
            }
        } else if ($selected == 'external') {
            if ($search_st) {
                $search_st = urldecode($search_st);
                $this->db->like('c_name', $search_st);
                $this->db->from('courses');
                $this->db->where('type', 3);
                $num = $this->db->count_all_results();
            } else {
                $this->db->where('type', 3);
                $num = $this->db->count_all_results('courses');
            }
        }
        return $num;
    }

    public function coursesNames($facultyID) {
        $this->db->where('facultyID', $facultyID);
        $this->db->select('c_name,c_id,c_picture');
        $this->db->from('courses');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getCourseData() {
        $this->db->select('c_name,c_id');
        $this->db->from('courses');
        $query = $this->db->get();
        return $query->result();
    }

    /* ------------------------------------- Comments ------------------------------------ */

    public function comments($courseid, $offset = 0) {
        $this->db->limit(5,$offset);
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get_where('courses_comments', array('courseID' => $courseid));

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function numofcomments($courseid) {
        $num = 0;

        $this->db->where('courseID' , $courseid);
        $this->db->from('courses_comments');
        $num = $this->db->count_all_results();

        return $num;
    }

    public function addComment($data) {
        $this->db->insert('courses_comments', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    public function deleteComment($id) {
        $this->db->where("id", $id);
        $this->db->delete('courses_comments');

        $this->db->where("commentID", $id);
        $this->db->delete('comments_replys');
    }

    public function editComment($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('courses_comments', $data);
    }

    public function getComment($id) {
        $this->db->select('comment');
        $this->db->where('id', $id);
        $this->db->from('courses_comments');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function replys() {
        
        $this->db->select('*');
        $this->db->from('comments_replys');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function addReply($data) {
        $this->db->insert('comments_replys', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    public function deleteReply($id) {
        $this->db->where("id", $id);
        $this->db->delete('comments_replys');
    }

    public function editReply($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('comments_replys', $data);
    }

    public function getReply($id) {
        $this->db->select('reply');
        $this->db->where('id', $id);
        $this->db->from('comments_replys');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function countReplys() {
        $this->db->select('commentID , COUNT(commentID) as num');
        $this->db->group_by('commentID');
        $this->db->from('comments_replys');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    public function Register_course($courseID, $userID) {
        $data = array("users_courses_ID" => $courseID, "courses_users_ID" => $userID);
        $this->db->insert('users_courses', $data);
    }

    public function Unregister_course($courseID, $userID) {
        $this->db->where('users_courses_ID', $courseID);
        $this->db->where('courses_users_ID', $userID);
        $this->db->delete('users_courses');
    }

    public function isFacultyCourse($courseID) {
        $this->db->where('c_id', $courseID);
        $this->db->where('type', 1);
        $this->db->select('c_id');
        $this->db->from('courses');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isStudent($usrID) {
        $this->db->where('id', $usrID);
        $this->load->model('filesMod');
        //$usertypeid = $this->filesMod->getidusertype('professor');
        //$this->db->where('usertypeID',$usertypeid);
        $this->db->where('usertypeID', 1);
        $this->db->select('id');
        $this->db->from('users');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getcoursename($courseid) {
        $this->db->where('c_id', $courseid);
        $this->db->select('*');
        $this->db->from('courses');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $name = $row->c_name;
            }
        }
        if (isset($name)) {
            return $name;
        } else {
            return false;
        }
    }

    public function get_all_reg_course($courseid) {

        // SELECT `id`, `users_courses_ID`, `courses_users_ID` FROM `users_courses` WHERE 1
        $ids = [];
        $this->db->where('users_courses_ID', $courseid);
        $this->db->select('*');
        $this->db->from('users_courses');
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result() as $row) {
            $ids[$i] = $row->courses_users_ID;
            $i++;
        }
        return $ids;
    }

    public function getCommentpersonid($commentid) {
        $this->db->select('*');
        $this->db->where('id', $commentid);
        $this->db->from('courses_comments');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $data['userID'] = $row->userID;
                $data['courseID'] = $row->courseID;
                return $data;
            }
        } else {
            return false;
        }
    }

    public function get_all_user_id_reply_comment($commentid) {

        // SELECT `id`, `reply`, `commentID`, `userID` FROM `comments_replys` WHERE 1
        $this->db->where('commentID', $commentid);
        $this->db->select('userID');
        $this->db->from('comments_replys');
        $query = $this->db->get();
        $i = 0;
        $ids = [];
        foreach ($query->result() as $row) {
            if (in_array($row->userID, $ids)) {
                $ids[$i] = $row->userID;
                $i++;
            }
        }
        return $ids;
    }

    public function get_name_person($id) {
        $this->db->where('id', $id);
        $this->db->select('*');
        $this->db->from('users');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $name = $row->username;
                if ($name == "NULL")
                    $name = $row->name;
            }
        }
        if (isset($name)) {
            return $name;
        } else {
            return null;
        }
    }

    public function getallprofandinst($courseid) {

        // SELECT `profID`, `sub1_ID`, `sub1`, `sub2_ID`, `sub2`, `sub3_ID`, `sub3`, `active` FROM `prof_subjects` WHERE 1
        // SELECT `InstID`, `sub1_ID`, `sub1`, `sub2_ID`, `sub2`, `sub3_ID`, `sub3`, `active` FROM `inst_subjects` WHERE 1
        $table = array('prof_subjects', 'inst_subjects');
        $id = array('profID', 'InstID');
        $i = 0;
        $uid = [];
        for ($l = 0; $l < 2; $l++) {
            $this->db->where('sub1_ID', $courseid);
            $this->db->or_where('sub2_ID', $courseid);
            $this->db->or_where('sub3_ID', $courseid);
            $this->db->select('*');
            $this->db->from($table[$l]);
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                foreach ($query->result() as $row) {
                    $Uid[$i] = $row->{$id[$l]};
                    $i++;
                }
            }
        }
        return $uid;
    }

    public function addRate($rate, $userid, $crid) {
        // get current course info
        $this->db->select('*');
        $this->db->from('courses');
        $this->db->where("c_id=$crid");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $course = $query->result()[0];
        }
        // check if user has rated the course before
        $this->db->select('*');
        $this->db->from('users_rated');
        $this->db->where("usrID=$userid and crID=$crid");
        $query = $this->db->get();
        if ($query->num_rows() <= 0) {
            // insert record into users_rated
            $data = array(
                "usrID" => $userid,
                "crID" => $crid,
                "last_rate_value" => $rate
            );
            $this->db->insert('users_rated', $data);

            // update rate
            $data = array(
                "users_rates" => $course->users_rates + 1,
                "rate" => $course->rate + $rate
            );
            $this->db->where("c_id = $crid");
            $this->db->update('courses', $data);
        } else {
            // getting users rated result
            $user_rated = $query->result()[0];

            // update rate
            $data = array(
                "rate" => ($course->rate - $user_rated->last_rate_value) + $rate
            );
            $this->db->where("c_id = $crid");
            $this->db->update('courses', $data);

            // update last rate value
            $data = array(
                "last_rate_value" => $rate
            );
            $this->db->where("usrID=$userid and crID=$crid");
            $this->db->update('users_rated', $data);
        }
        return true;
    }

    public function getLastRate($usrid, $crid) {
        // get last rate value
        $this->db->select('*');
        $this->db->from('users_rated');
        $this->db->where("usrID=$usrid and crID=$crid");
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            // getting users rated result
            $user_rated = $query->result()[0];
            return $user_rated->last_rate_value;
        }
        return false;
    }

    public function checkCourseRegisteration($usrid, $crid, $usertype) {
        $this->db->select('*');
        if ($usertype == 1) {
            $this->db->from('users_courses');
            $this->db->where("users_courses_ID=$crid and courses_users_ID=$usrid");
        } else if ($usertype == 2) {
            $this->db->from('prof_subjects');
            $this->db->where("profID=$usrid and (sub1_ID=$crid or sub2_ID=$crid or sub3_ID=$crid)");
        } else {
            $this->db->from('inst_subjects');
            $this->db->where("InstID=$usrid and (sub1_ID=$crid or sub2_ID=$crid or sub3_ID=$crid)");
        }
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

}
