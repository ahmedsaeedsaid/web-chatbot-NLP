<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class profileMod extends CI_Model {

    public function loadInfo($id) {
        $query = $this->db->get_where('users', array('id' => $id));
        if ($query->num_rows() == 1) {
            $user = $query->row();
            return $user;
        }
    }

    public function loadUsers(){
        $this->db->select('*');
        $this->db->from('users');
        $query = $this->db->get();

        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return false;
        }
    }

    public function updateInfo($id , $data){

    	$this->db->where('id' , $id);
    	$this->db->update('users' , $data);

    }

    public function sethiddeninfo($data){
        $this->db->insert('profile_info_privacy' , $data);
    }

    public function updatehiddeninfo($id , $data){
        $this->db->where('userid' , $id);
        $this->db->update('profile_info_privacy' , $data);
    }

    public function loadhiddeninfo($id){
        $query = $this->db->get_where('profile_info_privacy' , array('userid' => $id));
        if($query->num_rows() > 0){
            $hidden = $query->row();
            return $hidden;
        }
    }

    public function username_exists($username){
        $query = $this->db->get_where('users', array('username' => $username));
        if($query->num_rows() > 0)
            return true;
        else
            return false;
    }

    /****************************** Professor Subjects ***************************/

    public function loadSubjects($id , $table){

        if($table == 'prof_subjects')
            $query = $this->db->get_where($table, array('profID' => $id));
        elseif ($table == 'inst_subjects')
            $query = $this->db->get_where($table, array('InstID' => $id));
        

        if($query->num_rows() > 0){
            $subs = $query->row();
            return $subs;
        }
    }

    public function addSubjects($data , $table){
        $this->db->insert($table , $data);
    }

    public function updateSubjects($profid , $data , $table){

        if($table == 'prof_subjects')
            $this->db->where('profID' , $profid);
        elseif ($table == 'inst_subjects')
            $this->db->where('InstID' , $profid);
        $this->db->update($table , $data);
    }
    
    public function checkMember($usrid){
        $this->db->select('*');
        $this->db->from('geeks_members');
        $this->db->where("userid = $usrid");
        $query = $this->db->get();
        if($query->num_rows() > 0){
            return true;
        }else{
            return false;
        }
    }

}
