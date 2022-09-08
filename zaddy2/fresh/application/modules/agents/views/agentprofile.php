            <div class="row padded col-md-12 " style="width: 100%;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark">
                            <h4>Merchant Profile: <?=strtoupper($agent->names)?></h4>
                        </div>
                        <div class="card-body">
                            
                            <?php //print_r($agent); ?>
                 
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>agents/saveAgentEdit/<?=$agent->agentNo?>/1" enctype="multipart/form-data">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-4">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="names" class="form-control" required autocomplete="off" value="<?=$agent->names?>">
                                                    <label class="form-label">Full Name</label>
                                                </div>
                                            </div>

                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="nin" class="form-control" autocomplete="off"  value="<?=$agent->nin?>">
                                                    <label class="form-label">National ID No.</label>
                                                </div>
                                            </div>

                                            

                                              <div class="form-group">
                                                <label class="form-label" style="display:block; text-align: center;">Passport Photo</label>
                                                
                                                <center>
                                                   <img onclick="$('#photo').click()" src="<?php echo ASSET_URL; ?>img/people/<?=$agent->photo?>" class="img img-thumbnail preview" width="200px;">
                                                    <input style="display: none;" type="file" name="photo" id="photo">
                                                </center>

                                                    <br>
                                            </div>


                                    </div>

                                    <div class="col-sm-4">
                                   
                                            
                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="location" class="form-control"  required autocomplete="off"  value="<?=$agent->location?>">
                                                    <label class="form-label">Address</label>
                                                </div>
                                            </div>

                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="email" class="form-control"  autocomplete="off"  value="<?=$agent->email?>">
                                                    <label class="form-label">Email</label>
                                                </div>
                                            </div>

                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="phoneNumber" class="form-control"  autocomplete="off"  value="<?=$agent->phoneNumber?>">
                                                    <label class="form-label" required>Phone</label>
                                                </div>
                                            </div>

                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="agentNo" class="form-control"  autocomplete="off"  value="<?=$agent->agentNo?>" readonly>
                                                    <label class="form-label" required>Agent Number:</label>
                                                </div>
                                            </div>

                                             <div class="form-group">
                                                <label class="form-label">
                                                <i class="icon icon-attach_file"></i> KYC Attachment: 
                                                </label>
                                                    <input  type="file" name="kyc" id="kyc">
                                                    <br>
                                            </div>


                                            <div class="form-group">
                                                
                                                <input type="submit" class="btn btn-success pull-right col-md-12" name="" value="SAVE CHANGES">
                                            </div>

                                          

                                        </div>
                                        
                                        </form>
                                        
                                        
                                        <div class="col-sm-4">
                                            
                                            <form class="form-material" method="post" action="">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="password" name="old" class="form-control" required autocomplete="off" >
                                                    <label class="form-label">Old Password</label>
                                                </div>
                                            </div>

                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="password" name="new" class="form-control" autocomplete="off"  >
                                                    <label class="form-label">New Password.</label>
                                                </div>
                                            </div>
                                            
                                            
                                                <div class="form-group">
                                                
                                                <input type="submit" class="btn btn-info pull-right col-md-6" name="" value="UPDATE PASSWORD">
                                            </div>
                                            
                                            </form>
                                    </div>
                                    </div>

                                </div>
                            
                    
                </div>
                </div>
            </div>

              
      