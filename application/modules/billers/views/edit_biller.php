                    <div class=" card w-100" >
                        <div class="card-header text-muted">
                            <h4>Biller : <?php echo $biller->billerName; ?>

                            <a href="<?php echo BASEURL."billers/list"; ?>" class="btn btn-success pull-right">
                                   BACK TO ALL BILLERS
                                   </a>
                            </h4>
                        </div>
                        <div class="card-body">

                            <form class="form-material" method="post" action="<?php echo BASEURL;?>billers/saveBillerEdits/<?php echo $biller->billerId;?>" enctype="multipart/form-data">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="billerName" class="form-control" required autocomplete="off" value=" <?php echo $biller->billerName; ?>">
                                                    <label class="form-label">Biller Name</label>
                                                </div>
                                            </div>
                                        <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="fieldlabel" class="form-control" required autocomplete="off" value=" <?php echo $biller->fieldlabel; ?>">
                                                    <label class="form-label">Field Name (App)</label>
                                                </div>
                                            </div>
                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select name="categoryId" class="form-control" required autocomplete="off">

                                                        <option value="<?php echo $biller->categoryId; ?>" selected>
                                                            <?php echo $biller->categoryName; ?>
                                                        </option>

                                                        <?php
                                                        foreach ($categories as $cat):
                                                     ?>
                                                    <option value="<?php echo $cat->providerId; ?>">
                                                        <?php echo $cat->categoryName; ?>
                                                        </option>
                                                     <?php endforeach; ?>
                                                    </select>
                                                    <label class="form-label">Category</label>
                                                </div>
                                            </div>

                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select name="status" class="form-control" required autocomplete="off">


                                                        <option value="<?php echo $biller->status; ?>" selected>
                                                            <?php echo
                                                            ($biller->status=='1')? 'Active' :'Inactive'; ?>
                                                        </option>

                                                        <option value="1">
                                                            Active
                                                        </option>
                                                         <option value="0">
                                                            In-Active
                                                        </option>


                                                    </select>
                                                    <label class="form-label">Status</label>
                                                </div>
                                            </div>

                                            <div class="form-group form-float">
                                               <div class="form-line">
                                                   <select name="provider" class="form-control" required autocomplete="off">


                                                       <option value="<?php echo $biller->provider; ?>" selected>
                                                           <?php echo $biller->provider; ?>
                                                       </option>

                                                       <option value="Interswitch">
                                                           Interswitch
                                                       </option>
                                                        <option value="Ezeemoney">
                                                           Ezeemoney
                                                       </option>


                                                   </select>
                                                   <label class="form-label">Provider</label>
                                               </div>
                                           </div>





                                    </div>

                                    <div class="col-sm-6">

                                               <div class="form-group">
                                                <label class="form-label">Biller Logo</label>

                                                   <img onclick="$('#photo').click()" src="<?php echo ASSET_URL; ?>img/billers/<?php echo $biller->picture;; ?>" class="img img-thumbnail preview" width="200px;">
                                                    <input style="display: none;" type="file" name="picture" id="photo">

                                                    <br>
                                            </div>



                                            <div class="form-group">

                                                <input type="submit" class="btn btn-success pull-right" name="" value="Save Changes">
                                            </div>



                                        </div>

                                    </div>

                                </div>
                            </form>

                </div>
                </div>

                <br>


                     <div class="row">
                <div class="col-md-12">
                    <div class="card no-b shadow">

                        <div class="card-header text-muted">
                            <h2>Biller Items
                            </h2>
                        </div>

                        <div class="card-body p-0">


                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <tbody>
                                    <?php foreach($items as $item): ?>
                                    <tr class="">

                                        <td>
                                            <h6><?php echo $item->itemName; ?></h6>
                                            <small class="text-dark">
                                   <?php  echo ($item->status=='1')? 'Active':'Inative';
                                             ?>
                                            </small>
                                        </td>
                                        <td>
                                            UGX <?php echo $item->itemAmount; ?>
                                        </td>
                                    <td>
                                            <a class="btn-fab btn-fab-sm btn-primary shadow text-white" href="<?php echo BASEURL.'billers/editItem/'.$item->id; ?>"><i class="icon-pencil" ></i></a>
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

            <br>
