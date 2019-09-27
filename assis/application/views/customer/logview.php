<?php
if(!isset($_SESSION['show_tutorial_scenarios_list'])){
    $_SESSION['show_tutorial_scenarios_list'] = 1;
} else {
    $_SESSION['show_tutorial_scenarios_list'] = 0;
}
?>
<link href="<?php echo base_url(); ?>styles/css/logview.css" media="all" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(); ?>styles/js/jquery.blockUI.js"></script>
<link href="<?php echo base_url(); ?>styles/css/bootstrap-treeview.css" rel="stylesheet" />
<link href="<?php echo base_url(); ?>styles/css/qa.css" rel="stylesheet" />
<div id="wrapper">
    <!-- Add Question Modal -->
    <div class="modal fade" id="add-question-modal" style="z-index:500;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title">Add Question</h3>
                </div>
                <div class="modal-body">
                    <form method="POST" id="add-question-form">
                        <input type="hidden" id="question" value="" />
                        <div class="row">
                            <div class="col-sm-6">
                                <p><b>Search:</b></p>
                                <div class="form-group">
                                    <label for="input-expand-node" class="sr-only">Search Tree:</label>
                                    <input class="form-control" id="btn-search-tree" placeholder="Enter text to search" value="" type="input">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6" id="tree-body-add">
                                <div id="default-tree"></div>
                            </div>
                            <div class="col-sm-6">
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
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id='addRow' data-action='add' form="add-question-form" class="btn btn-primary">Add Question</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Assign Question Modal -->
    <div class="modal fade" id="assign-question-modal" style="z-index:1000;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title">Assign Question</h3>
                </div>
                <div class="modal-body">
                    <form method="POST" id="add-question-form">
                        <input type="hidden" id="assignQuestionId" value="" />
                        <div class="row">
                            <div class="col-sm-8">
                                <p><b>Search:</b></p>
                                <div class="form-group">
                                    <label for="input-expand-node" class="sr-only">Search Tree:</label>
                                    <input class="form-control" id="btn-search-tree" placeholder="Enter text to search" value="" type="input">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6" id="tree-body-assign">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id='assignRow' data-action='assign' class="btn btn-primary">Assign Question</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Similarity Modal -->
    <div class="modal fade" id="similarity">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title">Similar Questions</h3>
                </div>
                <div class="modal-body">
                    <table style="width:100%" id="similarity-table" class="table table-striped table-responsive table-hover">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Accuracy</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td id="ques-6">What is optimal solutions?</td>
                                <td>
                                    <div data-percent="80" class="small green accuracy"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>What is Da7i7a?</td>
                                <td>
                                    <div data-percent="43" class="small red accuracy"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <div id="page-wrapper">
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Logs Management <small>View Log</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li class="active">
                            <i class="fa fa-dashboard"></i> Dashboard / <i class="fa fa-user"></i> View Log
                        </li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="container">
                    <div class="row">
                        <label>User Email: <?= $user_email ?></label>
                        <div class="Messenger_messenger">
                            <div class="Messenger_content">
                                <div class="Messages" id="msg-list">
                                    <div class="Messages_list">
                                        <?php
                                            foreach($log_details as $details){
                                        ?>
                                        <div class="outgoing_msg">
                                            <div class="sent_msg">
                                                <img src="<?= base_url() ?>styles/images/user.png" width="50" height="50" />
                                                <p id="user-msg-<?= $details['id'] ?>"><?= $details['user_query'] ?></p>
                                                <span class="actions actions-add-question"><a href="" class="actions-add-href" data-msg-id="<?= $details['id'] ?>"><i class="fa fa-plus fa-2x"></i></a></span>
                                                <span class="actions actions-similarity"><a href="" class="actions-similarity-href" data-msg-id="<?= $details['id'] ?>"><i class="fa fa-search-plus fa-2x"></i></a></span>
                                                <span class="actions assign-icon">
                                                <a data-msg-id="<?= $details['id'] ?>" class="actions-attach-href" href=""><i class="fa fa-paperclip fa-2x"></i></a></span>
                                                <span class="actions badge custom-badges"><?= $details['num_of_occurences'] ?></span>
                                                <span class="time_date"><?= $details['msgdatetime']?></span>
                                            </div>
                                        </div>
                                        <div class="incoming_msg">
                                            <div class="received_msg">
                                                <div class="received_withd_msg">
                                                    <img src="<?= base_url() ?>styles/images/bot.png" width="50" height="50" />
                                                    <p><?= $details['bot_reply'] ?></p>
                                                    <span class="time_date" id="welcome_msg-time"><?= $details['msgdatetime']?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url(); ?>styles/js/bootstrap-treeview.js"></script>
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
                        $("#add-question-modal").modal('hide');
                        $('#addRow').attr('disabled', false);
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

            /* END TREEVIEW SECTION */

            $("#generate-tags").on('click', function() {
                var Question_value = $("#question").val().trim();
                var Answer_value = $("#answer").val().trim();
                if (Question_value && Answer_value) {
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
                            'Authorization': "Bearer " + "<?= $token ?>",
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
                    Swal.fire(
                        'Sorry!',
                        'Please Enter a Questions and its Answer first',
                        'error'
                    );
                }
            });

            $('#addRow').on('click', function() {
                var action = $(this).attr('data-action');
                var Question_value = $("#question").val().trim();
                var Answer_value = $("#answer").val().trim();
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
                    var scenario = LastNode.scenario_id;
                    var parent = 0;
                    if (LastNode.is_scenario == 0) {
                        parent = LastNode.question_id;
                    }
                    $('#addRow').attr('disabled', true);
                    $.ajax({
                        url: "<?= base_url("customer/saveQASC") ?>",
                        type: "POST",
                        data: {
                            action: 'insert',
                            Question: Question_value,
                            Tags: Tags_final_array,
                            Answer: Answer_value,
                            parent: parent,
                            scenario: scenario,
                            question_id: 0,
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
            
            $('#assignRow').on('click', function() {
                var checked = $('#default-tree').treeview('getChecked');
                if (LastNode == null || checked.length == 0) {
                    Swal.fire(
                        'Sorry!',
                        'Please Select a parent question or scenario',
                        'error'
                    );
                    return;
                }
                var scenario = LastNode.scenario_id;
                var to_be_assigned_to = 0;
                if (LastNode.is_scenario == 0) {
                    to_be_assigned_to = LastNode.question_id;
                }
                $('#assignRow').attr('disabled', true);
                var assignQuestionId = $("#assignQuestionId").val();
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
                            'Assigned Question Successfully!',
                            'success'
                        );
                        $("#assign-question-modal").modal('hide');
                        $('#assignRow').attr('disabled', false);
                    }
                });
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
        $(".accuracy").percircle();
        $(".actions-similarity-href").on('click', function(e) {
            e.preventDefault();
            var ms_id = $(this).attr('data-msg-id');
            var msg = $("#user-msg-" + ms_id).html();
            $("#similarity").modal('show');
        });
        $(".actions-add-href").on('click', function(e) {
            e.preventDefault();
            var ms_id = $(this).attr('data-msg-id');
            var msg = $("#user-msg-" + ms_id).html();
            $("#question").val(msg);
            $("#add-question-modal").modal('show');
        });
        $("#similarity").on('shown.bs.modal', function() {
            $($.fn.dataTable.tables(true)).DataTable()
                .columns.adjust();
        });
        $("#add-question-modal").on('shown.bs.modal', function() {
            $('#scenarios-dropdown:not(.select2-hidden-accessible,:hidden)').select2({
                placeholder: 'Select an option',
            });
        });
        tippy('.actions-similarity', {
            content: "Similar Questions",
            placement: 'bottom',
            arrow: true,
            arrowType: 'round',
            animation: "perspective",
            theme: "light-border",
            //followCursor: "horizontal",
        });
        tippy('.actions-add-question', {
            content: "Add Question",
            placement: 'bottom',
            arrow: true,
            arrowType: 'round',
            animation: "perspective",
            theme: "light-border",
            //followCursor: "horizontal",
        });
        tippy('.custom-badges', {
            content: "Number Of Occurences",
            placement: 'bottom',
            arrow: true,
            arrowType: 'round',
            animation: "perspective",
            theme: "light-border",
            //followCursor: "horizontal",
        });
        tippy('.assign-icon', {
            content: "Assign This Question to another question answer",
            placement: 'bottom',
            arrow: true,
            arrowType: 'round',
            animation: "perspective",
            theme: "light-border",
            //followCursor: "horizontal",
        });
        <?php if ($_SESSION['show_tutorial_scenarios_list']) { ?>
        let tourOptions = {
            options: {
                darkLayerPersistence: true,
            },
            tips: [{
                title: '<span class="tour-title-icon">😁</span>Here we go!',
                description: 'Here you can Acitvate/Deactivate scenario and add Q&A Pairs.',
                selector: '#tour-scenario-list-step-1',
                x: 30,
                y: 30,
                offx: 11,
                offy: 0,
                position: 'top',
                onSelected: false
            }, {
                title: '<span class="tour-title-icon">😁</span>Training!',
                description: 'Here you can train your bot on the added scenarios and their questions.',
                selector: '#train',
                x: -40,
                y: 50,
                offx: 11,
                offy: 0,
                position: 'left',
                onSelected: false
            }]
        };

        let tour = window.ProductTourJS;
        tour.init(tourOptions);
        tour.start();
        <?php } ?>
        $('#similarity-table').DataTable({
            "scrollX": true,
            "autoWidth": false,
            "ordering": false
        });

        $(".actions-attach-href").on('click', function(e) {
            e.preventDefault();
            var ques_id = $(this).attr('data-msg-id');
            $("#assignQuestionId").val(ques_id);
            $('#default-tree').treeview('collapseAll', {
                silent: true
            });
            $('#default-tree').treeview('uncheckAll', {
                silent: true
            });
            $("#default-tree").detach().appendTo('#tree-body-assign');
            $("#assign-question-modal").modal('show');
        });
        // Code For Showing modals on top of each other
        $(document).on('show.bs.modal', '.modal', function() {
            var zIndex = 1040 + (10 * $('.modal:visible').length);
            $(this).css('z-index', zIndex);
            setTimeout(function() {
                $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
            }, 0);
        });
        // When assign modal is closed return tree element to its original location (In Add Question Form)
        // This solution is made to avoid creating and initializing another tree object
        $('#assign-question-modal').on('hidden.bs.modal', function () {
            $('#default-tree').treeview('collapseAll', {
                silent: true
            });
            $('#default-tree').treeview('uncheckAll', {
                silent: true
            });
            $("#default-tree").detach().appendTo('#tree-body-add');
        });

    </script>
