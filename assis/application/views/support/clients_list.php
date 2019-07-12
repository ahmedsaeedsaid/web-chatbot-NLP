<style>
    .Support{
        background-color:#ed0b0b;
    }
    
    .Pending{
        background-color:#3479da;
    }
    
    .Served{
        background-color:#027102;
    }
</style>
<div id="wrapper">
    <div id="page-wrapper">

        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Clients Management <small>Clients List</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li class="active">
                            <i class="fa fa-dashboard"></i> Dashboard / <i class="fa fa-user"></i> Clients
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
                                <th>Company Name</th>
                                <th>Domain</th>
                                <th>Client Name</th>
                                <th>Client Email</th>
                                <th>Platform</th>
                                <th>Website Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($clients){
                            $i = 1;
                             foreach ($clients as $client) { ?>
                            <tr id="user-<?= $client['id'] ?>" <?php if (!$client['active']) { ?>class="danger" <?php } ?>>
                                <td><?= $i ?></td>
                                <td><?= $client['name'] ?></td>
                                <td><a href="<?= $client['domain'] ?>"><?= $client['domain'] ?></a></td>
                                <td><?= $client['cname'] ?></td>
                                <td><?= $client['cemail'] ?></td>
                                <td><?= $client['pname'] ?></td>
                                <td><?= $client['wname'] ?></td>
                                <td><span class="badge <?= $client['status'] ?>"><?= $client['status'] ?></span></td>
                                <td style="text-align:center">
                                    <?php if ($client['active']) { ?>
                                    <a href="javascript:ToggleActive(0,<?= $client['id'] ?>)" title="Deactivate" id="status-<?= $client['id'] ?>"><img src="<?= base_url() ?>styles/icons/action_active.gif" alt="Status"></a>
                                    <?php } else { ?>
                                    <a href="javascript:ToggleActive(1,<?= $client['id'] ?>)" title="Activate" id="status-<?= $client['id'] ?>"><img src="<?= base_url() ?>styles/icons/action_purge.gif" alt="Status"></a>
                                    <?php } ?>
                                    <a href="javascript:DeleteUser(<?= $client['id'] ?>)" title="Delete"><img src="<?= base_url() ?>styles/icons/action_delete.gif" alt="Delete"></a>
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
            url: "<?= base_url() ?>Clients/toggleActive",
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

    function DeleteUser(id) {
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
    }

    $(document).ready(function() {
        $('#clients').DataTable({
            "scrollX": true
        });
    });

</script>
