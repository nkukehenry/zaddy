                    <div class=" card w-100" >
                        <div class="card-header text-muted">
                            <h4>Edit Item</h4>
                        </div>
                        <div class="card-body">

                            <form class="form-material" method="post" action="<?php echo BASEURL;?>billers/saveItemEdit/<?=$item->id?>" enctype="multipart/form-data">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="itemName" class="form-control" value="<?=$item->itemName?>" required autocomplete="off">
                                                    <label class="form-label">Item Name</label>
                                                </div>
                                            </div>

                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="itemCode" value="<?=$item->itemCode?>" class="form-control"  autocomplete="off">
                                                    <label class="form-label">Item Code</label>
                                                </div>
                                            </div>
                                        
                                           <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="itemAmount" value="<?=$item->itemAmount?>" class="form-control" readonly autocomplete="off">
                                                    <label class="form-label">Item Amount</label>
                                                </div>
                                            </div>
                                        
                                            <div class="form-group form-float">
                                                    <input type="checkbox" name="usesPhone" <?=($item->usesPhone)?"checked":""?> value="1" class="form-control checkbox"  autocomplete="off">
                                                    <label class="form-label">Requires Phone Number</label>
                                            </div>
                                        
                                         <div class="form-group form-float">
                                                    <input type="checkbox" name="requiresAmount" value="1" <?=($item->requiresAmount)?"checked":""?> class="form-control"  autocomplete="off">
                                                    <label class="form-label">Requires Amount</label>
                                            </div>
                                          <div class="form-group">
                                                    <input type="checkbox" name="requiresPin" value="1" <?=($item->requiresPin)?"checked":""?> class="form-control"  autocomplete="off">
                                                    <label class="form-label">Requires OTP</label>
                                                
                                            </div>
                                        
                                            <div class="form-group form-float">
                                                    <input type="checkbox" name="requiresNarration" value="1" <?=($item->requiresNarration)?"checked":""?> class="form-control"  autocomplete="off">
                                                    <label class="form-label">Requires Narration</label>
                                                
                                            </div>
                                        
                                           <div class="form-group form-float">
                                                    <input type="checkbox" name="status" value="1" <?=($item->status)?"checked":""?> class="form-control"  autocomplete="off">
                                                    <label class="form-label">Is Active</label>
                                                
                                            </div>


                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select name="billerId" class="form-control" required autocomplete="off">
                                                        <option disabled selected>Select Biller</option>
                                                        <?php
                                                        foreach ($billers as $biller):
                                                           $attr = "";
                                                          if($biller->billerId == $item->billerId):
                                                             $attr = "selected";
                                                           endif;
                                                     ?>
                                                    <option value="<?php echo $biller->billerId; ?>" <?=$attr?>>
                                                        <?php echo $biller->billerName; ?>
                                                        </option>
                                                     <?php endforeach; ?>
                                                    </select>
                                                    <label class="form-label">Biller</label>
                                                </div>
                                            </div>

                                    </div>

                                        <div class="col-sm-6">

                                            <div class="form-group">

                                                <input type="submit" class="btn btn-success pull-right" name="" value="Save Item">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </form>

                </div>
                </div>
