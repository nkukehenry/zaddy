    
            <div class="row">
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">


                            
                                    
                            
                                <form method="post" class="form-material" action="<?php echo base_url(); ?>reports/statement" >
                                    
                                    <div class="row clearfix" style="padding-top:80px; padding-left:40px;">
                                    <div class="col-md-2">
                                     <div class="form-group form-float">
                                                <div class="form-line">
                                                     <input class="form-control" type="text" autocomplete="off" name="agentno" value="<?php echo $agentNo; ?>">
                                                    <label class="form-label">AGENT NUMBER</label>
                                                </div>
                                            </div>
                                    </div>
                                    
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
                                    <div class="col-md-6">
                                        <input class="btn btn-success btn-sm" type="submit" value="Search">
                                    
                                       <?php  if($agent){ ?>
                                        <a href="<?php echo base_url(); ?>reports/getPdfStatement/<?php echo $agentNo; ?>/<?php echo $search['startDate']; ?>/<?php echo $search['endDate']; ?>" target="_blank" class="btn btn-success btn-sm">EXPORT TO PDF</a>
                                        
                                        <a href="<?php echo base_url(); ?>reports/exportExcelStatement/<?php echo $agentNo; ?>/<?php echo $search['startDate']; ?>/<?php echo $search['endDate']; ?>" class="btn btn-success btn-sm">EXPORT TO EXCEL</a>
                                        
                                        <?php } ?>
                                    </div>
                                    <br>
                                    </div>
                                </form>
                            
                            
                            <div class="table-responsive">
                                <center>
                                    <br>
                                <?php 

                                //print_r($transactions[0]);

                                if($agent)
                                             echo "<h2><b> AGENT: ".$agent->names."</b></h2>"; ?>
                                             
                                             <?php if(!empty($agentNo) && !$agent)
                                             echo "<h2 class='text-danger'> Unrecognised Agent Number</h2>"; ?>
                                             <br>
                                             <h2>
                                             STARTING BALANCE: UGX
                                             <?php echo number_format((float)$startingBalance, 2, '.', ''); ?>
                                             <h2>
                                             
                                </center>
                                             
                                <table class="table table-hover ">

                                    <thead class="text-white" style="font-weight: bolder; background-color: grey; color: #fff;">
                                        <th>DATE</th>
                                        <th>ITEM NAME</th>
                                        <th>FROM</th>
                                        <th>FROM NAME</th>
                                        <th>TO</th>
                                        <th>TO NAME</th>
                                        <th>TRAN REF</th>
                                        <th>STATUS</th>
                                        <th>IMPACT/AMOUNT</th>
                                        <th>BALANCE</th>
                                    </thead>
                                    <tbody>
                                    <?php 
                                        $balance=number_format((float)$startingBalance, 2, '.', '');
                                        
                                        //print_r($transactions);
                                        
                                        $class="text-danger";
                                        
                                    foreach($transactions as $transaction):
                                        
                                        $balance += $transaction->impact;
                                        
                                        if($transaction->impact>0)
                                          $class="text-green";
                                         if($transaction->impact<0)
                                          $class="text-danger";
                                    ?>
                                    <tr class="">
                                        <td>
                                            <?php echo $transaction->paymentDate; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if ($transaction->paymentCode=="LOAD")
                                            {  
                                                if($transaction->impact<0)
                                                echo  "WALLET DEBIT";
                                                if($transaction->impact>0)
                                                echo  "WALLET LOAD";
                                                
                                            } 
                                           else if($transaction->paymentCode=="SHARE"){ echo "FLOAT SHARE"; } 
                                            else if($transaction->paymentCode=="COMMS"){ echo $transaction->narration; } 
                                            else { echo Modules::run("billers/getItemName",$transaction->paymentCode); }?>
                                        </td>
                                         <td class="text-dark">
                                            <?php 
                                            
                                             if($transaction->impact>0){
                                                echo $transaction->agentNo;
                                              } 
                                              else if($transaction->impact<0 ){
                                                  
                                                 echo $transaction->agentNo;
                                              }
                                            ?>
                                        </td>
                                         <td class="text-dark">
                                            <?php 
                                            
                                             if($transaction->impact>0){
                                                
                                                
                                                echo $transaction->customerName;
                                              } 
                                              else if($transaction->impact<0 ){
                                                  echo $transaction->names;
                                              }
                                            ?>
                                        </td>
                                        
                                        
                                        <td class="text-dark">
                                            <?php 
                                            
                                             if($transaction->impact>0){
                                                echo $transaction->customerNo;
                                              } 
                                              else if($transaction->impact<0 ){
                                                  
                                                 echo $transaction->customerNo;
                                              }
                                            ?>
                                        </td>
                                        
                                         <td class="text-dark">
                                            <?php if($transaction->impact>0){
                                                  echo  $transaction->names;
                                            }
                                              else if($transaction->impact<0 ){
                                           
                                                 echo $transaction->customerName;
                                              }?>
                                        </td>
                                        <td class="text-dark">
                                            <?php echo $transaction->requestRef; ?>
                                        </td>
                                        <td><?php echo $transaction->finalStatus; ?></td>
                                        <td class="<?php echo $class; ?>">
                                            UGX <?php echo number_format($transaction->impact); ?>
                                        </td>
                                        <td >
                                           <b></b>UGX <?php echo number_format($balance); ?></b>
                                        </td>
                                     
                                    </tr>
                                  <?php endforeach;  ?>
                                  
                                  <tfoot>
                                      <th colspan="6">CLOSING BALANCE </th>
                                      <th colspan="3" class="text-green">UGX <?php echo number_format($balance); ?></th>
                                  </tfoot>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

            <?php //echo $links ; ?>
      