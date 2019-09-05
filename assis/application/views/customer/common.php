<?php
if(!isset($_SESSION['show_tutorial_dash'])){
    $_SESSION['show_tutorial_dash'] = 1;
} else {
    $_SESSION['show_tutorial_dash'] = 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        <?php
            if (isset($title)): echo $title;
            endif;
            ?>
    </title>
    <!--<noscript>
        For full functionality of this site it is necessary to enable JavaScript.
        Here are the <a href="https://www.enable-javascript.com/" target="_blank">
        instructions how to enable JavaScript in your web browser</a>.
    </noscript>-->
    <link href="<?php echo base_url(); ?>styles/images/logo1.png" rel="icon" type="image/png">

    <!-- Bootstrap core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Font Awesome core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <!-- RatingYo CSS -->
    <link href="<?php echo base_url(); ?>styles/RateYo/jquery.rateyo.min.css" media="all" rel="stylesheet" type="text/css" />

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="<?php echo base_url(); ?>styles/css/ie10-viewport-bug-workaround.css" media="all" rel="stylesheet" type="text/css" />
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>styles/css/carousel.css" media="all" rel="stylesheet" type="text/css" />
    <!-- datatable -->
    <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />

    <!-- JQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Droid+Sans:400,700">


    <!-- Custom CSS -->
    <link href="<?php echo base_url(); ?>styles/css/cms/sb-admin.css" rel="stylesheet">

    <!-- Morris Charts CSS -->
    <link href="<?php echo base_url(); ?>styles/css/cms/plugins/morris.css" rel="stylesheet">

    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>styles/fonts/cms/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>styles/fonts/cms/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>styles/vendor/cms/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>styles/vendor/cms/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>styles/vendor/cms/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>styles/vendor/cms/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>styles/vendor/cms/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>styles/vendor/cms/noui/nouislider.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>styles/sweetalert/sweetalert2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>styles/select2/select2.min.css">
    <!--===============================================================================================-->

    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url(); ?>styles/css/tour.css" rel="stylesheet" type="text/css">


    <!-- jQuery library -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>

    <!-- bootstrap library -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>

    <!-- select2 UI library  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js"></script>

    <!-- JQuery UI library -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous">



    </script>

    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>styles/js/tour.js"></script>
    <!-- Pre-defined Functions to use across website -->
    <script>
        function showErrorMsg(title, body) {
            var dynamicDialog = $('<div id="conformBox">' +
                '<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">' +
                '</span>' + body + '</div>');
            dynamicDialog.dialog({
                title: title,
                closeOnEscape: true,
                modal: true,
                buttons: [{
                    text: "OK",
                    click: function() {
                        $(this).dialog("close");
                    }
                }]
            });
        }

    </script>

    <script src="<?php echo base_url(); ?>styles/js/jquery.nicescroll.min.js"></script>
    <script src="<?php echo base_url(); ?>styles/js/wow.min.js"></script>
    <script>
        new WOW().init();

    </script>

    <!-- Morris Charts JavaScript -->
    <!--<script src="<?php echo base_url(); ?>styles/js/cms/plugins/morris/raphael.min.js"></script>
        <script src="<?php echo base_url(); ?>styles/js/cms/plugins/morris/morris.min.js"></script>
        <script src="<?php echo base_url(); ?>styles/js/cms/plugins/morris/morris-data.js"></script>-->

    <script>
        $("body").niceScroll();

    </script>

    <style>
        #geeks_word_span_e1 {
            color: #f89a1d;
        }

        #geeks_word_span_e2 {
            color: #2a5baa;
        }

        .container {
            padding: 20px;
        }

        .profile-card {
            background-color: #222222;
            margin-bottom: 20px;

        }

        .profile-pic {
            border-radius: 50%;
            position: absolute;
            top: -65px;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 1;
            max-width: 100px;
            -webkit-transition: all 0.4s;
            transition: all 0.4s;
        }


        .profile-info {
            color: #BDBDBD;
            padding: 25px;
            position: relative;
            margin-top: 15px;
        }

        .profile-info h2 {
            color: #E8E8E8;
            letter-spacing: 4px;
            padding-bottom: 12px;
        }

        .profile-info span {
            display: block;
            font-size: 12px;
            color: #4CB493;
            letter-spacing: 2px;
        }

        .profile-info a {
            color: #4CB493;
        }

        .profile-info i {
            padding: 15px 35px 0px 35px;
        }


        .profile-card:hover .profile-pic {

            transform: scale(1.1);
        }

        .profile-card:hover .profile-info hr {
            opacity: 1;
        }




        /* Underline From Center */

        .hvr-underline-from-center {
            display: inline-block;
            vertical-align: middle;
            -webkit-transform: translateZ(0);
            transform: translateZ(0);
            box-shadow: 0 0 1px rgba(0, 0, 0, 0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            -moz-osx-font-smoothing: grayscale;
            position: relative;
            overflow: hidden;
        }

        .hvr-underline-from-center:before {
            content: "";
            position: absolute;
            z-index: -1;
            left: 52%;
            right: 52%;
            bottom: 0;
            background: #FFFFFF;
            border-radius: 50%;
            height: 3px;
            -webkit-transition-property: all;
            transition-property: all;
            -webkit-transition-duration: 0.2s;
            transition-duration: 0.2s;
            -webkit-transition-timing-function: ease-out;
            transition-timing-function: ease-out;
        }

        .profile-card:hover .hvr-underline-from-center:before,
        .profile-card:focus .hvr-underline-from-center:before,
        .profile-card:active .hvr-underline-from-center:before {
            left: 0;
            right: 0;
            height: 1px;
            background: #CECECE;
        }

        modal {
            text-align: center;
            padding: 0 !important;
        }

        .modal:before {
            content: '';
            display: inline-block;
            height: 100%;
            vertical-align: middle;
            margin-right: 35%;
        }

        .modal-dialog {
            display: inline-block;
            text-align: left;
            vertical-align: middle;
        }

    </style>
</head>

<body>
    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog" style="padding-left: 13%;">
        <div class="modal-dialog">
            <div class="container">
                <div class="col-md-4">
                    <div class="profile-card text-center">
                        <img class="img-responsive" src="https://images.unsplash.com/photo-1451188502541-13943edb6acb?crop=entropy&fit=crop&fm=jpg&h=975&ixjsv=2.1.0&ixlib=rb-0.3.5&q=80&w=1925">
                        <div class="profile-info">
                            <img class="profile-pic" src="https://pbs.twimg.com/profile_images/711000557742395396/jzm8hqwW.jpg">
                            <h2 class="hvr-underline-from-center"><?= $this->session->userdata('assis_customername') ?></h2>
                            <?php if($this->session->userdata('assis_customeremail') != ''){ ?>
                            <input value="<?= $this->session->userdata('assis_customeremail') ?>" id="email" type="hidden">
                            <a href="" id="email-link"><i class="fa fa-envelope-o fa-2x"></i></a>
                            <?php } ?>
                        </div>
                        <button style="margin-left: 80%;margin-bottom: 3%;margin-right: 3%;" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?= base_url() ?>customer/main">
                <p id="geeks_word_p">CUSTOMER PORTAL</p>
            </a>
        </div>
        <!-- Top Menu Items -->
        <ul class="nav navbar-right top-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i> <b class="caret"></b></a>
                <ul class="dropdown-menu message-dropdown">
                    <li class="message-preview">
                        <a href="#">
                            <div class="media">
                                <span class="pull-left">
                                    <img class="media-object" src="http://placehold.it/50x50" alt="">
                                </span>
                                <div class="media-body">
                                    <h5 class="media-heading"><strong>John Smith</strong>
                                    </h5>
                                    <p class="small text-muted"><i class="fa fa-clock-o"></i> Yesterday at 4:32 PM</p>
                                    <p>Lorem ipsum dolor sit amet, consectetur...</p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="message-preview">
                        <a href="#">
                            <div class="media">
                                <span class="pull-left">
                                    <img class="media-object" src="http://placehold.it/50x50" alt="">
                                </span>
                                <div class="media-body">
                                    <h5 class="media-heading"><strong>John Smith</strong>
                                    </h5>
                                    <p class="small text-muted"><i class="fa fa-clock-o"></i> Yesterday at 4:32 PM</p>
                                    <p>Lorem ipsum dolor sit amet, consectetur...</p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="message-preview">
                        <a href="#">
                            <div class="media">
                                <span class="pull-left">
                                    <img class="media-object" src="http://placehold.it/50x50" alt="">
                                </span>
                                <div class="media-body">
                                    <h5 class="media-heading"><strong>John Smith</strong>
                                    </h5>
                                    <p class="small text-muted"><i class="fa fa-clock-o"></i> Yesterday at 4:32 PM</p>
                                    <p>Lorem ipsum dolor sit amet, consectetur...</p>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="message-footer">
                        <a href="#">Read All New Messages</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bell"></i> <b class="caret"></b></a>
                <ul class="dropdown-menu alert-dropdown">
                    <li>
                        <a href="#">Alert Name <span class="label label-default">Alert Badge</span></a>
                    </li>
                    <li>
                        <a href="#">Alert Name <span class="label label-primary">Alert Badge</span></a>
                    </li>
                    <li>
                        <a href="#">Alert Name <span class="label label-success">Alert Badge</span></a>
                    </li>
                    <li>
                        <a href="#">Alert Name <span class="label label-info">Alert Badge</span></a>
                    </li>
                    <li>
                        <a href="#">Alert Name <span class="label label-warning">Alert Badge</span></a>
                    </li>
                    <li>
                        <a href="#">Alert Name <span class="label label-danger">Alert Badge</span></a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="#">View All</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?= $this->session->userdata('assis_customername') ?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#" data-toggle="modal" data-target="#myModal" id="profile"><i class="fa fa-fw fa-user"></i> Profile</a>
                        <div id="profile-body"></div>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-fw fa-envelope"></i> Inbox</a>
                    </li>
                    <li>
                        <a href="#"><i class="fa fa-fw fa-gear"></i> Settings</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a href="<?= base_url() ?>customer/logout"><i class="fa fa-fw fa-power-off"></i> Log Out</a>
                    </li>
                </ul>
            </li>
        </ul>
        <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav side-nav">
                <li class="active" id="tour-step-1">
                    <a href="<?= base_url() ?>customer/main"><i class="fa fa-fw fa-dashboard"></i> Dashboard</a>
                </li>
                <li>
                    <a href="javascript:;" data-toggle="collapse" data-target="#user-drop-down"><i class="fa fa-fw fa-arrows-v"></i> Bot <i class="fa fa-fw fa-caret-down"></i></a>
                    <ul id="user-drop-down" class="collapse in" aria-expanded="true">
                        <li id="tour-step-2">
                            <a class="active" href="<?php echo base_url() ?>customer/addScenario">Add Scenario</a>
                        </li>
                        <li id="tour-step-3">
                            <a href="<?php echo base_url() ?>customer/scenariosList">Scenario List</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#"><i class="fa fa-fw fa-bar-chart-o"></i> Charts</a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-fw fa-table"></i> Tables</a>
                </li>
                <li>
                    <a href="#"><i class="fa fa-fw fa-edit"></i> Forms</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>
    <script type="text/javascript">
        function copyToClipboard() {
            /* Get the text field */
            var copyText = document.getElementById("email");

            /* Select the text field */
            copyText.select();

            /* Copy the text inside the text field */
            document.execCommand("copy");
        }
        $(document).ready(function() {
            <?php if (isset($page) && $page == 'main' && $_SESSION['show_tutorial_dash']) { ?>
            let tourOptions = {
                options: {
                    darkLayerPersistence: true,
                },
                tips: [{
                    title: '<span class="tour-title-icon">😁</span>Here we go!',
                    description: 'The Dashboard allows you to preview your bot staistics',
                    image: "https://picsum.photos/300/200/?random",
                    selector: '#tour-step-1',
                    x: 90,
                    y: 0,
                    offx: 11,
                    offy: 50,
                    position: 'right',
                    onSelected: false
                }, {
                    title: '<span class="tour-title-icon">😁</span>Scenarios!',
                    description: 'From here you can add scenarios. <a href="#">more</a>',
                    image: "https://picsum.photos/300/200/?random",
                    selector: '#tour-step-2',
                    x: 90,
                    y: 0,
                    offx: 11,
                    offy: 20,
                    position: 'right',
                    onSelected: false
                }, {
                    title: '<span class="tour-title-icon">😁</span>List!',
                    description: 'From here you can view & manage scenarios and Q&A Pairs.',
                    image: "https://picsum.photos/300/200/?random",
                    selector: '#tour-step-3',
                    x: 90,
                    y: 0,
                    offx: 11,
                    offy: 20,
                    position: 'right',
                    onSelected: false
                }]
            };

            let tour = window.ProductTourJS;
            tour.init(tourOptions);

            tour.start();
            <?php } ?>

            $("#email-link").on('click', function(e) {
                e.preventDefault();
                copyToClipboard();
            });
        });

    </script>
    <script type="text/javascript" src="<?php echo base_url(); ?>styles/sweetalert/sweetalert2.all.min.js"></script>
