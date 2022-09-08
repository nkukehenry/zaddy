<?php

$config= Modules::run("settings/getAll");



?>

<style>
    .modal-backdrop {
  z-index: -1;
}
</style>


        <!--<div class="col-md-3" style="padding-top: 2em;">
            <img  src="<?php echo BASEURL; ?>assets/img/basic/<?php echo $config->logo; ?>" width="100px">

          </div>-->

            
           <!-- div class="box-body">
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
             -->
                 <?php
               $billers=Modules::run("billers/getAll");
               
              // print_r($billers);
             ?>
             
             <div class="row">
                 
             <div class="col-md-6" style="padding-top: 2em; padding-bottom: 2em;">
            <ul class="list-group">
                <h3>ACTIVE BILLERS</h3>
            <?php
            
             foreach($billers as $biller):
                 
                 if($biller->status==1){
            ?>
              <li class="list-group-item text-left"><?php echo $biller->billerName; ?>
              <a href="#stop<?php echo $biller->billerId; ?>" data-toggle="modal" class="btn btn-danger btn-outline btn-sm pull-right">De-Activate</a>
              </li>
              
              <!-- Modal -->
            <div id="stop<?php echo $biller->billerId; ?>" class="modal fade" role="dialog">
              <div class="modal-dialog modal-md">
            
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    
                    <h4 class="modal-title">De-activate <?php echo $biller->billerName; ?></h4>
                    
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <p>Are you sure you want turn-off this biller ?</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    
                    <a href="<?php echo BASEURL; ?>billers/blockBiller/<?php echo $biller->billerId; ?>" class="btn btn-danger" >Proceed</a>
                  </div>
                </div>
            
              </div>
            </div>
          
          
          <?php 
          
                 }
          
          endforeach;
          ?>
          
          </ul>
          </div>
          
            <div class="col-md-6" style="padding-top: 2em; padding-bottom: 2em;">
            <ul class="list-group">
                
                <h3>IN-ACTIVE BILLERS</h3>
            <?php
            
             foreach($billers as $biller):
                 
                 if($biller->status==0){
            ?>
              <li class="list-group-item text-left"><?php echo $biller->billerName; ?>
              <a href="#activate<?php echo $biller->billerId; ?>" data-toggle="modal" class="btn btn-success btn-outline btn-sm pull-right">Activate</a>
              </li>
              
              
              <!-- Modal -->
            <div id="activate<?php echo $biller->billerId; ?>" class="modal fade" role="dialog">
              <div class="modal-dialog modal-md">
            
                <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    
                    <h4 class="modal-title">Activate <?php echo $biller->billerName; ?></h4>
                    
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <p>Are you sure you want turn-on this biller ?</p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    
                    <a href="<?php echo BASEURL; ?>billers/unblockBiller/<?php echo $biller->billerId; ?>" class="btn btn-success" >Proceed</a>
                  </div>
                </div>
            
              </div>
            </div>
          
          
              </li>
          
          
          <?php 
          
                 }
          
          endforeach;
          ?>
          
          </ul>
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