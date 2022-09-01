            <div class="row padded col-md-12 " style="width: 100%;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark">
                            <h4>New Loan</h4>
                        </div>
                        <div class="card-body">
                 
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>loans/saveLoan" enctype="multipart/form-data">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select name="customerId" class="form-control" required autocomplete="off" >
                            
                                <option value="" selected disabled>SELECT CUSTOMER</option>
                            <?php foreach($customers as $customer): ?>
                                                        
                              <option value="<?php echo $customer->cid; ?>">
                                <?php echo $customer->customerName; ?>
                                </option>
                                        
                                        <?php endforeach; ?>
                                        
                                                    </select>
                                                    <label class="form-label">Customer</label>
                                                </div>
                                            </div>

                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="number" name="amount" class="form-control" autocomplete="off" required>
                                                    <label class="form-label">Amount</label>
                                                </div>
                                            </div>
                                            
                                     <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" onfocus="(this.type='date')" name="dateBorrowed" class="form-control" autocomplete="off" required>
                                                    <label class="form-label">Date</label>
                                                </div>
                                            </div>

                                    </div>

                                    <div class="col-sm-6">
                                   
                                             <div class="form-group form-float">
                            <div class="form-line">
                                <select name="type" class="form-control" required autocomplete="off">
                                    
                                    <option value="" selected disabled>SELECT TYPE</option>
                                    
                                        <option value="MTN Float">MTN Float</option>
                                        <option value="Airtel Float">Airtel Float</option>
                                         <option value="CASH">CASH</option>
                                        
                                         <option value="BANK TRANSFER">BANK TRANSFER</option>
                                         <option value="ELLY PAY FLOAT">ELLY PAY FLOAT</option>
                                         <option value="OTHER">OTHER</option>
                                                    </select>
                                    <label class="form-label">Loan Type</label>
                                                </div>
                                            </div>
                                            
                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <textarea name="details" class="form-control"   autocomplete="off"></textarea>
                                                    <label class="form-label">Details</label>
                                                </div>
                                            </div>
                                            
                                            <br>
                                             <br>

                                            <div class="form-group">
                                                
                                                <input type="submit" class="btn btn-success pull-right col-md-12"  value="SAVE LOAN">
                                            </div>

                                          

                                        </div>

                                    </div>

                                </div>
                            </form>
                    
                </div>
                </div>
            </div>

              
      