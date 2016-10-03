<h4>Friend request</h4>
<?php if (isset($users)) : ?>
	<table class="table table-hover">
		<tr>
			<th>Name</th>
			<th>Sex</th>
			<th>Birthday</th>
			<th>Address</th>
			<th></th>
		</tr>
		<?php  foreach ($users as $user) : ?>
			<tr>
				<td><a href="/lesson/friend/view/<?php echo $user['user_info']['id']; ?>"><?php echo $user['user_info']['fullname']; ?></a></td>
				<td><?php echo ($user['user_info']['sex'] == 1) ? 'Male' : 'Female'; ?></td>
				<td><?php echo $user['user_info']['birthday']; ?></td>
				<td><?php echo $user['user_info']['address']; ?></td>
				<td><a class="btn btn-default accept-button" id-value="<?php echo $user['id']; ?>" <?php echo $user['request_status'] ? 'disabled' : ''; ?>>Accept</a> <a class="btn btn-default delete-button" id-value="<?php echo $user['id']; ?>" <?php echo $user['request_status'] ? 'disabled' : ''; ?>>Delete</a></td>
			</tr>
		<?php endforeach; ?>
	</table>
<?php else : ?>
	<?php if (isset($message)) : ?>
		<?php foreach ($message as $m) :?>
			<p class="text-center"><?php echo $m ;?></p>
		<?php endforeach; ?>
	<?php endif; ?>
<?php endif; ?>