<h4>Message list</h4>
<?php  if (isset($data_messages)) : ?>
<table class="table table-hover">
	<?php foreach ($data_messages as $msg) : ?>
			<tr>
				<td>Message from <?php echo $msg['fullname']; ?></td>
				<td><button class="btn btn-default view-message" name-value="<?php echo $msg['fullname']; ?>" content-value="<?php echo $msg['message'];?>" id-value="<?php echo $msg['id']; ?>">Show message</button></td>
			</tr>
	<?php endforeach; ?>
</table>
<?php endif; ?>
<div class="modal fade" tabindex="-1" role="dialog" id="message-modal">
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