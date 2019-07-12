<?php

$url = rtrim(__Dir__, 'models\support');
require_once $url . '/classes/user.php';

class LogMod extends CI_Model {

    public function authenticateCMSUser(User $usr) {
        // Prepare the query
        $query = $this->db->get_where('users', array('username' => $usr->name, 'password' => md5($usr->pass)));
        if ($query->num_rows() > 0) {
            // user info object
            $usr = $query->row();
            if ($usr->active) {
                $data['assis_userid'] = $usr->id;
                $data['assis_username'] = $usr->username;
                $data['assis_name'] = $usr->name;
                $data['assis_job'] = $usr->job;
                $data['assis_brief'] = $usr->brief;
                $data['assis_email'] = $usr->email;
                $data['assis_facebook'] = $usr->facebook;
                $data['assis_role'] = $usr->role;
                $this->session->set_userdata($data);
                return true;
            }
        }
        return false;
    }

}
