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

    <!-- Bootstrap core CSS
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->

    <!-- Header CSS -->
    <link href="<?php echo base_url(); ?>styles/css/header.css" media="all" rel="stylesheet" type="text/css" />

    <!-- RatingYo CSS -->
    <link href="<?php echo base_url(); ?>styles/RateYo/jquery.rateyo.min.css" media="all" rel="stylesheet" type="text/css" />


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="<?php echo base_url(); ?>styles/css/ie10-viewport-bug-workaround.css" media="all" rel="stylesheet" type="text/css" />
    <!-- Custom styles for this template -->
    <link href="<?php echo base_url(); ?>styles/css/carousel.css" media="all" rel="stylesheet" type="text/css" />
    <!-- Footer CSS -->
    <link href="<?php echo base_url(); ?>styles/css/footer.css" media="all" rel="stylesheet" type="text/css" />
    <!-- Courses CSS -->
    <!-- <link href="<?php echo base_url(); ?>styles/css/courses.css" media="all" rel="stylesheet" type="text/css" /> -->

    <!-- jQuery library -->
    <script src="<?php echo base_url(); ?>styles/js/jquery-3.3.1.min.js"></script>

    <!-- bootstrap library -->
    <!--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>-->

    <!-- JQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Droid+Sans:400,700">

    <!--<link rel="stylesheet" href="<?php echo base_url(); ?>styles/css/home-animate.css">

    <link rel="stylesheet" href="<?php echo base_url(); ?>styles/css/home-style.css">-->

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo base_url(); ?>styles/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>styles/css/styles.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>styles/css/main.css">

    <style type="text/css">
        #notfi {
            width: 500px;
        }

        #notfi a {
            color: black;
        }

        #notfi li {
            padding: 7px 0 7px 10px;
            border-radius: 5px;
        }

        #notfi li:hover {
            background-color: #2a5baa;
            cursor: pointer;
        }

        #notfi li:hover a {
            color: white;
        }

        .hidden {
            display: none;
        }

    </style>

    <!-- JQuery UI library -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
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
    <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.js"></script>
        <style type="text/css">
            .no-js #loader { display: none;  }
            .js #loader { display: block; position: absolute; left: 100px; top: 0; }
            .loader{
                position: fixed;
                left: 0px;
                top: 0px;
                width: 100%;
                height: 100%;
                z-index: 9999;
                background: url('<?php echo base_url(); ?>styles/images/load.gif') center no-repeat #fff;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style> -->
</head>

<body>
    <!-- Start Header  -->
    <div class="header" <?php if (($this->session->userdata('usertypeID') == 2 && $this->session->userdata('subActive') == 0) || ($this->session->userdata('usertypeID') == 3 && $this->session->userdata('instActive') == 0)) { echo'data-toggle="modal" data-target="#myModal"'; } ?>>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <div class="container" id="header-div">
                <!-- Brand and toggle get grouped for better mobile display -->
                <a class="navbar-brand" href="<?php echo base_url(); ?>">
                    <img src="<?php echo base_url(); ?>styles/images/OptimalSiteLogo.png" style="width: 100px;">
                </a>
                <!-- Brand and toggle get grouped for better mobile display
                <a class="navbar-brand" href="<?php echo base_url(); ?>">
                    <img src="<?php echo base_url(); ?>styles/images/bot.png">
                </a> -->
                <a class="navbar-brand" href="<?php echo base_url(); ?>"><b style="color:white">Optimal Web Assistant</b></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse " id="mainNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown nav-item">
                            <a href="#" class="nav-link" id="navbarDropdownMenuLink" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">About Us</a>
                            <ul style="left: 0; right: auto" class="dropdown-menu  dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                <!--<li><a href="<?php echo base_url(); ?>ProjectsCont">Our projects</a></li>-->
                                <li class="dropdown-item"><a class="dropdown-item" href="<?php echo base_url(); ?>ContactCont">Contact Us</a></li>
                                <!--<li role="separator" class="divider"></li>-->
                                <li class="dropdown-item"><a class="dropdown-item" href="<?php echo base_url(); ?>FAQCont">FAQ</a></li>
                                <li class="dropdown-item"><a class="dropdown-item" href="https://goo.gl/forms/RsFpHBw5PNn9iNQs1" target="_blank">Report A Bug</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- End Navbar -->
    </div>
    <!-- End Header -->


</body>

<!-- Java Script -->
<script src="<?php echo base_url(); ?>styles/js/popper.min.js"></script>
<script src="<?php echo base_url(); ?>styles/bootstrap/bootstrap.min.js"></script>
<script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
<script src="<?php echo base_url(); ?>styles/js/imagesloaded.pkgd.min.js"></script>
<script src="<?php echo base_url(); ?>styles/js/jquery.nicescroll.min.js"></script>
<script src="<?php echo base_url(); ?>styles/js/main.js"></script>
