    
    
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
                            <h4>Capture Received Commissions</h4>
                        </div>
                        <div class="card-body">
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>agents/recordReceived" enctype="multipart/form-data">
                                <div class="row clearfix">
                            
                            
                            <div class="col-sm-12 col-lg-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text"  name="description" class="form-control"  autocomplete="off"  required >
                                            <label class="form-label">Narration</label>
                                        </div>
                                    </div>
                            </div>
                           
                            <div class="col-sm-12 col-lg-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="number" name="amount" class="form-control"  autocomplete="off"  required>
                                            <label class="form-label">Amount</label>
                                        </div>
                                    </div>
                            </div>
                      
                            <div class="col-sm-12 col-lg-3">
                                <input type="submit" class="btn btn-info" value="SAVE PAYMENT" onclick=""></input>
                            </div>
                            </form>
                            
                            <
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
                                            <th>NARRATION</th>
                                            <th>AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    
                                    if(!empty($commissions)):
                                         $total=0;
                                    foreach($commissions as $com):
                                        $total+= $com->amount;
                                    ?>

                                    <tr class="">
                                        <td>
                                            <h6><?php echo  $com->description; ?></h6>
                                        </td>
                                    
                                       
                                       <td>
                                         UGX 
                                        <?php  echo  $com->amount; ?>
                                                    
                                        </td>

                                       
                                    </tr>
                                  <?php 
                                      endforeach;   
                                   ?>
                                   
                                   <tr class="green text-white">
                                        <td>
                                           <b>TOTAL COMMISION RECEIVED</b> 
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
      