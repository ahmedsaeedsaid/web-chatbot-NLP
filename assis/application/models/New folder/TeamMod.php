<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class TeamMod extends CI_Model {

    public function insert_team($team_data) {
        if ($this->db->insert("team", $team_data)) {
            // return true;
            return $this->db->insert_id();
        } else {
            // return false;
            return -1;
        }
    }

    public function edit_team($team_id, $team_data) {
        $this->db->where("team_id", $team_id);
        if ($this->db->update("team", $team_data)) {
            return true;
        } else {
            return false;
        }
    }

    public function delete_team($team_id) {
        $this->db->where('team_id', $team_id);
        if ($this->db->delete('team')) {
            return true;
        } else {
            return false;
        }
    }

    public function fetshAllTeamsData($offset) {
        //echo "Ofset From fetch Fun Is ".$offset;
        $numOfTeams = $this->db->count_all('team');
        $this->db->select('*');
        $this->db->from('team');
        $this->db->limit(10, $offset);
        if ($offset == 0) {
            $this->db->limit(10, $offset);
        } else {
            if ($offset <= $numOfTeams) {
                $this->db->limit(10, $offset - 10);
            } else if ($offset > $numOfTeams) {
                $remain = $numOfTeams - ($offset - 10);
                $this->db->limit($remain, $offset - 10);
            }
        }
        $query = $this->db->get();

        if ($query) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    function numberOfTeams() {
        return $this->db->count_all('team');
    }

    function insertMemberIntoTeam($data) {
        if ($this->db->insert("team_members", $data)) {
            return true;
        } else {
            return false;
        }
    }

    function deleteMemberFormTeam($request_id) {
        $this->db->where('request_id', $request_id);
        if ($this->db->delete('teams_requests'))
            return true;
        else {
            return false;
        }
    }
    function getTeam($team_id){
        $this->db->select('*');
        $this->db->from('team');
        $this->db->where('team_id',$team_id);
       
        return $this->db->get();
    }
    function fetsh_teams_requests() {
        $this->db->select('*');
        $this->db->from('teams_requests');
        return $this->db->get();
    }
    function insertJoinRequest($data){
        if ($this->db->insert("teams_requests", $data)) {
            return true;
        } else {
            return false;
        }
    }


    
    public function getnumofteam($course_id)
    {

        $this->db->select('count(*) as num');
        if($course_id != -1){
                $this->db->where('course_id', $course_id);
            }
        $this->db->from('team');
        $query = $this->db->get();
        $i = 0 ; 
        foreach ($query->result() as $row) 
        {
            return $row->num;
        }
        return $i;
    }

    
    // public function showteams($offset, $course_id = -1 , $teamId = -1) {

    //     $this->load->model("notificationMod");
    //     $this->load->model("coursesMod");
    //     // $this->notificationMod->get_name_person()
    //     $data = [];
    //      // $numOfTeams = $this->db->count_all('team');
    //     // if ($course_id != -1) {
    //         $numOfTeams = $this->getnumofteam($course_id);
    //     // }
    //     $this->db->select('*');
    //     $this->db->from('team');
    //     if ($course_id != -1) {
    //         $this->db->where('course_id', $course_id);
    //     }

    //     if ($teamId != -1) {
    //         $this->db->where('team_id', $teamId);
    //     }

    //     $this->db->limit(10, $offset);
    //     $query = $this->db->get();
    //     $i = 0;
    //     foreach ($query->result() as $row) {
    //         $data[$i]['team_id'] = $row->team_id;
    //         $data[$i]['team_name'] = $row->team_name;
    //         $data[$i]['course_id'] = $row->course_id;
    //         $data[$i]['course_name'] = $this->coursesMod->getcoursename($row->course_id);
    //         $data[$i]['leader_id'] = $row->leader_id;
    //         $data[$i]['leader_name'] = $this->notificationMod->get_name_person($row->leader_id);
    //         $data[$i]['members_name'] = $this->getMembersname($row->team_id);
    //         // $data[$i]['leader_img'] = base_url()."styles/images/".$row->timg;
    //         $data[$i]['leader_img'] = base_url()."styles/images/teams_imgs/".$row->timg;            
    //         $data[$i]['max_number'] = $row->max_number;
    //         $data[$i]['actual_number'] = $row->actual_number;
    //         $data[$i]['team_description'] = $row->team_description;
    //         $i++;
    //     }
    //     $data['count'] = $i;
    //     $data['numOfTeams'] = $numOfTeams;
    //     if($numOfTeams % 10 == 0)
    //     {
    //         $data['pages'] = $numOfTeams / 10;
    //     }
    //     else 
    //     {
    //         $data['pages'] = intval(($numOfTeams / 10) + 1);
    //     }
        
    //     return $data;
    // }


    public function showmyteams($offset, $course_id = -1 , $teamId = -1) {

        $this->load->model("notificationMod");
        $this->load->model("coursesMod");
        // $this->notificationMod->get_name_person()
        $data = [];
         // $numOfTeams = $this->db->count_all('team');
        // if ($course_id != -1) {
            $numOfTeams = $this->getnumofteam($course_id);
        // }
        $this->db->select('*');
        $this->db->from('team');
        if ($course_id != -1) {
            $this->db->where('course_id', $course_id);
        }

        if ($teamId != -1) {
            $this->db->where('team_id', $teamId);
        }

        $this->db->limit(10, $offset);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result() as $row) {
            if($teamId == -1)
            {
                
                    
                if(($this->haverequest($row->team_id, $this->session->userdata('userid')) || in_array($this->session->userdata('userid') , $this->TeamMod->getMembersid($row->team_id)) || $this->session->userdata('userid') == $row->leader_id))
                {
                    $data[$i]['team_id'] = $row->team_id;
                    $data[$i]['team_name'] = $row->team_name;
                    $data[$i]['course_id'] = $row->course_id;
                    $data[$i]['course_name'] = $this->coursesMod->getcoursename($row->course_id);
                    $data[$i]['leader_id'] = $row->leader_id;
                    $data[$i]['leader_name'] = $this->notificationMod->get_name_person($row->leader_id);
                    $data[$i]['members_name'] = $this->getMembersname($row->team_id);
                    $data[$i]['leader_img'] = base_url()."styles/images/teams_imgs/".$row->timg;
                    $data[$i]['max_number'] = $row->max_number;
                    $data[$i]['actual_number'] = $row->actual_number;
                    $data[$i]['team_description'] = $row->team_description;
                    $i++;
                }
            }
            else
            {
                $data[$i]['team_id'] = $row->team_id;
                $data[$i]['team_name'] = $row->team_name;
                $data[$i]['course_id'] = $row->course_id;
                $data[$i]['course_name'] = $this->coursesMod->getcoursename($row->course_id);
                $data[$i]['leader_id'] = $row->leader_id;
                $data[$i]['leader_name'] = $this->notificationMod->get_name_person($row->leader_id);
                $data[$i]['members_name'] = $this->getMembersname($row->team_id);
                $data[$i]['leader_img'] = base_url()."styles/images/teams_imgs/".$row->timg;
                $data[$i]['max_number'] = $row->max_number;
                $data[$i]['actual_number'] = $row->actual_number;
                $data[$i]['team_description'] = $row->team_description;
                $i++;
            }
        }
        $data['count'] = $i;
        $data['numOfTeams'] = $numOfTeams;
        if($numOfTeams % 10 == 0)
        {
            $data['pages'] = $numOfTeams / 10;
        }
        else 
        {
            $data['pages'] = intval(($numOfTeams / 10) + 1);
        }
        
        return $data;
    }


     public function showteams($offset, $course_id = -1 , $teamId = -1) {

        $this->load->model("notificationMod");
        $this->load->model("coursesMod");
        // $this->notificationMod->get_name_person()
        $data = [];
         // $numOfTeams = $this->db->count_all('team');
        // if ($course_id != -1) {
            $numOfTeams = $this->getnumofteam($course_id);
        // }
        $this->db->select('*');
        $this->db->from('team');
        if ($course_id != -1) {
            $this->db->where('course_id', $course_id);
        }

        if ($teamId != -1) {
            $this->db->where('team_id', $teamId);
        }

        $this->db->limit(10, $offset);
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result() as $row) {
            if($teamId == -1)
            {
                
                    
                if(!($this->haverequest($row->team_id, $this->session->userdata('userid')) || in_array($this->session->userdata('userid') , $this->TeamMod->getMembersid($row->team_id)) || $this->session->userdata('userid') == $row->leader_id))
                {
                    $data[$i]['team_id'] = $row->team_id;
                    $data[$i]['team_name'] = $row->team_name;
                    $data[$i]['course_id'] = $row->course_id;
                    $data[$i]['course_name'] = $this->coursesMod->getcoursename($row->course_id);
                    $data[$i]['leader_id'] = $row->leader_id;
                    $data[$i]['leader_name'] = $this->notificationMod->get_name_person($row->leader_id);
                    $data[$i]['members_name'] = $this->getMembersname($row->team_id);
                    $data[$i]['leader_img'] = base_url()."styles/images/teams_imgs/".$row->timg;
                    $data[$i]['max_number'] = $row->max_number;
                    $data[$i]['actual_number'] = $row->actual_number;
                    $data[$i]['team_description'] = $row->team_description;
                    $i++;
                }
            }
            else
            {
                $data[$i]['team_id'] = $row->team_id;
                $data[$i]['team_name'] = $row->team_name;
                $data[$i]['course_id'] = $row->course_id;
                $data[$i]['course_name'] = $this->coursesMod->getcoursename($row->course_id);
                $data[$i]['leader_id'] = $row->leader_id;
                $data[$i]['leader_name'] = $this->notificationMod->get_name_person($row->leader_id);
                $data[$i]['members_name'] = $this->getMembersname($row->team_id);
                $data[$i]['leader_img'] = base_url()."styles/images/teams_imgs/".$row->timg;
                $data[$i]['max_number'] = $row->max_number;
                $data[$i]['actual_number'] = $row->actual_number;
                $data[$i]['team_description'] = $row->team_description;
                $i++;
            }
        }
        $data['count'] = $i;
        $data['numOfTeams'] = $numOfTeams;
        if($numOfTeams % 10 == 0)
        {
            $data['pages'] = $numOfTeams / 10;
        }
        else 
        {
            $data['pages'] = intval(($numOfTeams / 10) + 1);
        }
        
        return $data;
    }

    
    // public function showteams($offset,$course_id = -1)
    // {

    //         $this->load->model("notificationMod");
    //         $this->load->model("coursesMod");
    //         // $this->notificationMod->get_name_person()
    //      $data = [];
    //         $numOfTeams = $this->db->count_all('team');
    //         $this->db->select('*');
    //         $this->db->from('team');
    //         if($course_id != -1)
    //         {
    //            $this->db->where('course_id', $course_id); 
    //         }
    //         $this->db->limit(10, $offset);
    //         if ($offset == 0) {
    //             $this->db->limit(10, $offset);
    //         } else {
    //             if ($offset <= $numOfTeams) {
    //                 $this->db->limit(10, $offset - 10);
    //             } else if ($offset > $numOfTeams) {
    //                 $remain = $numOfTeams - ($offset - 10);
    //                 $this->db->limit($remain, $offset - 10);
    //             }
    //         }
    //         $query = $this->db->get();
    //         $i = 0;
    //         foreach ($query->result() as $row) {
    //             $data[$i]['team_id'] = $row->team_id;
    //             $data[$i]['team_name'] = $row->team_name;
    //             $data[$i]['course_id'] = $row->course_id;
    //             $data[$i]['course_name'] = $this->coursesMod->getcoursename($row->course_id);
    //             $data[$i]['leader_id'] = $row->leader_id;
    //             $data[$i]['leader_name'] = $this->notificationMod->get_name_person($row->leader_id);
    //             $data[$i]['members_name'] = $this->getMembersname($row->leader_id);
    //             $data[$i]['max_number'] = $row->max_number;
    //             $data[$i]['actual_number'] = $row->actual_number;
    //             $data[$i]['team_description'] = $row->team_description;
    //             $i++;
    //         }
    //         return $data;
    // }


    public function getMembersname($Teamid)
    {
        $data = [] ;
        $this->load->model("notificationMod");  
        $this->db->select('*');
        $this->db->where('team_id', $Teamid);
        $this->db->from('team_member');
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result() as $row) {
            $data[$i] = $this->notificationMod->get_name_person($row->std_id);
            $i++;
        }
        return $data;                                   
    }


     public function isneedmember($teamid)
    {
        //SELECT * FROM `team` WHERE `max_number` = `actual_number` and `team_id` = 1 
        
        $this->db->where('team_id', $teamid); 
        $this->db->where('max_number', 'actual_number'); 
        $this->db->select('*');
        $this->db->from('team'); 
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return false;
        }
        else {
            return true;    
        }
    }


     public function getMembersid($Teamid)
    {
        $data = [] ;
        $this->load->model("notificationMod");  
        $this->db->select('*');
        $this->db->where('team_id', $Teamid);
        $this->db->from('team_member');
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result() as $row) {
            $data[$i] = $row->std_id;
            $i++;
        }
        return $data;                                   
    }


     public function haverequest($teamid,$userid)
    {
        //SELECT `request_id`, `std_id`, `std_name`, `team_id`, `team_name` FROM `teams_requests` WHERE 1
        
        $this->db->where('team_id', $teamid);
        $this->db->where('std_id', $userid); 
        $this->db->select('*');
        $this->db->from('teams_requests'); 
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        }
        else {
            
            return false;    
        }
    }

    
     public function getLeaderimg($Leader_id)

    {

        $this->db->where('id',$Leader_id);

        $this->db->select('*');

        $this->db->from('users');

        $query = $this->db->get();

        if($query->num_rows()>0)

        {

            foreach ($query->result() as $row) {

               $img = base_url()."styles/images/users_imgs/profile/".$row->img;

            }

        }

        if(isset($img))

        {

            return $img;

        }
        else
        {
            return null;
        }



    }


    public function courses()
    {
        $res = [];   
        $this->load->model("coursesMod");
        $q = $this->coursesMod->coursesNames();
        $i =0;
        // foreach ($q->result() as $row) {
        foreach ($q as $row) {
            $res[$i]= $row->c_name." | ".$row->c_id;
            $i ++ ;
        }
        return $res;
    }



    public function makerequest($teamid,$userid)
    {
        // SELECT `request_id`, `std_id`, `std_name`, `team_id`, `team_name` FROM `teams_requests` WHERE 1

        $data = $this->showteams(0, -1 , $teamid);

        $this->load->model("notificationMod");

        $username = $this->notificationMod->get_name_person($userid);

        $team_data  = array('std_id' => $userid , 'std_name'=> $username ,'team_id'=> $teamid ,'team_name' => $data[0]['team_name']);

        if( $this->db->insert("teams_requests", $team_data) ){
            $arr[0] =   $this->db->insert_id();
            $arr[1] =   $data[0]['leader_id'];
            return $arr;

        }
        else{
            return null;
        }
    }


    
    public function getrequireinfo($request_id)
    {
        // SELECT `request_id`, `std_id`, `std_name`, `team_id`, `team_name` FROM `teams_requests` WHERE 1
        $data = [];
        $this->db->select('*');
        $this->db->where('request_id', $request_id);
        $this->db->from('teams_requests');
        $query = $this->db->get();
        $i = 0;
        foreach ($query->result() as $row) {
            $data['name'] = $row->std_name;
            $data['tid'] = $row->team_id;
            $data['uid']   = $row->std_id;
        }
        return $data;

    }

    public function deletereqbyid($reqid)
    {
        $this->db->where('request_id', $reqid);
        if ($this->db->delete('teams_requests')) {
            return true;
        } else {
            return false;
        }

    }
    public function deletereq($team_id,$std_id)
    {
        //SELECT `request_id`, `std_id`, `std_name`, `team_id`, `team_name` FROM `teams_requests` WHERE 1
        $this->db->where('team_id', $team_id);
        $this->db->where('std_id', $std_id);
        if ($this->db->delete('teams_requests')) {
            return true;
        } else {
            return false;
        }

    }

    public function insert_member_team($member_data) {
        if ($this->db->insert("team_member", $member_data)) {
            return true;
        } else {
            return false;
        }
    }

    public function isrequest($reqid)
    {
        $this->db->select('*');
        $this->db->where('request_id', $reqid);
        $this->db->from('teams_requests');
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            return true;    
        }
        else 
        {
            return false;
        }
    } 




    public function getactualnum($teamid)
    {
        //SELECT * FROM `team` WHERE `max_number` = `actual_number` and `team_id` = 1 
        $this->db->select('actual_number');
        $this->db->where('team_id', $teamid);
        $this->db->from('team');
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            return  (intval($row->actual_number) + 1);
        }
    }

    public function delete_notification($not_id) {
        $this->db->where('notification_Id', $not_id);
        if ($this->db->delete('notification')) {
            return true;
        } else {
            return false;
        }
    }
}
