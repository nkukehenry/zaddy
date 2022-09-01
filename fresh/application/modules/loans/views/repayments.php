    
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
                            <h4>Search Customers</h4>
                        </div>
                        <div class="card-body">
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>loans/customers" enctype="multipart/form-data">
                                <div class="row clearfix">
                            
                            <div class="col-sm-12 col-lg-3">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" name="names" class="form-control"  autocomplete="off" value="<?php echo (!empty($search['names']))? $search['names']:''; ?>" >
                                            <label class="form-label">Customer Name</label>
                                        </div>
                                    </div>
                            </div>
                      
                            <div class="col-sm-12 col-lg-3">
                                <input type="submit" class="btn btn-success" value="Search Now"></input>
                            </div>
                            
                            </form>
                             </div>
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
                                    foreach($payments as $loan):
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
                                                <?php echo $loan->details; ?> 
                                            </h6>
                                        
                                        </td>
                                       
                                       <td>
                                            <h6 class="text-info">AMOUNT: UGX 
                                    <?php echo number_format($loan->amount);?>
                                                    
                                                </h6>
                                            <small class="text-muted">
                                                Paid: <?php 

                                                echo date('d, F Y h:i:s',strtotime($loan->paymentDate)); ?>
                                                    
                                                </small>
                                        </td>
                                        
                                     
                                       
                                    </tr>
                                    
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
      