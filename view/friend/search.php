<h4>Search result for '<?php echo isset($search_content) ? $search_content : ""; ?>'</h4>
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
					<td><a href="/lesson/friend/view/<?php echo $user['id']; ?>"><?php echo $user['fullname']; ?></a></td>
					<td><?php echo ($user['sex'] == 1) ? 'Male' : 'Female'; ?></td>
					<td><?php echo $user['birthday']; ?></td>
					<td><?php echo $user['address']; ?></td>
					<td><a class="btn btn-default add-button" id-value="<?php echo $user['id']; ?>" <?php echo $user['request_status'] ? 'disabled' : ''; ?>>Add friend</a></td>
				</tr>
		<?php  endforeach; ?>
	</table>
<?php else : ?>
	<?php if (isset($message)) : ?>
		<?php foreach ($message as $m) : ?>
			<p class="text-center"><?php echo $m ;?></p>
		<?php endforeach; ?>
	<?php endif; ?>
<?php endif; ?>