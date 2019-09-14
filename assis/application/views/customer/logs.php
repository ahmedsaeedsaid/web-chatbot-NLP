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
                    <table id="logs" class="table table-responsive table-hover table-bordered" width="100%" cellspacing="0">
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
        
        $('#logs').DataTable({
            "scrollX": true
        });
    });

</script>
