<h4>Follow list</h4>
<?php if ($follows): ?>
	<table class="table table-hover" id="follow-table">
	<?php foreach ($follows as $follow): ?>
	<tr>
			<td><a href="/lesson/friend/view/<?php echo $follow['user_from_id']; ?>"><?php echo $follow['user_from_name']; ?></a></td>
			<td><?php echo $follow['type'] ?></td>
			<td>
				<a href="/lesson/friend/view/<?php echo $follow['user_to_id']; ?>"><?php echo $follow['user_to_name']; ?></a> 
				<?php if (!$follow['is_view']): ?>
				<span class="label label-info">New</span>
				<?php endif ?>
			</td>
	</tr>
	<?php endforeach ?>
	</table>
<?php else: ?>
	<p class="text-center">Not found</p>
<?php endif ?>

