<?php
if(!isset($_SESSION['show_tutorial_qa'])){
    $_SESSION['show_tutorial_qa'] = 1;
} else {
    $_SESSION['show_tutorial_qa'] = 0;
}
$scenario_id = 0;
?>
<style>
    td {
        text-align: center;
    }

    #default-tree {
        margin-bottom: 9%;
    }
</style>
<meta name="optimal-bot-verification" content="<?= $token ?>" />
<link href="<?php echo base_url(); ?>styles/css/qa.css" rel="stylesheet" />
<link href="<?php echo base_url(); ?>styles/css/bootstrap-treeview.css" rel="stylesheet" />
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
                <div class="col-sm-2">
                    <p><b>Search:</b></p>
                    <div class="form-group">
                        <label for="input-expand-node" class="sr-only">Search Tree:</label>
                        <input class="form-control" id="btn-search-tree" placeholder="Enter text to search" value="" type="input">
                    </div>
                </div>
                <div class="col-sm-2" style="margin-top: 30px;width: auto;">
                    <button type="button" class="btn btn-success" id="btn-expand-all"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Expand All</button>
                </div>
                <div class="col-sm-2" style="margin-top: 30px;width: auto;">
                    <button type="button" class="btn btn-success" id="btn-collapse-all"><i class="fa fa-minus" aria-hidden="true"></i>&nbsp;Collapse All</button>
                </div>
                <div class="col-sm-2" style="margin-top: 30px;width: auto;">
                    <button type="button" class="btn btn-success" id="add-sc">Add Scenario</button>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div id="default-tree" style="width:75%;"></div>
                    <div id="attached_questions_cont" style="display:none;margin-bottom:1%;">
                        <div class="col-lg-12">
                            <h3 class="page-header">
                                Attached Questions
                            </h3>
                        </div>
                        <table id="attached_questions" class="table table-responsive table-hover table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>SN</th>
                                    <th>Attached To</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="attached_questions_body">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4">
                    <?php
                        $csrf = array(
                            'name' => $this->security->get_csrf_token_name(),
                            'hash' => $this->security->get_csrf_hash()
                        );

                    ?>
                    <form id="form-add-account" action="" method="POST">
                        <input type="hidden" name="<?=$csrf['name']?>" value="<?=$csrf['hash']?>" />
                        <input type="hidden" id="question_id" name="question_id" value="" />
                        <div class="form-group">
                            <label for="question">User Query:</label>
                            <textarea required name="question" id="question" class="form-control" style="resize: none;    height: 90px;" placeholder="Add what a user might ask"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="answer">Bot Answer:</label>
                            <textarea required name="answer" id="answer" class="form-control" style="resize: none;height: 90px;" placeholder="Add what a bot should answer with"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="answer">Suggested Text</label>
                            <input type="text" class="form-control" id="suggested_text" name="suggested_text" placeholder="Add a text to represent the question in bot suggested actions">
                        </div>

                        <div class="form-group">
                            <label for="answer">Tags:&nbsp;&nbsp;&nbsp;</label>
                            <button type="button" id="generate-tags" class="blue">Generate Tags</button>
                            <input type="text" class="form-control" id="tags" name="tags" placeholder="Add specific keywords that is related to your question"><br />
                            <div id="tags-textarea" class="form-control">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-4 text-center">
                                <button id="addRow" type="button" class="btn btn-default">Add Question</button>
                            </div>
                            <div class="col-lg-1 text-center">
                                <button type="button" id="delete-question" class="btn btn-danger" style="display:none;">Delete</button>
                            </div>
                            <div class="col-lg-offset-1 col-lg-3 text-center">
                                <button type="button" id="cancel-adding-row" class="btn btn-warning" style="display:none;">Cancel</button>
                            </div>
                            <div class="col-lg-2 text-center">
                                <button type="button" id="attach-question" class="btn blue" style="display:none;">Attach</button>
                            </div>
                        </div>
                    </form>
                    <form id="form-add-scenario" method="POST" action="<?= base_url("Customer/saveScenario") ?>" style="display:none;">
                        <input type="hidden" name="<?=$csrf['name']?>" value="<?=$csrf['hash']?>" />
                        <div class="form-group">
                            <label for="answer">Scenario Name:</label>
                            <input type="hidden" name="sc-action" id="sc-action" value="add">
                            <input type="hidden" name="scenario" id="sc-id" value="">
                            <input type="text" id="sc-name" name="name" class="form-control" placeholder="Name" required="">
                            <?php
                            echo '<label class="text-danger">' . $this->session->flashdata("name") . '</label>';
                            ?>
                        </div>
                        <button type="submit" id="sc-btn" class="btn btn-primary submit">Add</button>
                        <button type="button" id="delete-sc" class="btn btn-danger">Delete</button>
                        <button type="button" id="cancel-adding-sc" class="btn btn-warning">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>styles/js/tour.js"></script>
<script src="<?php echo base_url(); ?>styles/js/bootstrap-treeview.js"></script>
<script src="<?php echo base_url(); ?>styles/js/jquery.blockUI.js"></script>
<script>
    /* START Predefined Functions */
    function trainBot(action) {
        var param = JSON.stringify({
            name: 'createBot',
            param: {}
        });
        $.ajax({
            type: "POST",
            url: "https://localhost:5002/",
            data: param,
            headers: {
                'Authorization': "Bearer " + "<?= $token ?>",
                'Content-Type': 'application/json',
            },
            success: function(data) {
                if ('error' in data) {
                    document.write(data.error.message);
                    return;
                }
                $('#addRow').attr('disabled', false);
                if (action == 'save') {
                    Swal.fire(
                        'Success!',
                        'Question Saved Successfully!',
                        'success'
                    );
                } else if (action == 'delete') {
                    Swal.fire(
                        'Deleted!',
                        'Question has been deleted.',
                        'success'
                    );
                }
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        });
    }

    function getAllNodeChilds(mainNode, childs_question_ids) {
        mainNode.nodes.forEach(function(node) {
            childs_question_ids.push(node.question_id);
            if ('nodes' in node) {
                getAllNodeChilds(node, childs_question_ids);
            }
        });
        return childs_question_ids;
    }
    // unique array
    function unique_array(a) {
        var seen = {};
        var out = [];
        var len = a.length;
        var j = 0;
        for (var i = 0; i < len; i++) {
            var item = a[i];
            if (seen[item] !== 1) {
                seen[item] = 1;
                out[j++] = item;
            }
        }
        return out;
    }
    /* END Predefined Functions */

    /* START TOUR */
    <?php if ($_SESSION['show_tutorial_qa']) { ?>
    let tourOptions = {
        options: {
            darkLayerPersistence: true,
        },
        tips: [{
            title: '<span class="tour-title-icon">😁</span>Adding a Scenario!',
            description: 'Here you can add a new scenario. Scenarios are the top level node of the tree that contain all your related question under it. <a href="#">more</a>',
            selector: '#add-sc',
            x: 50,
            y: 30,
            offx: 11,
            offy: 0,
            position: 'top',
            onSelected: false
        }, {
            title: '<span class="tour-title-icon">😁</span>Training Bot!',
            description: 'You can add a user query and the corresponding bot answer.',
            selector: '#question',
            x: 0,
            y: 120,
            offx: 11,
            offy: 0,
            position: 'left',
            onSelected: false
        }, {
            title: '<span class="tour-title-icon">😁</span>Keywords!',
            description: 'You can add keywords that are related to your question. <a href="#">more</a>',
            selector: '#tags-textarea',
            x: 0,
            y: 30,
            offx: 11,
            offy: 0,
            position: 'left',
            onSelected: false
        }, {
            title: '<span class="tour-title-icon">😁</span>Adding Questions!',
            description: 'Click here to add the question.',
            selector: '#addRow',
            x: 50,
            y: 140,
            offx: 11,
            offy: 0,
            position: 'bottom',
            onSelected: false
        }]
    };

    let tour = window.ProductTourJS;
    tour.init(tourOptions);

    tour.start();
    <?php } ?>

    /* END TOUR */

    $(document).ajaxStart(function() {
        $.blockUI({
            message: '<img src="<?= base_url() ?>styles/icons/loading.gif" />',
            baseZ: 2000,
            css: {
                border: 'none',
                backgroundColor: 'transparent',
                cursor: 'wait'
            }
        });
    }).ajaxStop($.unblockUI);
    $(document).ready(function() {
        /* START TREEVIEW SECTION */
        var LastNode = null;
        var LastSelectedNode = null;
        // Grapping Tree View from php
        var myTree = [<?= $tree_nodes ?>];
        // Initialzing Tree View
        $('#default-tree').treeview({
            data: myTree,
            // custom icons
            expandIcon: 'glyphicon glyphicon-plus',
            collapseIcon: 'glyphicon glyphicon-minus',
            emptyIcon: 'glyphicon',
            nodeIcon: '',
            selectedIcon: '',
            checkedIcon: 'glyphicon glyphicon-check',
            uncheckedIcon: 'glyphicon glyphicon-unchecked',

            // colors
            color: undefined, // '#000000',
            backColor: undefined, // '#FFFFFF',
            borderColor: undefined, // '#dddddd',
            onhoverColor: '#F5F5F5',
            selectedColor: '#FFFFFF',
            selectedBackColor: '#428bca',
            searchResultColor: '#D9534F',
            searchResultBackColor: undefined, //'#FFFFFF',

            // enables links
            enableLinks: false,

            // highlights selected items
            highlightSelected: true,

            // highlights search results
            highlightSearchResults: true,

            // shows borders
            showBorder: true,

            // shows icons
            showIcon: true,

            // shows checkboxes
            showCheckbox: true,

            // shows tags
            showTags: true,

            // enables multi select
            multiSelect: false
        });

        // Make tree collapsed by default
        $('#default-tree').treeview('collapseAll', {
            silent: true
        });

        // When a node is checked, uncheck all other nodes
        $('#default-tree').on('nodeChecked', function(event, data) {
            $('#default-tree').treeview('uncheckAll', {
                silent: true
            });
            $(this).treeview('checkNode', [data.nodeId, {
                silent: true
            }]);
            LastNode = data;
        });

        // On click event to handle expand nodes of the tree
        $("#btn-expand-all").on('click', function() {
            $('#default-tree').treeview('expandAll', {
                silent: true
            });
        });

        // On click event to handle collapse nodes of the tree
        $("#btn-collapse-all").on('click', function() {
            $('#default-tree').treeview('collapseAll', {
                silent: true
            });
        });

        // On keyup event to handle searching mechanism in the tree
        $("#btn-search-tree").on('keyup', function() {
            var search_content = $(this).val();
            $('#default-tree').treeview('search', [search_content, {
                ignoreCase: true, // case insensitive
                exactMatch: false, // like or equals
                revealResults: true // reveal matching nodes
            }]);
        });

        // when node is selected, append edit form
        $('#default-tree').on('nodeSelected', function(event, data) {
            LastSelectedNode = data;
            if (data.is_scenario) {
                $("#form-add-scenario").css('display', 'block');
                $("#form-add-account").css('display', 'none');
                $("#sc-name").val(data.text);
                $("#sc-btn").text('Update');
                $("#sc-action").val('update');
                $("#sc-id").val(data.scenario_id);
            } else {
                $("#form-add-scenario").css('display', 'none');
                $("#form-add-account").css('display', 'block');
                var id = data.question_id;
                var parentNodeId = $('#default-tree').treeview('getParent', data.nodeId).nodeId;
                $('#default-tree').treeview('uncheckAll', {
                    silent: true
                });
                $('#default-tree').treeview('checkNode', [parentNodeId]);
                $("#question_id").val(id);
                $.ajax({
                    type: "POST",
                    url: "<?= base_url('customer/getQA') ?>",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: id
                    },
                    success: function(data) {
                        data = JSON.parse(data);
                        var attached_questions = data.attached_questions;
                        data = data.ques;
                        $("#question").val(data.question);
                        $("#answer").val(data.answer);
                        $("#suggested_text").val(data.suggested_text);
                        var tags = data.tags;
                        var length = tags.length;
                        var tags_html = "";
                        for (var i = 0; i < length; i++) {
                            tags_html += "<span class='tag-span badge badge-info'>" + tags[i] + "<a class='remove-tag' href=''>&nbsp;&nbsp;×</a></span>";
                        }
                        $("#tags-textarea").html(tags_html);
                        $('#addRow').text("Update Question");
                        var html = "";
                        var i = 1;
                        var table = $('#attached_questions').DataTable();
                        var rows = [];
                        attached_questions.forEach(function(question) {
                            rows.push([i, question.question, `<a href="" data-id="` + question.id + `" title="Delete" class="removeAttachedQuestion"><img src="<?= base_url() ?>styles/icons/action_delete.gif" alt="Delete Question"></a>`]);
                            i += 1;
                        });
                        table.rows.add(rows).draw();
                        $('#cancel-adding-row').css('display', 'block');
                        $('#delete-question').css('display', 'block');
                        $('#attach-question').css('display', 'block');
                        $('#attached_questions_cont').css('display', 'block');
                    }
                });
            }
        });

        $("body").on('click', ".removeAttachedQuestion", function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var table = $('#attached_questions').DataTable();
            $td = $(this);
            $.ajax({
                url: "<?= base_url("customer/deleteAttachtedQuestion") ?>",
                type: "POST",
                data: {
                    id: id,
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                },
                success: function() {
                    Swal.fire(
                        'Success!',
                        'Attached Question Removed!',
                        'success'
                    );
                    table.row($td.closest('tr')).remove().draw();
                    /*if (!table.data().any()) {
                        table.clear();
                        table.draw();
                    }*/
                }
            });
        });

        // On click event to handle cancel updating record
        $("#cancel-adding-row").on('click', function() {
            $('#addRow').text("Add Question");
            $("#question").val("");
            $("#answer").val("");
            $("#suggested_text").val("");
            $("#tags-textarea").html("");
            $('#cancel-adding-row').css('display', 'none');
            $('#delete-question').css('display', 'none');
            $('#attach-question').css('display', 'none');
            $('#attached_questions_cont').css('display', 'none');
        });

        // On click event to handle cancel updating record
        $("#cancel-adding-sc").on('click', function() {
            $('#form-add-scenario').css('display', 'none');
            $('#form-add-account').css('display', 'block');
            $("#sc-name").val("");
            $("#sc-btn").text('Add');
            $("#sc-action").val('add');
            $("#sc-id").val("");
        });

        // On click event to handle deleting scenario
        $("#delete-sc").on('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "All scenario questions will be deleted, you will not be able to recover these questions!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "POST",
                        url: "<?= base_url('customer/deleteScenario') ?>",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id: $("#sc-id").val()
                        },
                        success: function(data) {
                            Swal.fire(
                                'Deleted!',
                                'Scenario has been deleted.',
                                'success'
                            );
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        }
                    });
                }
            });
        });

        // On click event to handle deleting question
        $("#delete-question").on('click', function() {
            if (LastSelectedNode == null) {
                Swal.fire(
                    'Sorry!',
                    'Please Select a question to delete',
                    'error'
                );
                return;
            }
            Swal.fire({
                title: 'Are you sure?',
                text: "This Question will be deleted permanently and its children will be attached to its parent",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.value) {
                    var childs_question_ids = [];
                    var current_question_id = LastSelectedNode.question_id;
                    var parent = 0;
                    if ('nodes' in LastSelectedNode) {
                        var LastSelectedNodeParent = $('#default-tree').treeview('getParent', LastSelectedNode.nodeId);
                        parent = LastSelectedNodeParent.question_id;
                        childs_question_ids = getAllNodeChilds(LastSelectedNode, childs_question_ids);
                    }
                    $.ajax({
                        type: "POST",
                        url: "<?= base_url('customer/deleteQA') ?>",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            parent: parent,
                            current_question_id: current_question_id,
                            childs_question_ids: childs_question_ids
                        },
                        success: function(data) {
                            trainBot('delete');
                        }
                    });
                }
            });
        });

        // On click event to handle deleting question
        $("#attach-question").on('click', function() {
            if (LastNode == null) {
                Swal.fire(
                    'Sorry!',
                    'Please Select a question to attach to',
                    'error'
                );
                return;
            } else if (LastNode.is_scenario == 1) {
                Swal.fire(
                    'Sorry!',
                    'Please Select a question not a scenario',
                    'error'
                );
                return;
            }
            $('#attach-question').attr('disabled', true);
            var to_be_assigned_to = LastNode.question_id;
            var assignQuestionId = LastSelectedNode.question_id;
            $.ajax({
                url: "<?= base_url("customer/assignQuestion") ?>",
                type: "POST",
                data: {
                    question_id: assignQuestionId,
                    to_be_assigned_to: to_be_assigned_to,
                    '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                },
                success: function() {
                    Swal.fire(
                        'Success!',
                        'Attached Question Successfully!',
                        'success'
                    );
                    $('#attach-question').attr('disabled', false);
                }
            });
        });

        /* END TREEVIEW SECTION */

        $("#add-sc").on('click', function() {
            $("#form-add-account").css('display', 'none');
            $("#form-add-scenario").css('display', 'block');
        });

        $("#generate-tags").on('click', function() {
            var Question_value = $("#question").val().trim();
            var Answer_value = $("#answer").val().trim();
            if (Question_value && Answer_value) {
                if (document.querySelector("meta[name=optimal-bot-verification]")) {
                    var token = document.querySelector("meta[name=optimal-bot-verification]").getAttribute("content");
                    var param = JSON.stringify({
                        name: 'suggestionTags',
                        param: {
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
                            var tags = unique_array(data.response.result.tags);
                            var tags_html = "";
                            tags.forEach(function(tag) {
                                tags_html += "<span class='tag-span badge badge-info'>" + tag + "<a class='remove-tag' href=''>&nbsp;&nbsp;×</a></span>";
                            });
                            $("#tags-textarea").html($("#tags-textarea").html() + tags_html);
                        }
                    });
                } else {
                    document.write("Forbidden, Access is denied!");
                    return;
                }
            } else {
                Swal.fire(
                    'Sorry!',
                    'Please Enter a Questions and its Answer first',
                    'error'
                );
            }
        });

        $('#addRow').on('click', function() {
            var addRowText = $('#addRow').text();
            var Question_value = $("#question").val().trim();
            var Answer_value = $("#answer").val().trim();
            var parent_question = $("#parent_question").val();
            var suggested_text = $("#suggested_text").val();
            var checked = $('#default-tree').treeview('getChecked');
            if (LastNode == null || checked.length == 0) {
                Swal.fire(
                    'Sorry!',
                    'Please Select a parent question or scenario',
                    'error'
                );
                return;
            }
            if (Question_value && Answer_value) {
                // Get tags
                var tags = $("#tags-textarea").children();
                // Empty tags area
                $("#tags-textarea").empty();
                var length = tags.length;
                var tags_html = "";
                var Tags_final_array = [];
                for (var i = 0; i < length; i++) {
                    $(tags[i]).children('.remove-tag').remove();
                    Tags_final_array.push($(tags[i]).text());
                }
                var action = '';
                if (addRowText == "Add Question") {
                    action = 'insert';
                } else {
                    action = 'update';
                }
                var scenario = LastNode.scenario_id;
                var parent = 0;
                if (LastNode.is_scenario == 0) {
                    parent = LastNode.question_id;
                }
                var question_id = $("#question_id").val();
                if (question_id == "") {
                    question_id = 0;
                }
                $('#addRow').attr('disabled', true);
                $.ajax({
                    url: "<?= base_url("customer/saveQASC") ?>",
                    type: "POST",
                    data: {
                        action: action,
                        Question: Question_value,
                        Tags: Tags_final_array,
                        Answer: Answer_value,
                        parent: parent,
                        scenario: scenario,
                        question_id: question_id,
                        suggested_text: suggested_text,
                        '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                    },
                    success: function() {
                        trainBot('save');
                    }
                });
            } else {
                Swal.fire(
                    'Sorry!',
                    'Please Enter a Questions and its Answer first',
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

        $('#attached_questions').DataTable();

    });

</script>
