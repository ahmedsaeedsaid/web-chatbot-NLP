<?php

$url = rtrim(__Dir__, 'models');
require_once $url . 'classes/user.php';

class authMod extends CI_Model {

    public function addUser($usr) {
        $data = array(
            'name' => $usr->name,
            'email' => $usr->email,
            'BD' => $usr->BD,
            'country' => $usr->country,
            'password' => md5($usr->pass),
            'gender' => $usr->gender,
            'UniversityID' => $usr->university,
            'facID' => $usr->faculty,
            'phone' => $usr->phone,
            'usertypeID' => $usr->usertypeID
        );
        if(isset($usr->img)){
           $data['img'] = $usr->img;
        }
        $this->db->insert('users', $data);
        $id = $this->db->insert_id();
        if ($usr->usertypeID == 1) {
            $data = array(
                'userid' => $id,
                'level' => $usr->level,
                'department_id' => $usr->department_id,
                'facultyID' => $usr->facultyID
            );
            $this->db->insert('students', $data);
        } else if ($usr->usertypeID == 2) {
            $data = array(
                'userid' => $id,
                'phd' => $usr->phD
            );
            $this->db->insert('professors', $data);
        } else {
            $data = array(
                'userid' => $id,
                'master' => $usr->master
            );
            $this->db->insert('instructors', $data);
        }
        return $id;
    }

    public function facultyID_exists($fID) {
        $query = $this->db->get_where('students', array('facultyID' => $fID));
        if ($query->num_rows() > 0)
            return true;
        else
            return false;
    }

    public function get_faculty_levels($faculty){
        $query = $this->db->get_where('faculties', array('id' => $faculty));
        if($query->num_rows() > 0){
            $fac = $query->row();
            return $fac->number_of_levels;
        }else{
            return 0;
        }
    }

    public function get_faculty_departments($level, $uni, $fac){
        $this->db->where('university_id', $uni);
        $this->db->where('faculty_id', $fac);
        $this->db->where('starting_level <=' , $level);
        $this->db->where('active' , 1);
        $this->db->from('departments');
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $depart = $query->result();
            return $depart;
        }else{
            return false;
        }

    }

    public function authenticateUser(User $usr, $remember) {
        // Prepare the query
        $query = $this->db->get_where('users', array('email' => $usr->email, 'password' => md5($usr->pass)));
        if ($query->num_rows() > 0) {
            // user info object
            $usr = $query->row();
            if ($usr->active) {
                $name = null;
                if ($usr->username) {
                    $name = $usr->username;
                } else {
                    $name = $usr->name;
                }
                if ($usr->usertypeID == 2) {
                    $query = $this->db->get_where('professors', array('userid' => $usr->id));
                    if ($query->num_rows() > 0) {
                        $pro = $query->row();
                        if ($pro->approved == 1) {
                            $this->setUserSession($usr, $name, $remember);
                            $query = $this->db->get_where('prof_subjects', array('profID' => $usr->id));
                            if ($query->num_rows() == 1) {
                                $subs = $query->row();
                                $data['subActive'] = $subs->active;
                                $this->session->set_userdata($data);
                            }
                        } else {
                            return false;
                        }
                    } else {
                        $this->setUserSession($usr, $name, $remember);
                    }
                } else if ($usr->usertypeID == 3) {
                    $query = $this->db->get_where('instructors', array('userid' => $usr->id));
                    if ($query->num_rows() > 0) {
                        $inst = $query->row();
                        if ($inst->approved == 1) {
                            $this->setUserSession($usr, $name, $remember);
                            $query = $this->db->get_where('inst_subjects', array('InstID' => $usr->id));
                            if ($query->num_rows() > 0) {
                                $subs = $query->row();
                                $data['instActive'] = $subs->active;
                                $this->session->set_userdata($data);
                            }
                        } else {
                            return false;
                        }
                    } else {
                        $this->setUserSession($usr, $name, $remember);
                    }
                } else if($usr->usertypeID == 1){
                    $query = $this->db->get_where('students', array('userid' => $usr->id));
                    if($query->num_rows() > 0){
                        $student = $query->row();
                        $this->setUserSession($usr, $name, $remember);
                        $data['studentLevel'] = $student->level;
                        $this->session->set_userdata($data);
                    }else{
                        $this->setUserSession($usr, $name, $remember);
                    }
                }
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function setUserSession($usr, $name, $remember) {
        if ($remember != "false") {
            $cookie = array(
                'name' => 'login_credentials',
                'value' => $usr->id,
                'expire' => '864000',
            );
            $this->input->set_cookie($cookie);
        }

        $data = array(
            'userid' => $usr->id,
            'username' => $name,
            'usertypeID' => $usr->usertypeID,
            'facultyID' => $usr->facID,
            'userimg' => $usr->img
        );
        $this->session->set_userdata($data);
    }

    public function checkUserExists(User $usr) {
        $res = $this->db->get_where('users', array('email' => $usr->email));
        if ($res->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function ActivateUser($id) {
        $this->db->where('id', $id);
        $res = $this->db->update('users', array('active' => 1));
        if ($res) {
            return true;
        }
        return false;
    }

    public function VerifyDoctor($id) {
        $this->db->where('userid', $id);
        $res = $this->db->update('professors', array('approved' => 1));
        if ($res) {
            return true;
        }
        return false;
    }

    public function VerifyInstructor($id) {
        $this->db->where('userid', $id);
        $res = $this->db->update('instructors', array('approved' => 1));
        if ($res) {
            return true;
        }
        return false;
    }

    public function updatePass($email, $pass) {
        $data = array(
            'password' => md5($pass)
        );
        $email = urldecode($email);
        $this->db->where('email', $email);
        $res = $this->db->update('users', $data);
        return $res;
    }

    public function adduserFacebook($userID, $userFacebookID) {
        $data = array(
            'user_Id' => $userID,
            'user_facebook_Id' => $userFacebookID
        );
        $this->db->insert('user_facebook', $data);
    }

    public function get_User_Id($user_facebook_Id) {

        $this->db->where('user_facebook_Id', $user_facebook_Id);
        $this->db->select('user_Id');
        $this->db->from('user_facebook');
        $res = $this->db->get();
        foreach ($res->result() as $row) {
            return $row->user_Id;
        }
        return NULL;
    }

    public function authenticateUserFacebook($userID) {
        // Prepare the query
        $query = $this->db->get_where('users', array('id' => $userID));
        if ($query->num_rows() > 0) {
            // user info object
            $usr = $query->row();
            if ($usr->active) {
                $name = null;
                if ($usr->username) {
                    $name = $usr->username;
                } else {
                    $name = $usr->name;
                }
                if ($usr->usertypeID == 2) {
                    $query = $this->db->get_where('professors', array('userid' => $usr->id));
                    if ($query->num_rows() > 0) {
                        $pro = $query->row();
                        if ($pro->approved == 1) {
                            $data = array(
                                'userid' => $usr->id,
                                'username' => $name,
                                'usertypeID' => $usr->usertypeID,
                                'facultyID' => $usr->facID,
                                'userimg' => $usr->img
                            );
                            $this->session->set_userdata($data);
                            $query = $this->db->get_where('prof_subjects', array('profID' => $usr->id));
                            if ($query->num_rows() == 1) {
                                $subs = $query->row();
                                $data['subActive'] = $subs->active;
                                $this->session->set_userdata($data);
                            }
                        } else {
                            return false;
                        }
                    } else {
                        $data = array(
                            'userid' => $usr->id,
                            'username' => $name,
                            'usertypeID' => $usr->usertypeID,
                            'facultyID' => $usr->facID,
                            'userimg' => $usr->img
                        );
                        $this->session->set_userdata($data);
                    }
                } else if ($usr->usertypeID == 3) {
                    $query = $this->db->get_where('instructors', array('userid' => $usr->id));
                    if ($query->num_rows() > 0) {
                        $inst = $query->row();
                        if ($inst->approved == 1) {
                            $data = array(
                                'userid' => $usr->id,
                                'username' => $name,
                                'usertypeID' => $usr->usertypeID,
                                'facultyID' => $usr->facID,
                                'userimg' => $usr->img
                            );
                            $this->session->set_userdata($data);
                            $query = $this->db->get_where('inst_subjects', array('InstID' => $usr->id));
                            if ($query->num_rows() > 0) {
                                $subs = $query->row();
                                $data['instActive'] = $subs->active;
                                $this->session->set_userdata($data);
                            }
                        } else {
                            return false;
                        }
                    } else {
                        $data = array(
                            'userid' => $usr->id,
                            'username' => $name,
                            'usertypeID' => $usr->usertypeID,
                            'facultyID' => $usr->facID,
                            'userimg' => $usr->img
                        );
                        $this->session->set_userdata($data);
                    }
                } else if($usr->usertypeID == 1){
                    $query = $this->db->get_where('students', array('userid' => $usr->id));
                    if($query->num_rows() > 0){
                        $student = $query->row();
                        $data = array(
                            'userid' => $usr->id,
                            'username' => $name,
                            'usertypeID' => $usr->usertypeID,
                            'facultyID' => $usr->facID,
                            'studentLevel' => $student->level,
                            'userimg' => $usr->img
                        );
                        $this->session->set_userdata($data);
                    }else{
                        $data = array(
                            'userid' => $usr->id,
                            'username' => $name,
                            'usertypeID' => $usr->usertypeID,
                            'facultyID' => $usr->facID,
                            'userimg' => $usr->img
                        );
                        $this->session->set_userdata($data);
                    }
                }
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function savecoverandlink($email, $coverlink, $link) {
        $this->db->where('email', $email);
        $data = array('cimg' => $coverlink, 'facebook' => $link);
        $res = $this->db->update('users', $data);
        if ($res) {
            return true;
        }
        return false;
    }

    public function getUser($id) {
        $this->db->select("*");
        $this->db->from("users");
        $this->db->where('id', $id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $user = $query->result()[0];
            // setting session
            $data = array(
                'userid' => $user->id,
                'username' => $user->username,
                'usertypeID' => $user->usertypeID,
                'facultyID' => $usr->facID,
                'userimg' => $usr->img
            );
            $this->session->set_userdata($data);
        }
    }

}
