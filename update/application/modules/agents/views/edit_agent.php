            <div class="row padded col-md-12 " style="width: 100%;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark">
                            <h4>Edit Agent</h4>
                        </div>
                        <div class="card-body">
                            
                            <?php //print_r($agent); ?>
                 
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>agents/saveAgentEdit/<?=$agent->agentNo?>" enctype="multipart/form-data">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
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


                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select name="gender" class="form-control" required autocomplete="off">
                                                        <option value="M">Male</option>
                                                        <option value="F">Female</option>
                                                    </select>
                                                    <label class="form-label">Gender</label>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select name="status" class="form-control" required autocomplete="off">
                                                        <option value="1">Active</option>
                                                        <option value="0">Blocked</option>
                                                    </select>
                                                    <label class="form-label">Status</label>
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

                                    <div class="col-sm-6">
                                   
                                            
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

                                    </div>

                                </div>
                            </form>
                    
                </div>
                </div>
            </div>

              
      