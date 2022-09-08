<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">

  <style type="text/css">
    .card-title{
      font-weight: bolder;
    }
     .iconny{
      max-width: 20%;
    }
    .btn{
      border:1px solid #750F27;;
      color: #750F27;
      border-radius: 0px;
    }
    .btn:hover{
       color: #750F27;
    }
  </style>
  
  <div class="row" style="margin-left: 90%;">
  <a  href="<?php echo BASEURL; ?>merchant" class="btn btn-outline-info pull-right" style="display:block; margin:5px;"> GO BACK</a>
    </div>   
    
    <div class="container">
         
      <div class="row hidden-md-up">
          
          <?php 
          
          
          foreach($billers as $biller):
              if (($hint!=="ANY") && (strpos($biller->billerName,$hint)!== false)
              || $hint=="ANY"){
          ?>

        <div class="col-md-4 py-1">
          <div class="card" >
            <div class="card-block">
              <h5 class="card-title">
                   <?php echo $biller->billerName; ?>
               </h5>
              <a href="<?php echo BASEURL; ?>eService/<?php echo $biller->billerId; ?>" class="card-link btn btn-sm pull-right" style="text-decoration: none;">CONTINUE</a>
            </div>
          </div>
        </div>
        
        <?php 
                  
              } 
        endforeach; ?>

      
    
       
      </div>
    </div>
  
