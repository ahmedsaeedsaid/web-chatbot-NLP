<?php



class notificationMod extends CI_Model {





    public function get_not_read_notification($person_Id) // run when he/she login in |run one only|

    {

        $this->db->where('show_person_id',$person_Id);

        $this->db->where('notification_seen',0);

        $this->db->select('show_notification_id');

        $this->db->from('persons_shows_notification');

        $query = $this->db->get();

        return $query->num_rows();

    }



    /*public function Insert_Notification($idperson,$discription,$url)

    {

        $data = array("notification_Description" =>$discription ,"notification_person_id" =>$idperson ,"notification_Url"=>$url);

        $this->db->insert('notification',$data);

        $this->db->where("notification_Description",$discription);

        $this->db->where("notification_person_id",$idperson);

        $this->db->where("notification_Url",$url);

        $this->db->select("notification_Id");

        $this->db->from("notification");

        $query = $this->db->get();

        if($query->num_rows()>0)

        {

            foreach ($query->result() as $row) {

               $notification_Id = $row->notification_Id;

            }

        }

        if(isset($notification_Id))

        {

            return $notification_Id;

        }

        else

        {

            return null;

        }

    }*/





    public function who_show_notification($notificationId,$personsId)

    {

        foreach ($personsId as $value) {

            $data = array('show_notification_id' =>$notificationId ,'show_person_id' =>$value );

            $this->db->insert('persons_shows_notification',$data);   

        }

    }



    public function get_last_notification($id)

    {

        $num_notification = intval($this->get_not_read_notification($id));

        if($num_notification > intval($this->session->userdata('last_num')))

        {

            $this->db->where('show_person_id',$id);

            $this->db->order_by('show_notification_id','DESC');

            $this->db->limit(1,0);

            $this->db->select('show_notification_id');

            $this->db->from('persons_shows_notification');

            $query = $this->db->get();

            if($query->num_rows() > 0)

            {

                foreach ($query->result() as $row) {

                    $res = $row->show_notification_id;

                }

                $this->new_notification($res);

            }

            $this->set_last_num($id);

        }

    }





    public function set_last_num($id)

    {

        $data = array('last_num'=>$this->get_not_read_notification($id));

        $this->session->set_userdata($data);

    }





    public function new_notification($notificationId) // show new notification

    {

        $this->db->where("notification_Id",$notificationId);

        $this->db->select("*");

        $this->db->from("notification");

        $query = $this->db->get();

        if($query->num_rows()>0)

        {

            foreach ($query->result() as $row) {

                //echo "<p data-id='$notificationId' class='text-danger' data-href=\"$row->notification_Url\">".$this->get_name_person($row->notification_person_id)." : $row->notification_Description</p>";


                echo "<p data-id='$notificationId' class='text-danger' data-href=\"$row->notification_Url\">".$this->get_name_person($row->notification_person_id)." : $row->not_title</p>";

            }

        }

    }   



    



   public function get_all_notification($personsId) // get from database all notification 

    {

        /*

            <li>

                <a href="<?php echo base_url(); ?>LoginCont/logout">

                    

                </a>

            </li>

        */

        $this->db->where('show_person_id',$personsId);

        $this->db->order_by('show_notification_id','DESC');

        $this->db->limit(10,0);

        $this->db->select('*');

        $this->db->from('persons_shows_notification');

        $query = $this->db->get();

        if($query->num_rows() > 0)

        {

            $i=0;

            foreach ($query->result() as $row) {

                $res[$i] = $row->show_notification_id;

                $read_or_not[$i] = $row->notification_read;

                $i++;

            }

            $i=0;

           foreach ($res as $value) {

                $this->db->where("notification_Id",$value);

                $this->db->select("*");

                $this->db->from("notification");

                $query = $this->db->get();

                if($query->num_rows()>0)

                {

                    foreach ($query->result() as $row) {

                        if($read_or_not[$i] == 0){

                        //echo "<li role='button' data-id='$row->notification_Id' class='readnot readed'><a id='notification-link' href=\"$row->notification_Url\">".$this->get_name_person($row->notification_person_id)." : $row->notification_Description</a></li>";

                        echo "<li role='button' data-id='$row->notification_Id' class='readnot readed'><a id='notification-link' href=\"$row->notification_Url\"><img src='". base_url() ."styles/images/users_imgs/profile/" . $this->get_img_person($row->notification_person_id) . "' class='rounded-circle' width='30px' height='30px' alt='' style='margin-right:5px;'><b>".$this->get_name_person($row->notification_person_id)."</b> $row->not_title</a></li><hr style='margin:auto;'/>";


                        }

                        else {

                        //echo "<li class='readnot' data-id='$row->notification_Id'><a id='notification-link' href=\"$row->notification_Url\">".$this->get_name_person($row->notification_person_id)." : $row->notification_Description</a></li>";


                        echo "<li class='readnot' data-id='$row->notification_Id'><a id='notification-link' href=\"$row->notification_Url\"><img src='". base_url() ."styles/images/users_imgs/profile/" . $this->get_img_person($row->notification_person_id) . "' class='rounded-circle' width='30px' height='30px' alt='' style='margin-right:5px;'><b>".$this->get_name_person($row->notification_person_id)."</b> $row->not_title</a></li><hr style='margin:auto;'/>";

                        }

                    }

                }

                $i++;

                

            }

        }



    }



    public function read_all_notification($personsId) // person see his/her notification |update col(read) from 0 to 1 |

    {

        $data = array(' notification_read' => 1 );

        $this->db->where('show_person_id',$personsId);

        $this->db->update('persons_shows_notification',$data);

    }







    public function seen_all_notification($personsId) // person see his/her notification |update col(read) from 0 to 1 |

    {

        $data = array(' notification_seen' => 1 );

        $this->db->where('show_person_id',$personsId);

        $this->db->update('persons_shows_notification',$data);

    }





    public function read_notification($personsId,$notificationId) // person read his/her notification |update col(read) from 0 to 1 |

    {

        $data = array(' notification_read' => 1 );

        $this->db->where('show_person_id',$personsId);

        $this->db->where('show_notification_id',$notificationId);

        $this->db->update('persons_shows_notification',$data);

    }





    public function seen_notification($personsId,$notificationId) // person see his/her notification |update col(read) from 0 to 1 |

    {

        $data = array(' notification_seen' => 1 );

        $this->db->where('show_person_id',$personsId);

        $this->db->where('show_notification_id',$notificationId);

        $this->db->update('persons_shows_notification',$data);

    }



    public function get_id_person($email)

    {

        $this->db->where('email',$email);

        $this->db->select('id');

        $this->db->from('users');

        $query = $this->db->get();

        if($query->num_rows()>0)

        {

            foreach ($query->result() as $row) {

               $id = $row->id;

            }

        }

        if(isset($id))

        {

            return $id;

        }

        else

        {

            return null;

        }



    }





    public function get_name_person($id)

    {

        $this->db->where('id',$id);

        $this->db->select('name');

        $this->db->from('users');

        $query = $this->db->get();

        if($query->num_rows()>0)

        {

            foreach ($query->result() as $row) {

               $name = $row->name;

            }

        }

        if(isset($id))

        {

            return $name;

        }

        else

        {

            return null;

        }



    }





/******************************************31/8/2017****************************************************/





    public function get_img_person($id) {

        $this->db->where('id', $id);

        $this->db->select('img');

        $this->db->from('users');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {

                $img = $row->img;

            }

        }

        if (isset($img)) {

            return $img;

        } else {

            return null;

        }

    }



    public function get_Top_notification($personsId) { // get Top notification to notification page  

        $this->db->where('show_person_id', $personsId);

        $this->db->order_by('show_notification_id', 'DESC');

        $this->db->limit(10, 0);

        $this->db->select('*');

        $this->db->from('persons_shows_notification');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            $i = 0;

            foreach ($query->result() as $row) {

                $res[$i] = $row->show_notification_id;

                $readornot[$i] = $row->notification_read;

                $i++;

            }

            $i = 0;

            foreach ($res as $value) {

                $this->db->where("notification_Id", $value);

                $this->db->select("*");

                $this->db->from("notification");

                $query = $this->db->get();

                if ($query->num_rows() > 0) {

                    foreach ($query->result() as $row) {

                        $data['personName'][$i] = $this->get_name_person($row->notification_person_id);

                        $data['personImg'][$i] =  $this->get_img_person($row->notification_person_id);

                        //$data['description'][$i] = $row->notification_Description;



                        $data['description'][$i] = $row->not_title;

                        $data['url'][$i] = $row->notification_Url;

                        $data['time'][$i] = $row->notification_time;

                        $data['read'][$i] = $readornot[$i];

                        $data['notification_Id'][$i] = $value;

                        $i++;

                    }

                }

            }

        }

        if(isset($data))

        {

            return $data;

        }

        else

        {

            return null;

        }

    }









     public function get_another_notification($personsId,$start) { // get Top notification to notification page  

        $this->db->where('show_person_id', $personsId);

        $this->db->order_by('show_notification_id', 'DESC');

        $this->db->limit(10, $start);

        $this->db->select('*');

        $this->db->from('persons_shows_notification');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            $i = 0;

            foreach ($query->result() as $row) {

                $res[$i] = $row->show_notification_id;

                $readornot[$i] = $row->notification_read;

                $i++;

            }

            $i = 0;

            foreach ($res as $value) {

                $this->db->where("notification_Id", $value);

                $this->db->select("*");

                $this->db->from("notification");

                $query = $this->db->get();

                if ($query->num_rows() > 0) {

                    foreach ($query->result() as $row) {

                        $data['personName'][$i] = $this->get_name_person($row->notification_person_id);

                        $data['personImg'][$i] =  $this->get_img_person($row->notification_person_id);


                        //$data['description'][$i] = $row->notification_Description;

                        $data['description'][$i] = $row->not_title;

                        $data['url'][$i] = $row->notification_Url;

                        $data['time'][$i] = $row->notification_time;

                        $data['read'][$i] = $readornot[$i];

                        $data['notification_Id'][$i] = $value;

                        echo '<a href="'.$data['url'][$i].'" class="new_not" data-id="'. $data['notification_Id'][$i].'">';

                        if ($data['read'][$i] ==1) 

                            echo '<div class="read not container-fluid">';

                        else

                            echo '<div class="noread not container-fluid">';   

                        echo '<div class="row">';

                        echo '<div class="col-sm-1">';

                        echo '<img src="'.base_url().'styles/images/users_imgs/profile/'.$data['personImg'][$i].'" width="70" height="70">';        

                        echo '</div>';        

                        echo '<div class="notif col-sm-10">';        

                        echo '<div class="noti col-sm-10">';        

                        echo '<div class="col-sm-12">';        

                        echo $data['personName'][$i].' : '. $data['description'][$i];        

                        echo '<div>'.$data['time'][$i].'</div>';            

                        echo '</div>';            

                        echo '</div>';            

                        echo '</div>';            

                        echo '</div>';            

                        echo '</div>';            

                        echo '</a>';

                        $i++;  

                    }

                }

            }

        }

        else

        {

            echo "no notification";

        }

    }







    public function get_info_notification($notificationID)

    {

        $this->db->where("notification_Id", $notificationID);

        $this->db->select("*");

        $this->db->from("notification");

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {

                $data['personName'] = $this->get_name_person($row->notification_person_id);

                $data['personImg'] =  $this->get_img_person($row->notification_person_id);


                /*


                */
                    $data['PERID'] =  $row->notification_person_id;

                /*


                */

                //$data['description'] = $row->notification_Description;

                $data['description'] = $row->not_body;

                $data['time'] = $row->notification_time;

            }

        }

        if(isset($data))

        {

            return $data;

        }

        else

        {

            return null;

        }

    }





    public function haspermission($notId,$userId)

    {

        $this->db->where("show_notification_id", $notId);

        $this->db->where("show_person_id", $userId);

        $this->db->select("*");

        $this->db->from("persons_shows_notification");

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;

        }

        else

        {

            return false;

        }

    }



    

    public function Insert_Notification($idperson,$discription,$title)
    {
        //$data = array("notification_Description" =>$discription ,"notification_person_id" =>$idperson ,"notification_Url"=>"");

        $data = array("not_body" =>$discription ,"notification_person_id" =>$idperson ,"notification_Url"=>"","not_title"=>$title);
        $this->db->insert('notification',$data);

        $notification_Id = $this->db->Insert_id();
        $this->updateurl($notification_Id);
        return $notification_Id;


        /*$this->db->where("notification_Description",$discription);
        $this->db->where("notification_person_id",$idperson);
        $this->db->where("notification_Url","");
        $this->db->select("notification_Id");
        $this->db->from("notification");
        $query = $this->db->get();
        if($query->num_rows()>0)
        {
            foreach ($query->result() as $row) {
               $notification_Id = $row->notification_Id;
            }
        }
        if(isset($notification_Id))
        {
            $this->updateurl($notification_Id);
            return $notification_Id;
        }
        else
        {
            return null;
        }*/
    }


    public function updateurl($notification_Id)
    {
        $this->db->where("notification_Id",$notification_Id);
        $notification_Url = base_url()."NotificationCont/notificationpage/".$notification_Id;
        $data = array("notification_Url"=>$notification_Url);
        $this->db->update("notification",$data);

    }


    public function get_user_Id_courses($coursesId)
    {
        $this->db->where('users_courses_ID', $coursesId);
        $this->db->select('*');
        $this->db->from('users_courses');
        $query =  $this->db->get();
        if ($query->num_rows() > 0) {
            $i=0;
            foreach ($query->result() as $row) {
                $userid[$i] = $row->courses_users_ID;
                $i++;
            }
        }
        if(isset($userid))
        {
            return $userid;
        }
        else
        {
            return null;
        }

    }

    public function sendnotificationToStudend($f_Id,$Fname,$userid,$coursesId,$coursename)
    {
        $idperson = $this->get_user_Id_courses($coursesId);
        $Pname = $this->get_name_person($userid);
        //$discription = $Pname ." upload file or folder that name is ". $Fname . " go to : <br> <a href='".base_url()."filesCont/files/".$f_Id."'> matrial </a>";
        $title    = 'A material in '.$coursename.' has been uploaded';
        $discription = $Pname.' has uploaded a material in '.$coursename.', you can follow this link: <a href="'.base_url().'filesCont/files/'.$f_Id.'">'.$Fname.'</a>';
        //$notification_Id = $this->Insert_Notification($userid,$discription);
        $notification_Id = $this->Insert_Notification($userid,$discription,$title);
        $this->who_show_notification($notification_Id,$idperson);
    }




    
    public function get_notification_person_id($notificationId)
    {
        $this->db->where("notification_Id",$notificationId);
        $this->db->select("notification_person_id");
        $this->db->from("notification");
        $query = $this->db->get();
        if($query->num_rows()>0)
        {
            foreach ($query->result() as $row) {
               $notificationpersonid = $row->notification_person_id;
            }
        }
        if(isset($notificationpersonid))
        {
            $this->updateurl($notificationpersonid);
            return $notificationpersonid;
        }
        else
        {
            return null;
        }

        
    }



    public function is_doctor($id)
    {
        $role = $this->get_role_person($id);
        $this->db->where('id', $role);
        $this->db->select('type');
        $this->db->from('user_type');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $role = $row->type;
            }
        }
        if (isset($role)) {
            if($role == "professor")
                return true;
            else
                return false;
        } else {
            return false;
        }

    }
















    

    public function get_Names_And_Ids($name)
    {
        $this->db->like('name', $name, 'both'); 
        $this->db->limit(3,0); 
        $this->db->select('*');
        $this->db->from('users');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {

                echo 
                "<div role='button' data-type='S' data-id='".$row->id."' class=\"perant col-sm-8\">
                    <div class=\"col-sm-3\">
                        <img width='50' height='50' src='".base_url()."styles/images/users_imgs/profile/".$row->img."'>
                    </div>
                    <div class=\"name col-sm-8\">
                        <div>
                            ".$row->name."
                        </div>
                    </div>
                </div>";
            }
        }

    }


    public function get_Names_of_courses_And_Ids($name)
    {
        $usrID = $this->session->userdata('userid');
        $this->load->model('filesMod');
        $this->db->like('c_name', $name, 'both'); 
        // $this->db->limit(3,0); 
        $this->db->select('*');
        $this->db->from('courses');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if ($this->filesMod->profOrInstTeach($usrID, $row->c_id)) {
                     echo 
                    "<div role='button' data-type='C' data-id='".$row->c_id."' class=\"perant col-sm-8\">
                        <div class=\"col-sm-3\">
                            <img width='50' height='50' src='".base_url()."styles/images/".$row->c_picture."'>
                        </div>
                        <div class=\"name col-sm-8\">
                            <div>
                                ".$row->c_name."
                            </div>
                        </div>
                    </div>";
                }


            }
        }

    }



    public function get_email_person($id)
    {
        $this->db->where('id',$id);
        $this->db->select('email');
        $this->db->from('users');
        $query = $this->db->get();
        if($query->num_rows()>0)
        {
            foreach ($query->result() as $row) {
               $email = $row->email;
            }
        }
        if(isset($email))
        {
            return $email;
        }
        else
        {
            return null;
        }

    }



    
    private function sendnotificationMail($mail, $doctorname, $description,$url) 
     {
        $this->load->library('email');
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.gmail.com';
        $config['smtp_port'] = '465';
        $config['smtp_timeout'] = '7';
        $config['smtp_user'] = 'da7i7a.fcih@gmail.com';
        $config['smtp_pass'] = '123456789da7i7a';
        $config['charset'] = 'utf-8';
        $config['newline'] = "\r\n";
        $config['mailtype'] = 'html'; // or html
        $config['validation'] = TRUE; // bool whether to validate email or not      
        $this->email->initialize($config);
        $this->email->from('da7i7a.fcih@gmail.com', 'Da7i7a Team');
        $this->email->to($mail);
        $this->email->subject('Da7i7a sign-in confirmation');
        $msg = "<!DOCTYPE html>"
                . "<html>"
                . "<head></head>"
                . "<body>"
                . "<p>" . $doctorname . " informs you </p>"
                . "<p><strong>".$description."</p>"
                . "<a href='".$url."'>".$url."</a>"
                . "<p>Thanks</p>"
                . "<p>Da7i7a Team</p>"
                . "</body>"
                . "</html>";
        $this->email->message($msg);
        if (!$this->email->send()) {
            return false;
        } else {
            return true;
        }
    }


public function get_role_person($id) {
        $this->db->where('id', $id);
        $this->db->select('usertypeID');
        $this->db->from('users');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $role = $row->usertypeID;
            }
        }
        if (isset($role)) {
            return $role;
        } else {
            return null;
        }
    }



    
}







?>