<?php



class QaMod extends CI_Model {



	public function loadQuestions($courseid){

		

		$query = $this->db->get_where('questions_answers' , array('courseID' => $courseid));

		if($query->num_rows() > 0){

			return $query->result_array();

		}else{

			return false;

		}

	}

	public function get_question_and_answer($limit,$courseid) {
        $this->db->limit(10, ($limit - 1) * 10);
        $this->db->from('questions_answers');
        $this->db->where('courseID' , $courseid);
        $this->db->select('id');
        $this->db->select('question');
        $this->db->select('answer');
        $this->db->select('type');
        $this->db->select('code_language');
        return $this->db->get();
    }

	public function get_num_pages($courseid) {
        $this->db->from('questions_answers');
        $this->db->where('courseID',$courseid);
        $this->db->select('*');
        $res = $this->db->get();
        return ceil($res->num_rows() / 10);
    }

    public function get_new_question($limit,$courseid) {
        $result = $this->get_question_and_answer($limit,$courseid);
        $codeNames = $this->codes_names();
        if($result->num_rows() > 0){
            $count = 1;
	        foreach ($result->result() as $row) {
	            if($row->type == 1){

                    echo'
                    <div class="card">
                        <div class="card-header" id="headingOne'.$count.'">


                            <h5 class="mb-0">
                                <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseOne'.$count.'" aria-expanded="true" aria-controls="collapseOne'.$count.'">

                                    '.$row->question.'

                                </button>
                            </h5>

                        </div>

                        <div class="collapse" id="collapseOne'.$count.'" aria-labelledby="headingOne'.$count.'" data-parent="#accordionExample">

                            <div class="card-body">
                                <p>'.$row->answer.'</p>
                            </div>

                        </div>
                    </div>
                    <script>';

                if($count == 1){
                    echo'
                    $("#collapseOne1").attr("aria-expanded",true);
                    $("#collapseOne1").addClass("show");';
                }
                echo'
                    </script>
                    <script>
                        $("button").on(\'click\',function(){
                            $("body").css("height",100%);
                        });
                    </script>

                ';
                $count++;

	            }else if($row->type == 2){

	              echo'
                    <div class="card">

                        <div class="card-header" id="headingOne'.$count.'">

                          <h5 class="mb-0">
                                <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseOne'.$count.'" aria-expanded="true" aria-controls="collapseOne'.$count.'">

                                    '.$row->question.'

                                </button>
                          </h5>

                          
                        </div>

                        <div class="collapse" id="collapseOne'.$count.'" aria-labelledby="headingOne'.$count.'" data-parent="#accordionExample">

                            <div class="card-body" id="editor'.$count.'">

                              <xmp>'.$row->answer.'</xmp>

                            </div>
                        </div>

                        
                    </div>
                        <script>
                            $("button").on(\'click\',function(){
                                $("body").css("height",100%);
                            });
                        </script>
	                  <script src="<?php echo base_url(); ?>/ace-builds-master/src-noconflict/ace.js" type="text/javascript" charset="utf-8"></script>';

                      foreach($codeNames as $cn){
                        if($row->code_language == $cn['id']){
                            echo '<script>
                                var editor = ace.edit("editor'.$count.'");
                                editor.setTheme("ace/theme/monokai");
                                editor.session.setMode("ace/mode/'.$cn['name'].'");
                                editor.setOptions({
                                    maxLines: Infinity
                                });


                                
                            </script>';
                        }
                    }

                    echo '<script>';

                    if($count == 1){
                        echo'
                        $("#collapseOne1").attr("aria-expanded",true);
                        $("#collapseOne1").addClass("show");';
                    }
                    echo'</script>';

                    $count++;

	            }

          	}

        }else{

          echo '<div class="card" style="text-align:center">

                    <div class="card-header">

                        <h2 style="font-family:\'Raleway\'; ">No Questions</h2>

                      

                    </div>

                </div>';
        }
    }

    public function codes_names(){
        $this->db->select("id , name");
        $this->db->from("questions_answers_code");
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $names = $query->result_array();
            return $names;
        }
    }


}