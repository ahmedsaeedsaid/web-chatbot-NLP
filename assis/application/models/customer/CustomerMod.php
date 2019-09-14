<?php

$url = rtrim(__Dir__, 'models\customer');
require_once $url . '/classes/customer.php';

class customerMod extends CI_Model {
    private $html;
    private $childs;
    
    public function __construct(){
        $html = '';
    }
    
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
        $this->db->select('*');
        $this->db->from('scenarios');
        $res = $this->db->get();
        if($res){
            return $res->result_array();
        }
        return false;
    }

    public function saveScenario($data, $scenario_id, $action) {
        if($action == 'add'){
            $data['companyId'] = $this->session->userdata('assis_companyid');
            $this->db->insert('scenarios', $data);
        } else {
            $this->db->where('id', $scenario_id);
            $this->db->where('companyId', $this->session->userdata('assis_companyid'));
            $this->db->update('scenarios', $data);
        }
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
    
    public function deleteScenario ($id) {
        $this->db->where('id', $id);
        $this->db->delete('scenarios');
        $this->db->where('scenario', $id);
        $this->db->delete('optimal_bot_q_a');
    }
    
    public function deleteQA ($parent, $current_question_id, $childs_question_ids) {
        $this->db->where('id', $current_question_id);
        $this->db->delete('optimal_bot_q_a');
        foreach($childs_question_ids as $child){
            $data = array(
                'parent' => $parent
            );
            $this->db->where('id', $child);
            $this->db->update('optimal_bot_q_a', $data);
        }
    }
    
    public function saveQASC ($Question, $scenario, $tags, $action, $question_id) {
        if($action == 'update'){
            $this->db->where('id', $question_id);
            $this->db->where('client_id', $this->session->userdata('assis_companyid'));
            $this->db->update('optimal_bot_q_a', $Question);
            $this->insertQuestionTags($tags, $scenario, $question_id);
        } else {
            $Question["client_id"] = $this->session->userdata('assis_companyid');
            $this->db->insert('optimal_bot_q_a', $Question);
            $this->insertQuestionTags($tags, $scenario, $this->db->insert_id());
        }
    }
    
    private function insertQuestionTags($tags, $scenario, $question_id) {
        $question_tags_ids = array();
        foreach($tags as $tag){
            $this->db->select('id');
            $this->db->from('q_a_tags');
            $this->db->where('tag', $tag);
            $res = $this->db->get();
            $tag_id = 0;
            if(!$res->num_rows()){
                $this->db->insert('q_a_tags', array("tag" => $tag));
                $tag_id = $this->db->insert_id();
            } else {
                $tag_row = $res->row();
                $tag_id = $tag_row->id;
            }
            $question_tags_ids[] = $tag_id;
        }
        $this->db->delete('optimal_bot_tags', array('q_a_id' => $question_id));
        $question_tags_data = array();
        foreach($question_tags_ids as $question_tag){
            $data = array();
            $data['tag_id'] = $question_tag;
            $data['q_a_id'] = $question_id;
            $question_tags_data[] = $data;
        }
        $this->db->insert_batch('optimal_bot_tags', $question_tags_data);
    }
    
    private function createRequiredTables($db_forge) {
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

    public function getQASC()
    {
        $html = '';
        $this->db->select('*');
        $this->db->from('scenarios');
        $this->db->where('companyId', $this->session->userdata('assis_companyid'));
        $res = $this->db->get();
        $scenarios = $res->result_array();
        foreach($scenarios as $scenario){
            $this->db->select('*');
            $this->db->from('optimal_bot_q_a');
            $this->db->where('parent', 0);
            $this->db->where('client_id', $this->session->userdata('assis_companyid'));
            $this->db->where('scenario', $scenario['id']);
            $res = $this->db->get();
            $tags = [];
            $questions = $res->result_array();
            $scenario_childs = '';
            foreach($questions as $question){
                $question_childs = $this->getChildsQA($question['id'], $scenario['id'], 1);
                if($question_childs){
                    $scenario_childs .= '{"text":" '. $question['question'] . '", "nodes":[' . $question_childs . ' ], "question_id":' . $question['id'] . ', "is_scenario":0, "scenario_id":' . $scenario['id'] . '},';
                } else {
                    $scenario_childs .= '{"text":" '. $question['question'] . '", "question_id":' . $question['id'] . ', "is_scenario":0, "scenario_id":' . $scenario['id'] . '},';
                }
            }
            $html .= '{"text":" '. $scenario['name'] . '", "nodes":[' . $scenario_childs . ' ], "scenario_id":' . $scenario['id'] . ', "is_scenario":1},';
        }
        return $html;
    }
    
    private function getChildsQA($question_id, $scenario, $reset){
        if($reset){
            $this->html = '';
            $this->childs = array();
        }
        $this->db->select('*');
        $this->db->from('optimal_bot_q_a');
        $this->db->where('parent', $question_id);
        $this->db->where('scenario', $scenario);
        $this->db->where('client_id', $this->session->userdata('assis_companyid'));
        $res = $this->db->get();
        $questions = $res->result_array();
        $size = count($questions);
        if($size){
            $i = 0;
            foreach($questions as $question){
                $size-=1;
                $this->db->select('*');
                $this->db->from('optimal_bot_q_a');
                $this->db->where('parent', $question['id']);
                $this->db->where('scenario', $scenario);
                $this->db->where('client_id', $this->session->userdata('assis_companyid'));
                $res = $this->db->get();
                $childs = $res->result_array();
                if(count($childs)){
                    $this->html .= '{
                    "text": "' . $question['question'] . '",
                    "question_id": ' . $question['id'] . ',
                    "is_scenario": 0,
                    "scenario_id": ' . $scenario . ',
                    "nodes":[';
                } else {
                    $this->html .= '{
                    "text": "' . $question['question'] . '",
                    "question_id": ' . $question['id'] . ',
                    "is_scenario": 0,
                    "scenario_id": ' . $scenario . ',
                    ';
                    $this->html .= '},';
                }
                $this->getChildsQA($question['id'], $scenario, 0);
                if($i == count($childs)-1){
                    if(count($childs)){
                        $this->html .= ']}';
                    }
                } else {
                    if(count($childs)){
                        $this->html .= ']},';
                    }
                }
                $i += 1;
            }
            return $this->html;
        }
        return '';
    }
    
    public function getQA($question_id)
    {
        $question = array();
        $this->db->select('*');
        $this->db->from('optimal_bot_q_a');
        $this->db->where('id', $question_id);
        $this->db->where('client_id', $this->session->userdata('assis_companyid'));
        $res = $this->db->get();
        $question = $res->row();
        $tags = [];
        $this->db->select('q_a_tags.tag');
        $this->db->from('optimal_bot_tags');
        $this->db->join('q_a_tags', 'q_a_tags.id=optimal_bot_tags.tag_id');
        $this->db->where('optimal_bot_tags.q_a_id', $question->id);
        $res = $this->db->get();
        $tags_array = $res->result_array();
        $tags = array();
        foreach($tags_array as $tag){
            $tags[] = $tag['tag'];
        }
        $question->tags = $tags;
        return $question;
    }
    
    public function getLogs()
    {
        $this->db->select('session_id');
        $this->db->from('logs');
        $this->db->where('companyId', $this->session->userdata('assis_companyid'));
        $this->db->group_by('session_id');
        $res = $this->db->get();
        return $res->result_array();
    }
    
    public function getLogDetails($session_id)
    {
        $this->db->select('*');
        $this->db->from('logs');
        $this->db->where('session_id', $session_id);
        $this->db->where('companyId', $this->session->userdata('assis_companyid'));
        $res = $this->db->get();
        return $res->result_array();
    }

}
