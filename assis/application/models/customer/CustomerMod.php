<?php

$url = rtrim(__Dir__, 'models\customer');
require_once $url . '/classes/customer.php';

class customerMod extends CI_Model {
    private function ConencttoClientDB()
    {
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
        return array($db,$db_forge);
    }

    public function getAllScenarios() {
        $db = $this->ConencttoClientDB()[0];
        $db->select('*');
        $db->from('scenarios');
        $res = $db->get();
        return $res->result_array();
    }

    public function addScenario($data) {
        $db = $this->ConencttoClientDB()[0];
        if($db->table_exists('scenarios'))
        {
            $db->insert('scenarios', $data);
        }
        
    }

    public function toggleActive($active, $id) {
        $db = $this->ConencttoClientDB()[0];
        $db->where('id', $id);
        $db->update('scenarios', array("active" => $active));
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
    
    public function saveQASC ($Questions_generated,$scenario) {
        $dbData = $this->ConencttoClientDB();
        $db = $dbData[0];
        $db_forge = $dbData[1];
        $last_id = 0;
        // define table fields
        if($db->table_exists('optimal_bot_q_a'))
        {
            
            $db->delete('optimal_bot_q_a', array('scenario' => $scenario)); 
        }
        else
        {
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
        }
        $last_row = $db->order_by('id',"desc")
            ->limit(1)
            ->get('optimal_bot_q_a')
            ->row();

        if($last_row)
        {
            $last_id = $last_row->id;
        }
        for($i=0;$i<count($Questions_generated) ;$i++ )
        {
            $Questions_generated[$i]['id']+=$last_id;
            $Questions_generated[$i]['parent']+=$last_id;
        }
        $db->insert_batch('optimal_bot_q_a', $Questions_generated); 
    }

    public function getQASC($scenario_id)
    {
        $dbData = $this->ConencttoClientDB();
        $db = $dbData[0];
        $db_forge = $dbData[1];
        if($db->table_exists('optimal_bot_q_a'))
        {
            
            $db->select('*');
            $db->from('optimal_bot_q_a');
            $db->where('scenario', $scenario_id);
            $res = $db->get();
            $result = $res->result_array();
            return $result;
        }
        return array();
        
        
    }

}
