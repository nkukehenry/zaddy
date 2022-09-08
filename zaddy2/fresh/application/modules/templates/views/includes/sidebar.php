
<?php
     $user=$this->session->userdata();
     $config= Modules::run("settings/getAll");
?>

<style type="text/css">
    

</style>
<aside class="main-sidebar fixed offcanvas shadow" data-toggle='offcanvas'>
    <section class="sidebar ">
        <div class="w-80px mt-3 mb-3 ml-3">
          
            <img src="<?php echo ASSET_URL; ?>img/basic/logo.png" width="60px" alt="<?php echo $config->system_name; 
            
                
            ?>">
            
           
        
       
        </div>

        <div class="relative">
            <a data-toggle="collapse" href="#userSettingsCollapse" role="button" aria-expanded="false"
               aria-controls="userSettingsCollapse" class="btn-fab btn-fab-sm absolute fab-right-bottom fab-top btn-dark shadow1 ">
                <i class="icon icon-more_vert"></i>
            </a>
            <div class="user-panel p-3 light mb-2  bg-main">
                <div>
                    <div class="float-left image">
                        <img class="user_avatar" src="<?php echo ASSET_URL; ?>img/people/<?php echo $user['photo']; ?>" alt="User Image">
                    </div>
                    <div class="float-left info">
                        <h6 class="font-weight-light mt-2 mb-1 text-white"><?php echo $user['names']; ?></h6>
                        <a href="#" class="text-white">NO: <?php echo $user['agentNo'];  ?></a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="collapse multi-collapse" id="userSettingsCollapse">
                    <div class="list-group mt-3 shadow">
                        <!--<a href="#" class="list-group-item list-group-item-action ">-->
                        <!--    <i class="mr-2 icon-person_outline text-blue"></i>Profile-->
                        <!--</a>-->
                        <!--<a href="#" class="list-group-item list-group-item-action"><i-->
                        <!--        class="mr-2 icon-cogs text-yellow"></i>Settings</a>-->
                        <!--<a href="#" class="list-group-item list-group-item-action"><i-->
                        <!--        class="mr-2 icon-vpn_key text-mild"></i>Change Password</a>-->
                        <a href="<?php echo BASEURL; ?>auth/logout" class="list-group-item list-group-item-action"><i
                                class="mr-2 icon-lock text-mild"></i>Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="header"><strong>MAIN NAVIGATION</strong></li>
            <li><a href="<?php echo BASEURL; ?>admin/in">
                <i class="icon icon-dashboard  text-main s-18"></i> <span>Dashboard</span> 
            </a>
            </li>
            
            <li class="treeview"><a href="#">
                <i class="icon icon icon-users text-main s-18"></i>
                <span>Agents</span>
                <i class="icon icon-angle-left s-18 pull-right"></i>
            </a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo BASEURL; ?>agents/list"><i class="icon icon-list-alt"></i>Agent List</a>
                    </li>
                    <?php if($user['role']==1): ?>
                    <li><a href="<?php echo BASEURL; ?>agents/add"><i class="icon icon-add"></i>Add
                        New </a>
                    </li>
                    <?php endif ?>
                </ul>
            </li>
            
            <?php if($user['role']==1): ?>
             <li class="treeview"><a href="#">
                <i class="icon icon icon-money text-main s-18"></i>
                <span>Liquidity</span>
                <i class="icon icon-angle-left s-18 pull-right"></i>
            </a>
                <ul class="treeview-menu">
                    
                    <li><a href="<?php echo BASEURL; ?>payment/loads"><i class="icon icon-list-alt"></i>Agent Load</a>
                    </li>
                    <li><a href="<?php echo BASEURL; ?>agents/commissionlist"><i class="icon icon-list-alt"></i>Commission Payment</a>
                    </li>
                    <li><a href="<?php echo BASEURL; ?>agents/paidCommission"><i class="icon icon-list-alt"></i>Record Provider Comission</a>
                    </li>
                    
                    
                </ul>
            </li>
            
            <?php endif; ?>
            
             <li class="treeview"><a href="#">
                <i class="icon icon icon-money text-main s-18"></i>
                <span>Agent Statement</span>
                <i class="icon icon-angle-left s-18 pull-right"></i>
            </a>
                <ul class="treeview-menu">
                    
                    <li><a href="<?php echo BASEURL; ?>reports/statement"><i class="icon icon-list-alt"></i>Agent Statement</a>
                    </li>
                </ul>
            </li>




            <li class="treeview"><a href="#"><i class="icon icon-list  text-main s-18"></i> Transactions<i
                    class="icon icon-angle-left s-18 pull-right"></i></a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo BASEURL; ?>reports/transactions"><i class="icon icon-circle-o"></i>Transactions List</a>
                    </li>
                 
                    <!--<li><a href="#"><i class="icon icon-search"></i>-->
                    <!--    Transaction Look Up-->
                    <!--</a>-->
                    <!--</li>-->
                </ul>
            </li>
            
            <?php if($user['role']==1): ?>
            
            <li class="treeview"><a href="#"><i class="icon icon-money  text-main s-18"></i> Loans<i
                    class="icon icon-angle-left s-18 pull-right"></i></a>
                <ul class="treeview-menu">
                    
                     <li>
                        <a href="<?php echo BASEURL; ?>loans/loanPayment"><i class="icon icon-circle-o"></i>Repayment</a>
                    </li>
                    
                    <li><a href="<?php echo BASEURL; ?>loans/loanOut"><i class="icon icon-circle-o"></i>New Loan</a>
                    </li>
                    <li><a href="<?php echo BASEURL; ?>loans/addCustomer"><i class="icon icon-user"></i>
                        Add Customer
                    </a>
                    </li>
                    
                    <li><a href="<?php echo BASEURL; ?>loans/loanList"><i class="icon icon-circle-o"></i>View Loans</a>
                    </li>
                    
                    <li><a href="<?php echo BASEURL; ?>loans/repayments"><i class="icon icon-circle-o"></i>View Repayments</a>
                    </li>
                    
                    <li><a href="<?php echo BASEURL; ?>loans/customers"><i class="icon icon-circle-o"></i>Customers List</a>
                    </li>
                    
                    <!--<li><a href="<?php echo BASEURL; ?>loans/loanList"><i class="icon icon-circle-o"></i>All Loans</a>
                    </li>-->
                   
                </ul>
            </li>

            <!--<li class="treeview"><a href="#"><i class="icon icon-key text-main s-18"></i>Login Accounts<i-->
            <!--        class="icon icon-angle-left s-18 pull-right"></i></a>-->
            <!--    <ul class="treeview-menu">-->
            <!--        <li><a href="<?php echo BASEURL; ?>"><i class="icon icon-circle-o"></i>All Accounts</a>-->
            <!--        </li>-->
            <!--        <li><a href="<?php echo BASEURL; ?>"><i class="icon icon-padlock"></i> Link Agent</a>-->
            <!--        </li>-->
            <!--        <li><a href="<?php echo BASEURL; ?>"><i class="icon icon-unlock"></i> Reset Logins</a>-->
            <!--        </li>-->
            <!--        <li><a href="#"><i class="icon icon-search"></i>-->
            <!--            Profile Look Up-->
            <!--        </a>-->
            <!--        </li>-->
            <!--    </ul>-->
            <!--</li>-->
         
            <li class="header  mt-3 bg-main"><strong>UTILITIES</strong></li>

            <li class="treeview"><a href="#">
                <i class="icon icon-cogs text-main s-18"></i>
                <span>Configurations</span>
                <i class="icon icon-angle-left   s-18 pull-right"></i>
            </a>
                <ul class="treeview-menu">
                    
                    <li><a href="<?php echo BASEURL; ?>settings/configure">
                        <i class="icon icon-cogs light- text-main s-14"></i> <span>General Settings</span>
                    </a>
                    </li>
                    
                     <li><a href="<?php echo BASEURL; ?>auth/users">
                        <i class="icon icon-users light- text-main s-14"></i> <span>Admin Users</span>
                    </a>
                    </li>
                </ul>
            </li>
            
            <?php endif; ?>
       
            
        </ul>
    </section>
</aside>
<!--Sidebar End-->