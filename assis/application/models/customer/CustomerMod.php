<?php

$url = rtrim(__Dir__, 'models\customer');
require_once $url . '/classes/customer.php';

class customerMod extends CI_Model {

    public function getAllScenarios() {
        $this->db->select('*');
        $this->db->from('scenarios');
        $res = $this->db->get();
        return $res->result_array();
    }

    public function addScenario($data) {
        $this->db->insert('scenarios', $data);
    }

    public function toggleActive($active, $id) {
        $this->db->where('id', $id);
        $this->db->update('scenarios', array("active" => $active));
    }

    public function finishedTrainingFirstTime($id) {
        $this->db->where('id', $id);
        $this->db->update('company', array("first_train" => 1));
    }

    public function checkIfTrainedFirstTime($id) {
        $this->db->select('first_train');
        $this->db->from('company');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $res = $query->row();
        if($res->first_train){
            return true;
        }
        return false;
    }
    
    public function saveQASC ($Questions_generated) {
        $this->load->model('subscribeFormMod');
        $company = $this->subscribeFormMod->getCompanyById($this->session->userdata('assis_companyid'));
        // DB Config
        $config = array(
            "hostname" => $company->db_server,
            "username" => $company->db_username,
            "password" => $company->db_password,
            "database" => $company->db_name,
            "dbdriver" => $company->db_driver,
            "db_debug" => false
        );
        $db = @$this->load->database($config, TRUE);
        $db_forge = $this->load->dbforge($db, TRUE);
        // define table fields
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'question' => array(
                'type' => 'TEXT'
            ),
            'answer' => array(
                'type' => 'TEXT'
            ),
            'parent' => array(
                'type' => 'INT',
                'constraint' => 9
            ),
            'scenario' => array(
                'type' => 'INT',
                'constraint' => 9
            )
        );
        $db_forge->add_field($fields);
        // define primary key
        $db_forge->add_key('id', TRUE);
        // create table
        $db_forge->create_table('optimal_bot_q_a');
        $db->insert_batch('optimal_bot_q_a', $Questions_generated); 
    }

}
