<?php
if(!isset($_SESSION['show_tutorial_scenarios_list'])){
    $_SESSION['show_tutorial_scenarios_list'] = 1;
} else {
    $_SESSION['show_tutorial_scenarios_list'] = 0;
}
?>
<style>
    .Support {
        background-color: #ed0b0b;
    }

    .Pending {
        background-color: #3479da;
    }

    .Served {
        background-color: #027102;
    }

</style>
<div id="wrapper">
    <div id="page-wrapper">

        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Logs Management <small>Logs List</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li class="active">
                            <i class="fa fa-dashboard"></i> Dashboard / <i class="fa fa-user"></i> Logs
                        </li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <table id="clients" class="table table-responsive table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Session ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($logs){
                            $i = 1;
                             foreach ($logs as $log) { ?>
                            <tr id="log-<?= $log['session_id'] ?>">
                                <td><?= $i ?></td>
                                <td><?= $log['session_id'] ?></td>
                                <td style="text-align:center" id="tour-scenario-list-step-1">
                                    <a href="<?= base_url(); ?>customer/logview/<?= $log['session_id'] ?>" title="Questions"><img src="<?= base_url() ?>styles/icons/view.png" width="23" height="23" alt="View"></a>
                                </td>
                            </tr>
                            <?php $i += 1; }}
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function ToggleActive(active, id) {
        $.ajax({
            url: "<?= base_url() ?>customer/toggleScenarioActive",
            type: "POST",
            data: {
                active: active,
                id: id,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function() {
                var img = '';
                var title = '';
                var active_val = 0;
                if (active == 1) {
                    title = 'Deactivate';
                    img = 'action_active.gif';
                    $("#user-" + id).removeClass('danger');
                } else {
                    active_val = 1;
                    title = 'Activate';
                    img = 'action_purge.gif';
                    $("#user-" + id).addClass('danger');
                }
                $("#status-" + id).attr('href', 'javascript:ToggleActive(' + active_val + ',' + id + ')');
                $("#status-" + id).attr('title', title);
                $("#status-" + id + " img").attr('src', '<?= base_url() ?>styles/icons/' + img);
            }
        });
    }

    /*function DeleteUser(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will permanently delete this client!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "<?= base_url() ?>Clients/deleteClient",
                    type: "POST",
                    data: {
                        id: id,
                        '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                    },
                    success: function() {
                        $("#user-" + id).remove();
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'Client is deleted successfully!',
                            icon: 'success'
                        });
                    }
                });
            } else {
                Swal.fire("Cancelled", "The client is safe :)", "error");
            }
        });
    }*/

    $(document).ready(function() {
        <?php if ($_SESSION['show_tutorial_scenarios_list']) { ?>
        let tourOptions = {
            options: {
                darkLayerPersistence: true,
            },
            tips: [{
                title: '<span class="tour-title-icon">😁</span>Here we go!',
                description: 'Here you can Acitvate/Deactivate scenario and add Q&A Pairs.',
                image: "https://picsum.photos/300/200/?random",
                selector: '#tour-scenario-list-step-1',
                x: 30,
                y: 30,
                offx: 11,
                offy: 0,
                position: 'top',
                onSelected: false
            },{
                title: '<span class="tour-title-icon">😁</span>Training!',
                description: 'Here you can train your bot on the added scenarios and their questions.',
                image: "https://picsum.photos/300/200/?random",
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
        
        $('#clients').DataTable({
            "scrollX": true
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
