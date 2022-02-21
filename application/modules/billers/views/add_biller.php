
                    <div class=" card w-100" >
                        <div class="card-header text-muted">
                            <h4>Add Biller</h4>
                        </div>
                        <div class="card-body">

                            <form class="form-material" method="post" action="<?php echo BASEURL;?>billers/storeBiller" enctype="multipart/form-data">
                                <!-- Input -->
                                <div class="body">
                                    <div class="row clearfix">
                                        <div class="col-sm-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" name="billerName" class="form-control" required autocomplete="off">
                                                    <label class="form-label">Biller Name</label>
                                                </div>
                                            </div>

                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select name="provider" class="form-control" required autocomplete="off">
                                                        <option value="Interswitch">Interswitch</option>
                                                        <option value="Ezeemoney">Ezeemoney</option>
                                                    </select>
                                                    <label class="form-label">Provider</label>
                                                </div>
                                            </div>

                                             <div class="form-group form-float">
                                                <div class="form-line">
                                                    <select name="categoryId" class="form-control" required autocomplete="off">
                                                        <option disabled selected>Select Category</option>
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

                                    </div>

                                    <div class="col-sm-6">

                                               <div class="form-group">
                                                <label class="form-label">Biller Logo</label>

                                                   <img onclick="$('#photo').click()" src="<?php echo ASSET_URL; ?>img/people/player.png" class="img img-thumbnail preview" width="200px;">
                                                    <input style="display: none;" type="file" name="picture" id="photo">
                                                    <br>
                                            </div>

                                            <div class="form-group">

                                                <input type="submit" class="btn btn-success pull-right" name="" value="Save Biller">
                                            </div>



                                        </div>

                                    </div>

                                </div>
                            </form>

                </div>
                </div>
