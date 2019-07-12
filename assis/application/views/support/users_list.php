<div id="wrapper">
    <div id="page-wrapper">

        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Users Management <small>Users List</small>
                    </h1>
                    <ol class="breadcrumb">
                        <li class="active">
                            <i class="fa fa-dashboard"></i> Dashboard / <i class="fa fa-user"></i> Users
                        </li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <table id="users" class="table table-responsive table-hover table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Name</th>
                                <th>User Name</th>
                                <th>Email</th>
                                <th>Job</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($users){
                            $i = 1;
                             foreach ($users as $user) { ?>
                            <tr id="user-<?= $user['id'] ?>" <?php if (!$user['active']) { ?>class="danger" <?php } ?>>
                                <td><?= $i ?></td>
                                <td><?= $user['name'] ?></td>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['email'] ?></td>
                                <td><?= $user['job'] ?></td>
                                <td style="text-align:center">
                                    <?php if ($user['active']) { ?>
                                    <a href="javascript:ToggleActive(0,<?= $user['id'] ?>)" title="Deactivate" id="status-<?= $user['id'] ?>"><img src="<?= base_url() ?>styles/icons/action_active.gif" alt="Status"></a>
                                    <?php } else { ?>
                                    <a href="javascript:ToggleActive(1,<?= $user['id'] ?>)" title="Activate" id="status-<?= $user['id'] ?>"><img src="<?= base_url() ?>styles/icons/action_purge.gif" alt="Status"></a>
                                    <?php } ?>
                                    <a href="javascript:DeleteUser(<?= $user['id'] ?>)" title="Delete"><img src="<?= base_url() ?>styles/icons/action_delete.gif" alt="Delete"></a>
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
            url: "<?= base_url() ?>Users/toggleActive",
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
            text: "You will permanently delete this user!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "<?= base_url() ?>Users/deleteUser",
                    type: "POST",
                    data: {
                        id: id,
                        '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
                    },
                    success: function() {
                        $("#user-" + id).remove();
                        Swal.fire({
                            title: 'Deleted!',
                            text: 'User is deleted successfully!',
                            icon: 'success'
                        });
                    }
                });
            } else {
                Swal.fire("Cancelled", "The user is safe :)", "error");
            }
        });
    }

    $(document).ready(function() {
        $('#users').DataTable({
            "scrollX": true
        });
    });

</script>
