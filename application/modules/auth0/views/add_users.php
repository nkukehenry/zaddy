<?php

$config= Modules::run("settings/getAll");

$locations=Modules::run("location/getAll");

$locationData='<option value="">--Select--</option>';


//compile locations to choose from

foreach ($locations as $location):

$locationData .='<option value="'.$location->location_id.'">'.$location->location_name.'</option>';

endforeach;

?>


<div class="container" style="width: 100%">

    <form class="user_form" method="post" enctype="multipart/form-data">

        <table>

        <tr>
            <td colspan="7"><span class="status"></span></td>
            
            <td colspan="1"><button type="submit" class="btn btn-sm btn-success">Save</button></td>
             <td colspan="1"><button type="reset" class="btn btn-sm btn-warning clear">Reset All</button></td>
        </tr>

        </table>

    <table id="myTable" class="order-list" cellpadding="0" style="border-collapse: collapse;">

   
   <thead>
        <tr>
            <th style="width:2%;">#</th>
            <th>Username</th>
            <th>Location</td>
            <th>Level</th>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>Staff ID</th>
            <th>Photo</th>
            <th style="width:10%;"></th>
        </tr>
    </thead>
   
    <tbody class="tb">
        <tr>
            <td data-label=""></td>
            
            <td data-label="Username:">
                <input type="text" required name="username"  class="form-control" required/>
            </td>
         
            <td data-label="Location">
                <select name="location_id"  class="form-control" required>

                <?php echo $locationData; ?>

                </select>
            </td>

              <td data-label="Level">
                <select name="role"  class="form-control" required>

                <option value="user">Normal user</option>
                <option value="sadmin">Administrator</option>

                </select>
            </td>

            <td data-label="First name">
                <input type="text" name="firstname"  class="form-control" required/>
            </td>
            <td data-label="last Name">
                <input type="text" name="lastname"  class="form-control" required/>
            </td>
            <td data-label="Staff ID">
                <input type="text" name="staffid"  class="form-control" />

                <input type="hidden" name="password" value="<?php echo $config->system_pass; ?>"  class="form-control" />
            </td>
            <td data-label="Photo">
                <input type="file" name="photo" title="User will handle this on their profile" data-toggle="tooltip"  disabled/>
            </td>

            <td data-label="" class="col-sm-2"><a class="deleteRow"></a></td>
        </tr>
    </form>

      

        <?php 

        $users=Modules::run("auth/getAll");

        $no=1;

        foreach($users as $user):  ?>

        <tr>
            <td data-label="#"><?php echo $no; ?></td>
            <td data-label="Username:"><?php echo $user->username; ?></td>
            <td data-label="Location:"><?php echo $user->location_name; ?></td>
            <td data-label="Role:"><?php echo $user->role; ?></td>
            <td data-label="first Name:"><?php echo $user->firstname; ?></td>
            <td data-label="Last Name:"><?php echo $user->lastname; ?></td>
            <td data-label="Staff Id:"><?php echo $user->staffid; ?></td>
            <td data-label="Photo:"><a href="#" data-toggle="modal" data-target="#img<?php echo $user->user_id; ?>" title="Click to show photo" data-toggle="tooltip">Show Photo</a></td>
            <td><a data-toggle="modal" data-target="#user<?php echo $user->user_id; ?>" href="#">Edit</a>
                |
            <?php if($user->state==1){ ?>

              <a data-toggle="modal" data-target="#block<?php echo $user->user_id; ?>" href="#">Block</a>
              <?php } else{ ?>
           
            <a data-toggle="modal" data-target="#unblock<?php echo $user->user_id; ?>" href="#">Activate</a>

              <?php } ?>

          </td>
            
        </tr>


<!--small modal to show Image-->
        <div class="modal" id="img<?php echo $user->user_id; ?>">
            <div class="modal-dialog">
                <div class="modal-body">

                    <h1><a href="#" style="color: #FFF;" class="pull-right" data-dismiss="modal">&times;</a></h1>

                    <center><img class="img img-thumbnail" src="<?php echo base_url()."assets/images/sm/".$user->photo; ?>" alt="No Image"/></center>
                    
                </div>
            </div>
        </div>
<!--/small modal to show Image-->

<!---include supporting modal-->

  <?php 

  include('user_details_modal.php');
  include('confirm_reset.php');
  include('confirm_block.php');

  if($user->state==0){
 
 include('confirm_unblock.php');

  }

    $no++;
    endforeach ?>

           </tbody>
   
</table>



</div>




<script>

$(document).ready(function () {

    //collapse menu on this page

if(window.location.href=="<?php echo base_url(); ?>auth/users#" || window.location.href=="<?php echo base_url(); ?>auth/users"){

$('.skin-blue').addClass('sidebar-collapse');

}




//delete a row from the form
    $("table.order-list").on("click", ".del_btn", function (event) {
        $(this).closest("tr").remove();       
        counter -= 1
    });



//Submit new user data

$(".user_form").submit(function(e){

    e.preventDefault();


    $('.status').html('<img style="max-height:50px" src="<?php echo base_url();?>assets/images/busy.gif">');



    var formData=new FormData(this);

    console.log(formData);

    var url="<?php echo base_url(); ?>auth/addUser";

    $.ajax({
        url: url,
        method:'post',
        contentType:false,
        processData:false,
        data:formData,
     success: function(result){

        console.log(result);

        setTimeout(function(){

            $('.status').html(result);

            $.notify(result,'info');

            $('.status').html('');

            $('.clear').click();

        },3000);
        
     
    }
    });//ajax


});//form submit



//Submit user update

$(".update_user").submit(function(e){

    e.preventDefault();


    $('.status').html('<img style="max-height:50px" src="<?php echo base_url();?>assets/images/busy.gif">');



    var formData=new FormData(this);

    console.log(formData);

    var url="<?php echo base_url(); ?>auth/updateUser";

    $.ajax({
        url: url,
        method:'post',
        contentType:false,
        processData:false,
        data:formData,
     success: function(result){

        console.log(result);

        setTimeout(function(){

            $('.status').html(result);

            $.notify(result,'info');

            $('.status').html('');

            $('.clear').click();

        },3000);
        
     
    }
    });//ajax


});//form submit




//reset user password

$(".reset").submit(function(e){

    e.preventDefault();


    $('.status').html('<img style="max-height:50px" src="<?php echo base_url();?>assets/images/busy.gif">');



    var formData=$(this).serialize();

    console.log(formData);

    var url="<?php echo base_url(); ?>auth/resetPass";

    $.ajax({
        url: url,
        method:'post',
        data:formData,
     success: function(result){

        console.log(result);

        setTimeout(function(){

            $('.status').html(result);

            $.notify(result,'info');

            $('.status').html('');

            $('.clear').click();

        },3000);
        
     
    }
    });//ajax


});//form submit


//block user

$(".block").submit(function(e){

    e.preventDefault();


    $('.status').html('<img style="max-height:50px" src="<?php echo base_url();?>assets/images/busy.gif">');



    var formData=$(this).serialize();

    console.log(formData);

    var url="<?php echo base_url(); ?>auth/blockUser";

    $.ajax({
        url: url,
        method:'post',
        data:formData,
     success: function(result){

        console.log(result);

        setTimeout(function(){

            $('.status').html(result);

            $.notify(result,'info');

            $('.status').html('');

            $('.clear').click();

        },3000);
        
     
    }
    });//ajax


});//form submit


//block user

$(".unblock").submit(function(e){

    e.preventDefault();


    $('.status').html('<img style="max-height:50px" src="<?php echo base_url();?>assets/images/busy.gif">');



    var formData=$(this).serialize();

    console.log(formData);

    var url="<?php echo base_url(); ?>auth/unblockUser";

    $.ajax({
        url: url,
        method:'post',
        data:formData,
     success: function(result){

        console.log(result);

        setTimeout(function(){

            $('.status').html(result);

            $.notify(result,'info');

            $('.status').html('');

            $('.clear').click();

        },3000);
        
     
    }
    });//ajax


});//form submit



});//doc ready






</script>