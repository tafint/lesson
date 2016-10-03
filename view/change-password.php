<div>
	<?php if (isset($page)) : ?>
		<p class="pull-left"><a href="/lesson/user/home">Home::<?php echo $page; ?></a></p>
	<?php endif; ?>
	<p class="pull-right"><a href="#">Hello <?php echo isset($user) ? $user['fullname'] : ''; ?></a></p>
	<div class="clearfix"></div>
</div>
<div class="panel panel-default <?php echo $edit_status ? '' : 'hide'; ?>">
	<div class="panel-heading">Change password</div>
  <div class="panel-body">
  	<div></div>
     <form id="change-password-form" class="form-horizontal" method="POST" action="/lesson/user/changepassword">
 		<?php
 		if (isset($message)) :
 		?>
 		<div class="alert alert-warning">
	 		<ul>
	 		<?php 
	 			foreach ($message as $m) {
	 				echo '<li>' . $m .'</li>';
	 			}
	 		?>	
	 		</ul>
	 	</div>
 		<?php
 		endif;
 		?>
 		<div class="form-group">
 			<label class="col-md-6">Current password</label>
 			<div class="col-md-6">	
 				<input type="password" class="form-control" name="password" required />
 			</div>
 		</div>
 		<div class="form-group">
 			<label class="col-md-6">New password</label>
 			<div class="col-md-6">		
 				<input type="password" class="form-control" name="new-password" required />
 			</div>
 		</div>
 		<div class="form-group">
 			<label class="col-md-6">Confirm password</label>
 			<div class="col-md-6">		
 				<input type="password" class="form-control" name="confirm-password" required />
 			</div>
 		</div>
 		<div class="form-group">
 			<div class="col-md-12 text-right">		
 				<button type="submit" class="btn btn-default">Change</button>
 			</div>
 		</div>
     </form>
  </div>
</div>
<p class="text-center <?php echo $edit_status ? 'hide' : ''; ?>">
	Your password change successfull.
</p>