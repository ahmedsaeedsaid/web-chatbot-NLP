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


                    <?php
                        $csrf = array(
                            'name' => $this->security->get_csrf_token_name(),
                            'hash' => $this->security->get_csrf_hash()
                        );

                    ?>
                    <form class="col-md-4" method="POST" action="<?= base_url("Customer/saveScenario") ?>">
                        <input type="hidden" name="<?=$csrf['name']?>" value="<?=$csrf['hash']?>" />
                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Name" required="">
                            <?php
                            echo '<label class="text-danger">' . $this->session->flashdata("name") . '</label>';
                            ?>
                        </div>
                        <br />
                        <input class="btn btn-success submit" type="submit" name="submit" value="Add" />
                    </form>
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
