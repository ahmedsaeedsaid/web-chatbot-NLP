<meta name="optimal-bot-verification" content="<?= $token ?>" />
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
                                    <th>Tags</th>
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
                                    <td>
                                        <?php foreach($QAs[$i]['tags'] as $tag){ ?>
                                        <span class='tag-span badge badge-info'><?= $tag ?></span>
                                        <?php } ?>
                                    </td>
                                    <td><button class="btn btn-primary btn-xs Edit-row">Edit</button> <button class="btn btn-danger btn-xs Delete-row">Delete</button></td>
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
                            <label for="answer">Tags:</label>
                            <input type="text" class="form-control" id="tags" name="tags" placeholder="Add a tag"><br />
                            <div id="tags-textarea" class="form-control">
                            </div>
                            <!--<input type="answer" class="form-control" id="answer">-->
                        </div>

                        <div class="form-group">
                            <label for="parent_question">Parent:</label>
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
                if (viewportBottom < requireHeight) {
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
            "ordering": false,
            "columnDefs": [{
                "searchable": false,
                "targets": 3
            }, ]
        });

        $('#addRow').on('click', function() {
            $('#addRow').attr('disabled', true);
            var addRowText = $('#addRow').text();
            if (addRowText == "Add Question") {
                //refreshParents();
                var Question_value = $("#question").val().trim();
                var Answer_value = $("#answer").val().trim();
                var parent_question = $("#parent_question").val();
                // Get tags
                var tags = $("#tags-textarea").children();
                // Empty tags area
                $("#tags-textarea").empty();
                var length = tags.length;
                var tags_html = "";
                for (var i = 0; i < length; i++) {
                    $(tags[i]).children('.remove-tag').remove();
                    tags_html += $(tags[i])[0].outerHTML;
                }
                // Check for Meta Tag
                if (document.querySelector("meta[name=optimal-bot-verification]")) {
                    var token = document.querySelector("meta[name=optimal-bot-verification]").getAttribute("content");
                    var param = JSON.stringify({
                        name: 'suggestionTags',
                        param: {
                            //statement: Question_value
                            statement: Question_value + " " + Answer_value
                        }
                    });
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "https://localhost:5002/",
                        data: param,
                        headers: {
                            'Authorization': "Bearer " + token,
                            'Content-Type': 'application/json',
                        },
                        success: function(data) {
                            if ('error' in data) {
                                document.write(data.error.message);
                                return;
                            }
                            var tags = data.response.result.tags;
                            tags.forEach(function(tag){
                                tags_html += "<span class='tag-span badge badge-info'>" + tag + "</span>";
                            });
                            if (Question_value != "" && Answer_value != "") {
                                var rowNode = myTable.row.add([
                                    "",
                                    Question_value,
                                    Answer_value,
                                    parent_question,
                                    tags_html,
                                    '<button  class="btn btn-primary btn-xs Edit-row">Edit</button> <button  class="btn btn-danger btn-xs Delete-row" >Delete</button>  '
                                ]).draw(false).node();
                                $(rowNode)
                                    .css('color', 'red')
                                    .animate({
                                        color: '#5a5a5a'
                                    }, 2000);
                                refreshIndexes();
                                redrowSelect();
                                $("#question").val("");
                                $("#answer").val("");
                                $("#tags").val("");
                            }
                            $('#addRow').attr('disabled', false);
                        }
                    });
                } else {
                    document.write("Forbidden, Access is denied!");
                    return;
                }
            } else {
                var Question_value = $("#question").val().trim();
                var Answer_value = $("#answer").val().trim();
                var parent_question = $("#parent_question").val();
                // Get tags
                var tags = $("#tags-textarea").children();
                // Empty tags area
                $("#tags-textarea").empty();
                var length = tags.length;
                var tags_html = "";
                for (var i = 0; i < length; i++) {
                    $tags = $(tags[i]).clone();
                    $tags.children('.remove-tag').remove();
                    tags_html += $tags[0].outerHTML;
                }
                if (Question_value != "" && Answer_value != "") {
                    var row_editing = $("#questionsTable tbody tr:nth-child(" + editing_SL + ")");
                    row_editing.children(":nth-child(2)").text(Question_value);
                    row_editing.children(":nth-child(3)").text(Answer_value);
                    if (parent_question != editing_SL)
                        row_editing.children(":nth-child(4)").text(parent_question);
                    row_editing.children(":nth-child(5)").html(tags_html);
                    $("#question").val("");
                    $("#answer").val("");
                    $("#tags").val("");
                    $("#parent_question").val("0");
                }
                $('#addRow').text("Add Question");
                $('#addRow').attr('disabled', false);
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
            var tags = $(this).parent().parent().children(":nth-child(5)").children();
            var length = tags.length;
            console.log(length);
            var tags_html = "";
            for (var i = 0; i < length; i++) {
                console.log(tags[i]);
                $tags = $(tags[i]).clone();
                console.log($tags);
                $tags.append('<a class="remove-tag" href="">&nbsp;&nbsp;×</a>');
                tags_html += $tags[0].outerHTML;
            }
            $("#question").val(Question_value);
            $("#answer").val(Answer_value);
            $("#parent_question").val(parent_question);
            $("#tags-textarea").html(tags_html);
            $('#addRow').text("Update Question");
        });

        $('#submit_question').on('click', function() {
            var Questions_generated = [];
            var Tags_final_array = [];
            $("#questionsTable tbody tr td:first-child").each(function(index) {
                if ($(this).text() != "No data available in table") {
                    var tags = $(this).parent().children(":nth-child(5)").children();
                    var tags_array = [];
                    var length = tags.length;
                    for (var i = 0; i < length; i++) {
                        var tag = $(tags[i]).text();
                        tags_array.push(tag);
                    }
                    var Question_ob = {
                        id: index + 1,
                        Question: $(this).parent().children(":nth-child(2)").text(),
                        Answer: $(this).parent().children(":nth-child(3)").text(),
                        parent: $(this).parent().children(":nth-child(4)").text(),
                        scenario: <?= $scenario_id ?>
                    }
                    Questions_generated.push(Question_ob);
                    Tags_final_array.push(tags_array);
                }
            });
            if (Questions_generated) {
                $.ajax({
                    url: "<?= base_url("customer/saveQASC") ?>",
                    type: "POST",
                    data: {
                        Questions_generated: Questions_generated,
                        Tags: Tags_final_array,
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
                        setTimeout(function() {
                            window.location = "<?= base_url("customer/scenariosList") ?>";
                        }, 3000);
                    }
                });
            } else {
                Swal.fire(
                    'Failed!',
                    'There are no questions to add!',
                    'error'
                );
            }
        });


        // Handling Enter key pressed on tags text area
        $('#tags').keypress(function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            // If enter key is pressed
            if (keycode == '13') {
                event.preventDefault();
                // Get Text
                var text = $("#tags").val();
                text = text.trim();
                if (text) {
                    // Empty Inputfield
                    $("#tags").val("");
                    // Append Added tag
                    $('#tags-textarea').append("<span class='tag-span badge badge-info'>" + text + "<a class='remove-tag' href=''>&nbsp;&nbsp;&times;</a></span>");
                }
            }
        });

        $("body").on('click', '.remove-tag', function(e) {
            e.preventDefault();
            $(this).parent('span').remove();
        });

    });

</script>
