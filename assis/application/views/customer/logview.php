<?php
if(!isset($_SESSION['show_tutorial_scenarios_list'])){
    $_SESSION['show_tutorial_scenarios_list'] = 1;
} else {
    $_SESSION['show_tutorial_scenarios_list'] = 0;
}
?>
<link href="<?php echo base_url(); ?>styles/css/logview.css" media="all" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url(); ?>styles/js/jquery.blockUI.js"></script>
<div id="wrapper">
    <!-- Add Question Modal -->
    <div class="modal fade" id="add-question-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 class="modal-title">Add Question</h3>
                </div>
                <div class="modal-body">
                    <form method="POST" id="add-question-form">
                        <input type="hidden" id="question" value="" />
                        <div class="form-group">
                            <label for="answer">Bot Answer:</label>
                            <textarea required name="answer" id="answer" class="form-control" style="resize: none;height: 90px;" placeholder="Add what a bot should answer with"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="answer">Scenario:</label>
                            <select id="scenarios-dropdown" class="form-control">
                                <option></option>
                                <?php foreach($scenarios as $scenario){ ?>
                                <option value="<?= $scenario['id'] ?>"><?= $scenario['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" id='add-ques-btn' form="add-question-form" class="btn btn-primary">Add Question</button>
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
                                <td>What is Geeks?</td>
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
                                                <span class="actions actions-similarity"><a href="" class="actions-similarity-href" data-msg-id="<?= $details['id'] ?>"><i class="fa fa-search-plus fa-2x"></i></a></span>
                                                <span class="actions actions-add-question"><a href="" class="actions-add-href" data-msg-id="<?= $details['id'] ?>"><i class="fa fa-plus fa-2x"></i></a></span>
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
                        $('#add-ques-btn').attr('disabled', false);
                    }, 1500);
                }
            });
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
            // Form Submission
            $("#add-question-form").on('submit', function(e) {
                e.preventDefault();
                // Disable Adding Question Button
                $('#add-ques-btn').attr('disabled', true);
                var Question = $("#question").val().trim();
                var Answer = $("#answer").val().trim();
                var scenario = $("#scenarios-dropdown").val();
                $.ajax({
                    url: "<?= base_url("customer/saveQASC") ?>",
                    type: "POST",
                    data: {
                        action: 'add',
                        Question: Question,
                        Answer: Answer,
                        parent: 0,
                        scenario: scenario,
                        '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                    },
                    success: function() {
                        trainBot('save');
                    }
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
            $("#train").on('click', function() {
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
                        Swal.fire({
                            title: 'Success!',
                            text: 'Training Was Successfull, An email with further instruction has been sent to you!',
                            icon: 'success'
                        });
                        setTimeout(function() {
                            window.location = "<?= base_url("customer/sendBotScriptEmail") ?>";
                        }, 4000);
                    }
                });
            });
        });

    </script>
