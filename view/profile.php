<div>
	<?php if (isset($page)) : ?>
		<p class="pull-left"><a href="/lesson/user/home">Home::<?php echo $page; ?></a></p>
	<?php endif; ?>
	<p class="pull-right"><a href="#">Hello <?php echo isset($user) ? $user['fullname'] : ''; ?></a></p>
	<div class="clearfix"></div>
</div>
<div class="panel panel-default">
	<div class="panel-heading"><span class="edit-hide hide">Edit </span>Profile</div>
  <div class="panel-body">
  	<div></div>
     <form id="edit-form" class="form-horizontal" method="POST" action="/lesson/user/profile">
     	<input type="hidden" name="edit-status" id="edit-status" value="<?php echo ($edit_status==false) ? 0 :1; ?>">
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
 			<label class="col-md-6">Fullname</label>
 			<div class="col-md-6">	
 				<span class="edit-show"><?php echo $user['fullname']; ?></span>
 				<input class="form-control edit-hide hide" name="fullname" value="<?php echo $user['fullname']; ?>"/>
 			</div>
 		</div>
 		<div class="form-group">
 			<label class="col-md-6">Username</label>
 			<div class="col-md-6">		
 				<?php echo $user['username']; ?>
 			</div>
 		</div>
 		<div class="form-group">
 			<label class="col-md-6">Email</label>
 			<div class="col-md-6">		
 				<?php echo $user['email']; ?>
 			</div>
 		</div>
 		<div class="form-group">
 			<label class="col-md-6">Address</label>
 			<div class="col-md-6">	
 				<span class="edit-show"><?php echo $user['address']; ?></span>
 				<input class="form-control edit-hide hide" name="address" value="<?php echo $user['address']; ?>"/>
 			</div>
 		</div>
 		<div class="form-group">
 			<label class="col-md-6">Sex</label>
 			<div class="col-md-6">	
 				<span class="edit-show"><?php echo ($user['sex'] ==1) ? 'Male' : 'Female'; ?></span>
 				<div  class="edit-hide hide">
	 				<input type="radio" value="1" name="sex" <?php echo ($user['sex'] ==1) ? 'checked' : ''; ?>/> Male
	 				<input type="radio" value="2" name="sex" <?php echo ($user['sex'] ==2) ? 'checked' : ''; ?>/> Female
 				</div>
 			</div>
 		</div>
 		<div class="form-group">
 			<label class="col-md-6">Birthday</label>
 			<div class="col-md-6">	
 				<span class="edit-show"><?php echo $user['birthday']; ?></span>
 				<div  class="edit-hide hide">
 					<input type="hidden" name="birthday" id="birthday" value="<?php echo $user['birthday']; ?>">
	 				<div class="row">
	 					<div class="col-md-4">
	 						<select id="form-date" class="form-control" name="date" value="<?php echo explode('-',$user['birthday'])[2]; ?>"></select>
	 					</div>
	 					<div class="col-md-4">
	 						<select id="form-month" class="form-control" name="month" value="<?php echo explode('-',$user['birthday'])[1]; ?>"></select>
	 					</div>
	 					<div class="col-md-4">
	 						<select id="form-year"  class="form-control" name="year" value="<?php echo explode('-',$user['birthday'])[0]; ?>"></select>
	 					</div>
 					</div>	
 				</div>
 			</div>
 		</div>
 		<div class="form-group">
 			<div class="col-md-12 text-right">		
 				<a id="submit-edit" class="btn btn-default edit-hide hide">Save</a>
 				<a id="change-button" class="btn btn-default edit-show">Change</a>
 			</div>
 		</div>
     </form>
  </div>
</div>