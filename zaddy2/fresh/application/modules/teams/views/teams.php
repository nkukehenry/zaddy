    
            <div class="row">
                <div class="col-md-12">
                    <div class="card no-b shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover ">
                                    <tbody>
                                    <?php foreach($teams as $team): ?>
                                    <tr class="">
                                        <td class="w-10">
                                            <img src="<?php echo ASSET_URL; ?>img/clubs/<?php echo $team->logo; ?>" alt="">
                                        </td>
                                        <td>
                                            <h6><?php echo $team->team_name; ?></h6>
                                            <small class="text-muted"><?php echo $team->home_field; ?></small>
                                        </td>
                                        <td>
                                            <?php $players=Modules::run('players/count',$team->id); ?>
                                            <span class="badge badge-success"><?php echo $players; ?> Players</span>
                                        </td>
                                        <td>
                                            <?php 
                                               $tournaments=json_decode($team->tournaments);

                                            foreach ($tournaments as $key => $value):

                                            $tournament=Modules::run('tournaments/getBy_id',$value);
                                            //print_r($tournament);
                                             ?>
                                            <span><i class="icon icon-trophy text-info"></i>
                                            <?php echo $tournament['tournament_name']; ?>
                                           </span>
                                        <?php endforeach; ?>
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
      