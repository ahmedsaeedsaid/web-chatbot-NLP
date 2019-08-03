<?php

$url = rtrim(__Dir__, 'models\customer');
require_once $url . '/classes/customer.php';

class LogMod extends CI_Model {

    public function authenticateCustomer(Customer_obj $cust) {
        // Prepare the query
        $query = $this->db->get_where('client', array('email' => $cust->email, 'password' => md5($cust->password)));
        if ($query->num_rows() > 0) {
            // user info object
            $usr = $query->row();
            if ($usr->active) {
                $query = $this->db->get_where('company', array('client_id' => $usr->id));
                $com = $query->row();
                $data['assis_customerid'] = $usr->id;
                $data['assis_customername'] = $usr->name;
                $data['assis_customeremail'] = $usr->email;
                $data['assis_companyid'] = $com->id;
                $this->session->set_userdata($data);
                return true;
            }
        }
        return false;
    }

}
