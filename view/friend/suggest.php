<h4>Friend suggestion list</h4>
<div class="row">
<?php if (isset($users)): ?>
	<?php foreach ($users as $user): ?>
	<div class="col-md-6 friend-block">
		<div class="row">
			<div class="col-xs-4 friend-name"><a href="/lesson/friend/view/<?php echo $user['id']; ?>"><?php echo $user['fullname']; ?></a></div>
			<div class="col-xs-2 thumbnail"><img src="<?php echo ($user['avatar'] != '') ? ('/lesson/'.$user['avatar']) : '/lesson/public/images/avatar.png'?>"></div>
			<div class="col-xs-6 friend-action">
				<a class="unfriend-btn <?php echo $user['is_friend'] ? '' : 'hide'; ?>" id-value="<?php echo $user['id'] ?>">Unfriend</a> 
				<a class="addfriend-btn <?php echo $friend['is_friend'] ? 'hide' : ($friend['is_request'] ? 'hide' : ''); ?>" id-value="<?php echo $user['id'] ?>">Add friend</a> 
				<span class="request-status  <?php echo $user['is_friend'] ? 'hide' : ($user['is_request'] ? '' : 'hide'); ?>" id-value="<?php echo $user['id'] ?>">Have request friend</span>
			</div>
		</div>
	</div>
	<?php endforeach ?>
<?php else: ?>
	<p class="text-center">Not found</p>
<?php endif ?>
</div>