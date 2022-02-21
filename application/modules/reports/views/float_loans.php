    
            <div class="row">
                
                 <div class="col-md-12 " style="width: 100%; margin-bottom:10px;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark">
                            <h4>Search Transactions</h4>
                        </div>
                        <div class="card-body">
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>reports/floatLoans" enctype="multipart/form-data">
                                <div class="row clearfix">
                            
                            <div class="col-sm-12 col-lg-2">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" onfocus="(this.type='date')" name="startDate" class="form-control"  autocomplete="off" value="<?php echo $search['startDate']; ?>">
                                            <label class="form-label">Start Date</label>
                                        </div>
                                    </div>
                            </div>
                            
                            <div class="col-sm-12 col-lg-2">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" onfocus="(this.type='date')" name="endDate" class="form-control"  autocomplete="off" value="<?php echo $search['endDate']; ?>">
                                            <label class="form-label">End Date</label>
                                        </div>
                                    </div>
                            </div>
                            <div class="col-sm-12 col-lg-2">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" name="agentNo" class="form-control"  autocomplete="off" value="<?php echo $search['agentNo']; ?>" >
                                            <label class="form-label">Agent Number</label>
                                        </div>
                                    </div>
                            </div>
                            
                            <div class="col-sm-12 col-lg-3">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" name="customerNo" class="form-control"  autocomplete="off" value="<?php echo $search['customerNo']; ?>" >
                                            <label class="form-label">Customer Number</label>
                                        </div>
                                    </div>
                            </div>
                            
                            <div class="col-sm-12 col-lg-2">
                                       <div class="form-group ">
                                                <div class="form-line">
                                                    <select name="tranStatus" class="form-control"  autocomplete="off">
                                                        <option value="" disabled selected>Choose Status</option>
                                                        <option value="">ALL</option>
                                                        <option value="SUCCESSFUL">SUCCESSFUL</option>
                                                        <option value="PENDING">PENDING</option>
                                                        <option value="FAILED">FAILED</option>
                                                    </select>
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

                                    <thead class="text-white" style="font-weight: bolder; background-color: grey; color: #fff;">
                                        <th>DATE</th>
                                        <th>AGENT</th>
                                         <th>NAME</th>
                                        <th>TRAN REF</th>
                                        <th>ITEM</th>
                                        <th>AMOUNT</th>
                                        <th>STATUS</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                    <?php 
                                    //print_r($transactions[0]);
                                    
                                    foreach($transactions as $transaction): ?>
                                    <tr class="">
                                        <td>
                                            <?php echo $transaction->paymentDate; ?>
                                        </td>
                                        <td>
                                            <?php echo $transaction->agentNo; ?>
                                        </td>
                                    
                                        <td>
                                           <small> <?php echo $transaction->customerName; ?></small>
                                        </td>
                                        <td class="text-dark">
                                            <?php echo $transaction->requestRef; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($transaction->paymentCode=="LOAD"){ echo  "WALLET LOAD";} 
                                            else if($transaction->paymentCode=="SHARE"){ echo "FLOAT SHARE"; } 
                                            else if($transaction->paymentCode=="COMMS"){ echo $transaction->narration; } 
                                            else { echo Modules::run("billers/getItemName",$transaction->paymentCode); }?>
                                        </td>
                                        <td>
                                            UGX <?php echo number_format($transaction->amount); ?>
                                        </td>

                                        <td class="text-dark">
                                            <?php echo ($transaction->loan_settled)?'SETTLED' : 'NOT SETTLED'; ?>
                                        </td>
                                        <td>
                                            <a class="btn btn-fab-sm btn-primary shadow text-white" data-toggle="modal" data-target="#details<?php echo $transaction->noDup; ?>"><i class="icon-documents3"></i></a>
                                        </td>
                                    </tr>
                                    
                                    
                                <!-- Modal -->
                                <div id="details<?php echo $transaction->noDup; ?>" class="modal fade" role="dialog" data-backdrop="false">
                                  <div class="modal-dialog modal-lg">
            
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        
                                        <h4 class="modal-title">Details</h4>
                                        <button type="button" class="close pull-right" data-dismiss="modal">&times;</button>
                                      </div>
                                      <div class="modal-body " id="<?php echo $transaction->requestRef; ?>">
                                          <div class="row clearfix">
                                         <div class="col-md-6">
                                             <ul class="list-group" style="list-style:none;">
                                                  
                                              <li class="list-group-item">DATE : <?php echo $transaction->paymentDate; ?></li>
                                              <li class="list-group-item">CUSTOMER ID : <?php echo $transaction->customerNo; ?></li>
                                              <li class="list-group-item">CUSTOMER NAME : <?php echo $transaction->customerName; ?></li>
                                              <li class="list-group-item">CUSTOMER PHONE : <?php echo $transaction->customerPhone; ?></li>
                                              
                                              <li class="list-group-item">ITEM : <?php 
                                                            if ($transaction->paymentCode=="LOAD"){ echo  "WALLET LOAD";} 
                                                            else if($transaction->paymentCode=="SHARE"){ echo "FLOAT SHARE"; } 
                                                            else { echo Modules::run("billers/getItemName",$transaction->paymentCode); }?>
                                            </li>
                                              <li class="list-group-item">AMOUNT : <?php echo $transaction->amount; ?></li>
                                            </ul>
                                        </div>
                                        
                                        <div class="col-md-6">
                                             <ul class="list-group" style="list-style:none;">
                                              <li class="list-group-item">TERMINAL ID : <?php echo $transaction->agentNo; ?></li>
                                              <li class="list-group-item">AGENT NAME : 
                                                  <?php $agent=Modules::run("agents/getByAgentNo",$transaction->agentNo);
                                                    echo $agent->names;  ?>
                                              <li class="list-group-item">STATUS : <?php echo $transaction->finalStatus; ?></li>
                                              <li class="list-group-item msg">MESSAGE : <?php echo $transaction->responseMessage; ?></li>
                                              
                                             <?php if(!$transaction->loan_settled): ?>
                                              <li class="list-group-item" > 
                                               <button onclick="markSettled('<?php echo $transaction->requestRef; ?>')" class="btn btn-info markSettled">SETTLE LOAN</button>
                                             </li>
                                             <?php endif; ?>
                                               
                                               <li class="results<?php echo $transaction->requestRef; ?>">
                                                   
                                               </li>
                                            </ul>
                                        </div>
                                        </div>
                                                                                
                                      </div>
                                      <div class="modal-footer">
                                          
                                          <?php if($transaction->tranStatus!=='FAILED'){ ?>
                                        <button type="button" onclick="printDiv('<?php echo $transaction->requestRef; ?>')" class="btn btn-info" >PRINT</button>
                                        
                                        <?php } ?>
                                        
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                      </div>
                                    </div>
                                
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
            <script>
            
             function markSettled(requestRef){
                 
                                    $('.results'+requestRef).html("<h4>Checking status....</h4>");
                                
                                   $.ajax({
                                    method:"GET",
                                    url:"<?php echo base_url(); ?>payment/settleFloatLoan/"+requestRef,
                                    success:function(response){
                                        
                                        $('.results'+requestRef).html(response);
                                    }
                                    
                                });
                                
                            }
                      
                            
            </script>          
                            
                            
      