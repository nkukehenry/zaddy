<!-- Default modal Size -->
<div class="modal fade" id="cat<?php echo $category->category_id; ?>"  >
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="defaultModalLabel">Category: <?php echo $category->category_name; ?></h4>
            </div>
            <div class="modal-body"> 

              <ul class="list-group">

                <form class="update_cat">

                    <span class="status"></span>

                <?php if($category->image_id){ ?>
                <li class="list-group-item list-group-item-default">
                    
                   <img src="<?php echo base_url(); ?>assets/images/items/<?php echo $category->image_id; ?>" width='100px' />

                </li>

                <?php } ?>

                <input type="hidden" name="category_id" value="<?php echo $category->category_id; ?>">

                <li class="list-group-item list-group-item-default"><strong style="margin-right: 1em;"> Category: </strong> <input name="category_name" type="text" value="<?php echo $category->category_name; ?>" class="form-control" /> </li>
                <li class="list-group-item list-group-item-default"><strong style="margin-right: 1em;">Description: </strong> <input type="text" value="<?php echo $category->description; ?>" class="form-control input-lg" name="description" /> </li>
               

              </ul>

             </div>
            <div class="modal-footer">

                <button class="btn bg-aqua" type="submit">Save Chnages</button>

                <a class="pull-right btn" href="#" data-dismiss="modal">Close</a>
            </form>
            </div>
        </div>
    </div>
</div>
