    
 <?php
     $user=$this->session->userdata();
?>
            <div class="row">
                
                
                    <div class="col-md-12 " style="width: 100%; margin-bottom:10px;">
                    <div class=" card w-100" >
                        <div class="card-header text-dark">
                            <h4>Search Agents</h4>
                            
                        </div>
                        <div class="card-body">
                            <form class="form-material" method="post" action="<?php echo BASEURL;?>agents/list" enctype="multipart/form-data">
                                <div class="row clearfix">
                            
                            <div class="col-sm-12 col-lg-3">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" name="names" class="form-control"  autocomplete="off" value="<?php echo (!empty($search['names']))? $search['names']:''; ?>" >
                                            <label class="form-label">Agent Name</label>
                                        </div>
                                    </div>
                            </div>
                           
                            <div class="col-sm-12 col-lg-3">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" name="agentNo" class="form-control"  autocomplete="off" value="<?php echo (!empty($search['agentNo']))? $search['agentNo']:''; ?>" >
                                            <label class="form-label">Agent Number</label>
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
                                    foreach($agents as $agent):

                                     $balance=0;
                                     //Modules::run("agents/getAgentBalance",$agent->agentNo);
                                      $commisssion=0;
                                      //Modules::run("agents/getAgentCommission",$agent->agentNo);
                                     
                                        ?>
                                    <tr class="">
                                        <td class="w-10">
                                            <img class="avatar avatar-lg" src="<?php echo ASSET_URL; ?>img/people/<?php echo $agent->photo; ?>" alt="">
                                        </td>
                                        <td>
                                            <h6><?php echo $agent->names; ?></h6>
                                            <small class="text-dark"><?php echo $agent->agentNo; ?></small>
                                        </td>
                                        <td>
                                            <h6>Location: <?php echo $agent->location; ?></h6>
                                            <small class="text-muted">Contact: <?php echo $agent->phoneNumber; ?> </small>
                                        </td>
                                       
                                       <td>
                                            <div  id="bal<?=$agent->agentNo?>" style="display: none;">
                                                <h6 class="text-dark "> 
                                                    <span class="bal<?=$agent->agentNo?>"></span>
                                                    <?php echo ($balance)? number_format($balance): 0; ?>
                                                    
                                                    <br>
                                                      <span class="text-green com<?=$agent->agentNo?>" > COMMISSION: UGX <?php echo ($commisssion)? number_format($commisssion): 0; ?>
                                                      </span>
                                                    </h6>
                                                <small class="text-muted">
                                                    <?php 

                                                    echo date('d, F Y h:i:s',strtotime($agent->lastActivity)); ?>
                                                        
                                                </small>
                                            </div>
                                            <a  class="btn btn-primary btn-outline btn-sm btn<?php echo $agent->agentNo;?>" agent="agentNo" onClick="getBalance('<?php echo $agent->agentNo;?>')">
                                                Check Balance
                                            </a>
                                        </td>

                                        <td>
                                            <?php if($agent->status==1): ?>
                                            <span class="badge badge-success text-white text-caps">
                                                Active
                                            </span>
                                            <?php endif; ?>

                                            <?php  if($agent->status==0): ?>
                                            <span class="badge badge-danger text-white text-caps">
                                                Inactive
                                            </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            
                                            <?php if($user['role']==1): ?>
                                            
                                            <a href="<?php echo EDIT_AGENT_LINK; ?>/<?php echo $agent->agentNo; ?>" class="btn-fab btn-fab-sm btn-primary shadow text-white" title="Edit Agent"><i class="icon-pencil"></i></a>
                                            
                                            <a href="<?php echo BASEURL; ?>agents/createLogin/<?php echo $agent->agentNo; ?>" class="btn-fab btn-fab-sm btn-primary shadow text-white"  title="Agent Login"><i class="icon-lock"></i></a>
                                            
                                            <?php endif; ?>
                                        
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

                                <script type="text/javascript">
                                    
                                    function getBalance(agentNo){

                                            console.log(agentNo);

                                            $('.btn'+agentNo).html('Please wait...')

                                            $.ajax({
                                                method:'GET',
                                                url:'<?php echo base_url();?>agents/restBalance/'+agentNo,
                                                success:function(response){

                                                var balances=JSON.parse(response);

                                                $('.bal'+agentNo).html("BALANCE: UGX "+balances.balance);

                                                $('.com'+agentNo).html("COMMISSION: UGX "+balances.commission);

                                                $('#bal'+agentNo).show();
                                                $('.btn'+agentNo).hide();

                                                console.log(response);
                                                }
                                            });
                                    }
                                </script>
      