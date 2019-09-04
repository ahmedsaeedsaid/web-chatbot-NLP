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
        if($res){
            return $res->result_array();
        }
        return false;
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
    
    public function createScenariosTable (){
        $dbData = $this->ConencttoClientDB();
        $db = $dbData[0];
        $db_forge = $dbData[1];
        if(!$db->table_exists('scenarios')){
            $fields = array(
                'id' => array(
                    'type' => 'INT',
                    'constraint' => 9,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
                ),
                'name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 255
                ),
                'companyId' => array(
                    'type' => 'INT',
                    'constraint' => 9
                ),
                'active' => array(
                    'type' => 'INT',
                    'constraint' => 9,
                    'default' => 1
                )
            );
            $db_forge->add_field($fields);
            // define primary key
            $db_forge->add_key('id', TRUE);
            // create table
            $db_forge->create_table('scenarios');
        }
    }
    
    public function saveQASC ($Questions_generated, $scenario, $tags) {
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
            $this->createTagsTables($db_forge);
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
        $this->insertQuestionTags($db, $tags, $scenario);
    }
    
    private function insertQuestionTags($db, $tags, $scenario) {
        $all_question_tags_ids = array();
        foreach($tags as $tag){
            $question_tags_ids = array();
            foreach($tag as $oneTag){
                $db->select('id');
                $db->from('q_a_tags');
                $db->like('tag', $oneTag, 'both');
                $res = $db->get();
                $tag_id = 0;
                if(!$res->num_rows()){
                    $db->insert('q_a_tags', array("tag" => $oneTag));
                    $tag_id = $db->insert_id();
                } else {
                    $tag_row = $res->row();
                    $tag_id = $tag_row->id;
                }
                $question_tags_ids[] = $tag_id;
            }
            $all_question_tags_ids[] = $question_tags_ids;
        }
        // Get Newly inserted Ids of Q&A pairs
        $db->select('id');
        $db->from('optimal_bot_q_a');
        $db->where('scenario', $scenario);
        $res = $db->get();
        $result = $res->result_array();
        for($i = 0 ; $i < count($result) ; $i++){
            $db->delete('optimal_bot_tags', array('q_a_id' => $result[$i]['id']));
            // Loop through question 1 tags to add them
            $question_tags_data = array();
            foreach($all_question_tags_ids[$i] as $question_tag){
                $data = array();
                $data['tag_id'] = $question_tag;
                $data['q_a_id'] = $result[$i]['id'];
                $question_tags_data[] = $data;
            }
            $db->insert_batch('optimal_bot_tags', $question_tags_data);
        }
    }
    
    private function createTagsTables($db_forge) {
        // Creating optimal_bot_q_a
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
        
        // Creating optimal_bot_tags
        $fields = array(
            'tag_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE
            ),
            'q_a_id' => array(
                'type' => 'INT',
                'unsigned' => TRUE,
            )
        );
        $db_forge->add_field($fields);
        // define primary key
        $db_forge->add_key('tag_id', TRUE);
        $db_forge->add_key('q_a_id', TRUE);
        // create table
        $db_forge->create_table('optimal_bot_tags');
        
        // Creating q_a_tags
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 9,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'tag' => array(
                'type' => 'VARCHAR',
                'constraint' => 50
            )
        );
        $db_forge->add_field($fields);
        // define primary key
        $db_forge->add_key('id', TRUE);
        // create table
        $db_forge->create_table('q_a_tags');
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
            $questions = $res->result_array();
            for($i = 0 ; $i < count($questions) ; $i++){
                $db->select('q_a_tags.tag');
                $db->from('optimal_bot_tags');
                $db->join('q_a_tags', 'q_a_tags.id=optimal_bot_tags.tag_id');
                $db->where('optimal_bot_tags.q_a_id', $questions[$i]['id']);
                $res = $db->get();
                $tags_array = $res->result_array();
                $tags = array();
                foreach($tags_array as $tag){
                    $tags[] = $tag['tag'];
                }
                $questions[$i]['tags'] = $tags;
            }
            return $questions;
        }
        return array();
        
        
    }

}
