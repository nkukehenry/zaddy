<!DOCTYPE html>
<html lang="zxx">

<?php

$config= Modules::run("settings/getAll");


?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo ASSET_URL; ?>img/basic/logo.png" type="image/x-icon">
    <title><?php echo $config->system_name; ?></title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/app.css">
    <style>
        .loader {
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: #F5F8FA;
            z-index: 9998;
            text-align: center;
        }

        .plane-container {
            position: absolute;
            top: 50%;
            left: 50%;
        }
    </style>
    <!-- Js -->
    <!--
    --- Head Part - Use Jquery anywhere at page.
    --- http://writing.colin-gourlay.com/safely-using-ready-before-including-jquery/
    -->
    <script>(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if(x=="ready"){w.bindReadyQ.push(y);}else{w.readyQ.push(x);}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if(f===d||f===u){return a}else{p(f)}}})(window,document)</script>
</head>
<body class="light">
<!-- Pre loader -->
<div id="loader" class="loader">
    <div class="plane-container">
        <div class="preloader-wrapper small active">
            <div class="spinner-layer spinner-blue">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-red">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-yellow">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>

            <div class="spinner-layer spinner-green">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                <div class="circle"></div>
            </div><div class="circle-clipper right">
                <div class="circle"></div>
            </div>
            </div>
        </div>
    </div>
</div>
<div id="app">
<main>
    <div id="primary" class="p-t-b-100 height-full">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mx-md-auto paper-card" style="background-color: #750f27;">
                    <div class="text-center">
                        <img src="<?php echo base_url(); ?>assets/img/basic/<?php echo $config->main_logo; ?>" width="100px">
                        <h3 class="mt-2 text-white"><?php echo $config->system_name; ?></h3>
                            <small style="padding:5px;">Merchant Login</small>
                            <br>
                    </div>
                    <form action="<?php echo base_url(); ?>auth/login" method="post">
                           <?php 
                           echo "<font color='red'>".$this->session->flashdata('msg')."</font>";
                            ?>
                        <div class="form-group has-icon"><i class="icon-envelope-o"></i>
                            <input type="text" class="form-control form-control-lg"
                                   placeholder="Merchant Username" name='username'>
                        </div>
                        <div class="form-group has-icon"><i class="icon-user-secret"></i>
                            <input type="password" class="form-control form-control-lg"
                                   placeholder="Merchant Password" name="pass">
                        </div>
                        <input type="submit" class="btn btn-dark btn-lg btn-block" value="Log In">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- #primary -->
</main>

</div>
<!--/#app -->
<script src="assets/js/app.js"></script>

<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>
</body>
</html>