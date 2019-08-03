<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php
            if (isset($title)): echo $title;
            endif;
            ?></title>
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

        <!-- jQuery library -->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>

        <!-- bootstrap library -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>

        <!-- JQuery UI CSS -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css" />

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Droid+Sans:400,700">


        <!-- Custom CSS -->
        <link href="<?php echo base_url(); ?>styles/css/cms/sb-admin.css" rel="stylesheet">

        <!-- Morris Charts CSS -->
        <link href="<?php echo base_url(); ?>styles/css/cms/plugins/morris.css" rel="stylesheet">

        <!-- JQuery UI library -->
        <script
            src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
            integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
            crossorigin="anonymous">
        </script>
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
                    buttons:
                            [{
                                    text: "OK",
                                    click: function () {
                                        $(this).dialog("close");
                                    }
                                }]
                });
            }
        </script>
        <link href="<?php echo base_url(); ?>styles/css/cms/cms_login.css" media="all" rel="stylesheet" type="text/css" />

        <style>
            #geeks_word_p{
                font-size: 25px;
                line-height: 30px;
                font-weight: bold;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row" id="pwd-container">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <section class="login-form">
                        <?php
                        $attributes = array('name' => 'login_form', 'id' => 'cms_log_form', 'class' => 'form-horizontal', 'role' => 'login');
                        echo form_open(base_url() . 'customer/login', $attributes);
                        ?>
                        <p id="geeks_word_p">CUSTOMER PORTAL</p>
                        <?php
                        echo form_error('dusername');
                        echo form_input(array('id' => 'demail', 'name' => 'demail', 'type' => 'email', 'class' => 'form-control input-lg', 'placeholder' => 'Email', 'required' => ''));
                        echo form_error('dpass');
                        echo form_input(array('id' => 'dpass', 'name' => 'dpass', 'type' => 'password', 'class' => 'form-control input-lg', 'placeholder' => 'Password', 'required' => ''));
                        if (isset($err)) {
                            if ($err === 'normal_login_auth_error') {
                                ?>
                                <script type="text/javascript">
                                    showErrorMsg('Invalid credentials', 'Wrong username or password!');
                                </script>
                                <?php
                            }
                        }
                        echo form_submit(array('name' => 'actionButton', 'type' => 'submit', 'id' => 'actionButton', 'class' => 'btn btn-lg btn-primary btn-block', 'value' => 'Sign in'));
                        ?>
                        <div>
                            <a href="#">reset password</a>
                        </div>
                        </form>
                        <div class="form-links">
                            <a href="<?= base_url() ?>">Bot HomePage</a>
                        </div>
                    </section>  
                </div>
                <div class="col-md-4"></div>
            </div> 
        </div>
    </body>
</html>