<?php

error_reporting(0);

?>
<html>
<head>
    <title>Agent Statement</title>
<style>
body {font-family: Arial;
	font-size: 10pt;
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
		<center><h2><?=$heading?> <br> <?=strtoupper($agent->names)?>

     <br>  <i>STARTING BALANCE: UGX  <?php echo number_format((float)$startingBalance, 2, '.', ''); ?></i>
  </h2>

    </center>
	</td>

	
</tr>
<tr><td></td> <td></td></tr>

</table>
<table width="100%" class="items" style="font-size: 10pt; border-collapse: collapse; " cellpadding="8">


 <thead>
     <tr>
         <th>DATE</th>
        <th>ITEM NAME</th>
        <th>FROM</th>
        <th>FROM NAME</th>
        <th>TO</th>
        <th>TO NAME</th>
        <th  width="15%">TRAN REF</th>
        <th width="9%">STATUS</th>
        <th>IMPACT/AMOUNT</th>
        <th width="10%">BALANCE</th>
        </tr>
 </thead>
<tbody>
          <?php 
              $balance=number_format((float)$startingBalance, 2, '.', '');
              
              //print_r($transactions);
              
              $class="text-danger";
              
          foreach($transactions as $transaction):
              
              $balance += $transaction->impact;
              
              if($transaction->impact>0)
                $class="text-green";
               if($transaction->impact<0)
                $class="text-danger";
          ?>
          <tr class="">
              <td>
                  <?php echo $transaction->paymentDate; ?>
              </td>
              <td>
                  <?php 

                  $tranData =json_decode($transaction->requestObject);

                  if(!empty($tranData->itemName)){
                     echo  $tranData->itemName;
                  }

                  else if($transaction->paymentCode=="SHARE"){ echo "FLOAT SHARE"; } 
                  else if($transaction->paymentCode=="COMMS"){ echo $transaction->narration; } 
                  else if ($transaction->paymentCode=="LOAD"){ echo  "WALLET LOAD";}
                  else { 
                    echo (empty($transaction->itemName))?$transaction->itemName:$transaction->narration; 
                  }

                  ?>
              </td>
               <td class="text-dark">
                  <?php 
                  

                   if($transaction->impact>0){
                    if($transaction->paymentCode=="LOAD" || $transaction->paymentCode=="SHARE")
                      echo $transaction->agentNo;
                    else
                      echo $transaction->customerNo;
                    } 
                    else if($transaction->impact<0 ){
                        
                       echo $transaction->agentNo;
                    }

                  ?>
              </td>
               <td class="text-dark">
                  <?php 
                  
                   if($transaction->impact>0){
                      echo $transaction->customerName;
                    } 
                    else if($transaction->impact<0 ){
                        echo $transaction->names;
                    }
                  ?>
              </td>
              
              
              <td class="text-dark">
                  <?php 
                  
                   if($transaction->impact>0){
                      echo $transaction->customerNo;
                    } 
                    else if($transaction->impact<0 ){
                        
                       echo $transaction->customerNo;
                    }
                  ?>
              </td>
              
               <td class="text-dark">
                  <?php if($transaction->impact>0){

                    if($transaction->paymentCode=="COMMS" || $transaction->paymentCode=="LOAD")
                      echo $transaction->customerName;
                    else
                        echo  $transaction->names;
                  }
                    else if($transaction->impact<0 ){
                 
                       echo $transaction->customerName;
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
            <th colspan="6">CLOSING BALANCE </th>
            <th colspan="4" class="text-green">UGX <?php echo number_format($balance); ?></th>
        </tr>
          </tbody>

</table>
<br>
<br>
<caption><b>NOTE: This statement covers all SUCCESSFUL & PENDING transactions and all wallet loads</b></caption>

</body>

</html>


