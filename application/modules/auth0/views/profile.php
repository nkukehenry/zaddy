   <?php

   $userdata=$this->session->userdata;

   $names=explode(" ", $userdata['names']);

   $lastname=$names[0];

   $firstname=$names[1];

   ?>


   <!-- Main content -->
    <section class="content">


      <div class="row">
        <div class="col-md-4">

          <!-- Profile Image -->
          <div class="box box-danger">
            <div class="box-body box-profile">
              
<img onclick="$('#photo').click();" class="profile-user-img img-responsive img-circle userphoto" src="<?php echo base_url(); ?>assets/images/sm/<?php echo $userdata['photo']; ?>" data-toggle="tooltip" title="Click to change photo">

		<div id="upload-demo" style="width:100%; display:none; margin: 0px; padding: 0px;"></div>

                    <center><button style="display: none;" class="crop btn">Resize</button></center>
      

              <h3 class="profile-username text-center"><?php echo $userdata['names']; ?></h3>

              <p class="text-muted text-center"><?php echo $userdata['location_name']; ?></p>

              <ul class="list-group list-group-unbordered">
                 <li class="list-group-item">
                  <b>USERNAME</b> <a class="pull-right" style="padding-right:40%;"><?php echo $userdata['username']; ?></a>
                </li>
                <li class="list-group-item">
                  <b>STAFF ID</b> <a class="pull-right"><?php echo $userdata['staffid']; ?></a>
                </li>
                <!--li class="list-group-item">
                  <b>DEPARTMENT</b> <a class="pull-right"><?php echo $userdata['department']; ?></a>
                </li-->
                

              <a href="#" data-toggle="modal" data-target="#change_pass" class="btn btn-warning btn-block"><b>CHANGE PASSWORD</b></a>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

      
        </div>
        <!-- /.col -->
        <div class="col-md-8">

          <div class="panel">
            <div class="panel-body">

              <span class=""><?php echo $this->session->flashdata('msg'); ?></span>

                <form class="form-horizontal" class="profile" method="post" action="<?php echo base_url();?>auth/updateProfile">
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">FIRST NAME</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="firstname" id="inputName" placeholder="Name" value="<?php echo $firstname; ?>">
                    </div>

                  </div>

                   <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">LAST NAME</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="lastname" id="inputName" placeholder="Last Name" value="<?php echo $lastname; ?>">
                    </div>

                  </div>

                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">USERNAME</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" name="username" id="inputName" placeholder="Username" value="<?php echo $userdata['username']; ?>">
                    </div>

                  </div>

                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">STAFF ID</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputName" name="staffid" placeholder="Staff ID" value="<?php echo $userdata['staffid']; ?>">
                    </div>

                    <input type="hidden" name="user_id" value="<?php echo $userdata['user_id']; ?>">

                    <input type="file"  style="display:none;" id="photo">

                  <input type="text" name="photo"  style="display:none;" id="pic">

                  </div>

                   

                  <div class="form-group">
                    <div class="col-sm-12 ">
                      <button type="submit" class="btn btn-danger pull-right">Save Changes</button>
                    </div>
                  </div>
                </form>

              </div>
              </div>
            
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
