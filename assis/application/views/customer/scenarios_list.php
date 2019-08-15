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
                        Scenarios Management <small>Scenarios List</small>
                        <button class="btn btn-primary" style="float:right" id="train">Train</button>
                    </h1>
                    <ol class="breadcrumb">
                        <li class="active">
                            <i class="fa fa-dashboard"></i> Dashboard / <i class="fa fa-user"></i> Scenarios
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
                                <th>Scenario Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($scenarios){
                            $i = 1;
                             foreach ($scenarios as $scenario) { ?>
                            <tr id="scenario-<?= $scenario['id'] ?>" <?php if (!$scenario['active']) { ?>class="danger" <?php } ?>>
                                <td><?= $i ?></td>
                                <td><?= $scenario['name'] ?></td>
                                <td style="text-align:center">
                                    <?php if ($scenario['active']) { ?>
                                    <a href="javascript:ToggleActive(0,<?= $scenario['id'] ?>)" title="Deactivate" id="status-<?= $scenario['id'] ?>"><img src="<?= base_url() ?>styles/icons/action_active.gif" alt="Status"></a>
                                    <?php } else { ?>
                                    <a href="javascript:ToggleActive(1,<?= $scenario['id'] ?>)" title="Activate" id="status-<?= $scenario['id'] ?>"><img src="<?= base_url() ?>styles/icons/action_purge.gif" alt="Status"></a>
                                    <?php } ?>
                                    <a href="<?= base_url(); ?>customer/questions/<?= $scenario['id'] ?>" title="Questions"><img src="<?= base_url() ?>styles/icons/Q&A.png" width="23" height="23" alt="Questions"></a>
                                    <!--<a href="javascript:DeleteUser(<?= $client['id'] ?>)" title="Delete"><img src="<?= base_url() ?>styles/icons/action_delete.gif" alt="Delete"></a>-->
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
                url: "http://localhost:5002/",
                data: param,
                headers: {
                    'Authorization': "Bearer " + "<?= $token ?>",
                    'Content-Type':'application/json',
                },
                success: function(data) {
                    if('error' in data){
                        document.write(data.error.message);
                        return;
                    }
                    Swal.fire({
                        title: 'Success!',
                        text: 'Training Was Successfull, An email with further instruction has been sent to you!',
                        icon: 'success'
                    });
                    setTimeout(function(){
                        window.location = "<?= base_url("customer/sendBotScriptEmail") ?>";
                     }, 4000);
                }
            });
        });
    });

</script>
