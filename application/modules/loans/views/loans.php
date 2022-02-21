    
    <style>
       
            .modal-backdrop {
              z-index: -1;
            }
            
 .modal {
position: fixed !important;
}
    </style>
            <div class="row">
                
                
                   
                   
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <tbody>
                                    <?php 
                                    foreach($loans as $loan):
                                        ?>
                                    <tr class="">
                                        <td class="w-10">
                                            <img class="avatar avatar-lg" src="<?php echo ASSET_URL; ?>img/people/<?php echo $loan->photo; ?>" alt="">
                                        </td>
                                        <td>
                                            <h6><?php echo $loan->customerName; ?></h6>
                                        </td>
                                        <td>
                                            <h6>
                                                <?php echo $loan->type; ?> 
                                            </h6>
                                        
                                        <small class="text-muted">
                                                <?php 

                                                echo $loan->details; ?>
                                                    
                                                </small>
                                        </td>
                                       
                                       <td>
                                            <h6 class="text-info">AMOUNT: UGX 
                                    <?php echo number_format($loan->amount);?>
                                                    
                                                </h6>
                                            <small class="text-muted">
                                                Given: <?php 

                                                echo date('d, F Y h:i:s',strtotime($loan->dateBorrowed)); ?>
                                                    
                                                </small>
                                        </td>
                                        
                                        <td>
                                            <h6 class="text-green">PAID: UGX 
                                    <?php 
                                    $paid=Modules::run("loans/getPaidAmount",$loan->id);
                                    echo number_format($paid);?>
                        
                                                </h6>
                                            <small class="text-danger">
                                                    BAL: UGX <?php echo number_format(($loan->amount)-$paid); ?>
                                                </small>
                                        </td>
                                        
                                        
                                        <td>
                                            <a data-toggle="modal" href="#loan<?php echo $loan->id; ?>"><i class="icon icon-money"></i> Repay</a>
                                        </td>
                                       
                                    </tr>
                                    
                                    
                                    <div class="modal fade"  role="dialog" id="loan<?php echo $loan->id; ?>">
                                        <div class="modal-dialog">
                                            <form method="post" action="<?php echo base_url(); ?>loans/repay/<?php echo $loan->id; ?>">
                                            <div class="modal-content">
                                            <div class="modal-body">
                                                <div class="form-group">
                                       <input class="form-control" name="amount" type="number" required placeholder="Enter Amount">
                                                </div>
                                                
                                                <div class="form-group">
                                       <input class="form-control" type="text" name="details" placeholder="Narration">
                                                </div>
                                                
                                            </div>
                                            <div class="modal-footer">
                                            <a href="#" data-dismiss="modal">Close</a>
                                            <button type="submit" class="btn btn-success">Save Payment</button>
                                            </div>
                                          </div>
                                          </form>
                                         </div>
                                    </div>
                                    
                                  <?php endforeach;  ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

            <?php echo $links ; ?>
            
            
                            
                                <br>
                                <br>
      