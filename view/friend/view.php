<?php if (!isset($error)) : ?>
	<div class="row">
		<div class="col-md-3 text-center">
			<div class="thumbnail profile-avatar">
				<img class="img-rounded" src="<?php echo ($profile['avatar'] != '') ? ('/lesson/'.$profile['avatar']) : '/lesson/public/images/avatar.png'?>" alt="avatar">

			<?php if ($is_owner && true): ?>
				<a class="btn btn-sm btn-info">Change</a>
			<?php endif; ?>
			</div>

		<?php if (($user['id'] != $profile['id']) && true): ?>
			<p class="text-center">
				<button class="btn btn-sm btn-default p-addfriend-btn <?php echo $is_friend ? 'hide' : ($is_request ? 'hide' : '') ?>" id-value="<?php echo $profile['id'] ?>">Friend</button>
				<button class="btn btn-sm btn-default p-unfriend-btn <?php echo $is_friend ? '' : 'hide' ?>" id-value="<?php echo $profile['id'] ?>">Unfriend</button>
				<button class="btn btn-sm btn-default p-addfavorite-btn <?php echo $is_favorite ? 'hide' : '' ?>" id-value="<?php echo $profile['id'] ?>">Favorite</button>
				<button class="btn btn-sm btn-default p-unfavorite-btn <?php echo $is_favorite ? '' : 'hide' ?>" id-value="<?php echo $profile['id'] ?>">Unfavorite</button>
				<button class="btn btn-sm btn-default p-addfollow-btn <?php echo $is_follow ? 'hide' : '' ?>" id-value="<?php echo $profile['id'] ?>">Follow</button>
				<button class="btn btn-sm btn-default p-unfollow-btn <?php echo $is_follow ? '' : 'hide' ?>" id-value="<?php echo $profile['id'] ?>">Unfollow</button>
			</p>
		<?php endif ?>

		</div>	
		<div class="col-md-9">
			<div id="fullname">
				<h3 class="info-output" name-part="fullname">
					<span><?php echo $profile['fullname']; ?></span>

				<?php if ($is_owner && true): ?>
					<a class="open-edit" href="#"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				<?php endif ?>

				</h3>
				<form class="form form-inline info-input hide" name-part="fullname">
			    	<div class="form-group">
					    <input class="form-control" value="<?php echo $profile['fullname'] ?>"/>
					</div>
		    		<button class="btn btn-default dynamic-edit-submit" name-part="fullname">Save</button>
		    	</form>
			</div>
			<div class="row">
				<div class="col-xs-3"><label>Email : </label></div>
				<div class="col-xs-9" id="email">
					<div class="info-output" name-part="email">
						<span><?php echo $profile['email']; ?></span>

					<?php if ($is_owner && true): ?>
						<a class="open-edit"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
					<?php endif ?>

					</div>
					<form class="form form-inline info-input hide" name-part="email">
				    	<div class="form-group">
						    <input type="text" class="form-control" value="<?php echo $profile['email'] ?>"/>
						</div>
			    		<button class="btn btn-default dynamic-edit-submit" name-part="email">Save</button>
			    	</form>
				</div>
				
			</div>
			<div class="row">
				<div class="col-xs-3"><label>Birthday : </label></div>
				<div class="col-xs-9" id="birthday">
					<div class="info-output" name-part="birthday">
						<span><?php echo $profile['birthday']; ?></span>
						
					<?php if ($is_owner && true): ?>
						<a class="open-edit"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
					<?php endif ?>

					</div>
					<form class="form form-inline info-input hide" name-part="birthday">
				    	<div class="form-group">
				    		<input type="hidden" name="birthday" value="<?php echo $profile['birthday'] ?>">		
						    <select class="form-control" name="form-date" id="form-date">
					      	</select>
					      	<select class="form-control" name="form-month" id="form-month">
					      	</select>
					      	<select class="form-control" name="form-year" id="form-year">
					      	</select>
						</div>
			    		<button class="btn btn-default dynamic-edit-submit" name-part="sex">Save</button>
			    	</form>
			    </div>
			</div>
			<div class="row">
				<div class="col-xs-3"><label>Sex : </label></div>
				<div class="col-xs-9" id="sex">
					<div class="info-output" name-part="sex">
						<span><?php echo ($profile['sex'] == 1) ? 'Male' : 'Female'; ?></span>
						
					<?php if ($is_owner && true): ?>
						<a class="open-edit"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
					<?php endif ?>

					</div>
					<form class="form form-inline info-input hide" name-part="sex">
				    	<div class="form-group">
						    <input type="radio" name="sex" value="1" <?php echo ($profile['sex'] == 1) ? 'checked' :''; ?>/> Male
						    <input type="radio" name="sex" value="2" <?php echo ($profile['sex'] == 2) ? 'checked' :''; ?>/> Female
						</div>
			    		<button class="btn btn-default dynamic-edit-submit" name-part="sex">Save</button>
			    	</form>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-3"><label>Address : </label></div>
				<div class="col-xs-9" id="address">
					<div class="info-output" name-part="address">
						<span><?php echo $profile['address']; ?></span>

					<?php if ($is_owner && true): ?>
						<a class="open-edit"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
					<?php endif ?>

					</div>
					<form class="form form-inline info-input hide" name-part="address">
				    	<div class="form-group">
						    <input type="text" class="form-control" value="<?php echo $profile['address'] ?>"/>
						</div>
			    		<button class="btn btn-default dynamic-edit-submit" name-part="address">Save</button>
			    	</form>
			    </div>
			</div>
			<div class="row">
				<div class="col-xs-3"><label>Role name : </label></div>
				<div class="col-xs-9">
					<span>
					<?php foreach ($groups as $g): ?>
						<?php if ($profile['group_id'] == $g['level']) { echo $g['name']; } ?>
					<?php endforeach ?>
					</span>
				</div>
			</div>
		</div>
	</div>
<?php if ($permisson && !$is_owner && false): ?>
	<div class="row">
		<div class="col-md-6">

		<?php if(isset($conversations)) : ?>
			<div class="m-b-10 chat-show">
				<table class="table table-hover">
				<?php foreach ($conversations as $con) : ?>
					<tr>
						<td>Conversation with <?php echo $con['fullname']; ?></td>
						<td><button class="btn btn-default show-conversation" fullname-value="<?php echo $con['fullname']; ?>" id-value="<?php echo $con['id']; ?>">Show message</button></td>
					</tr>
				<?php endforeach; ?>
				</table>
			</div>
		<?php endif; ?>

		</div>
		<div class="col-md-6">
			<!-- CHATBOX  -->
			<div class="m-b-10 chat-show chat-box" >
				<table>

			<?php 
				$current_msg = 0 ;
				foreach ($message_log as $key => $message) : ?>
				<p class="<?php echo ($message['user_id'] == $message_log[$key-1]['user_id']) ? 'next-message' :''; ?>" user-id-value="<?php echo $message['user_id'] ;?>"> <?php echo ($message['user_id'] == $message_log[$key-1]['user_id']) ? '' : ((($message['user_id'] == $user['id']) ? $user['fullname'] : $profile['fullname']).' : '); ?><?php echo $message['message']; ?></p>
			<?php 
				$current_msg = $message['id'];
				endforeach; 
			?>
				</table>
			</div>
			<!-- CHATFORM  -->
			<form id="message-form" class="form" >
				<input type="hidden" name="current-message" value="<?php echo isset($current_msg) ? $current_msg : 0; ?>"/>
				<input class="form-control" name="user-id-to" type="hidden" value="<?php echo $profile['id']; ?>"/>
			
			<?php if (($user['group_id'] == 1) || ($user['group_id'] == $profile['group_id']) || $is_friend): ?>
				<div class="input-group">
					<input class="form-control" name="message-content" type="text"/>
					
						<span class="input-group-btn">
				        	<button class="btn btn-default" type="submit" type="button">Send</button>
				    	</span>
				</div>
			<?php endif ?>

			</form>
			
		</div>
	</div>
<?php endif; ?>
	<!-- TAB  -->

<?php if (true): ?>
	<div>
	  	<ul class="nav nav-tabs" role="tablist">
	    	<li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
    
    	<?php if ($is_owner || $is_friend): ?>
		    <li role="presentation"><a href="#friendlist" aria-controls="profile" role="tab" data-toggle="tab">Friendlist (<?php echo sizeof($friends) ?>)</a></li>
		    <li role="presentation"><a href="#favoritelist" aria-controls="messages" role="tab" data-toggle="tab">Favoritelist (<?php echo sizeof($favorites) ?>)</a></li>
    	<?php endif ?>

	  	</ul>
	  	<div class="tab-content">
	  	<!-- HOME TAB -->
		    <div role="tabpanel" class="tab-pane active" id="home"> 
		    	<ul class="nav nav-pills" role="tablist">
				    <li role="presentation" class="active"><a href="#intro" aria-controls="home" role="tab" data-toggle="tab">Introduction</a></li>
				    <li role="presentation"><a href="#picture" aria-controls="profile" role="tab" data-toggle="tab">Picture</a></li>
				    <li role="presentation"><a href="#location" aria-controls="messages" role="tab" data-toggle="tab">Location</a></li>
				</ul>
				<div class="tab-content">
					<!-- INTRODUCTION TAB -->
				    <div role="tabpanel" class="tab-pane active" id="intro">
				    	<p class="info-output" name-part="introduction">
				    	<?php if ($is_owner): ?>
				    		<a class="open-edit" href="#"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
				    	<?php endif ?>
				    		<span><?php echo $profile['introduction'] ? : 'Nothing'; ?></span> 
				    	</p>
				    	<form class="form info-input hide" name-part="introduction">
					    	<div class="form-group">
							    <textarea class="form-control"><?php echo $profile['introduction'] ?></textarea>
							</div>
				    		<button class="btn btn-default dynamic-edit-submit" name-part="introduction">Save</button>
				    	</form>
				    </div>
				    <!-- PICTURE TAB -->
				    <div role="tabpanel" class="tab-pane" id="picture">
					    <div class="row">

					    <?php if ($is_owner): ?>	
					    	<div class="m-b-10 col-md-3">
					    		<form class="picture-block upload-block">
					    			<input type="file" name="image-upload[]" id="image-upload" accept="image/gif, image/jpeg, image/png" multiple="">
					    		</form>
					    		<div class="progress hide">
								  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" >
								    60%
								  </div>
								</div>
					    	</div>
					    <?php endif ?>
					    
					    <?php foreach ($images as $image): ?>	    	
					    	<div class="m-b-10 col-md-3">
					    		<div class="picture-block <?php echo $is_friend ? 'picture-block-friend' : ''; ?> <?php echo $is_owner ? 'picture-block-owner' : ''; ?>">
					    			<div class="picture-thumbnail fancybox" data-fancybox-group="galery1" id-value="<?php echo $image['id']; ?>" source-image="/lesson/<?php echo $image['path'] ?>" >
					    				<img src="/lesson/<?php echo $image['thumbnail']; ?>">
					    			</div>
					    			<ul class="list-group text-center">

					    			<?php if ($is_owner): ?>
						    			<li class="list-group-item"><a class="delete-image-btn" id-value="<?php echo $image['id']; ?>">Delete</a></li>
						    		<?php endif ?>

						    			<li class="list-group-item"><a class="view-btn fancybox" data-fancybox-group="galery2" id-value="<?php echo $image['id']; ?>">View <span>(<?php echo $image['view']; ?>)</span></a></li>
						    		
						    		<?php if ($is_friend || $is_owner): ?>
						    			<li class="list-group-item">
						    				<a class="unlike-btn ready-btn <?php echo $image['is_like'] ? '' : 'hide'; ?>" id-value="<?php echo $image['id']; ?>">Unlike <span>(<?php echo $image['like']; ?>)</span></a>
						    				<a class="like-btn ready-btn <?php echo $image['is_like'] ? 'hide' : ''; ?>" id-value="<?php echo $image['id']; ?>">Like <span>(<?php echo $image['like']; ?>)</span></a>
						    			</li>
						    		<?php endif ?>

					    			</ul>
					    		</div>
					    	</div>
					    <?php endforeach ?>

					    </div>
				    </div>
				    <!-- LOCATION TAB -->
				    <div role="tabpanel" class="tab-pane" id="location">
				    	<form>
					        <input type="hidden" name="lng" value="<?php echo $profile['lng']; ?>">
		      				<input type="hidden" name="lat" value="<?php echo $profile['lat']; ?>">
		  				</form>
				    	<div id="map"></div>
				    </div>
				</div>
		    </div>
	    <!-- END HOME TAB -->
		<?php if ($is_owner || $is_friend): ?>
		    <!-- FRIEND TAB -->
		    <div role="tabpanel" class="tab-pane" id="friendlist">
		    	<div class="row">

		    	<?php foreach ($friends as $friend): ?>
		    		<div class="col-md-6 friend-block">
		    			<div class="row">
		    				<div class="col-xs-4 friend-name"><a href="/lesson/friend/view/<?php echo $friend['user_info']['id']; ?>"><?php echo $friend['user_info']['fullname']; ?></a></div>
		    				<div class="col-xs-2 thumbnail"><img src="<?php echo ($friend['user_info']['avatar'] != '') ? ('/lesson/'.$friend['user_info']['avatar']) : '/lesson/public/images/avatar.png'?>"></div>
		    				<div class="col-xs-6 friend-action">
		    					<?php if ($friend['user_info']['id'] != $user['id']): ?>
		    						<a class="unfriend-btn <?php echo $friend['is_friend'] ? '' : 'hide'; ?>" id-value="<?php echo $friend['user_info']['id'] ?>">Unfriend</a> 
		    						<a class="addfriend-btn <?php echo $friend['is_friend'] ? 'hide' : ($friend['is_request'] ? 'hide' : ''); ?>" id-value="<?php echo $friend['user_info']['id'] ?>">Add friend</a> 
		    						<span class="request-status  <?php echo $friend['is_friend'] ? 'hide' : ($friend['is_request'] ? '' : 'hide'); ?>" id-value="<?php echo $friend['user_info']['id'] ?>">Have request friend</span>
		    					<?php endif ?>
		    				</div>
		    			</div>
		    		</div>
		    	<?php endforeach ?>

		    	</div>
		    </div>
		    <!-- END FRIEND TAB -->
		    <!-- FAVORITE TAB -->
		    <div role="tabpanel" class="tab-pane" id="favoritelist">
		    	<div class="row">

		    	<?php foreach ($favorites as $favorite): ?>
			    	<div class="col-md-6 friend-block">
						<div class="row">
							<div class="col-xs-4 friend-name">
								<a href="/lesson/friend/view/<?php echo $favorite['user_info']['id']; ?>">
									<?php echo $favorite['user_info']['fullname']; ?>
								</a>
							</div>
							<div class="col-xs-2 thumbnail"><img src="/lesson/public/images/avatar.png"></div>
							<div class="col-xs-6 friend-action">

							<?php if ($favorite['user_info']['id'] != $user['id']): ?>
	    						<a class="unfriend-btn <?php echo $favorite['is_friend'] ? '' : 'hide'; ?>" id-value="<?php echo $favorite['user_info']['id'] ?>">Unfriend</a> 
	    						<a class="addfriend-btn <?php echo $favorite['is_friend'] ? 'hide' : ($favorite['is_request'] ? 'hide' : ''); ?>" id-value="<?php echo $favorite['user_info']['id'] ?>">Add friend</a> 
	    						<span class="request-status  <?php echo $favorite['is_friend'] ? 'hide' : ($favorite['is_request'] ? '' : 'hide'); ?>" id-value="<?php echo $favorite['user_info']['id'] ?>">Have request friend</span>
	    						<a class="unfavorite-btn <?php echo $favorite['is_favorite'] ? '' : 'hide'; ?>" id-value="<?php echo $favorite['user_info']['id'] ?>">Unfavorite</a>
	    						<a class="addfavorite-btn <?php echo $favorite['is_favorite'] ? 'hide' : ''; ?>" id-value="<?php echo $favorite['user_info']['id'] ?>">Favorite</a>
		    				<?php endif ?>

							</div>
						</div>
					</div>
				<?php endforeach ?>

				</div>
		   	</div>
		    <!-- END FAVORITE TAB -->
		<?php endif; ?>

		</div>
	</div>
<?php endif ?>
	<!-- CONVERSATION MODAL -->
	<div class="modal fade" tabindex="-1" role="dialog" id="conversation-modal">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title"></h4>
	      </div>
	      <div class="modal-body">
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      </div>
	    </div>
	  </div>
	</div>
	<!-- END CONVERSATION MODAL -->
	<!-- MAP MODAL -->
	<?php if ($user['id'] == $profile['id']): ?>
		<div class="modal fade" tabindex="-1" role="dialog" id="confirm-location">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-body">
		      	<form>
		      		<input type="hidden" name="user-id" value="<?php echo $profile['id']; ?>">
		      	</form>
		        <p>Do you want change location to "<span class="new-address"></span>"?</p>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-primary submit-location">Yes</button>
		        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
		      </div>
		    </div>
		  </div>
		</div>
		<!-- END MAP MODAL -->
		<div class="modal fade" tabindex="-1" role="dialog" id="confirm-image">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-body">
		      	<form>
		      		<input type="hidden" name="user-id" value="<?php echo $profile['id']; ?>">
		      		<input type="hidden" name="image-id" value="0">
		      	</form>
		        <p>Do you want to delete image?</p>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-danger submit-image">Yes</button>
		        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
		      </div>
		    </div>
		  </div>
		</div>
		<!-- END MAP MODAL -->
		<!-- IMAGE MODAL -->
	<?php if ($user['id'] == $profile['id']): ?>
		<div class="modal fade" tabindex="-1" role="dialog" id="image-album">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		    	<div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title">Select image from album</h4>
		     </div>
		      <div class="modal-body">
		      	<form>
		      		<input type="hidden" name="image-value" value="0">
			        <div class="row">

			    	<?php foreach ($images as $image): ?>	    	
				    	<div class="m-b-10 col-md-6">
				    		<div class="picture-thumbnail" id-value="<?php echo $image['id']; ?>" source-image="/lesson/<?php echo $image['path'] ?>">
				    			<img src="/lesson/<?php echo $image['thumbnail']; ?>">
				    			<i class="glyphicon glyphicon-ok"></i>
				    		</div>
				    	</div>
					<?php endforeach ?>

			        </div>
		        </form>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-primary submit-avatar">Yes</button>
		        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
		      </div>
		    </div>
		  </div>
		</div>
		<!-- END IMAGE MODAL -->
	<?php endif ?>
	<?php endif ?>
<?php else : ?>
	<p class="text-center"><?php echo $message; ?></p>
<?php endif ?>



