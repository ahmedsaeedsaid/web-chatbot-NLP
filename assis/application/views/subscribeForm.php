
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php
            if (isset($title)): echo $title;
            endif;
            ?></title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />
  <link href="<?php echo base_url(); ?>styles/css/subscribeForm.css" rel="stylesheet"/>
  <style>

  </style>
 </head>
 <body>
<?php

?>
  <div class="container box">
   
   <form method="post" id="register_form" action="<?php echo base_url(); ?>Subscribe/submitSubscription">
    <input type="hidden" name="package_id" value="<?=$package_id?>">
    <ul class="nav nav-tabs">
     <li class="nav-item">
      <a class="nav-link active_tab1" style="border:1px solid #ccc" id="list_personal_details">Personal Details</a>
     </li>
     <li class="nav-item">
      <a class="nav-link inactive_tab1" id="list_general_details" style="border:1px solid #ccc">Company Details</a>
     </li>
     <li class="nav-item">
      <a class="nav-link inactive_tab1" id="list_Database_details" style="border:1px solid #ccc">Database Details</a>
     </li>
     
    </ul>
    <div class="tab-content" style="margin-top:16px;">
     <div class="tab-pane active" id="personal_details">
      <div class="panel panel-default">
       <div class="panel-heading">Personal Details</div>
       <div class="panel-body">
       <div class="contact-form">
            <div class="row">
                <div class="col-lg-12">
                    <label>Name</label>
                    <input type="text" name="name" id="name" class="form-control" />
                    <span id="error_name" class="text-danger"></span>
                </div>
                
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12">
                    <label>Email</label>
                    <input type="email" name="email" id="email" class="form-control" />
                    <span id="error_email" class="text-danger"></span>
                </div>
                
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12">
                    <label>Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" />
                    <span id="error_phone" class="text-danger"></span>
                </div>
                
            </div>
            <br>

            
        
        </div>
    
        
        <br />
        <div align="center">
         <button type="button" name="btn_personal_details" id="btn_personal_details" class="btn btn-info btn-lg">Next</button>
        </div>
        <br />
       </div>
      </div>
     </div>

     <div class="tab-pane fade" id="general_details">
      <div class="panel panel-default">
       <div class="panel-heading">Company Details</div>
       <div class="panel-body">
        <div class="contact-form">
            <div class="row">
                <div class="col-lg-6">
                    <label>Company Name</label>
                    <input type="text" name="company" id="company" class="form-control"  />
                    <span id="error_company" class="text-danger"></span>
                </div>
                <div class="col-lg-6">
                    <label>Domain</label>
                    <input type="text" name="domain" id="domain" class="form-control" />
                    <span id="error_domain" class="text-danger"></span>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-lg-6">
                    <label>Platform</label>
                    <select name="platform" id="platform" class="form-control">
                        <?php
                            foreach ($platforms as $platform)
                            {
                                ?>
                                <option value="<?=$platform->id?>"><?=$platform->name?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <span id="error_platform" class="text-danger"></span>
                </div>
                <div class="col-lg-6">
                    <label>website type</label>
                    <select name="website_type" id="website_type" class="form-control">
                    <?php
                            foreach ($websiteTypes as $websiteType)
                            {
                                ?>
                                <option value="<?=$websiteType->id?>"><?=$websiteType->name?></option>
                                <?php
                            }
                        ?>
                    </select>
                    <span id="error_website_type" class="text-danger"></span>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-lg-12">
                    <label>Description</label>
                    <textarea name="description" id="description" class="form-control" cols="15" rows="5" style="resize: none;"></textarea>
                    
                    <span id="error_description" class="text-danger"></span>
                </div>
                
            </div>
        
        </div>
        
        <br />
        <div align="center">
         <button type="button" name="previous_btn_personal_details" id="previous_btn_personal_details" class="btn btn-default btn-lg">Previous</button>
         <button type="button" name="btn_general_details" id="btn_general_details" class="btn btn-info btn-lg">Next</button>
        </div>
        <br />
       </div>
      </div>
     </div>
     <div class="tab-pane fade" id="Database_details">
      <div class="panel panel-default">
       <div class="panel-heading">Fill Database Details</div>
       <div class="panel-body">
       <div class="contact-form">
            <div class="row">
                <div class="col-lg-12">
                    <label>Database Server Name</label>
                    <input type="text" name="server" id="server" class="form-control" />
                    <span id="error_server" class="text-danger"></span>
                </div>
                
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12">
                    <label>Database username</label>
                    <input type="text" name="username" id="username" class="form-control" />
                    <span id="error_username" class="text-danger"></span>
                </div>
                
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12">
                    <label>Database Password</label>
                    <input type="text" name="password" id="password" class="form-control" />
                    <span id="error_password" class="text-danger"></span>
                </div>
                
            </div>
            <br>
            <div class="row">
                <div class="col-lg-12">
                    <label>Database Name</label>
                    <input type="text" name="DB_name" id="DB_name" class="form-control" />
                    <span id="error_DB_name" class="text-danger"></span>
                </div>
                
            </div>
            <br>

            
        
        </div>
        <br />
        <div align="center">
         <button type="button" name="previous_btn_general_details" id="previous_btn_general_details" class="btn btn-default btn-lg">Previous</button>
         <button type="button" name="btn_action_details" id="btn_action_details" class="btn btn-info btn-lg">Next</button>
        </div>
        <br />
       </div>
      </div>
     </div>
     
    </div>
   </form>
  </div>
 </body>
</html>

<script>
$(document).ready(function(){
 
 $('#btn_general_details').click(function(){
    $('#btn_general_details').attr("disabled", "disabled");
  var error_company = '';
  var error_domain = '';
  var error_description = '';
  var filter =  new RegExp(/^((?:(?:(?:\w[\.\-\+]?)*)\w)+)((?:(?:(?:\w[\.\-\+]?){0,62})\w)+)\.(\w{2,6})$/);
  
  if($.trim($('#company').val()).length == 0)
  {
   error_company = 'company name is required';
   $('#error_company').text(error_company);
   $('#company').addClass('has-error');
  }
  else
  {
   
    error_company = '';
    $('#error_company').text(error_company);
    $('#company').removeClass('has-error');
   
  }
  
  if($.trim($('#description').val()).length == 0)
  {
   error_description = 'description is required';
   $('#error_description').text(error_description);
   $('#description').addClass('has-error');
  }
  else
  {
   
    error_description = '';
   $('#error_description').text(error_description);
   $('#description').removeClass('has-error');
   
  }
  
  if($.trim($('#domain').val()).length == 0)
  {
    error_domain = 'domain is required';
   $('#error_domain').text(error_domain);
   $('#domain').addClass('has-error');
  }
  else
  {
    var doma = $('#domain').val();
    
        if(!filter.test(doma))
        {
            error_domain = 'invalid domain name';
            $('#error_domain').text(error_domain);
            $('#domain').addClass('has-error');
        }
        else
        {
            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>" + "Subscribe/validateDomain", 
                data: {domain: doma},
                dataType: "text",  
                cache:false,
                success: 
                    function(data){
                        $('#btn_general_details').removeAttr("disabled");
                        if(data=='no')
                        {
                            var error_domain = 'invalid domain';
                            $('#error_domain').text(error_domain);
                            $('#domain').addClass('has-error');
                        }
                        else
                        {
                            var error_domain = '';
                            $('#error_domain').text(error_domain);
                            $('#company').removeClass('has-error');
                        }
                        if($('#error_description').text() != '' || $('#error_domain').text() != '' || $('#error_company').text() != '')
                        {
                            return false;
                        }
                        else
                        {
                            $('#list_general_details').removeClass('active active_tab1');
                            $('#list_general_details').removeAttr('href data-toggle');
                            $('#general_details').removeClass('active');
                            $('#list_general_details').addClass('inactive_tab1');
                            $('#list_Database_details').removeClass('inactive_tab1');
                            $('#list_Database_details').addClass('active_tab1 active');
                            $('#list_Database_details').attr('href', '#personal_details');
                            $('#list_Database_details').attr('data-toggle', 'tab');
                            $('#Database_details').addClass('active in');
                        }
                        
                    }

            });
        }
         
  }

  
  
 });
 
 $('#previous_btn_general_details').click(function(){
  $('#list_Database_details').removeClass('active active_tab1');
  $('#list_Database_details').removeAttr('href data-toggle');
  $('#Database_details').removeClass('active in');
  $('#list_Database_details').addClass('inactive_tab1');
  $('#list_general_details').removeClass('inactive_tab1');
  $('#list_general_details').addClass('active_tab1 active');
  $('#list_general_details').attr('href', '#login_details');
  $('#list_general_details').attr('data-toggle', 'tab');
  $('#general_details').addClass('active in');
 });

 $('#previous_btn_personal_details').click(function(){
  $('#list_general_details').removeClass('active active_tab1');
  $('#list_general_details').removeAttr('href data-toggle');
  $('#general_details').removeClass('active in');
  $('#list_general_details').addClass('inactive_tab1');
  $('#list_personal_details').removeClass('inactive_tab1');
  $('#list_personal_details').addClass('active_tab1 active');
  $('#list_personal_details').attr('href', '#login_details');
  $('#list_personal_details').attr('data-toggle', 'tab');
  $('#personal_details').addClass('active in');
 });
 
 $('#btn_personal_details').click(function(){
  var error_name= '';
  var error_email = '';
  var error_phone= '';
  var mobile_validation = /^\d{10}$/;
  if($.trim($('#name').val()).length == 0)
  {
    error_name = 'Name is required';
   $('#error_name').text(error_name);
   $('#name').addClass('has-error');
  }
  else
  {
    error_name = '';
   $('#error_name').text(error_name);
   $('#name').removeClass('has-error');
  }

  if($.trim($('#email').val()).length == 0)
  {
    error_email = 'email is required';
   $('#error_email').text(error_email);
   $('#email').addClass('has-error');
  }
  else
  {
    error_email = '';
   $('#error_email').text(error_email);
   $('#email').removeClass('has-error');
  }


  if($.trim($('#phone').val()).length == 0)
  {
    error_phone = 'phone is required';
   $('#error_phone').text(error_phone);
   $('#phone').addClass('has-error');
  }
  else
  {
    error_phone = '';
   $('#error_phone').text(error_phone);
   $('#phone').removeClass('has-error');
  }

  
  
  
  if(error_phone != '' ||error_email != '' || error_name != '' )
  {
   return false;
  }
  else
  {
    $('#list_personal_details').removeClass('active active_tab1');
    $('#list_personal_details').removeAttr('href data-toggle');
    $('#personal_details').removeClass('active in');
    $('#list_personal_details').addClass('inactive_tab1');
    $('#list_general_details').removeClass('inactive_tab1');
    $('#list_general_details').addClass('active_tab1 active');
    $('#list_general_details').attr('href', '#login_details');
    $('#list_general_details').attr('data-toggle', 'tab');
    $('#general_details').addClass('active in');
  }
  
 });
 
 $('#btn_action_details').click(function(){
  var error_server = '';
  var error_username = '';
  var error_password = '';
  var error_DB_name = '';
  var mobile_validation = /^\d{10}$/;
  if($.trim($('#server').val()).length == 0)
  {
    error_server = 'Server Name is required';
   $('#error_server').text(error_server);
   $('#server').addClass('has-error');
  }
  else
  {
    error_server = '';
   $('#error_server').text(error_server);
   $('#server').removeClass('has-error');
  }

  if($.trim($('#username').val()).length == 0)
  {
    error_username = 'username is required';
   $('#error_username').text(error_username);
   $('#username').addClass('has-error');
  }
  else
  {
    error_username = '';
   $('#error_username').text(error_username);
   $('#username').removeClass('has-error');
  }


  if($.trim($('#password').val()).length == 0)
  {
    error_password = 'password is required';
   $('#error_password').text(error_password);
   $('#password').addClass('has-error');
  }
  else
  {
    error_password = '';
   $('#error_password').text(error_password);
   $('#password').removeClass('has-error');
  }

  if($.trim($('#DB_name').val()).length == 0)
  {
    error_DB_name = 'name is required';
   $('#error_DB_name').text(error_DB_name);
   $('#DB_name').addClass('has-error');
  }
  else
  {
    error_DB_name = '';
   $('#error_DB_name').text(error_DB_name);
   $('#DB_name').removeClass('has-error');
  }
  
  
  if(error_password != '' ||error_DB_name != '' || error_username != '' || error_server != '' )
  {
   return false;
  }
  else
  {
   $('#btn_action_details').attr("disabled", "disabled");
   $(document).css('cursor', 'prgress');
   $("#register_form").submit();
  }
  
 });
 
});
</script>