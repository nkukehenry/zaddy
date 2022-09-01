<!-- Default modal Size -->
<div class="modal fade" id="user<?php echo $user->user_id; ?>"  >
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="defaultModalLabel"><?php echo $user->firstname." ".$user->lastname; ?></h4>
            </div>
            <div class="modal-body"> 

              <form class="update_user" enctype="multipart/form-data" method="post" action="<?php echo base_url(); ?>auth/updateUser">

              <ul class="list-group">

                <center>
                <img  class="profile-user-img img-responsive img-thumbnail" src="<?php echo base_url(); ?>assets/images/sm/<?php echo $user->photo; ?>">
              </center>

              <br>

               
                <li class="list-group-item list-group-item-default"><strong style="margin-right: 1em;"> FIRST NAME: </strong> <input type="text" name="firstname" value="<?php echo $user->firstname; ?>" class="form-control" required/> </li>
                <li class="list-group-item list-group-item-default"><strong style="margin-right: 1em;">LAST NAME: </strong> <input type="text" name="lastname" value="<?php echo $user->lastname; ?>" class="form-control" required /> </li>
                <li class="list-group-item list-group-item-default"><strong style="margin-right: 1em;">USERNAME: </strong> <input type="text" name="username" value="<?php echo $user->username; ?>" class="form-control" required /> </li>
                <li class="list-group-item list-group-item-default" ><strong style="margin-right: 1em;">LOCATION: </strong> 

                  <select name="location_id"  class="form-control" required>

                    <option value="<?php echo $user->location_id; ?>"><?php echo $user->location_name; ?> </option>

                <?php echo $locationData; ?>

                </select>

                <input type="hidden" name="user_id" value="<?php echo $user->user_id; ?>">

                  

                </li>

                  <li class="list-group-item list-group-item-default"><strong style="margin-right: 1em;"> CHANGE PHOTO: </strong> <input type="file" name="photo" data-toggle="tooltip" title="Please let users change their photos via profile" disabled/> </li>
                

              </ul>

             </div>
            <div class="modal-footer">

                <?php if($user->state==1) { ?>

                <button type="submit"  data-toggle="modal" class="btn btn-info waves-effect">Save Changes</button>

                <button type="button"   data-toggle="modal" data-target="#block<?php echo $user->user_id; ?> "  class="btn btn-info waves-effect" onclick="$('.close').click();" >Block</button>

                <button type="button"   data-toggle="modal" data-target="#reset<?php echo $user->user_id; ?> "  class="btn btn-danger waves-effect" onclick="$('.close').click();"  >Reset Password</button>

                <?php } else{ 
                  // user is deactivated
                  ?>

                  <button type="button" data-toggle="modal" data-target="#unblock<?php echo $user->user_id; ?> "  class="btn btn-success waves-effect" onclick="$('.close').click();" class="btn btn-info waves-effect">Activate</button>


                <?php } ?>

                <a href="#" class="close btn" data-dismiss="modal">Close</a>

              </form>
            </div>
        </div>
    </div>
</div>
