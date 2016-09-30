<p class="text-right"><a href="#">Hello <?php echo isset($user) ? $user['fullname'] : ''; ?></a></p>
<h4>Main menu</h4>
<ul class="list-group">
	<li class="list-group-item"><a href="/lesson1/profile">Profile</a></li>
	<li class="list-group-item"><a href="/lesson1/change-email">Change email</a></li>
	<li class="list-group-item"><a href="/lesson1/change-password">Change password</a></li>
	<li class="list-group-item"><a href="/lesson1/logout">Logout</a></li>
	<li class="list-group-item list-group-item-info"><a href="/lesson1/list-friend"><b>Lesson2</b></a></li>
</ul>
