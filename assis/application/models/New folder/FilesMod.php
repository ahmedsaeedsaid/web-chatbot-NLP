<?php
class filesMod extends CI_Model {

    public static $FILE_ONLY_ME = 0;
    public static $FILE_COURSE_Read_ONLY = 1;
    public static $FILE_ALL_Read_ONLY = 2;
    public static $FILE_COURSE_Read_AND_WRITE = 3;
    public static $FILE_ALL_Read_AND_WRITE = 4;
    public static $READ_ONLY = 0;
    public static $READ_AND_WRITE = 1;
    public static $fa = array(
        'folder' => "fa fa-folder",
        'audio' => "fa fa-file-audio-o",
        'code' => "fa fa-file-code-o",
        'excel' => "fa fa-file-excel-o",
        'image' => "fa fa-file-image-o",
        'movie' => "fa fa-file-movie-o",
        'pdf' => "fa fa-file-pdf-o",
        'powerpoint' => "fa fa-file-powerpoint-o",
        'text' => "fa fa-file-text-o",
        'word' => "fa fa-file-word-o",
        'zip' => " fa fa-file-zip-o",
        'file' => "fa fa-file-o",
    );
    public static $type = array(
        'audio' => array('PCM', 'MAV', 'AIFF', 'MP3', 'AAC', 'OGG', 'WMA', 'FLAC', 'ALAC'),
        'code' => array('java', 'js', 'html', 'css', 'c', 'sql', 'php', 'cpp', 'cxx', 'cc', 'vb', 'cs', 'jsl'),
        'excel' => array('xls', 'xlsx'),
        'image' => array('JPEG', 'GIF', 'PNG', 'jpg'),
        'movie' => array('AVI', 'ASF', 'FLV', 'SWF', 'MOV', 'QT', 'MPG', 'MPEG', 'MP4', 'WMV'),
        'text' => array('txt'),
        'powerpoint' => array('ppsx', 'pptx', 'ppt'),
        'pdf' => array('pdf'),
        'zip' => array('zip', 'rar'),
        'word' => array('doc', 'docx')
    );

    public function addfolder($folder_name, $url, $userid, $type) {

        $this->addfile($folder_name, $url, $userid, $type);

        $this->makefolder($url, $folder_name);
    }

    public function addfile($folder_name, $url, $userid, $type) {

        $data = array(
            'files_url' => $url,
            'file_user_id' => $userid,
            'file_user_update_id' => $userid,
            'file_name' => $folder_name,
            'file_type' => $type
        );

        $this->db->insert('files', $data);

        $id = $this->getfileid($folder_name, $url, $userid, $type);

        $this->addfolderpermission($id);

        $this->createElement($url, $userid, $folder_name, $type, $id);

        $this->updateinfo($userid, $url);


        /*

         */
        if ($type != 'folder') {
            //$foldercontentid = $this->getidfolercontent($url);
            $arr = explode(' | ', $this->getidfolercontent($url));
            /**/
            $foldercontentid = $arr[0];
            $coursename = $arr[1];
            /**/
            $coursesId = $this->getcourseidbyFileId($id);
            $this->load->model('notificationMod');
            //$this->notificationMod->sendnotificationToStudend($foldercontentid,$folder_name,$userid,$coursesId);
            $this->notificationMod->sendnotificationToStudend($foldercontentid, $folder_name, $userid, $coursesId, $coursename);
        }


        /*

         */
    }

    /*
     */

    public function getidfolercontent($url) {
        $arr = explode('/', $url);
        $n = count($arr) - 1;
        $newarr['url'] = '';
        for ($i = 0; $i < $n - 1; $i++) {
            $newarr['url'] .= $arr[$i] . "/";
        }
        $newarr['foldername'] = $arr[$n - 1];
        //return $this->getfolderid($newarr['url'],$newarr['foldername']);
        return $this->getfolderid($newarr['url'], $newarr['foldername']) . " | " . $arr[1];
    }

    /*
     */

    public function createElement($url, $userid, $folder_name, $type, $id) {



        $this->load->model('notificationMod');

        $this->load->model('filesMod');

        // echo "

        //     <div class=\"details-info write\" data-type=\"" . $type . "\" data-id=\"" . $id . "\">

        //         <div class=\"col-sm-4\">

        //             <div class=\"col-sm-1\">

        //                 <i class=\"" . filesMod::$fa[$type] . " \"></i>

        //             </div>

        //             <div class=\"col-sm-10\">

        //                 " . $folder_name . "

        //             </div>

        //         </div>

        //         <div class=\"col-sm-3\">

        //             " . $this->notificationMod->get_name_person($userid) . "

        //         </div>

        //         <div class=\"col-sm-5\">

        //             " . $this->notificationMod->get_name_person($userid) . " - " . $this->getlastupdate($id) . "

        //         </div>

        //     </div>";
    }

    public function getlastupdate($id) {

        $this->db->where('files_id', $id);

        $this->db->select('file_last_update');

        $this->db->from('files');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {

                $last = date('Y-m-d h:i:s A', strtotime($row->file_last_update));
                // $last = $row->file_last_update;
            }
        }

        if (isset($last)) {

            return $last;
        } else {

            return null;
        }
    }

    public function getfileid($folder_name, $url, $userid, $type) {

        $this->db->where('files_url', $url);

        $this->db->where('file_user_id', $userid);

        $this->db->where('file_user_update_id', $userid);

        $this->db->where('file_name', $folder_name);

        $this->db->where('file_type', $type);

        $this->db->select('files_id');

        $this->db->from('files');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {

                $fileid = $row->files_id;
            }
        }

        if (isset($fileid)) {

            return $fileid;
        } else {

            return null;
        }
    }

    public function addfolderpermission($folderid) {

        $data = array(
            'file_id' => $folderid,
            'file_permission_role' => filesMod::$FILE_COURSE_Read_ONLY
        );

        $this->db->insert('file_permission', $data);
    }

    function makefolder($url, $folder_name) {

        $file = $url . $folder_name;

        if (!is_dir($file)) {

            mkdir($file);
        }
    }

    public function updatefolderpermission($folderid, $permission) {

        $this->db->where('file_id', $folderid);

        $data = array('file_permission_role' => $permission);

        $this->db->update('file_permission', $data);
    }

    public function removefile($fileid) {



        $url = $this->geturlfile($fileid);

        $folder_name = $this->getnamefile($fileid);

        $rfile = $url . $folder_name;

        if (is_dir($url . $folder_name)) {

            $this->removefolder($url, $folder_name);

            rmdir($rfile);

            $isdir = 1;
        } else {

            $this->rmfile($url . $folder_name);

            $isdir = 0;
        }

        $this->db->where('files_id', $fileid);

        $this->db->delete('files');

        if ($isdir == 1)
            $this->removeAllData($url . $folder_name);
    }

    public function removeAllData($url) {

        $this->db->like('files_url', $url, 'after');

        $this->db->delete('files');
    }

    public function rmfile($file) {

        unlink($file);
    }

    function removefolder($url, $folder_name) {

        $this->db->like('files_url', $url . $folder_name . "/", 'after');

        $this->db->select('*');

        $this->db->from('files');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            $i = 0;

            foreach ($query->result() as $row) {

                $res[$i] = $row->files_url . $row->file_name;

                $i++;
            }
        }

        if (isset($res)) {

            for ($i = (count($res) - 1); $i >= 0; $i--) {

                if (is_dir($res[$i])) {

                    rmdir($res[$i]);
                } else {

                    $this->rmfile($res[$i]);
                }
            }
        }
    }

    public function geturlfile($fileid) {

        $this->db->where('files_id', $fileid);

        $this->db->select('files_url');

        $this->db->from('files');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {

                $res = $row->files_url;
            }
        }

        if (isset($res)) {

            return $res;
        } else {

            return null;
        }
    }

    public function getnamefile($fileid) {

        $this->db->where('files_id', $fileid);

        $this->db->select('file_name');

        $this->db->from('files');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {

                $res = $row->file_name;
            }
        }

        if (isset($res)) {

            return $res;
        } else {

            return null;
        }
    }

    public function userPermission($folderid, $permission, $userid) {

        if ($this->hasPermission($folderid, $userid)) {

            $this->updateuserpermission($folderid, $permission, $userid);
        } else {

            $this->adduserspermission($folderid, $permission, array($userid));
        }
    }

    public function hasPermission($folderid, $userid) {

        $this->db->where('user_file_id', $folderid);

        $this->db->where('file_users_Id', $userid);

        $this->db->select('*');

        $this->db->from('file_user_permission');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function adduserspermission($folderid, $permission, $userid) {

        foreach ($userid as $value) {

            $data = array(
                'file_user_permission' => $permission,
                'user_file_id' => $folderid,
                'file_users_Id' => $value
            );

            $this->db->insert("file_user_permission", $data);
        }
    }

    public function updateuserpermission($folderid, $permission, $userid) {

        $this->db->where("user_file_id", $folderid);

        $this->db->where("file_users_Id", $userid);

        $data = array('file_user_permission' => $permission);

        $this->db->update("file_user_permission", $data);
    }

    public function savefile($filename, $data, $userid, $url, $type) {

        if (!file_exists($url . $filename)) {

            $file = file_get_contents($data);

            $cont = file_put_contents($url . $filename, $file);

            $this->addfile($filename, $url, $userid, $type);

            return true;
        } else {

            return false;
        }
    }

    public function getfilesinfoinfolder($folderid) {

        $url = $this->geturlfile($folderid);

        $folder_name = $this->getnamefile($folderid);

        $url = $url . $folder_name . "/";

        return $this->getFilesInfoInFolderByUrl($url);
    }

    public function getFilesInfoInFolderByUrl($url) {

        $this->db->where('files_url', $url);

        $this->db->select('*');

        $this->db->from('files');

        $query = $this->db->get();

        $this->load->model('notificationMod');

        if ($query->num_rows() > 0) {

            $i = 0;

            $userid = $this->session->userdata('userid');



            foreach ($query->result() as $row) {

                $fileid = $row->files_id;

                if ($this->hasPermissionToDelete($userid, $fileid)) {

                    $res['name'][$i] = $row->file_name;

                    $res['id'][$i] = $row->files_id;

                    $res['userid'][$i] = $row->file_user_id;

                    $res['username'][$i] = $this->notificationMod->get_name_person($row->file_user_id);

                    $res['updateuserid'][$i] = $row->file_user_update_id;

                    $res['updateusername'][$i] = $this->notificationMod->get_name_person($row->file_user_update_id);


                    $res['lastupdate'][$i] = date('Y-m-d h:i:s A', strtotime($row->file_last_update));
                    //$res['lastupdate'][$i] = $row->file_last_update;

                    $res['type'][$i] = $row->file_type;

                    $res['classreadorwrite'][$i] = "write";

                    $i++;
                } else if ($this->hasPermissionToRead($userid, $fileid)) {

                    $res['name'][$i] = $row->file_name;

                    $res['id'][$i] = $row->files_id;

                    $res['userid'][$i] = $row->file_user_id;

                    $res['username'][$i] = $this->notificationMod->get_name_person($row->file_user_id);

                    $res['updateuserid'][$i] = $row->file_user_update_id;

                    $res['updateusername'][$i] = $this->notificationMod->get_name_person($row->file_user_update_id);

                    $res['lastupdate'][$i] = date('Y-m-d h:i:s A', strtotime($row->file_last_update));

                    //$res['lastupdate'][$i] = $row->file_last_update;

                    $res['type'][$i] = $row->file_type;

                    $res['classreadorwrite'][$i] = "";

                    $i++;
                }
            }
        }

        if (isset($res)) {

            $res['url'][0] = $url;

            return $res;
        } else {

            $res['url'][0] = $url;

            return $res;
        }
    }

    public function getidforallfolder($url) {

        if ($url == "") {

            return -1;
        } else {



            $url = rtrim($url, '/');

            $data = explode('/', $url);

            $folder = $data[count($data) - 1];

            $pos = strrpos($url, $folder);

            $newurl = substr($url, 0, $pos);

            return $this->getfolderid($newurl, $folder) . "|" . $this->getidforallfolder($newurl);
        }
    }

    public function getfolderid($newurl, $folder) {

        $this->db->where('files_url', $newurl);

        $this->db->where('file_name', $folder);

        $this->db->select('files_id');

        $this->db->from('files');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {

                $fileid = $row->files_id;
            }
        }

        if (isset($fileid)) {

            return $fileid;
        } else {

            return null;
        }
    }

    public function FolderisExist($fileID) {

        $this->db->where("files_id", $fileID);

        $this->db->where('file_type', "folder");

        $this->db->from('files');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function FolderisExistinfolder($folder_name, $url) {

        $this->db->where("file_name", $folder_name);

        $this->db->where("files_url", $url);

        $this->db->where('file_type', "folder");

        $this->db->from('files');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function isSystemFolder($folder_name, $url) {

        $file = $url . $folder_name;

        if (is_dir($file)) {

            return true;
        } else {

            return false;
        }
    }

    public function updateinfo($userid, $url) {

        if (!$url == "") {



            $url = rtrim($url, '/');

            $data = explode('/', $url);

            $folder = $data[count($data) - 1];

            $pos = strrpos($url, $folder);

            $newurl = substr($url, 0, $pos);

            $folderid = $this->getfolderid($newurl, $folder);

            $this->updateuserupdate($folderid, $userid);

            $this->updatelastupdate($folderid);

            $this->updateinfo($userid, $newurl);
        }
    }

    public function updateuserupdate($folderid, $userid) {

        $this->db->where("files_id", $folderid);

        $data = array("file_user_update_id" => $userid);

        $this->db->update("files", $data);
    }

    public function updatelastupdate($folderid) {

        date_default_timezone_set('Africa/Cairo');

        $this->db->where("files_id", $folderid);

        $data = array("file_last_update" => date('Y-m-d H-i-s'));

        $this->db->update("files", $data);
    }

    public function hasPermissionToRead($userid, $fileid) {

        if ($this->ToAllRead($fileid))
            return true;

        else if ($this->ToGroupeRead($userid, $fileid))
            return true;

        else if ($this->takepermissionfromownerRead($userid, $fileid))
            return true;
        else
            return false;
    }

    public function hasPermissionToDelete($userid, $fileid) {

        if ($this->profOrInstTeach($userid, $this->getcourseidbyFileId($fileid)))
            return true;

        else if ($this->ownerfile($userid, $fileid))
            return true;

        else if ($this->ToAll($fileid))
            return true;

        else if ($this->ToGroupe($userid, $fileid))
            return true;

        else if ($this->takepermissionfromowner($userid, $fileid))
            return true;
        else
            return false;
    }

    public function ownerfile($userid, $fileid) {

        $this->db->where("file_user_id", $userid);

        $this->db->where("files_id", $fileid);

        $this->db->from('files');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function ToAll($fileid) {

        $this->load->model("filesMod");

        $this->db->where("file_id", $fileid);

        $this->db->where("file_permission_role", filesMod::$FILE_ALL_Read_AND_WRITE);

        $this->db->from('file_permission');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function ToAllRead($fileid) {

        $this->load->model("filesMod");

        $this->db->where("file_id", $fileid);

        $this->db->where("file_permission_role", filesMod::$FILE_ALL_Read_ONLY);

        $this->db->from('file_permission');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function ToGroupe($userid, $fileid) {

        if ($this->ToAllstudentjoinCourse($fileid)) {

            $url = $this->geturlfile($fileid) . $this->getnamefile($fileid);

            $url = explode("/", $url);

            $coursename = $url[1];

            $id = $this->getcourseid($coursename);

            if ($id == false) {

                return false;
            } else {

                if ($this->isJoincourseornot($userid, $id)) {

                    return true;
                } else
                    return false;
            }
        } else
            return false;
    }

    public function ToGroupeRead($userid, $fileid) {

        if ($this->ToAllstudentjoinCourseRead($fileid)) {

            $url = $this->geturlfile($fileid) . $this->getnamefile($fileid);

            $url = explode("/", $url);

            $coursename = $url[1];

            $id = $this->getcourseid($coursename);

            if ($id == false) {

                return false;
            } else {

                if ($this->isJoincourseornot($userid, $id)) {

                    return true;
                } else
                    return false;
            }
        } else
            return false;
    }

    public function ToAllstudentjoinCourse($fileid) {

        $this->load->model("filesMod");

        $this->db->where("file_id", $fileid);

        $this->db->where("file_permission_role", filesMod::$FILE_COURSE_Read_AND_WRITE);

        $this->db->from('file_permission');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function ToAllstudentjoinCourseRead($fileid) {

        $this->load->model("filesMod");

        $this->db->where("file_id", $fileid);

        $this->db->where("file_permission_role", filesMod::$FILE_COURSE_Read_ONLY);

        $this->db->from('file_permission');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function getcourseid($coursename) {

        $this->db->where('c_name', $coursename);

        $this->db->select('*');

        $this->db->from('courses');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            foreach ($query->result() as $row) {

                $id = $row->c_id;
            }
        }

        if (isset($id)) {

            return $id;
        } else {

            return false;
        }
    }

    public function isJoincourseornot($userid, $courseId) {

        $this->db->where('courses_users_ID', $userid);

        $this->db->where('users_courses_ID', $courseId);

        $this->db->from('users_courses');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function takepermissionfromowner($userid, $fileid) {

        $this->load->model("filesMod");

        $this->db->where('file_users_Id', $userid);

        $this->db->where('user_file_id', $fileid);

        $this->db->where('file_user_permission', filesMod::$READ_AND_WRITE);

        $this->db->from('file_user_permission');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function takepermissionfromownerRead($userid, $fileid) {

        $this->load->model("filesMod");

        $this->db->where('file_users_Id', $userid);

        $this->db->where('user_file_id', $fileid);

        $this->db->where('file_user_permission', filesMod::$READ_ONLY);

        $this->db->from('file_user_permission');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function isFolder($fileid) {

        $this->db->where('files_id', $fileid);

        $this->db->where('file_type', "folder");

        $this->db->select('*');

        $this->db->from('files');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            return true;
        } else {

            return false;
        }
    }

    public function renamefile($folderid, $newfoldername) {

        $url = $this->geturlfile($folderid);

        $oldfoldername = $this->getnamefile($folderid);

        $extensions = explode('.', $oldfoldername);

        $extensions = $extensions[count($extensions) - 1];

        $oldurl = $url . $oldfoldername;

        $newurl = $url . $newfoldername . "." . $extensions;

        $this->renamefolderinfiles($oldurl, $newurl);

        $this->db->where('files_id', $folderid);

        $data = array('file_name' => $newfoldername . "." . $extensions);

        $this->db->update('files', $data);
    }

    public function renamefolderinfiles($oldurl, $newurl) {

        rename($oldurl, $newurl);
    }

    public function renamefolder($folderid, $newfoldername) {

        $url = $this->geturlfile($folderid);

        $oldfoldername = $this->getnamefile($folderid);

        $oldurl = $url . $oldfoldername;

        $newurl = $url . $newfoldername;

        $this->renamefolderinfiles($oldurl, $newurl);

        $this->db->where('files_id', $folderid);

        $data = array('file_name' => $newfoldername);

        $this->db->update('files', $data);

        $pos = strlen($url);

        $this->modifiyurls($url, $oldfoldername, $newfoldername, $pos);
    }

    public function modifiyurls($url, $oldfoldername, $newfoldername, $pos) {

        $this->db->like('files_url', $url . $oldfoldername . '/', 'after');

        $this->db->select("*");

        $this->db->from('files');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            $i = 0;

            foreach ($query->result() as $row) {

                $res['id'][$i] = $row->files_id;

                $res['url'][$i] = $row->files_url;

                $i++;
            }

            for ($i = 0; $i < count($res['id']); $i++) {

                $res['url'][$i] = substr($res['url'][$i], 0, $pos) . $newfoldername . substr($res['url'][$i], $pos + strlen($oldfoldername));
            }



            for ($i = 0; $i < count($res['id']); $i++) {

                $this->db->where('files_id', $res['id'][$i]);

                $data = array('files_url' => $res['url'][$i]);

                $this->db->update('files', $data);
            }
        }
    }

    public function get_Names_And_Ids($fileid, $name) {

        $this->load->model("filesMod");

        $this->db->like('name', $name, 'both');

        $this->db->select('*');

        $this->db->from('users');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {

            $i = 0;

            foreach ($query->result() as $row) {

                if (!$this->hasPermissionToDelete($row->id, $fileid)) {

                    echo '<div data-per="' . filesMod::$READ_AND_WRITE . '" data-id="' . $row->id . '" class="showusers col-sm-6">

                            <div class="userimg col-sm-2">

                                <img src="' . base_url() . 'styles/images/users_imgs/profile/' . $row->img . '" width="50" height="50">

                            </div>

                            <div class="username col-sm-8">

                                ' . $row->name . '

                                <div>read and write</div>

                            </div>

                        </div>';

                    $i = $i + 0.5;

                    if (!$this->hasPermissionToRead($row->id, $fileid)) {

                        echo '<div data-per="' . filesMod::$READ_ONLY . '" data-id="' . $row->id . '" class="showusers col-sm-6">

                                    <div class="userimg col-sm-2">

                                        <img src="' . base_url() . 'styles/images/users_imgs/profile/' . $row->img . '" width="50" height="50">

                                    </div>

                                    <div class="username col-sm-8">

                                        ' . $row->name . '

                                        <div>read only</div>

                                    </div>

                                </div>';

                        $i = $i + 0.5;
                    }
                } else if ($this->takepermissionfromowner($row->id, $fileid)) {

                    if (!$this->hasPermissionToRead($row->id, $fileid)) {

                        echo '<div data-per="' . filesMod::$READ_ONLY . '" data-id="' . $row->id . '" class="showusers col-sm-6">

                                    <div class="userimg col-sm-2">

                                        <img src="' . base_url() . 'styles/images/users_imgs/profile/' . $row->img . '" width="50" height="50">

                                    </div>

                                    <div class="username col-sm-8">

                                        ' . $row->name . '

                                        <div>read only</div>

                                    </div>

                                </div>';

                        $i = $i + 0.5;
                    }
                }

                if ($i >= 3) {

                    break;
                }
            }
        }
    }

    public function getmenu($fileid, $name) {
        
    }

    public function profOrInstTeach($usrID, $courseid) {
        if (is_null($courseid))
            return false;
        if ($this->isProfisser($usrID)) {
            if ($this->ProfisserTeachCourse($usrID, $courseid))
                return true;
        }
        else {
            if ($this->isInstructor($usrID))
                if ($this->InstructorTeachCourse($usrID, $courseid))
                    return true;
        }
        return false;
    }

    public function isProfisser($usrID) {
        $this->db->where('id', $usrID);
        //$usertypeid = $this->getidusertype('professor');
        //$this->db->where('usertypeID',$usertypeid);
        $this->db->where('usertypeID', 2);
        $this->db->select('id');
        $this->db->from('users');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function isInstructor($usrID) {
        $this->db->where('id', $usrID);
        //$usertypeid = $this->getidusertype('instructor');
        //$this->db->where('usertypeID',$usertypeid);
        $this->db->where('usertypeID', 3);
        $this->db->select('id');
        $this->db->from('users');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /* public function ProfisserTeachCourse($usrID, $courseid) {
      $query = $QuerySql = "select `profID` from `inst_subjects` where `InstID` = " . $usrID . " and (`sub1_ID` = " . $courseid . " or `sub2_ID` = " . $courseid . " or `sub3_ID` =" . $courseid . ")";
      $this->db->where("(profID = ".$usrID.") and ( sub1_ID=".$courseid". or sub2_ID = ".$courseid." or sub3_ID = ".$courseid." )");
      $this->db->where('profID',$usrID);
      $this->db->or_where('sub1_ID',$courseid);
      $this->db->or_where('sub2_ID',$courseid);
      $this->db->or_where('sub3_ID',$courseid);
      $this->db->select('profID');
      $this->db->from('prof_subjects');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
      return true;
      } else {
      return false;
      }
      }

      public function InstructorTeachCourse($usrID, $courseid) {
      //$query = $QuerySql = "select `InstID` from `inst_subjects` where `InstID` = " . $usrID . " and (`sub1_ID` = " . $courseid . " or `sub2_ID` = " . $courseid . " or `sub3_ID` =" . $courseid . ")";
      $this->db->select('InstID');
      $this->db->from('inst_subjects');
      $this->db->where("`InstID` = " . $usrID . " and (`sub1_ID` = " . $courseid . " or `sub2_ID` = " . $courseid . " or `sub3_ID` =" . $courseid . ")");
      $query = $this->db->get();
      $this->db->where("(InstID = ".$usrID.") and ( sub1_ID=".$courseid". or sub2_ID = ".$courseid." or sub3_ID = ".$courseid." )");
      /*$this->db->where('InstID',$usrID);
      $this->db->or_where('sub1_ID',$courseid);
      $this->db->or_where('sub2_ID',$courseid);
      $this->db->or_where('sub3_ID',$courseid);
      $this->db->select('profID');
      $this->db->from('inst_subjects');
      $query = $this->db->get();
      if ($query->num_rows() > 0) {
      return true;
      } else {
      return false;
      }
      } */

    public function PI($tablename, $col, $checkCol, $usrID, $courseid) {
        $this->db->where($col, $usrID);
        $this->db->where($checkCol, $courseid);
        $this->db->select($col);
        $this->db->from($tablename);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function ProfisserTeachCourse($usrID, $courseid) {
        for ($i = 1; $i <= 3; $i++) {
            $res = $this->PI('prof_subjects', 'profID', 'sub' . $i . '_ID', $usrID, $courseid);
            if ($res) {
                return true;
            }
        }
        return false;
    }

    public function InstructorTeachCourse($usrID, $courseid) {

        for ($i = 1; $i <= 3; $i++) {
            $res = $this->PI('inst_subjects', 'InstID', 'sub' . $i . '_ID', $usrID, $courseid);
            if ($res) {
                return true;
            }
        }
        return false;
    }

    public function getcourseidbyFileId($fId) {
        $url = $this->geturlfile($fId);
        $url = $this->geturlfile($fId);
        $coursename = explode('/', $url);
        if (isset($coursename[1])) {
            return $this->getcourseid($coursename[1]);
        } else {
            $folder_name = $this->getnamefile($fId);
            if ($folder_name == 'materials')
                return null;
            else
                return $this->getcourseid($coursename[1]);
        }
        return null;
    }

    public function getfileidbynameandurl($Fname, $furl = null) {
        if ($furl == null) {
            $furl = 'materials/';
        }
        $this->db->where('files_url', $furl);
        $this->db->where('file_name', $Fname);

        $this->db->select('files_id');
        $this->db->from('files');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $id = $row->files_id;
            }
        }
        if (isset($id))
            return $id;
        else
            return null;
    }

    public function getidusertype($usertype) {
        $this->db->where('type', $usertype);
        $this->db->select('id');
        $this->db->from('user_type');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $id = $row->id;
            }
        }
        if (isset($id))
            return $id;
        else
            return null;
    }

    public function getpermission($files_id) {
        $this->db->where('file_id', $files_id);
        $this->db->select('file_permission_role');
        $this->db->from('file_permission');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $id = $row->file_permission_role;
            }
        }
        if (isset($id))
            return $id;
        else
            return null;
    }

    public function zipFile($zip, $url, $nameFolder, $urlFolder, $userid) {
        if ($urlFolder == "") {
            $zip->addEmptyDir($nameFolder);
        } else {
            $zip->addEmptyDir($urlFolder . $nameFolder);
        }
        $this->db->where('files_url', $url);
        $this->db->where('file_type != ', "folder");

        $this->db->select('files_url');
        $this->db->select('file_name');
        $this->db->select('files_id');
        $this->db->from('files');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if (($this->hasPermissionToDelete($userid, $row->files_id)) || ($this->hasPermissionToRead($userid, $row->files_id))) {
                    $newfile = $urlFolder . $nameFolder . "/" . $row->file_name;
                    $FILE_Name =$row->files_url ."/".$row->file_name;
                    $zip->addfile($FILE_Name,$newfile);
                }
            }
        }
        $this->db->where('files_url', $url);
        $this->db->where('file_type ', "folder");
        $this->db->select('file_name');
        $this->db->select('files_id');
        $this->db->from('files');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if (($this->hasPermissionToDelete($userid, $row->files_id)) || ($this->hasPermissionToRead($userid, $row->files_id))) {
                    if ($urlFolder == "") {
                        $urlFolder = $row->nameFolder . "/";
                    } else {
                        $urlFolder = $urlFolder . $row->nameFolder . "/";
                    }
                    // $this->zipFile($zip, $url, $row->file_name, $urlFolder);
                    $this->zipFile($zip, $url, $row->nameFolder, $urlFolder,$userid);
                    
                }
            }
        }
    }

    public function downloadFolder($Fid, $userid) {
        $zip = new ZipArchive;
        $url = $this->geturlfile($Fid);
        $name = $this->getnamefile($Fid);
        $name = explode(".", $name);
        $file = $url . $name[0] . ".zip";
        file_put_contents($file, null);
        if ($zip->open($file) === TRUE) {
            $url = $url . $name[0] . "/";
            $urlFile = $name[0] . "/";
            $this->zipFile($zip, $url, $name[0], "", $userid);
            $zip->close();
            return $file;
        }
    }

}