<!DOCTYPE html>

<?php
     $user=$this->session->userdata();
     $config= Modules::run("settings/getAll");
?>
<html lang="zxx">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo ASSET_URL; ?>img/basic/logo.png" type="image/x-icon">
    <title><?php echo $config->system_name; ?></title>
    <!-- CSS -->
       <link rel="stylesheet" href="https://pingendo.com/assets/bootstrap/bootstrap-4.0.0-alpha.6.css" type="text/css"> 
    <link rel="stylesheet" href="<?php echo ASSET_URL; ?>css/app.css">
    <style>
         
            #nprogress,.paper-nav-toggle{
              display: none;
            }
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
        .padded{
            padding: 1%;
        }
        .block{
            width: 100%;
        }
        .pull-right{
            float: right;
            margin-right: 2%;
        }
        .text-caps{
            text-transform: uppercase;
        }

        .bg-main{
            background-color: #0d5980;
            color: #fff;
        }
        .bg-mild{
            background-color: #CFC925;
            color: #045955;
        }
        .text-mild{
            color: #000;
        }
        .text-main{
            color: #0d5980;
        }
        .my-3 .col-md-3{
            margin-bottom: 5px;
        }



    </style>
    <!-- Js -->
    
    <script>(function(w,d,u){w.readyQ=[];w.bindReadyQ=[];function p(x,y){if(x=="ready"){w.bindReadyQ.push(y);}else{w.readyQ.push(x);}};var a={ready:p,bind:p};w.$=w.jQuery=function(f){if(f===d||f===u){return a}else{p(f)}}})(window,document)</script>
</head>
<body class="light">