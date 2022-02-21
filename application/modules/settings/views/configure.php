<?php

$config= Modules::run("settings/getAll");



?>
 <!-- Form Element sizes -->
          <div class="box box-danger">
            <div class="box-header">
              <h3 class="box-title">Configurations</h3>
              <span class="status pull-right"></span>
            </div>

            <div class="col-md-3" style="padding-top: 2em;">

            <img  src="<?php echo base_url(); ?>assets/images/<?php echo $config->logo; ?>">

          </div>

            <div class="col-md-8">
            <div class="box-body">
             <form class="config_form">
                <label>System Title</label>
              <input class="form-control input-lg" name="system_name" type="text" placeholder="" value="<?php echo $config->system_name; ?>" required>
              <br>
              <label>Copy year</label>
              <input  class="form-control" name="copy_year" type="number" value="<?php echo $config->copy_year; ?>" required>
              <br>
              <label>Default Password for New Accounts</label>
              <input  class="form-control" name="system_pass" type="text" value="<?php echo $config->system_pass; ?>" required>
              <br>
              <label>System Logo</label>
              <input  type="file" name="logo"  data-toggle="tooltip" title="No allowed Now">
              <div class="pull-right">
                  <input type="submit"  value="Save Changes" class="btn btn-success"  />
              </div>
            </form>
            </div>
            <!-- /.box-body -->
          </div>
       

      </div>
          <!-- /.box -->


<script>

$(document).ready(function () {



//Submit data

$(".config_form").submit(function(e){

    e.preventDefault();


    $('.status').html('<img style="max-height:50px" src="<?php echo base_url();?>assets/images/busy.gif">');



    var formData=$(this).serialize();

    var url="<?php echo base_url(); ?>settings/saveSettings";



    console.log(url);

    $.ajax({
        url: url,
        method:'post',
        data:formData,
     success: function(result){

        console.log(result);

        setTimeout(function(){

            $.notify(result,'info');

            $('.status').html('');

            $('.clear').click();

        },3000);
        
     
    }
    });//ajax


});//form submit






});//doc ready






</script>