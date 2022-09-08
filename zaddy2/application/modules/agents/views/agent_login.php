            <div class="row padded col-md-12 " style="width: 100%;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark">
                            <h4>Credential Manager</h4>
                        </div>
                        <div class="card-body">
                            
                             <?php //print_r($agent); print_r($user); ?>
                 
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>agents/saveAgentLogin/<?=$agent->agentNo?>" enctype="multipart/form-data">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text"  class="form-control" required autocomplete="off" value="<?=$agent->names?>" readonly>
                                                    <label class="form-label">Full Name</label>
                                                </div>
                                            </div>

                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" autocomplete="off"  value="<?=$agent->nin?>" readonly>
                                                    <label class="form-label">National ID No.</label>
                                                </div>
                                          </div>
                                          
                                        <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control"  autocomplete="off"  value="<?=$agent->phoneNumber?>" readonly>
                                                    <label class="form-label" required>Phone</label>
                                                </div>
                                            </div>

                                              <div class="form-group">
                                                <label class="form-label" style="display:block; text-align: center;">Passport Photo</label>
                                                
                                                <center>
                                                   <img  src="<?php echo ASSET_URL; ?>img/people/<?=$agent->photo?>" class="img img-thumbnail preview" width="100px;">
                                                    
                                                </center>

                                                    <br>
                                            </div>
                                            


                                    </div>

                                    <div class="col-sm-6">
                                    
                                    
                                              <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control"  autocomplete="off" name="username" value="<?=(!empty($user))? $user->username:$agent->phoneNumber?>"

                                                    <?=(!empty($user))?' readonly':''?>
                                                    >
                                                    <label class="form-label"
                                                     required>Username</label>
                                                </div>
                                            </div>
                                            
                                             <!-- <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="password" class="form-control"  autocomplete="off" name="password" >
                                                    <label class="form-label" required>Password</label>
                                                </div>
                                            </div>
                                              <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="password" class="form-control"  autocomplete="off" name="tranPin"  >
                                                    <label class="form-label" required>Transaction PIN</label>
                                                </div>
                                            </div>-->
                                      <?php if(empty($user)): ?>

                                            <div class="form-group">
                                                
                                                <input type="submit" class="btn btn-success pull-right col-md-12" name="" value="CREATE LOGIN">
                                            </div>

                                        <?php endif; ?>

                                        <?php if(!empty($user)): ?>

                                            <div class="form-group">
                                                
                                                <a href="<?php echo BASEURL;?>agents/resetAgent/<?=$agent->userId?>/<?=$agent->agentNo?>" class="btn btn-success pull-right col-md-12">
                                                    RESET PASSWORD
                                                </a>
                                                
                                                <br> <br>
                                                
                                                <a href="<?php echo BASEURL;?>agents/resetAgent/<?=$agent->userId?>/<?=$agent->agentNo?>/1" class="btn btn-info pull-right col-md-12">
                                                    RESET TRANSACTION PIN
                                                </a>
                                            </div>

                                        <?php endif; ?>

                                            
                                          

                                        </div>

                                    </div>

                                </div>
                            </form>
                    
                </div>
                </div>
            </div>

              
      