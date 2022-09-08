<div >

    <?php

$config= Modules::run("settings/getAll");

$user=$this->session->userdata();


?>
  
    <div class="sticky">
        <div class="navbar navbar-expand navbar-dark d-flex justify-content-between bd-navbar bg-main">
            <div class="relative">
                <img src="<?php echo base_url(); ?>assets/img/basic/<?php echo $config->main_logo; ?>" width="50px"> 
            </div>

            <h3 class="text-white"><?php echo $user['names']."-".$user['agentNo']; ?></h3>

            <?php ?>
<div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
        <!-- User Account-->
        <li class="dropdown custom-dropdown user user-menu ">
            <a href="#" class="nav-link" data-toggle="dropdown">
                <img src="<?php echo ASSET_URL; ?>img/people/<?php echo $user['photo']; ?>" class="user-image" alt="User Image">
                <i class="icon-more_vert "></i>
            </a>
            <div class="dropdown-menu p-4 dropdown-menu-right">
                <div class="row box justify-content-between my-4">
                    <div class="col">
                        <a href="<?php echo BASEURL; ?>reports/myTransactions">
                            <i class="icon-apps purple lighten-2 avatar  r-5"></i>
                            <div class="pt-1">Transactions</div>
                        </a>
                    </div>
                    <div class="col"><a href="<?php echo BASEURL; ?>agents/merchantProfile/<?php echo rawurlencode($user['agentNo']); ?>">
                        <i class="icon-user pink lighten-1 avatar  r-5"></i>
                        <div class="pt-1">Profile</div>
                    </a></div>
                    <div class="col">
                        <a href="<?php echo BASEURL; ?>auth/logout">
                            <i class="icon-lock indigo lighten-2 avatar  r-5"></i>
                            <div class="pt-1">Logout</div>
                        </a>
                    </div>
                </div>
           
               
            </div>
        </li>
    </ul>
</div>

<?php  ?>
        </div>
    </div>
</div>
