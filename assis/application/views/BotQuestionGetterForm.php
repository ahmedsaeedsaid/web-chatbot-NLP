<link href="<?php echo base_url(); ?>styles/css/BotQuestionGetterForm.css" rel="stylesheet" />
<div id="wrapper">
    <div id="page-wrapper">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Scenario Question &amp; Answer Pairs <small>Q&amp;A List</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li class="active">
                            <i class="fa fa-dashboard"></i> Dashboard / <i class="fa fa-book"></i> Q&amp;A
                        </li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-8">
                        <table class="table table-responsive table-hover table-bordered display" width="100%" cellspacing="0" id="questionsTable">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Question</th>
                                    <th>Answer</th>
                                    <th>Flow Question</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php for($i=0;$i<count($QAs);$i++){ ?>
                                    <tr>
                                        <td><?=($i+1)?></td>
                                        <td><?=$QAs[$i]['question']?></td>
                                        <td><?=$QAs[$i]['answer']?></td>
                                        <td><?php
                                                if($QAs[$i]['parent']!='0')
                                                   echo $QAs[$i]['parent']-$QAs[0]['id']+1;
                                                else
                                                   echo '0';
                                            ?></td>
                                        <td><button  class="btn btn-primary btn-xs Edit-row">Edit</button> <button  class="btn btn-danger btn-xs Delete-row" >Delete</button></td>
                                    </tr>

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-4 ">
                        <div class="form-group">
                            <label for="question">Question:</label>
                            <textarea name="question" id="question" class="form-control" style="resize: none;    height: 90px;"></textarea>
                            <!--<input type="question" class="form-control" id="question">-->
                        </div>

                        <div class="form-group">
                            <label for="answer">Answer:</label>
                            <textarea name="answer" id="answer" class="form-control" style="resize: none;    height: 90px;"></textarea>
                            <!--<input type="answer" class="form-control" id="answer">-->
                        </div>

                        <div class="form-group">
                            <label for="parent_question">Answer:</label>
                            <select type="parent_question" class="form-control" id="parent_question" style="height: 35px;">
                                <option value="0">primary Question</option>
                            </select>
                        </div>

                        <div class="col-lg-12 text-center">
                            <button id="addRow" class="btn btn-default">Add Question</button>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <button id="submit_question" class="btn btn-primary">Submit Question</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        redrowSelect();
        $('#parent_question').select2()
            .on('select2-open', function() {

                // however much room you determine you need to prevent jumping
                var requireHeight = 600;
                var viewportBottom = $(window).scrollTop() + $(window).height();

                // figure out if we need to make changes
                if (viewportBottom < requireHeight) 
                {           
                    // determine how much padding we should add (via marginBottom)
                    var marginBottom = requireHeight - viewportBottom;

                    // adding padding so we can scroll down
                    $(".aLwrElmntOrCntntWrppr").css("marginBottom", marginBottom + "px");

                    // animate to just above the select2, now with plenty of room below
                    $('html, body').animate({
                        scrollTop: $("#mySelect2").offset().top - 10
                    }, 1000);
                }
            });
        var editing_SL = "1";

        function refreshIndexes() {
            $("#questionsTable tbody tr td:first-child").each(function(index) {
                if ($(this).text() != "No data available in table")
                    $(this).text(index + 1);
            });
        }
        function refreshParents() {
            $("#questionsTable tbody tr td:nth-child(4)").each(function(index) {
                if ($(this).text() != "0")
                    $(this).text(parseInt($(this).text()) + 1);
            });
        }

        function redrowSelect() {
            $("#parent_question").html("");
            $("#parent_question").append('<option value="0">primary Question</option>');
            $("#questionsTable tbody tr td:first-child").each(function(index) {
                if ($(this).text() != "No data available in table") {
                    $("#parent_question").append('<option value="' + (index + 1) + '">' + (index + 1) + ' - ' + $(this).parent().children(":nth-child(2)").text() + '</option>');
                }
            });
        }

        var myTable = $('#questionsTable').DataTable({
            "columnDefs": [{
                "searchable": false,
                "targets": 3
            }, ]
        });

        $('#addRow').on('click', function() {
            var addRowText = $('#addRow').text();
            if (addRowText == "Add Question") {
                //refreshParents();
                var Question_value = $("#question").val().trim();
                var Answer_value = $("#answer").val().trim();
                var parent_question = $("#parent_question").val();
                console.log(Question_value);
                if (Question_value != "" && Answer_value != "") {
                    myTable.row.add([
                        "",
                        Question_value,
                        Answer_value,
                        parent_question,
                        '<button  class="btn btn-primary btn-xs Edit-row">Edit</button> <button  class="btn btn-danger btn-xs Delete-row" >Delete</button>  '
                    ]).draw(false);
                    refreshIndexes();
                    redrowSelect();
                    $("#question").val("");
                    $("#answer").val("");
                }
            } else {
                var Question_value = $("#question").val().trim();
                var Answer_value = $("#answer").val().trim();
                var parent_question = $("#parent_question").val();
                if (Question_value != "" && Answer_value != "") {
                    var row_editing = $("#questionsTable tbody tr:nth-child(" + editing_SL + ")");
                    row_editing.children(":nth-child(2)").text(Question_value);
                    row_editing.children(":nth-child(3)").text(Answer_value);
                    if (parent_question != editing_SL)
                        row_editing.children(":nth-child(4)").text(parent_question);
                    $("#question").val("");
                    $("#answer").val("");
                    $("#parent_question").val("0");
                }
                $('#addRow').text("Add Question");
            }
        });

        // Automatically remove a row of data
        $("#questionsTable").on('click', '.Delete-row', function() {
            var addRowText = $('#addRow').text();
            if (addRowText == "Add Question") {
                var removel_SL = $(this).parent().parent().children(":first").text();
                myTable
                    .row($(this).parent().parent())
                    .remove()
                    .draw();
                refreshIndexes();
                $("#questionsTable tbody tr td:nth-child(4)").each(function(index) {
                    if (removel_SL == $(this).text()) {
                        $(this).text("0");

                    } else if ($(this).text() > removel_SL) {
                        current_value = $(this).text();
                        $(this).text(current_value - 1);
                    }
                });
                redrowSelect();
            } else {
                Swal.fire(
                    'Failed!',
                    'can\'t delete any Question when editing',
                    'error'
                );
            }
        });

        $("#questionsTable").on('click', '.Edit-row', function() {
            editing_SL = $(this).parent().parent().children(":first").text();
            var Question_value = $(this).parent().parent().children(":nth-child(2)").text();
            var Answer_value = $(this).parent().parent().children(":nth-child(3)").text();
            var parent_question = $(this).parent().parent().children(":nth-child(4)").text();
            $("#question").val(Question_value);
            $("#answer").val(Answer_value);
            $("#parent_question").val(parent_question);
            $('#addRow').text("Update Question");
        });

        $('#submit_question').on('click', function() {
            var Questions_generated = [];
            $("#questionsTable tbody tr td:first-child").each(function(index) {
                if ($(this).text() != "No data available in table") {
                    var Question_ob = {
                        id: index + 1,
                        Question: $(this).parent().children(":nth-child(2)").text(),
                        Answer: $(this).parent().children(":nth-child(3)").text(),
                        parent: $(this).parent().children(":nth-child(4)").text(),
                        scenario: <?= $scenario_id ?>
                    }
                    Questions_generated.push(Question_ob);
                }
            });
            $.ajax({
                url: "<?= base_url("customer/saveQASC") ?>",
                type: "POST",
                data: {
                    Questions_generated: Questions_generated,
                    scenario: <?= $scenario_id ?>,
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                },
                success: function(res) {
                    console.log(res);
                    Swal.fire(
                        'Success!',
                        'Questions Saved Successfully!',
                        'success'
                    );
                    setTimeout(function(){
                        window.location = "<?= base_url("customer/scenariosList") ?>";
                     }, 3000);
                }
            });
        });
    });

</script>
