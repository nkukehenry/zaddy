            <div class="row padded col-md-12 " style="width: 100%;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark">
                            <h4>Register New Staff User</h4>
                        </div>
                        <div class="card-body">
                            
                            <?php
                            
                            //print_r($users);
                            
                            ?>
                 
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>auth/enrollUser" enctype="multipart/form-data">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="fullNames" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Full Name</label>
                                                </div>
                                            </div>
                                            
                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="username" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Username</label>
                                                </div>
                                            </div>
                                            
                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="password" name="password" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Password</label>
                                                </div>
                                            </div>

                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select name="userType" class="form-control" required autocomplete="off">
                                                        <option value="0">Monitoring</option>
                                                        <option value="1">Administrator</option>
                                                    </select>
                                                    <label class="form-label">User Role</label>
                                                </div>
                                            </div>


                                    </div>

                                    <div class="col-sm-6">
                                   
                                            <div class="form-group">
                                                
                                                <input type="submit" class="btn btn-success pull-right col-md-12" name="" value="SAVE AGENT">
                                            </div>

                                          

                                        </div>

                                    </div>

                                </div>
                            </form>
                    
                </div>
                </div>
            </div>


                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <tbody>
                                    <?php 
                                    
                                    foreach($users as $user):
                                        
                                        $name=$user->fullNames;
                                        
                                        (empty($name))? Modules::run('agents/getByUserId',$user->user_id)->names:$name;
                                        
                                        if(empty($name))
                                        $name=$user->username;
                                         
                                        
                                        
                                        ?>
                                    <tr class="">
                                        <td >
                                            <?php echo $name; ?>
                                        </td>
                                        
                                        </tr>
                                        
                                        <?php endforeach ?>
                                        </table>
              
      