            <div class="row padded col-md-12 ">
                    <div class=" card w-100" >
                        <div class="card-header text-muted">
                            <h4>Add New Player</h4>
                        </div>
                        <div class="card-body">
                 
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>players/savePlayer" enctype="multipart/form-data">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="lastname" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Last Name</label>
                                                </div>
                                            </div>
                                            
                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="firstname" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Other Names</label>
                                                </div>
                                            </div>

                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="dob"  required autocomplete="off"
                                                    class="date-time-picker form-control"
                                           data-options='{"timepicker":false, "format":"Y-m-d"}' >
                                                    <label class="form-label">Date of Birth</label>
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
                                                    <select name="team_id" class="form-control" required autocomplete="off">
                                                        <option disabled selected>Select team</option>
                                                        <?php $teams=Modules::run('teams/getAll');
                                                        foreach ($teams as $team):
                                                     ?>
                                                    <option value="<?php echo $team->id; ?>">
                                                        <?php echo $team->team_name; ?>
                                                        </option>
                                                     <?php endforeach; ?>
                                                    </select>
                                                    <label class="form-label">Team</label>
                                                </div>
                                            </div>
                                            
                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="identity_no" class="form-control" autocomplete="off">
                                                    <label class="form-label">Identity No.</label>
                                                </div>
                                            </div>

                                              <div class="form-group">
                                                <label class="form-label">Player Photo</label>
                                                
                                                   <img onclick="$('#photo').click()" src="<?php echo ASSET_URL; ?>img/people/player.png" class="img img-thumbnail preview" width="200px;">
                                                    <input style="display: none;" type="file" name="photo" id="photo">

                                                    <br>
                                            </div>


                                    </div>

                                    <div class="col-sm-6">
                                            <div class="form-group form-float col-md-6">
                                                <div class="form-line">
                                                    <input type="number" name="height" class="form-control" step=".01" required autocomplete="off">
                                                    <label class="form-label">Height</label>
                                                </div>
                                            </div>
                                            
                                           <div class="form-group form-float col-md-6">
                                                <div class="form-line">
                                                    <input type="number" name="weight" class="form-control" step=".01" required autocomplete="off">
                                                    <label class="form-label">Weight</label>
                                                </div>
                                            </div>


                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="email" class="form-control"  autocomplete="off">
                                                    <label class="form-label">Email</label>
                                                </div>
                                            </div>

                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="phone" class="form-control"  autocomplete="off">
                                                    <label class="form-label">Phone</label>
                                                </div>
                                            </div>


                                           <div class="card-body b-b">
                                                <div class="card-title">Previous teams (s)</div>
                                                <select class="select2" name="prev_teams[]" multiple="multiple"  autocomplete="off">

                                                    <?php $teams=Modules::run('teams/getAll');
                                                        foreach ($teams as $team):
                                                     ?>
                                                    <option value="<?php echo $team->id; ?>">
                                                        <?php echo $team->team_name; ?>
                                                        </option>
                                                     <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <br>

                                            <div class="form-group">
                                                
                                                <input type="submit" class="btn btn-success pull-right" name="" value="Save Team">
                                            </div>

                                          

                                        </div>

                                    </div>

                                </div>
                            </form>
                    
                </div>
                </div>
            </div>

              
      