            <div class="row">
                		
                       <div class="col-md-12">
                       <div class="card p-2">
                          <a href="<?=base_url()?>billers/refresh" class="btn btn-info col-md-4 pull-right">
                             CLEAR CACHE
                           </a>
                        </div>
                        </div>
            
              <div style="min-height:30px"></div>
                       
                <div class="col-md-12">
                
                
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                        
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <tbody>
                                    <?php foreach($billers as $biller): ?>
                                    <tr class="">
                                        <td class="w-10">
                                            <img src="<?php echo ASSET_URL; ?>img/billers/<?php echo $biller->picture; ?>" alt="">
                                        </td>
                                        <td>
                                            <h6><?php echo $biller->billerName; ?></h6>
                                            <small class="badge badge-primary">
                                   <?php  echo ($biller->status=='1')? 'Active':'Inative';
                                             ?>
                                            </small>
                                        </td>
                                        <td> <h6><?php echo $biller->categoryName; ?></h6>
                                          <small class="badge badge-dark">
                                              <?php  echo $biller->provider;?>
                                          </small>

                                        </td>
                                        <td>

                                            <a class="btn-fab btn-fab-sm btn-primary shadow text-white" href="<?php echo BASEURL.'billers/showBiller/'.$biller->billerId; ?>"><i class="icon-pencil" ></i></a>

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
