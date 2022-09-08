    
            <div class="row">
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <tbody>
                                    <?php foreach($referees as $referee): ?>
                                    <tr class="">
                                        <td class="w-10">
                                            <img class="avatar avatar-lg" src="<?php echo ASSET_URL; ?>img/people/<?php echo $referee->photo; ?>" alt="">
                                        </td>
                                        <td>
                                            <h6><?php echo $referee->last_name." ".$referee->firstname; ?></h6>
                                            <small class="text-dark">
                                                <?php 
                                                echo Modules::run('players/getAge',$referee->dob);
                                             ?>
                                             Years old
                                            </small>
                                        </td>
                                        <td>
                                            <a class="btn-fab btn-fab-sm btn-primary shadow text-white"><i class="icon-pencil"></i></a>
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
      