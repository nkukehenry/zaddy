
<?php

 include('includes/css_files.php');
 include('includes/preloader.php');

?>

<div id="app">
<?php

 include('includes/sidebar.php');
 include('includes/topbar.php');

?>



<div class="page has-sidebar-left height-full">

    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon-box"></i>
                        Dashboard
                    </h4>
                </div>
            </div>
           
        </div>
    </header>


    <div class="container-fluid relative animatedParent animateOnce">
        
            <!--Today Tab Start-->
            <div class="tab-pane animated fadeInUpShort" style="padding-top: 10px;">


            		<!--content here-->

            		<?php include('form.php'); ?>

            </div>
           
    </div>
</div>

</div>

<!--/#app -->
<script src="assets/js/app.js"></script>

<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>
</body>

</html>