<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class FaqMod extends CI_Model {

    public function Insert_Question($subject, $Correct_Answer) {
        $Question_data = array(
            "Question_subject" => $subject,
            "Question_Correct_Answer" => $Correct_Answer
        );
        $this->db->insert('faq_question', $Question_data);
    }

    public function Delete_Question($id) {
        $this->db->where('Question_ID', $id);
        $this->db->delete('faq_question');
    }

    public function Update_Subject_Question($id, $ques_subject) {
        $this->db->where('Question_ID', $id);
        $data = array("Question_subject" => $ques_subject);
        $this->db->update('faq_question', $data);
    }

    public function get_question_and_answer($limit) {
        $this->db->limit(10, ($limit - 1) * 10);
        $this->db->from('faq_question');
        $this->db->select('Question_subject');
        $this->db->select('Question_Correct_Answer');
        return $this->db->get();
    }

    public function get_num_pages() {
        $this->db->from('faq_question');
        $this->db->select('*');
        $res = $this->db->get();
        return ceil($res->num_rows() / 10);
    }

    public function get_new_question($limit) {
        $result = $this->get_question_and_answer($limit);
        $count = 1;
        foreach ($result->result() as $row) {
            echo '
            <div class="card">
                <div class="card-header" id="headingOne'.$count.'">


                    <h5 class="mb-0">
                        <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseOne'.$count.'" aria-expanded="true" aria-controls="collapseOne'.$count.'">

                            '. $row->Question_subject .'

                        </button>
                    </h5>

                </div>

                <div class="collapse" id="collapseOne'.$count.'" aria-labelledby="headingOne'.$count.'" data-parent="#accordionExample">

                    <div class="card-body">
                        <p>'.$row->Question_Correct_Answer.'</p>
                    </div>

                </div>
            </div>

			';
            $count++;
        }
    }

}
