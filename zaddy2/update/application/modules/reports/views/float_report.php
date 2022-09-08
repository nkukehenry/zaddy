    
            <div class="row">
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">


                            
                                    
                            
                                <form method="post" class="form-material" action="<?php echo base_url(); ?>reports/balancesReport" >
                                    
                                    <div class="row clearfix" style="padding-top:80px; padding-left:40px;">
                                    <div class="col-md-2">
                                     <div class="form-group form-float">
                                                <div class="form-line">
                                                     <input class="form-control" type="text" autocomplete="off" name="agentno" value="<?php echo $agentNo; ?>">
                                                    <label class="form-label">AGENT NUMBER</label>
                                                </div>
                                            </div>
                                    </div>

                                    <div class="col-md-2">
                                     <div class="form-group form-float">
                                                <div class="form-line">
                                                     <select class="form-control" name="dataSource">
                                                      <option value="0">Use Cached</option>
                                                       <option value="1">Ignore Cache</option>
                                                     </select>
                                                    <label class="form-label">DATA SOURCE</label>
                                                </div>
                                            </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <input class="btn btn-success btn-sm" type="submit" value="Search">
                                    
                                       <?php  if($balances){ ?>
                                        
                                        <a href="<?php echo base_url(); ?>reports/exportExcelBalances/<?php echo $agentNo; ?>" class="btn btn-success btn-sm">EXPORT TO EXCEL</a>
                                        
                                        <?php } ?>
                                    </div>
                                    <br>
                                    </div>
                                </form>
                            
                            
                            <div class="table-responsive">
                                <center>
                                    <br>
                                   
                                <table class="table table-hover ">

                                    <thead class="text-white" style="font-weight: bolder; background-color: grey; color: #fff;">
                                        <th>AGENT NAME</th>
                                        <th>AGENT NUMBER</th>
                                        <th>BALANCE</th>
                                        <th>COMMISSION</th>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $class="text-danger";
                                        $total= 0;
                                        $total_coms= 0;
                                        
                                    foreach($balances as $transaction):

                                        $total_coms += $transaction->commission;
                                        
                                        if($transaction->balance>0){
                                          $class="text-green";
                                          $total += $transaction->balance;
                                        }
                                         if($transaction->balance<0)
                                          $class="text-danger";
                                    ?>
                                    <tr class="">
                                        <td>
                                            <?php echo $transaction->names; ?>
                                        </td>
                                        <td>
                                            <?php echo $transaction->agentNo; ?>
                                        </td>
                                        
                                        <td class="<?php echo $class; ?>">
                                            UGX <?php echo number_format($transaction->balance); ?>
                                        </td>
                                        <td>
                                           <b>UGX <?php echo number_format($transaction->commission); ?></b>
                                        </td>
                                      
                                    </tr>
                                  <?php endforeach;  ?>
                                  
                                  <tfoot>
                                      <th colspan="2">TOTAL AGENT BALANCE </th>
                                      <th colspan="1" class="text-green">UGX <?php echo number_format($total); ?></th>
                                      <th colspan="1" class="text-blue">UGX <?php echo number_format($total_coms); ?></th>

                                      
                                  </tfoot>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            

            <?php //echo $links ; ?>
      