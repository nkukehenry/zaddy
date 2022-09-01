

<div class="stepper sw-main sw-theme-circles"
   data-options='{
   "useURLhash":true,
   "theme":"sw-theme-circles",
   "transitionEffect":"fade",
   "toolbarSettings":{
   "showNextButton":false,
   "showPreviousButton":false
   }
   }'>
   <ul class="nav step-anchor">
      <li><a  class="circle" href="#step-1">1</a></li>
      <li><a  class="circle"  href="#step-2">2</a></li>
      <li><a  class="circle" href="#step-3">3</a></li>
      <li><a  class="circle"  href="#step-4">4</a></li>
   </ul>
   <div class="card no-b  shadow">
      <div id="step-1" class="card-body text-center p-5">
         <h3 class="my-3">
            Step 1
         </h3>
            <?php include('add_team.php');  ?>

         <nav class="pt-3" aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item"><a class="page-link" href="#step-2">Next</a>
                    </li>
                </ul>
         </nav>

      </div>
      <div id="step-2" class="card-body text-center p-5">
         <h3 class="my-3">
            Step 2
         </h3>
         <nav class="pt-3" aria-label="Page navigation">
                <ul class="pagination">
                  <li class="page-item"><a class="page-link" href="#step-1">Previous</a>
                    </li>
                    <li class="page-item"><a class="page-link" href="#step-3">Next</a>
                    </li>
                </ul>
         </nav>
      </div>
      <div id="step-3" class="card-body text-center p-5">
         <h3 class="my-3">
            Step 3
         </h3>
         <a href="#step-4" class="btn btn-primary mb-3 btn-lg">Go To Next Step</a>
      </div>
      <div id="step-4" class="card-body text-center p-5">
         <h3 class="my-3">
            Step 4
         </h3>
         <a href="#step-1" class="btn btn-primary mb-3 btn-lg">Go To Next Step</a>
      </div>
   </div>
</div>