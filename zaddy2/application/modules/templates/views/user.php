
<?php

 include('includes/css_files.php');
 include('includes/preloader.php');

?>

<div id="app">
<?php

 include('includes/usertopbar.php');

?>



<div class="page  height-full">



    <div class="container-fluid relative animatedParent animateOnce">
        
            <!--Today Tab Start-->
            <div class="tab-pane animated fadeInUpShort" style="padding-top: 10px;">


            		<!--content here-->
            		 <?php echo $this->session->flashdata('msg'); ?>

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