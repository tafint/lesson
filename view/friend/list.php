<h4>Friend list</h4>
<?php  if (isset($list_friends)) : ?>
<table class="table table-hover">
	<tr>
		<th>Name</th>
		<th>Sex</th>
		<th>Birthday</th>
		<th>Address</th>
		<th></th>
	</tr>
	
	<?php foreach ($list_friends as $friend) : ?>
			<tr>
				<td><?php echo $friend['user']['fullname']; ?></td>
				<td><?php echo ($friend['user']['sex'] == 1) ? 'Male' : 'Female'; ?></td>
				<td><?php echo $friend['user']['birthday']; ?></td>
				<td><?php echo $friend['user']['address']; ?></td>
				<td><a class="btn btn-default" href="/lesson1/view-friend/<?php echo $friend['user']['id']; ?>">View</a></td>
			</tr>
	<?php  endforeach; ?>
	
</table>
<?php else: ?>
	<p class="text-center">Not have friend</p>
<?php endif; ?>