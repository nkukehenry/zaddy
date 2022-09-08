    
           
           <?php
     $user=$this->session->userdata();
?>
            <div class="row" >
                
                 <div class="col-md-12 " style="width: 100%;  margin-bottom:10px;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark">
                            <h4>Search Transactions</h4>
                        </div>
                        <div class="card-body">
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>reports/testTransactions" enctype="multipart/form-data">
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
                                                    <select name="tranStatus" class="form-control"   autocomplete="off">
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
                                
                                <a href="<?php echo BASEURL; ?>reports/exportTestExcelTrans/<?php echo $search['startDate']; ?>/<?php echo $search['endDate']; ?>/<?php echo $search['agentNo']; ?>/<?php echo $search['customerNo']; ?>/<?php echo $search['tranStatus']; ?>" class="btn btn-success" >
                                    EXCEL DOWNLAOD
                                </a>                            </div>
                            
                            </form>
                             </div>
                        </div>
                    </div>
                </div>
                
                   
                
                <div class="col-md-12" style="min-height:800px;">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover ">

                                    <thead class="text-white" style="font-weight: bolder; background-color: grey; color: #fff;">
                                        <th>DATE</th>
                                        <th>AGENT</th>
                                        <th>TRAN REF</th>
                                        <th>ITEM</th>
                                        <th>AMOUNT</th>
                                        <th>ACC. NO</th>
                                        <th>EARNED</th>
                                        <th>STATUS</th>
                                        <th></th>
                                    </thead>
                                    <tbody>
                                    <?php foreach($transactions as $transaction): ?>
                                    <tr class="">
                                        <td>
                                            <?php echo $transaction->paymentDate; ?>
                                        </td>
                                        <td>
                                            <?php echo $transaction->agentNo; ?>
                                        </td>
                                        <td class="text-dark">
                                            <?php echo $transaction->requestRef; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            
                                        if(strpos($transaction->requestRef,"COMS") !==false){
                                                
                                                echo "REDEEMED COMMISSION";
                                            }
                                            else
                                            if ($transaction->paymentCode=="LOAD"){ echo  "WALLET LOAD";} 
                                            else if($transaction->paymentCode=="SHARE"){ echo "FLOAT SHARE"; } 
                                            else if($transaction->paymentCode=="COMMS"){ echo $transaction->narration; } 
                                            else { echo Modules::run("billers/getItemName",$transaction->paymentCode); }
                                            
                                            
                                            ?>
                                        </td>
                                        <td>
                                            UGX <?php echo number_format($transaction->amount); ?>
                                        </td>
                                        <td>
                                           <small> <?php echo $transaction->customerNo; ?></small>
                                        </td>
                                        <td>
                                            <small>
                                            <?php 
                                            if($transaction->tranStatus=="SUCCESSFUL")
                                              echo Modules::run('reports/getFees',$transaction->amount,$transaction->paymentCode); 
                                            
                                            if($transaction->tranStatus!=="SUCCESSFUL")
                                               echo "0";
                                            ?>
                                           </small>
                                        </td>

                                        <td class="text-dark">
                                            <?php echo $transaction->finalStatus; ?>
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
                                              
                                              <li class="list-group-item" > 
                                               <button onclick="checkStatus('<?php echo $transaction->requestRef; ?>')" class="btn btn-info checkStatus">CHECK STATUS</button></li>
                                               
                                               <li class="results<?php echo $transaction->requestRef; ?>">
                                                   
                                               </li>
                                            </ul>
                                        </div>
                                        </div>
                                                                                
                                      </div>
                                      <div class="modal-footer">
                                          
                                        <button type="button" onclick="printDiv('<?php echo $transaction->requestRef; ?>')" class="btn btn-info" >PRINT</button>
                                        
                                        <?php if($user['role']==1): ?>
                                        
                                        <?php if($transaction->tranStatus=='FAILED' || $transaction->tranStatus=='PENDING') {?>
                                        <button type="button" onclick="markSuccess('<?php echo $transaction->requestRef; ?>')" class="btn btn-success" >MARK AS SUCCESSFUL</button>
                                        <?php } 
                                        else if($transaction->tranStatus=='SUCCESSFUL' || $transaction->tranStatus=='PENDING'){ ?>
                                        <button type="button" onclick="markFailed('<?php echo $transaction->requestRef; ?>')" class="btn btn-danger" >MARK AS FAILED</button>
                                        
                                        <?php } ?>
                                        
                                        <?php endif; ?>
                                        
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
            
             function checkStatus(requestRef){
                 
                                    $('.results'+requestRef).html("<h4>Checking status....</h4>");
                                
                                   $.ajax({
                                    method:"GET",
                                    url:"<?php echo base_url(); ?>payment/checkStatus/"+requestRef,
                                    success:function(response){
                                        
                                        console.log(response);
                                        
                                        var results=JSON.parse(response);
                                        
                                        var state="<h4 class='text-purple'> STATUS: "+results.finalStatus+"</h4>";
                                        
                                        if(results.valueCode)
                                        state+="<h4>VALUE CODE: "+results.valueCode+"</h4>";
                                        var msg=results.responseMessage.toString();
                                        
                                    if(results.responseCode=="90009")
                                        msg="TRANSACTION PENDING";
                                        
                                    if(!msg)
                                        msg="UNKNOWN STATUS, TRY LATER";
                                        
                                         msg=(results.responseMessage).replace(':','');
                                        
                                        
                                        if(msg)
                                          state+="<h4 class='text-info'>"+msg+"</h4>";
                                        
                                        $('.results'+requestRef).html(state);
                                        
                                        
                                    }
                                    
                                });
                                
                            }
                            
                            
                        function printDiv(requestRef) 
                        {
                            $('.checkStatus').hide();
                          var divToPrint=document.getElementById(requestRef);
                        
                          var newWin=window.open('','RECEIPT');
                          var msg=$('.msg').html().toString();
                          
                           msg=msg.replace("Quickteller","Elly Pay").replace("Interswitch","");
                        
                          $('.msg').html(msg);
                            
                          newWin.document.open();
                        
                          newWin.document.write('<html><body onload="window.print()"> <br><hr> <center><h2>ELLY PAY RECEIPT <br></h2><h4>'+requestRef+'</h4> </center>'+divToPrint.innerHTML+'<br><hr> <center>Contact: +256 704 878 224 <br><h2>Thank you for using Elly Pay</center></h2> <br></body></html>');
                        
                          newWin.document.close();
                        
                          setTimeout(function(){
                              newWin.close();
                               $('.checkStatus').show();
                          },5);
                                                
                        }
                            
                            
                            
            function markSuccess(ref) {
              
              var r = confirm("This transaction will be marked as successful, are you sure?");
              if (r == true) {
                console.log(ref);
                window.location.href = "<?php echo BASEURL; ?>payment/markSuccess/"+ref;
                
              } 
            }
            
             function markFailed(ref) {
              
              var r = confirm("This transaction will be marked as failed, are you sure?");
              if (r == true) {
                console.log(ref);
                window.location.href = "<?php echo BASEURL; ?>payment/markFailed/"+ref;
                
              } 
            }
            </script>          
                            
                            
      