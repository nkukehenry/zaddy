<style>
    .balnote{
        display:none;
    }
</style>
            	
                <div class="row my-3">
                    <div class="col-md-3">
                        <div class="counter-box  r-5 p-3 blue">
                            <div class="p-4">
                                <div class="float-right text-white">
                                    <span class="icon icon-people  s-48"></span>
                                </div>
                                <div class="counter-title text-white">Agents</div>
                                <h5 class="sc-counter mt-3 text-white"><?php
                                   echo Modules::run('agents/countAgents');
                                  $width1=(Modules::run('payment/countAgents')/100)*100;
                                ?></h5>
                            </div>
                            <div class="progress progress-xs r-0">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $width1; ?>%;" aria-valuenow="<?php echo Modules::run('agents/countAgents'); ?>"
                                     aria-valuemin="0" aria-valuemax="120" title="Initial target set at 100"></div>
                            </div>
                        </div>
                    </div>

                     <div class="col-md-3">
                        <div class="counter-box  r-5 p-3 red">
                            <div class="p-4">
                                <div class="float-right text-white">
                                    <span class="icon icon-close  s-48"></span>
                                </div>
                                <div class="counter-title text-white">Failed</div>
                                <h5 class="sc-counter mt-3 text-white"><?php 
                                echo Modules::run('payment/countfailed');
                                
                                $width2=(Modules::run('payment/countfailed')/10)*100;
                                
                                ?></h5>
                            </div>
                            <div class="progress progress-xs r-0">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $width2; ?>%;" aria-valuenow="<?php echo Modules::run('payment/countfailed'); ?>"
                                     aria-valuemin="0" aria-valuemax="10"></div>
                            </div>
                        </div>
                    </div>

                     <div class="col-md-3">
                        <div class="counter-box  r-5 p-3 green">
                            <div class="p-4">
                                <div class="float-right text-white">
                                    <span class="icon icon-check  s-48"></span>
                                </div>
                                <div class="counter-title text-white">Successful</div>
                                <h5 class="sc-counter mt-3 text-white"><?php
                                echo Modules::run('payment/countSuccess');
                                
                                 $width3=(Modules::run('payment/countSuccess')/100)*100;
                                
                                ?></h5>
                            </div>
                            <div class="progress progress-xs r-0">
                                      <div class="progress-bar" role="progressbar" style="width: <?php echo $width3; ?>%;" aria-valuenow="<?php echo Modules::run('payment/countSuccess'); ?>"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>

                     <div class="col-md-3">
                        <div class="counter-box  r-5 p-3 orange">
                            <div class="p-4">
                                <div class="float-right text-white">
                                    <span class="icon icon-pause  s-48"></span>
                                </div>
                                <div class="counter-title text-white">Pending</div>
                                <h5 class="sc-counter mt-3 text-white"><?php 
                                echo Modules::run('payment/countPending');
                                
                                $width4=(Modules::run('payment/countPending')/10)*100;
                                
                                ?></h5>
                            </div>
                            <div class="progress progress-xs r-0">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $width4; ?>%;" aria-valuenow="<?php echo Modules::run('payment/countPending'); ?>"
                                     aria-valuemin="0" aria-valuemax="10"></div>
                            </div>
                        </div>
                    </div>
                 
                </div>
                
                <div class="row row-eq-height">
                
                    <!---new playes start-->
                    <div class="col-md-4">
                        <div class="card my-3 no-b ">
                            <div class="card-header white b-0 p-3">
                                <div class="card-handle">
                                    <a data-toggle="collapse" href="#players" aria-expanded="false"
                                       aria-controls="players">
                                        <i class="icon-menu"></i>
                                    </a>
                                </div>
                                <h4 class="card-title">Agents</h4>
                                <small class="card-subtitle mb-2 text-muted">
                                   <b><?php echo count($agents); ?></b> Latest Agents
                                   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                   <small><a href="<?php echo base_url(); ?>agents/list">View All</a></small>
                               </small>
                            </div>
                            <div class="collapse show" id="players">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover earning-box">
                                            <tbody>
                                            <?php foreach ($agents as $agent): ?>
                                                <tr>
                                                <td class="w-10">
                                                    <a href="#" class="avatar avatar-lg" >
                                                        <img  src="<?php echo ASSET_URL; ?>img/people/<?php echo $agent->photo; ?>" alt="">
                                                    </a>
                                                </td>
                                                <td colspan="3">
                                                    <h6><?php echo $agent->names; ?></h6>
                                                    <small class="text-muted"><?php echo $agent->agentNo; ?></small>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!---new playes end-->

                     <!---fixtures-->
                    <div class="col-md-4">
                        <div class="card my-3 no-b ">
                            <div class="card-header white b-0 p-3">
                              
                                <h4 class="card-title">AGENTS' FLOAT VALUE </h4>
                            </div>
                            <div class="collapse show" id="fixtures">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover earning-box">
                                            <tbody>
                                                <tr>
                                                <td colspan="3">
                                                     <h4 class="agentbalance text-success">Fetching agents' float total...</h4> 
                                                     <note class="balnote"><small>Total float balances for agents</small></note>
                                                </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover earning-box">
                                            <tbody>
                                                <tr>
                                                <td colspan="3">
                                                     <h4 class="agentcommission text-success">Fetching agents' commission total...</h4> 
                                                     <note class="balnote"><small>Unpaid Commission for agents</small></note>
                                                </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-hover earning-box">
                                            <tbody>
                                                <tr>
                                                <td colspan="3">
                                                    
                                                    <h5>UNPAID LOANS</h5>
                                                     <h4 class="text-red">UGX 
                                                <?php 
                                                
                                            $balance=Modules::run("loans/getCustomerBalance");
                                     
                                               echo  number_format($balance);?></h4> 
                                                     <note ><small>Total Balance from unpaid loans</small></note>
                                                </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                     <div class="col-md-4">
                        <div class="card my-3 no-b ">
                            <div class="card-header white b-0 p-3">
                                
                                <h4 class="card-title">INTERSWITCH FLOAT VALUE </h4>
                            </div>
                            <div class="collapse show" id="fixtures">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover earning-box">
                                            <tbody>
                                                <tr>
                                                <td colspan="3">
                                                     <h4 class="balance text-blue">Fetching balance....</h4> 
                                                     <note class="balnote"><small>Balance at Interswitch</small></note>
                                                </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover earning-box">
                                            <tbody>
                                                <tr>
                                                <td colspan="3">
                                                     <h4 class="earned text-success">Fetching our commission ...</h4> 
                                                     <note class="balnote"><small>Commission at Interswitch</small></note>
                                                </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!---fixtures end-->
                    
                 
                    
                    <script>
                    
                    /*
                    
                    function fetchIswFloat(){
                                
                                   $.ajax({
                                    method:"GET",
                                    url:"<?php echo base_url(); ?>validation/checkBalance",
                                    success:function(bal){
                                    console.log(bal);
                                        $('.balance').html("<span class='icon icon-money  s-20'></span> UGX "+bal);
                                    }
                                    
                                });
                                
                            }
                            
                            function fetchAgentFloat(){
                                
                                   $.ajax({
                                    method:"GET",
                                    url:"<?php echo base_url(); ?>agents/getAgentTotalBalance",
                                    success:function(bal){
                                        $('.agentbalance').html("<span class='icon icon-money  s-20'></span> UGX "+bal);
                                    }
                                    
                                });
                                
                            }
                            
                            function fetchAgentCommission(){
                                
                                   $.ajax({
                                    method:"GET",
                                    url:"<?php echo base_url(); ?>reports/getTotalAgentCommission",
                                    success:function(bal){
                                        $('.agentcommission').html("<span class='icon icon-money  s-20'></span> UGX "+bal);
                                    }
                                    
                                });
                                
                            }
                            
                            function fetchTotalEarned(){
                                
                                   $.ajax({
                                    method:"GET",
                                    url:"<?php echo base_url(); ?>reports/getTotalEarned",
                                    success:function(bal){
                                        $('.earned').html("<span class='icon icon-money  s-20'></span> UGX "+bal);
                                    }
                                    
                                });
                                
                            }
                        */
                    
                   //$(document).ready(()=>{
              
                       /* window.setTimeout(function(){
                            
                            fetchIswFloat();
                            fetchAgentFloat();
                            fetchAgentCommission();
                         
                            
                         $('.balnote').show();
                        
                        },20000);
                        
                      window.setInterval(function(){
                      
                            fetchIswFloat();
                            fetchAgentFloat();
                            fetchAgentCommission();
                            fetchTotalEarned();
                       
                          
                        },40000)*/
                   
                   //});
                        
                    </script>


                   


                </div>