<div>
	<?php if (isset($page)) : ?>
		<p class="pull-left"><a href="/lesson1/home">Home::<?php echo $page; ?></a></p>
	<?php endif; ?>
	<p class="pull-right"><a href="#">Hello <?php echo isset($user) ? $user['fullname'] : ''; ?></a></p>
	<div class="clearfix"></div>
</div>
<div class="panel panel-default <?php echo $edit_status ? '' : 'hide'; ?>">
	<div class="panel-heading">Change email</div>
  <div class="panel-body">
  	<div></div>
     <form id="change-email-form" class="form-horizontal" method="POST" action="/lesson1/change-email">
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
 			<label class="col-md-6">Current email</label>
 			<div class="col-md-6">	
 				<span><?php echo $user['email']; ?></span>
 			</div>
 		</div>
 		<div class="form-group">
 			<label class="col-md-6">New email</label>
 			<div class="col-md-6">		
 				<input type="text" class="form-control" name="email" required/>
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
	A confirm email has been sent to <?php echo $user['email']; ?>. </br>
	Please click on the confirmation link to confirm your new email.
</p>