<?php

error_reporting(0);

?>
<html>
<head>
    <title>Agent Statement</title>
<style>
body {font-family: Arial;
	font-size: 12pt;
	max-width:21cm;
	max-height:29.7cm;
}
p {	margin: 0pt; }
table.items {
	border: 0.1mm solid #000000;
}
td { vertical-align: top; }
.items td {
	border-left: 0.2mm solid #000000;
	border-right: 0.2mm solid #000000;
}
table thead th { background-color: #EEEEEE;
	text-align: center;
	border: 0.1mm solid #000000;
	/*font-variant: small-caps;*/
}

.items tr td {
	border: 0.1mm solid #808080;
	
}

.items td.blanktotal {
	background-color: #EEEEEE;
	border: 0.1mm solid #000000;
	background-color: #FFFFFF;
	border: 0mm none #000000;
	border-top: 0.1mm solid #000000;
	border-right: 0.1mm solid #000000;
}
.items td.totals {
	text-align: right;
	border: 0.1mm solid #000000;
}
.items td.cost {
	text-align:left;
}
.logo{
margin-top:0em;
margin-left:20%;
margin-right:20%;
margin-bottom:0.5em;
}

.heading{
margin-top:0.4em;
margin-left:20%;
margin-right:10%;
margin-bottom:0.1em;
}

.title{
margin-top:0.0em;
margin-left:30%;
margin-right:10%;
margin-bottom:0.1em;
}

.text-danger{
    
    color:red;
}
.text-green{
    
    color:green;
    font-weight:bolder;
}
</style>
</head>
<body>


<table   width="100%" >
<tr style="border-right: 0; border-left: 0; border-top: 0;">
	<td colspan=1 style="border-right: 0; border-left: 0; border-top: 0; text-align: left;">
		<img src="<?php echo ASSET_URL; ?>img/basic/logo.png" style="float:left;" width="100px"> 
	</td>

	<td colspan=5 valign="bottom">
		<br>
		<br>
		<center><h2><?=$heading?> <br> <?=strtoupper($agent->names)?></h2></center>
	</td>

	
</tr>
<tr><td></td> <td></td></tr>

</table>
<table width="100%" class="items" style="font-size: 12pt; border-collapse: collapse; " cellpadding="8">


 <thead>
     <tr>
        <th>DATE</th>
        <th>ITEM NAME</th>
        <th>FROM</th>
                                        <!--<th>FROM NAME</th>-->
                                        <th>TO</th>
                                        <th>TO NAME</th>
        <th>TRAN REF</th>
        <th>STATUS</th>
        <th>AMOUNT</th>
        <th width="10%">BALANCE</th>
        </tr>
 </thead>
        <tbody>
        <?php 
            $balance=0;
            
            //print_r($transactions);
            
            $class="text-danger";
            
        foreach($transactions as $transaction):
            
            $balance += $transaction->impact;
            
            if($transaction->impact>0)
              $class="text-green";
             if($transaction->impact<0)
              $class="text-danger";
        ?>
        <tr>
            <td>
                <?php echo $transaction->paymentDate; ?>
            </td>
            <td>
                <?php if ($transaction->paymentCode=="LOAD"){ 
                    
                    if($transaction->impact<0)
                    echo  "WALLET DEBIT";
                    if($transaction->impact>0)
                    echo  "WALLET LOAD";
                    
                    
                } 
                                            else if($transaction->paymentCode=="COMMS"){ echo "COMMS"; } 
                                            else if($transaction->paymentCode=="SHARE"){ echo "FLOAT SHARE"; } 
                                            else { echo Modules::run("billers/getItemName",$transaction->paymentCode); }?>
            </td>
             <td class="text-dark">
                                            <?php 
                                            
                                             if($transaction->impact>0){
                                                echo $transaction->customerNo;
                                              } 
                                              else if($transaction->impact<0 ){
                                                  
                                                 echo $transaction->agentNo;
                                              }
                                            ?>
                                        </td>
                                         <!--<td class="text-dark">-->
                                            <?php 
                                            
                                            //  if($transaction->impact>0){
                                                
                                                
                                            //     echo $transaction->customerName;
                                            //   } 
                                            //   else if($transaction->impact<0 ){
                                            //       echo Modules::run("agents/getByAgentNo",$transaction->agentNo)->names;
                                            
                                            //   }
                                            ?>
                                        <!--</td>-->
                                        
                                        
                                        <td class="text-dark">
                                            <?php 
                                            
                                             if($transaction->impact>0){
                                                echo $transaction->agentNo;
                                              } 
                                              else if($transaction->impact<0 ){
                                                  
                                                 echo $transaction->customerNo;
                                              }
                                            ?>
                                        </td>
                                        
                                         <td class="text-dark">
                                            <?php if($transaction->impact>0){
                                                
                                                
                                                if($transaction->paymentCode=="COMMS" || $transaction->paymentCode=="SAHRE" ||$transaction->paymentCode=="LOAD"){
                                              echo Modules::run("agents/getByAgentNo",$transaction->agentNo)->names;
                                          }else{
                                              echo Modules::run("agents/getByAgentNo",$transaction->agentNo)->names;
                                          }
                                              } 
                                              else if($transaction->impact<0 ){
                                                  
                                          if($transaction->paymentCode=="COMMS" || $transaction->paymentCode=="SHARE" ||$transaction->paymentCode=="LOAD"){
                                              echo "SYSTEM";
                                          }else{
                                              echo $transaction->customerName;
                                          }
                                                
                                              }?>
                                        </td>
            <td class="text-dark">
                <?php echo $transaction->requestRef; ?>
            </td>
            <td><?php echo $transaction->finalStatus; ?></td>
            <td class="<?php echo $class; ?>">
                UGX <?php echo number_format($transaction->impact); ?>
            </td>
            <td >
               <b></b>UGX <?php echo number_format($balance); ?></b>
            </td>
         
        </tr>
      <?php endforeach;  ?>
      
      <tr>
          <td colspan="6">AGENT BALANCE </td>
          <td colspan="3" class="text-green">UGX <?php echo number_format($balance); ?></td>
      </tr>
        </tbody>

</table>
<br>
<br>
<caption><b>NOTE: This statement covers all SUCCESSFUL & PENDING transactions and all wallet loads</b></caption>

</body>

</html>


