    
    
     <style>
       
            .modal-backdrop {
              z-index: -1;
            }
            
 .modal {
position: fixed !important;
}
    </style>
    
            <div class="row">
                
                
                    <div class="col-md-12 " style="width: 100%; margin-bottom:10px;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark">
                            <h4>Search Commission period</h4>
                        </div>
                        <div class="card-body">
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>agents/commissionlist" enctype="multipart/form-data">
                                <div class="row clearfix">
                            
                            <div class="col-sm-12 col-lg-2">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" onfocus="(this.type='date')" name="start" class="form-control"  autocomplete="off" value="<?php echo (!empty($search['start']))? $search['start']:''; ?>" required>
                                            <label class="form-label">Start Date</label>
                                        </div>
                                    </div>
                            </div>
                            
                            <div class="col-sm-12 col-lg-2">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" onfocus="(this.type='date')" name="end" class="form-control"  autocomplete="off" value="<?php echo (!empty($search['end']))? $search['end']:''; ?>" required >
                                            <label class="form-label">End Date</label>
                                        </div>
                                    </div>
                            </div>
                           
                            <div class="col-sm-12 col-lg-2">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" name="agentNo" class="form-control"  autocomplete="off" value="<?php echo (!empty($search['agentNo']))? $search['agentNo']:''; ?>" >
                                            <label class="form-label">Agent Number</label>
                                        </div>
                                    </div>
                            </div>
                      
                            <div class="col-sm-12 col-lg-3">
                                <input type="submit" class="btn btn-info" value="Search Now" onclick=""></input>
                            </div>
                            </form>
                            
                            <?php if(!empty($comagents)){ ?>
                            <form  method="post" action="<?php echo BASEURL;?>agents/payComms"
                            
                            <div class="col-sm-12 col-lg-3">
                                <a href="#paynow" data-toggle="modal" class="btn btn-success" >CONFRIM PAYMENT</a>
                            </div>
                            
                            <!--hidden fields-->
                            <input type="hidden" onfocus="(this.type='date')" name="start" class="form-control"  autocomplete="off" value="<?php echo (!empty($search['start']))? $search['start']:''; ?>" >
                            
                            <input type="hidden" onfocus="(this.type='date')" name="end" class="form-control"  autocomplete="off" value="<?php echo (!empty($search['end']))? $search['end']:''; ?>" >
                            
                            <input type="hidden" name="agentNo" class="form-control"  autocomplete="off" value="<?php echo (!empty($search['agentNo']))? $search['agentNo']:''; ?>" >
                            
                            
                                    <div class="modal fade"  role="dialog" id="paynow">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                            <div class="modal-body">
                                        <center class="text-green">
                                            <b>PROCEED TO PAYMENT ?</b></center>
                                            
                                            <input placeholder="Enter narration" required class="form-control narration" name="narration">
                                            
                                            </input>
                                            </div>
                                            <div class="modal-footer">
                                            <a href="#" class="btn btn-warning" data-dismiss="modal">CANCEL</a>
                                            <button type="submit" class="btn btn-success" name="pay">CONFIRM & PAY</button>
                                            </div>
                                          </div>
                                         </div>
                                    </div>
                            </form>
                            
                            
                                <?php }  ?>
                             </div>
                        </div>
                    </div>
                </div>
                
                   
                   
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    
                                    <thead>
                                        <tr class="green text-white">
                                            <th>AGENT</th>
                                            <th>COMMISION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    
                                    if(!empty($comagents)):
                                         $total=0;
                                    foreach($comagents as $agent):
                                        $total+=$agent['commission'];
                                    ?>

                                    <tr class="">
                                        <td>
                                            <h6><?php echo $agent['names']; ?></h6>
                                            <small class="text-dark"><?php echo $agent['agentNo']; ?></small>
                                        </td>
                                    
                                       
                                       <td>
                                         UGX 
                                        <?php  echo $agent['commission']; ?>
                                                    
                                        </td>

                                       
                                    </tr>
                                  <?php 
                                      endforeach;   
                                   ?>
                                   
                                   <tr class="green text-white">
                                        <td>
                                           <b>TOTAL COMMISION TO PAY</b> 
                                       <td>
                                         UGX 
                                        <?php  echo  number_format((float)$total, 2, '.', ''); ?>
                                                    
                                        </td>

                                       
                                    </tr>
                                    
                                    <?php  endif;  ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
                            
                                <br>
                                <br>
      