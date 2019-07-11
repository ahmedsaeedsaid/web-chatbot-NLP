<?php

/* * To change this license header, choose License Headers in Project Properties. * To change this template file, choose Tools | Templates * and open the template in the editor. 
 */

class EvaluationMod extends CI_Model {


    public function getTopMembers() {

        $this->db->select('points , userid , position , committee');
        $this->db->from('geeks_members');
        $this->db->order_by('points', 'desc');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $score = $query->result();
            $sc = $query->result_array();

            $names = array();
            $committee = array();

            foreach ($score as $id) {
                $userid = $id->userid;
                $this->db->select('name , img , username , faculty');
                $this->db->where('id', $userid);
                $this->db->from('users');
                $query1 = $this->db->get();
                array_push($names, $query1->result_array());

                $commID = $id->committee;
                $this->db->select('name , acronym');
                $this->db->where('id', $commID);
                $this->db->from('geeks_committees');
                $query2 = $this->db->get();
                array_push($committee, $query2->result_array());

            }
        }

        $name = array();
        $img = array();
        $username = array();
        $faculty = array();
        $committeesName = array();
        $committeesAcronym = array();

        for ($i = 0; $i < sizeof($names); $i++) {
            $name[$i] = $names[$i][0]['name'];
            $img[$i] = $names[$i][0]['img'];
            $username[$i] = $names[$i][0]['username'];
            $faculty[$i] = $names[$i][0]['faculty'];
            $committeesName[$i] = $committee[$i][0]['name'];
            $committeesAcronym[$i] = $committee[$i][0]['acronym'];
        }

        $scor = array();
        $position = array();
        $id = array();
        $commID = array();
        for ($i = 0; $i < sizeof($sc); $i++) {
            $scor[$i] = $sc[$i]['points'];
            $position[$i] = $sc[$i]['position'];
            $id[$i] = $sc[$i]['userid'];
            $commID[$i] = $sc[$i]['committee'];
        }

        $all = array();
        $j = 0;
        for ($i = 0; $i < sizeof($sc); $i++) {
            $all[$j] = $name[$i];
            $all[$j + 1] = $scor[$i];
            $all[$j + 2] = $img[$i];
            $all[$j + 3] = $username[$i];
            $all[$j + 4] = $position[$i];
            $all[$j + 5] = $faculty[$i];
            $all[$j + 6] = $committeesName[$i];
            $all[$j + 7] = $committeesAcronym[$i];
            $all[$j + 8] = $id[$i];
            $all[$j + 9] = $commID[$i];
            $j += 10;
        }

        return $all;
    }

    public function getCommittees(){
        $this->db->select('id , name , acronym');
        $this->db->from('geeks_committees');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $committ = $query->result_array();
            return $committ;
        }

    }


}
