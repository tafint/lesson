<!DOCTYPE html>
<html>
<head>
	<title>Lession 1</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="/lesson1/public/libs/bootstrap-3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="/lesson1/public/libs/fancybox/fancybox.css">
	<link rel="stylesheet" type="text/css" href="/lesson1/public/css/style.css">
	<script type="text/javascript">
		var USER_ID = <?php echo isset($user) ? $user['id'] : 0; ?>
	</script>
<!-- 	<script src="https://maps.googleapis.com/maps/api/js?sensor=false&dummy=.js"></script> -->
	<script   src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="/lesson1/public/libs/bootstrap-3.3.7/js/bootstrap.min.js"></script>
	<script src="/lesson1/public/libs/fancybox/fancybox.pack.js"></script>
	<script src="/lesson1/public/libs/noty/packaged/jquery.noty.packaged.min.js"></script>
	<script src="/lesson1/public/js/main.js"></script>
</head>
<body>
	<div class="wrapper">
	<?php if (isset($navbar)) : ?>
		<nav class="navbar navbar-default">
		  	<div class="container">
		  		<div class="navbar-header">
			      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-menu" aria-expanded="false">
			        <span class="sr-only">Toggle navigation</span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			      </button>
			      <a class="navbar-brand" href="#">L2</a>
			    </div>
			    <div class="collapse navbar-collapse" id="main-menu">
				    <form class="navbar-form navbar-left" action="/lesson1/search" method="POST">
				        <div class="input-group">
				          <input type="text" class="form-control" placeholder="Search" name="s">
				           <span class="input-group-btn">
					        <button type="submit" class="btn btn-default">Submit</button>
					      </span>
				        </div>
				    </form>
				    <ul class="nav navbar-nav navbar-right">
				        <li><a href="/lesson1/">Home</a></li>
				        <li><a href="/lesson1/list-friend">Friend list (<?php echo isset($count_friend) ? $count_friend : 0; ?>)</a></li>
				        <li><a href="/lesson1/request-friend">Friend request (<?php echo isset($count_request) ? $count_request : 0; ?>)</a></li>
				    <?php if (($count_request <5) && false) : ?>
				        <li><a href="/lesson1/friend/suggest">Friend suggestion</a></li>
				    <?php endif; ?>
				    <?php if (false) : ?>
				        <li><a href="/lesson1/follow/index">Follow list</a></li>
				    <?php endif; ?>
				        <li><a href="/lesson1/message">Message (<?php echo isset($count_message) ? $count_message : 0; ?>)</a></li>
				    <?php if ($user['group_id'] ==1) { ?>
				   		<li><a href="/lesson1/manage">Management users</a></li> 
				    <?php } ?>
				        <li><a href="/lesson1/logout">Logout</a></li>
				        <li><a href="#">Hi <?php echo $user['fullname']; ?></a></li>
				    </ul>
			    </div>
			</div><!-- /.navbar-collapse -->
		</nav>
	<?php endif; ?>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
				
			

