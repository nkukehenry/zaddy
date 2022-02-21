            <div class="row padded col-md-12 ">
                    <div class=" card w-100" >
                        <div class="card-header text-muted">
                            <h4>Add New Team</h4>
                        </div>
                        <div class="card-body">
                 
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>teams/saveTeam" enctype="multipart/form-data">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="team_name" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Team Name</label>
                                                </div>
                                            </div>
                                            
                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="home_field" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Home Grounds</label>
                                                </div>
                                            </div>

                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="address" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Address</label>
                                                </div>
                                            </div>
                                            
                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="licence_no" class="form-control" autocomplete="off">
                                                    <label class="form-label">Licence No.</label>
                                                </div>
                                            </div>

                                              <div class="form-group">
                                                <label class="form-label">Club Logo</label>
                                                
                                                   <img onclick="$('#photo').click()" src="<?php echo ASSET_URL; ?>img/clubs/team.jpg" class="img img-thumbnail preview" width="150px;">
                                                    
                                                    <input style="display: none;" type="file" name="logo" id="photo">

                                                    <br>
                                            </div>


                                    </div>

                                    <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="owners" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Owner (s)</label>
                                                </div>
                                            </div>
                                            
                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="phone_no" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Phone No.</label>
                                                </div>
                                            </div>


                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="email" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Email</label>
                                                </div>
                                            </div>


                                           <div class="card-body b-b">
                                                <div class="card-title">Touranment (s)</div>
                                                <select class="select2" name="tournaments[]" multiple="multiple" required autocomplete="off">

                                                    <?php $tournaments=Modules::run('tournaments/getAll');
                                                        foreach ($tournaments as $tournament):
                                                     ?>
                                                    <option value="<?php echo $tournament->id; ?>">
                                                        <?php echo $tournament->tournament_name; ?>
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

              
      