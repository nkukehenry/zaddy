
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

    <header class="bg-main relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">

                <div class="col-md-1">
                <a href="#" data-toggle="push-menu" class="paper-nav-toggle pp-nav-toggle">
                    <i></i>
                </a>
                </div>
                <div class="col">

                    <h4 style="margin-top:15px;">
                        <i class="icon-box"></i>
                         <?php echo $page; ?>
                    </h4>
                </div>

            </div>

        </div>
    </header>


    <div class="container-fluid relative animatedParent animateOnce">



            <!--Today Tab Start-->
            <div class="tab-pane animated fadeInUpShort" style="padding-top: 10px;">


                    <?php echo $this->session->flashdata('msg'); ?>

            		<!--content here-->

            		<?php $this->load->view($module."/".$view); ?>

            </div>

    </div>
</div>

</div>

<!--/#app -->
<script src="<?php echo ASSET_URL; ?>js/app.js"></script>

<script>(function($,d){$.each(readyQ,function(i,f){$(f)});$.each(bindReadyQ,function(i,f){$(d).bind("ready",f)})})(jQuery,document)</script>


<script type="text/javascript">

function readURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function(e) {
      $('.preview').attr('src', e.target.result);
    }

    reader.readAsDataURL(input.files[0]);
  }
}

$("#photo").change(function() {
  readURL(this);
});

</script>
</body>

</html>
