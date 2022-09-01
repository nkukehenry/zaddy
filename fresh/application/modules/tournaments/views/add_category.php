<?php

$categories=Modules::run("category/getAll");



?>

<div class="panel">

    <div class="panel-body">

            <div class="table-title"  style="margin-bottom: 1em;">
                <div class="row">
                    <div class="col-sm-8 status">
                     
                    </div>
                    <div class="col-sm-4">
                        <a href="#addCat" class="btn btn-success" data-toggle="modal"><i class="fa fa-plus"></i> <span>Add Category</span></a>
                        
                        <a href="#deletecat" class="btn btn-danger" data-toggle="modal"><i class="fa fa-trash"></i> <span>Delete Selected</span></a>                        
                    </div>
                </div>
            </div>




            <table class="table table-striped  table-hover" id="table2">
                <thead>
                    <tr>
                        <th width="2%;">
                            <span class="custom-checkbox">
                                <input type="checkbox" id="selectAll">
                                <label for="selectAll"></label>
                            </span>
                        </th>
                        <th>Category</th>
                        <th>Details</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach($categories as $category): ?>
                    <tr id="row<?php echo $category->category_id; ?>">
                        <td>
                            <span class="custom-checkbox">
                                <input type="checkbox" id="checkbox1" name="cat_id[]"   value="<?php echo $category->category_id; ?>">
                                <label for="checkbox1"></label>
                            </span>
                        </td>
                        <td><?php echo $category->category_name; ?></td>
                        <td><?php echo $category->description; ?></td>
                        <td>
                            <a href="#" data-toggle="modal" data-target="#cat<?php echo $category->category_id; ?>" class="edit" ><i class="fa fa-edit" data-toggle="tooltip" title="View /Edit "></i> View | Edit</a> 
                        </td>
                    </tr>


                  

                  <?php 

                  include("category-details.php");


                  endforeach; ?>
                    
                </tbody>
            </table>
       
       </div>
   </div>
       

   
    <!-- Add category modal -->
    <div id="addCat" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <form class="category_form" method="post">
                    <div class="modal-header">                      
                        <h4 class="modal-title">Add Category</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body"> 

                    <span class="status"></span>

                        <div class="form-group">
                            <label>Category</label>
                            <input type="text" name='category_name' class="form-control" required>
                        </div>
                       
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" name="image_id" />
                        </div>                  
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                        <input type="reset" class="clear btn btn-default" value="Reset Form">
                        <input type="submit" class="btn btn-success" value="Save">
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Delete Modal HTML -->
    <div id="deletecat" class="modal fade">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form>
                    <div class="modal-header">                      
                        <h4 class="modal-title">Delete Category</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">                    
                        <p>Are you sure you want to delete these Records?</p>
                        <p class="text-warning"><small>This action cannot be undone.</small></p>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                        <input type="button" class="delete btn btn-danger" value="Delete">
                    </div>
                </form>
            </div>
        </div>
    </div>





<script>

$(document).ready(function () {

    
    // Select/Deselect checkboxes
    var checkbox = $('table tbody input[type="checkbox"]');
    $("#selectAll").click(function(){
        if(this.checked){
            checkbox.each(function(){
                this.checked = true;                        
            });
        } else{
            checkbox.each(function(){
                this.checked = false;                        
            });
        } 
    });


    checkbox.click(function(){
        if(!this.checked){
            $("#selectAll").prop("checked", false);
            
            $(this).attr('checker',"no");
        }
        else{

            $(this).attr('checker',"yes");
        }
    });


//deleting a category
  var selcheckbox = $('table tbody input[type="checkbox"]');
   
    $('.delete').click(function(e){

        e.preventDefault();

         selcheckbox.each(function(){
                
                if(this.checked){
            
            selcheckbox.each(function(){
                
                category_id=this.value;
                checker=$(this).attr('checker'); 
                
               
                if(checker=='yes'){
                //delete
            delete_now(category_id);
                }

            

                           
          
            });
        }    

        

            });


    });



//delete with ajax
    function delete_now(cat_id){

  
    $('.status').html('<img style="max-height:50px" src="<?php echo base_url();?>assets/images/busy.gif">');

    var url="<?php echo base_url(); ?>category/deleteCategory/"+cat_id;

    $.ajax({
        url: url,
        method:'post',
     success: function(result){

        console.log(result);

        setTimeout(function(){

            $.notify(result,'info');
            $('.status').html('');

            $('.clear').click();

            $('#row'+cat_id).fadeOut('slow');

        },3000);
        
     
    }
    });//ajax


}




//Submit data

$(".category_form").submit(function(e){

    e.preventDefault();


    $('.status').html('<img style="max-height:50px" src="<?php echo base_url();?>assets/images/busy.gif">');



    var formData=$(this).serialize();

    var url="<?php echo base_url(); ?>category/saveCategory";



    console.log(url);

    $.ajax({
        url: url,
        method:'post',
        data:formData,
     success: function(result){

        console.log(result);

        setTimeout(function(){

           $.notify(result,'info');
           $('.status').html('');

            $('.clear').click();

        },3000);
        
     
    }
    });//ajax


});//form submit



//Edit cat

$(".update_cat").submit(function(e){

    e.preventDefault();


    $('.status').html('<img style="max-height:50px" src="<?php echo base_url();?>assets/images/busy.gif">');



    var formData=$(this).serialize();

    var url="<?php echo base_url(); ?>category/updateCategory";



    console.log(url);

    $.ajax({
        url: url,
        method:'post',
        data:formData,
     success: function(result){

        console.log(result);

        setTimeout(function(){

            $.notify(result,'info');
            $('.status').html('');

            $('.clear').click();

        },3000);
        
     
    }
    });//ajax


});//form submit






});//doc ready






</script>