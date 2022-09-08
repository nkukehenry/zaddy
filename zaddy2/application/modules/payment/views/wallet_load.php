        <div class="row padded col-md-12 " style="width: 100%;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark">
                            <h4> Agent Wallet Load</h4>
                        </div>
                        <div class="card-body">
                 
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>" enctype="multipart/form-data" id="load">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="agentNo" class="form-control agentNo" required autocomplete="off">
                                                    <label class="form-label">AGENT NUMBER</label>
                                                </div>
                                            </div>

                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="number" name="amount" class="form-control" autocomplete="off">
                                                    <label class="form-label">AMOUNT</label>
                                                </div>
                                            </div>
                                            
                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="narration" class="form-control" required autocomplete="off">
                                                    <label class="form-label">NARRATION</label>
                                                </div>
                                            </div>


                                     

                                    </div>

                                    <div class="col-sm-6">
                                        
                                        <div class="form-group ">
                                            
                                            <h3  class="names"></h3>
                                            
                                        </div>
                                        
                                        <br>
                                   
                                         

                                            <div class="form-group ">
                                                
                                                <input type="button" onClick="validatAgent()" class="btn btn-info pull-right col-md-12" name="" value="VALIDATE">
                                            </div>
                                            
                                            <br>
                                            <br>
                                            
                                            <div class="form-group">
                                                
                                                <input onclick="submitLoad()" class="btn btn-success pull-right col-md-12 load" name="" value="CONFIRM LOAD">
                                            </div>

                                          

                                        </div>

                                    </div>

                                </div>
                            </form>
                            
                            <script>
                            
                            
                            var isDoing=false;
                            
                            function submitLoad(){
                                
                                 if(isDoing){
                                     
                                     $('.names').html("<b class='text-danger'> Please wait atleast 10 seconds between loads..</b>");
                                     
                                     window.setTimeout(function(){
                                            isDoing=false;
                                            $('.names').html("<b class='text-info'> You can now load again.</b>");
                                       },8000);
                                      
                                     return;
                                 }
                                
                                var data=$('#load').serialize();
                                
                                isDoing=true;
                                
                                $('.names').html("<b class='text-success'> Processing Load, please wait...</b>")
                                
                                $.ajax({
                                    url:'<?php echo base_url(); ?>payment/saveAgentLoad',
                                    method:'POST',
                                    data:data,
                                    success:function(response){
                                    console.log(response);
                                    var res=null;
                                    try{
                                     
                                       $('.names').html("<b class='text-success'>"+response+"</b>");
                                       
                                       window.setTimeout(function(){
                                            isDoing=false;
                                       },10000);
                                      
                                        
                                     
                                    }catch(error){
                                        
                                        console.log(error);
                                        
                                        $('.names').html("<b class='text-danger'>Agent load failed, check entered data</b>")
                                        
                                    }
                                     
                                        
                                    }
                                })
                                
                            }
                            
                            function validatAgent(){
                                
                                $('.names').html("Checking agent names....");
                                
                                var agentNo=$('.agentNo').val();
                                
                                $.ajax({
                                    url:'<?php echo base_url(); ?>agents/checkAgent/'+agentNo,
                                    method:'GET',
                                    success:function(response){
                                    console.log(response);
                                    var res=null;
                                    try{
                                     res=JSON.parse(response);
                                     
                                    
                                    if(res.names)
                                     $('.names').html("<b>"+res.names+"</b>");
                                     
                                     if(!res.names)
                                     $('.names').html("Failed getting agent name.")
                                     
                                    }catch(error){
                                        
                                        $('.names').html("<b class='text-danger'>Agent validation failed, check agent number</b>")
                                        
                                    }
                                     
                                        
                                    }
                                })
                            }
                                
                            </script>
                    
                </div>
                </div>
            </div>