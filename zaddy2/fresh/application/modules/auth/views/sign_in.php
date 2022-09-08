

<?php

$config= Modules::run("settings/getAll");


?>


<!doctype html>
<html class="no-js " lang="en">

<!-- Mirrored from thememakker.com/ax/university/html/sign-in.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 16 Feb 2018 01:00:34 GMT -->
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">

<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<title><?php echo $config->system_name; ?></title>
<meta name="description" content="Inventory">
<meta name="keywords" content="Dream,Africa,Schools">

<!-- Favicon-->
<link rel="icon" href="<?php echo base_url(); ?>assets/images/das.png" type="image/x-icon">
<!-- Custom Css -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
<link href="<?php echo base_url(); ?>assets/css/main.css" rel="stylesheet">
<link href="<?php echo base_url(); ?>assets/css/login.css" rel="stylesheet">

<!--  You can choose a theme from css/themes instead of get all themes -->
<link href="<?php echo base_url(); ?>assets/css/themes/all-themes.css" rel="stylesheet" />

<style type="text/css">
.form-control{

	color: #fff;
}
	

</style>


</head>

<body class="theme-blue">
<div class="authentication">
	<div class="container-fluid">
		<div class="row clearfix">
			<div class="col-lg-9 col-md-8 col-xs-12 p-l-0">
				<div class="l-detail">
					<h5 class="position">Welcome to</h5>
					<h1 class="position"><img src="<?php echo base_url(); ?>assets/images/<?php echo $config->logo; ?>"><span><?php echo $config->system_name; ?></span> </h1>
					<h3 class="position">Sign in to start your session</h3>
					<p class="position">Any attempt to access the system without due permissions will be tracked and penalised.</p>
				
				</div>
			</div>
			
			<div class="col-lg-3 col-md-4 col-xs-12 p-r-0" >
				<div class="card position" style="background-color:maroon; color: #fff;">

					<?php echo $this->session->flashdata('msg'); ?>
					
					<h4 class="l-login">Login Here </h4>
					<form class="col-md-12" id="sign_in" method="POST" action="<?php echo base_url(); ?>auth/login">
						<div class="form-group form-float">
							<div class="form-line">
								<input type="text" name='username' class="form-control" placeholder="Username">
								
							</div>
						</div>
						<div class="form-group form-float">
							<div class="form-line">
								<input type="password" name="pass" class="form-control" placeholder="Password">
								
							</div>
						</div>
						<input type="submit" class="btn btn-raised btn-warning waves-effect" value="SIGN IN"></button>
						<div class="text-left"> <a href="<?php echo base_url(); ?>auth/recovery">Forgot Password?</a> </div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div id="instance1"></div>
</div>

<!-- Jquery Core Js --> 
<script src="<?php echo base_url(); ?>assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->
<script src="<?php echo base_url(); ?>assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 

<script src="<?php echo base_url(); ?>assets/js/pages/login2/jparticles.js"></script>
<script src="<?php echo base_url(); ?>assets/js/pages/login2/particle.js"></script>

<script src="<?php echo base_url(); ?>assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js -->
<script src="<?php echo base_url(); ?>assets/js/pages/login2/event.js"></script>

<script type="text/javascript">
	
	$('#sign_in').submit(function(e){


     var formData=$(this).serialize();

     var url ="<?php echo base_url(); ?>auth/login";

     $.ajax({
        url: url,
        method:'post',
        data:formData,
     success: function(result){

        console.log(result);

        setTimeout(function(){

            $('.status').html(result);

        },3000);
        
     
    }
    });//ajax


	});


</script>



</body>


</html>
