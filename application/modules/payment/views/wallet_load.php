        <div class="row padded col-md-12 " style="width: 100%;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark">
                            <h4> Agent Wallet Load</h4>
                        </div>
                        <div class="card-body">
                 
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>payment/saveAgentLoad" enctype="multipart/form-data">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="agentNo" class="form-control" required autocomplete="off">
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
                                        
                                         <div class="form-group">
                                                    <input type="checkbox" name="isloan"  value="1" autocomplete="off">
                                                    <label class="form-label">FLOAT LOAN?</label>
                                                
                                            </div>


                                     

                                    </div>

                                    <div class="col-sm-6">
                                   
                                         

                                            <div class="form-group">
                                                
                                                <input type="submit" class="btn btn-success pull-right col-md-12" name="" value="CONFIRM LOAD">
                                            </div>

                                          

                                        </div>

                                    </div>

                                </div>
                            </form>
                    
                </div>
                </div>
            </div>