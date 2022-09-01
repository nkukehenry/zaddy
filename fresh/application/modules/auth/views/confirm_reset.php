<div class="modal fade" id="reset<?php echo $user->user_id; ?>">
<form class="reset" action="<?php echo base_url(); ?>auth/resetPass" method="post">
	<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">

					<h4>Reset password for <b><?php echo $user->firstname." ".$user->lastname; ?></b> ?</h4>

					<span class="status"></span>
					

						<input type="hidden" name="user_id" value="<?php echo $user->user_id; ?>">
						<input type="hidden" name="password" value="<?php echo $config->system_pass; ?>">


		
	</div>

	<div class="modal-footer">

<input type="submit" class="btn btn-danger" value="Yes, Reset">

<a href="#" data-dismiss="modal" class="btn">Close</a>
		
	</div>



		
	</div>

	</div>

</form>

</div>