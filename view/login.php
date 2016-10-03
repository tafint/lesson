<div class="panel panel-default">
	<div class="panel-heading">Login form</div>
  <div class="panel-body">
     <form class="form-horizontal" method="POST" action="/lesson/user/login">
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
 			<label class="col-md-6">Username(*)</label>
 			<div class="col-md-6">		
 				<input class="form-control" name="username"/>
 			</div>
 		</div>
 		<div class="form-group">
 			<label class="col-md-6">Password(*)</label>
 			<div class="col-md-6">		
 				<input class="form-control" type="password" name="password"/>
 			</div>
 		</div>

 		<div class="form-group">
 			<div class="col-md-12 text-right">		
 				<button type="submit" class="btn btn-default">Login</button>
 				<a href="/lesson/" class="btn btn-default">Cancel</a>
 			</div>
 		</div>
     </form>
  </div>
</div>