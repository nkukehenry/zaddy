            <style>
                                
                                #loading-bar-spinner.spinner {
                    left: 50%;
                    margin-left: -20px;
                    top: 50%;
                    margin-top: -20px;
                    position: absolute;
                    z-index: 19 !important;
                    animation: loading-bar-spinner 400ms linear infinite;
                }
                
                #loading-bar-spinner.spinner .spinner-icon {
                    width: 40px;
                    height: 40px;
                    border:  solid 4px transparent;
                    border-top-color:  #00C8B1 !important;
                    border-left-color: #00C8B1 !important;
                    border-radius: 50%;
                }
                
                @keyframes loading-bar-spinner {
                  0%   { transform: rotate(0deg);   transform: rotate(0deg); }
                  100% { transform: rotate(360deg); transform: rotate(360deg); }
                }


                .chosen{
                    
                    background-color:green;
                    color:#fff;
                }
                
            </style>
            
            
                            <a  href="<?php echo BASEURL; ?>merchant" class="btn btn-outline-info pull-right" style="display:block"> GO BACK</a>
            <div class="row padded col-md-12 " style="width: 100%;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark ">
                            <h4 >BILL PAYMENT</h4>
                            
                        </div>
                        <div class="card-body">
                            
                            <?php //print_r($agent); ?>
                 
                            <form class="form-material valForm" >
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        
                      <div class="col-sm-4">
                            <div class="card ">
                                <div class="card-header white">
                                    <i class="icon-clipboard-edit blue-text"></i>
                                    <strong class="text-info"> PAYMENT ITEMS </strong>
                                </div>
                                <div class="card-body p-0 bg-light slimScroll" data-height="300">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <!-- Table heading -->
                                            <tbody>
                                                
                                       <?php foreach($items as $item): ?>
                                            <tr>
                                         
                                                
                                                <td><?php echo strtoupper($item->itemName); ?></td>
                                                <td>
                                         <a  class="btn btn-outline-success btn-xs choose" 
                                         item='<?php echo json_encode($item); ?>'
                                         >CHOOSE</a>
                                                </td>
                                            </tr>
                                            
                                            <?php endforeach;  ?>
                                       
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer white">
                                    <h6 class="text-info">SELECT A PAYMENT ITEM</h6>
                                </div>
                            </div>
            
                                        </div>
                                        <div class="col-sm-4">
                                            
                                            
                                            
                                              <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="itemName" class="form-control text-green itemname"  autocomplete="off" readonly placeholder="ItemName" style="text-transform:uppercase;">
                                                    
                                                </div>
                                            </div>
                                            
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="customerNo" class="form-control customerNo" required autocomplete="off" >
                                                    <label class="form-label">CUSTOMER NO</label>
                                                </div>
                                            </div>
                                            
                                     <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="number" name="amount" class="form-control amount" required autocomplete="off" placeholder="AMOUNT" >
                                                  
                                                </div>
                                            </div>
                                            
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="tel" name="phone" class="form-control phone" required autocomplete="off" >
                                                    <label class="form-label">PHONE NUMBER</label>
                                                </div>
                                            </div>

                                      
                                        <div class="form-group">
                                                
                                                <input type="button" class="btn btn-info pull-right col-md-6 validate" name="" value="VALIDATE CUSTOMER">
                                            </div>
                                           
                                    </div>
                                    
                                    <div class="col-sm-4" >
                                        
                    <div class="confirm" style="display:none;">
                        
                    <div class="form-group">
                          <div class="col">
                                <div class="card">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item text-green"><b>TRANSACTION DETAILS</b></li>
                                        <li class="list-group-item custname"></li>
                                        <li class="list-group-item fees"></li>
                                        <li class="list-group-item total"></li>
                                    </ul>
                                </div>
                            </div>
                      </div>
                                    <div class="form-group">
                                                
                                       <input type="button" class="btn btn-success pull-right col-md-6 clear" name="" value="CONFIRM & PAY">
                                        </div>
                                            
                                            
                                    </div>
                                    
                                    
                                        <span class="error" style="display:block;padding-top:10%;">
                                            </span>
                                            
                                            <span class="status text-green text-center" style="display:block;padding-top:10%;">
                                            </span>
                                            
                                    </div>
                                    
                                    </div>

                                </div>
                                
                                </form>
                                
                                <span style="display:none" class="valRes"></span>
                            
                    
                </div>
                </div>
            </div>
            
            <script>
                
           $( document ).ready(function(){
               
               var item;
               var valResponse;
            
            var spinner='<div id="loading-bar-spinner" class="spinner"><div class="spinner-icon"></div></div>';
            
            $('.choose').on('click',function(e){
                
                $('.choose').removeClass("chosen");
            
                $(this).addClass('chosen');
                
                e.preventDefault();
            
                 item=JSON.parse($(this).attr('item'));
                
                  $('.itemname').val(item.itemName);
                console.log(item);
                
                if(item.isFixedAmount=='1' || parseInt(item.itemAmount)>0){
                    $('.amount').val(parseInt(item.itemAmount));
                    $('.amount').attr('readonly',true);
                }
            
            });
            
            
             $('.validate').on('click',function(e){
            
                $('.error').html("");
                $('.status').html("Validating, please wait..."+spinner);
                
                if(!item){
                    $('.error').html("<font color='red'><center><b> INVALID DATA: <br><br> CHOOSE PAYMENT ITEM FIRST</b></center></font>");
                    return;
                }
                
                
                var amount=$('.amount').val();
                var phone=$('.phone').val();
                var customerId=$('.customerNo').val();
                var paymentCode=item.paymentCode;
                var itemCode=item.itemCode;
                
                if(!customerId || !amount ){
                    
                    $('.error').html("<font color='red'><center><b> INVALID DATA: <br><br> PLEASE PROVIDE CUSTOMER DATA & AMOUNT</b></center></font>");
                    return;
                    
                }
                
                var data={
                    itemCode:itemCode,
                    paymentCode:paymentCode,
                    amount:amount,
                    customerId:customerId,
                    phoneNumber:phone,
                    agentId:"<?php echo $this->session->get_userdata()['agentNo']; ?>"
                };
                
                $.ajax({
                    url:"<?php echo BASEURL; ?>validation/webValidation/",
                    data:"data="+JSON.stringify(data),
                    method:'POST',
                    success:function(response){
                        console.log(response)
                        $('.status').html("");
                        valResponse=JSON.parse(response);
                        
                        $('.valRes').html(response);
                        
                        var status=valResponse.responseCode;
                        
                        if(status=="9000" || status=="90000"){
                            
                            $('.custname').html("NAME: "+valResponse.customerName);
                            $('.fees').html("CHARGES: "+parseInt(valResponse.surcharge));
                            
                            $('.total').html("TOTAL: "+parseInt(valResponse.amount)/100);
                            
                            $('.confirm').show();
                        }   
                        
                        else{
                            
                            $('.error').html("<font color='red'><b> TRANSACTION FAILED: <br><br> "+valResponse.reponseMessage+"</b></font>");
                        }
                        
                    }
                })
            
            });
            
            
               function printReceipt(payResponse) 
                        {
                          var receipt="";
                          
                          var newWin=window.open('','RECEIPT');
                          var msg=payResponse.responseMessage;
                          
                          receipt=msg;
                          
                          newWin.document.open();
                        
                          newWin.document.write('<html><body onload="window.print()"> <br><hr> <center><h2>ELLY PAY RECEIPT <br></h2><h4>HELLO WORLD</h4> </center>'+receipt+'<br><hr> <center>Contact: +256 704 878 224 <br><h2>Thank you for using Elly Pay</center></h2> <br></body></html>');
                        
                          newWin.document.close();
                        
                          setTimeout(function(){
                              newWin.close();
                          },5);
                                                
                        }
            
            
            $('.clear').on('click',function(e){
            
                $('.error').html("");
                
                $('.status').html("Processing, please wait..."+spinner);
                
                $('.confirm').hide();
                
                var amount=parseInt($('.amount').val());
                var phone=$('.phone').val();
                var customerId=$('.customerNo').val();
                var paymentCode=item.paymentCode;
                var itemCode=item.itemCode;
                
                valResponse=JSON.parse($('.valRes').html());
                
                console.log(valResponse);
                
                var paydata={
                    itemCode:itemCode,
                    paymentCode:paymentCode,
                    amount:amount,
                    customerId:customerId,
                    customerMobile:phone,
                    agentId:"<?php echo $this->session->get_userdata()['agentNo']; ?>",
                    transactionRef:valResponse.transactionRef,
                    requestRef:valResponse.shortTransactionRef,
                    customerName:valResponse.customerName
                };
                
                $.ajax({
                    url:"<?php echo BASEURL; ?>payment/webPayment",
                    data:"data="+JSON.stringify(paydata),
                    method:'POST',
                    success:function(response){
                        console.log(response)
                        
                        var payResponse=JSON.parse(response);
                        
                        $('.status').html("");
                        
                        var status=payResponse.responseCode;
                        
                        if(status=="9000" || status=="90000"){
                            
                           var msg=payResponse.responseMessage;
                           msg=msg.replace("Quickteller","Elly Pay").replace("Interswitch","");
                           
                           $('.status').html(msg);
                           
                           printReceipt(payResponse);
                        }   
                        
                        else{
                            
                             $('.clear').removeAttr('disabled');
                             
                             
                            $('.error').html("<center><b><font color='red'> PAYMENT FAILED: </font><br><br>"+payResponse.responseMessage+"</b></center>");
                            
                           
                        }
                        
                    }
                })
            
            });
            
            
         
            
            
                
           });
                
                
            </script>

              
      