<h4>Management users</h4>
<?php  if (sizeof($users) > 0) : ?>
<table class="table table-hover" id="management-table">
	<tr>
		<th>Name</th>
		<th>Sex</th>
		<th>Birthday</th>
		<th>Address</th>
		<th></th>
		<th></th>
	</tr>
	<?php foreach ($users as $user) : ?>
			<tr id-value="<?php echo $user['id']; ?>">
				<td><a href="/lesson/friend/view/<?php echo $user['id']; ?>"><?php echo $user['fullname']; ?></a></td>
				<td><?php echo ($user['sex'] == 1) ? 'Male' : 'Female'; ?></td>
				<td><?php echo $user['birthday']; ?></td>
				<td><?php echo $user['address']; ?></td>
				<td style="width:120px">
					<select class="form-control change-group" id-value="<?php echo $user['id']; ?>">
						<?php foreach ($groups as $group) { ?>
							<option value="<?php echo $group['id']; ?>" <?php echo($group['level'] == $user['group_id']) ? 'selected' :''; ?>><?php echo $group['name']; ?></option>
						<<?php } ?>
					</select>
				</td>
				<td style="width:150px">
					<button class="btn btn-default edit-user" id-value="<?php echo $user['id']; ?>">Edit</button>
					<button class="btn btn-default delete-user" id-value="<?php echo $user['id']; ?>">Delete</button>
				</td>
			</tr>
	<?php  endforeach; ?>
	<?php else: ?>
		<p class="text-center">Not have user</p>
	<?php endif; ?>
</table>
<div class="modal fade" tabindex="-1" role="dialog" id="management-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <p></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" id="edit-profile-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form class="form-horizontal">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        	<div id="show-message"></div>
        	<input type="hidden" name="user-id">
        	<div class="form-group">
			    <label for="inputEmail3" class="col-sm-2 control-label">Fullname</label>
			    <div class="col-sm-10">
			      <input type="text" class="form-control" name="fullname" placeholder="Fullname">
			    </div>
			</div>
			<div class="form-group">
			    <label for="inputEmail3" class="col-sm-2 control-label">Sex</label>
			    <div class="col-sm-10">
			      <input type="radio" value="1" name="sex"> Male
			      <input type="radio" value="2" name="sex"> Female
			    </div>
			</div>
			<div class="form-group">
			    <label for="inputEmail3" class="col-sm-2 control-label">Birthday</label>
			    <div class="col-sm-10 form-inline">
			      	<select class="form-control" name="form-date" id="form-date">
			      	</select>
			      	<select class="form-control" name="form-month" id="form-month">
			      	</select>
			      	<select class="form-control" name="form-year" id="form-year">
			      	</select>
			    </div>
			</div>
			<div class="form-group">
			    <label for="inputEmail3" class="col-sm-2 control-label">Address</label>
			    <div class="col-sm-10">
			      <input type="text" class="form-control" name="address" placeholder="Address">
			    </div>
			</div>
        
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" name="submit">Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
    </form>
  </div>
</div>