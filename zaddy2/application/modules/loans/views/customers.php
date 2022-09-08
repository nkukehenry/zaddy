    
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
                            
                             <!--<div class="form-group form-float">
                                        <div class="form-line">
                                          <select name="cid" class="form-control select2" required autocomplete="off" >
                            
                                           <option value="" selected disabled>ALL CUSTOMER</option>
                            
                                        
                                                    </select>
                                                </div>
                                            </div>-->
                           
                      
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
                                    foreach($customers as $customer):
                                        ?>
                                    <tr class="">
                                        <td class="w-10">
                                            <img class="avatar avatar-lg" src="<?php echo ASSET_URL; ?>img/people/<?php echo $customer->photo; ?>" alt="">
                                        </td>
                                        <td>
                                            <h6><?php echo $customer->customerName; ?></h6>
                                        </td>
                                        <td>
                                            <h6>Location: <?php echo $customer->location; ?></h6>
                                            <small class="text-muted">Contact: <?php echo $customer->phoneNumber; ?> </small>
                                        </td>
                                       
                                       <td>
                                            <h6 class="text-success">BORROWED: UGX 
                                                <?php 
                                                
                                     $balance=Modules::run("loans/getBorrowedAmount",$customer->cid);
                                     
                                               echo  number_format($balance);?>
                                                    
                                                </h6>
                                            <small class="text-muted">
                                          
                                                    Total from all loans taken
                                                </small>
                                        </td>
                                        
                                        <td>
                                            <h6 class="text-info">BALANCE: UGX 
                                                <?php 
                                                
                                     $balance=Modules::run("loans/getCustomerBalance",$customer->cid);
                                     
                                               echo  number_format($balance);?>
                                                    
                                                </h6>
                                            <small class="text-muted">
                                          
                                                    Total Unpaid Debt
                                                </small>
                                        </td>
                                        <td>
                                            <a href="<?php echo base_url(); ?>loans/customerEdit/<?php echo $customer->cid; ?>" class="btn-fab btn-fab-sm btn-primary shadow text-white"><i class="icon-pencil"></i></a>
                                            
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
      