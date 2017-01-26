<section id="admin-page">

<div class="container" >
<div id="duh">
<div align="center">

<h1><?php echo $website_name; ?>: Admin CP</h1>
<big>Maintain your website, keep a check on users and everything else from one single place.</big>
<hr>
</div>

<div align="center">

<div class="btn btn-group-justified">
<a href="admin_cmd?page=settings" class="btn btn-primary"><b>Settings</b></a>
<a href="admin_cmd?page=all_users" class="btn btn-primary"><b>All Users</b></a>
<a href="admin_cmd?page=banned_users" class="btn btn-primary"><b>Banned Users</b></a>
</div>

<div class="btn btn-group-justified">
<a href="admin_cmd?page=admins" class="btn btn-primary"><b>Administrators</b></a>
<a href="admin_cmd?page=admins" class="btn btn-primary"><b>Add an Administrator</b></a>
<a href="admin_cmd?page=mass_mail" class="btn btn-primary"><b>Mass-Mail</b></a>
</div>

</div>

<br>

<?php

if(isset($_GET['fetchusrdetails'])) {
	
$input = filterData($_GET['user']);
$matcher = filterData($_GET['matcher']);

$matcher_array = array("user_name","user_id");
	
if($input == "") {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
Input field is blank.
</div>';	
	
} elseif(in_array($matcher,$matcher_array) == FALSE) {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
Invalid match settings. Ah.
</div>';
	
} else {

if($matcher == "user_name") {
$user_query = mysqli_query($con,"SELECT * FROM users WHERE user_name = '{$input}' LIMIT 1");
} else {
$user_query = mysqli_query($con,"SELECT * FROM users WHERE user_id = '{$input}' LIMIT 1");	
}

if(mysqli_num_rows($user_query) < 0.99) {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
No user found. Take a look at the inputs.
</div>';
	
} else {

$user_details = mysqli_fetch_array($user_query);

echo '<table class="table table-striped table-hover">';

echo '<tr class="success">
<td>User ID</td>
<td>' . $user_details['user_id'] . '</td>
</tr>';

echo '<tr class="success">
<td>User Name</td>
<td>' . $user_details['user_name'] . '</td>
</tr>';

echo '<tr class="success">
<td>Email</td>
<td>' . $user_details['user_email'] . '</td>
</tr>';

echo '<tr class="success">
<td>Registration Date</td>
<td>' . $user_details['registration_datetime'] . '</td>
</tr>';

echo '<tr class="success">
<td>Registration IP</td>
<td>' . $user_details['registration_ip'] . '</td>
</tr>';

echo '<tr class="success">
<td>Last Login</td>
<td>' . $user_details['last_logged_in'] . '</td>
</tr>';

echo '<tr class="success">
<td>Admin Powers</td>';
if($user_details['account_status'] == 1) {
echo '<td>Yes</td>';
} else {
echo '<td>No</td>';
}
echo '</tr>';

echo '<tr class="success">
<td>Account Status</td>';
if($user_details['account_status'] == 1) {
echo '<td>Active</td>';
} else {
echo '<td>Inactive</td>';
}
echo '</tr>';

echo '<tr class="success">
<td>Control</td>
<td><a href="admin?editusrdetails=1&user=' . $user_details['user_id'] . '&matcher=user_id">Edit</a> | <a href="admin?deleteuser=1&user=' . $user_details['user_id'] . '&matcher=user_id" onclick="return confirm(\'Are you sure that you want to delete this user?\');">Delete</a></td>
</tr>';

echo '</table>';
	
}
}
	
} elseif(isset($_GET['editusrdetails'])) {
	
$input = filterData($_GET['user']);
$matcher = filterData($_GET['matcher']);

$matcher_array = array("user_name","user_id");

if($input == "") {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
Input field is blank.
</div>';	
	
} elseif(in_array($matcher,$matcher_array) == FALSE) {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
Invalid match settings. Ah.
</div>';
	
} else {

if($matcher == "user_name") {
$user_query = mysqli_query($con,"SELECT * FROM users WHERE user_name = '{$input}' LIMIT 1");
} else {
$user_query = mysqli_query($con,"SELECT * FROM users WHERE user_id = '{$input}' LIMIT 1");	
}

if(mysqli_num_rows($user_query) < 0.99) {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
No user found. Take a look at the inputs.
</div>';
	
} else {

$user_details = mysqli_fetch_array($user_query);

echo '<form action="admin" method="post">';

echo '<input type="hidden" name="user_id" class="form-control" value="' . $user_details['user_id'] . '">';
echo '<b>User Name:</b>
<input type="text" name="user_name" class="form-control" value="' . $user_details['user_name'] . '">';
echo '<br>';
echo '<b>User Email:</b>
<input type="text" name="user_email" class="form-control" value="' . $user_details['user_email'] . '">';
echo '<br>';
echo '<input type="submit" name="submit_change" class="form-control btn btn-danger" value="Confirm">';
echo '</form>';

}

}

} elseif(isset($_GET['unbanuser'])) {
	
$input = filterData($_GET['user']);
$matcher = filterData($_GET['matcher']);
$reason = filterData($_GET['reason']);

$matcher_array = array("user_name","user_id");

if($input == "") {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
Input field is blank.
</div>';	
	
} elseif(in_array($matcher,$matcher_array) == FALSE) {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
Invalid match settings. Ah.
</div>';
	
} else {

if($matcher == "user_name") {
$user_query = mysqli_query($con,"SELECT user_id, user_name, account_status FROM users WHERE user_name = '{$input}' LIMIT 1");
} else {
$user_query = mysqli_query($con,"SELECT user_id, user_name, account_status FROM users WHERE user_id = '{$input}' LIMIT 1");	
}

if(mysqli_num_rows($user_query) < 0.99) {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
No user found. Take a look at the inputs.
</div>';
	
} else {

$user_details = mysqli_fetch_array($user_query);

if($reason == "") {
$reason = "Admin has not specified any reason for this ban.";
}

if($user_details['account_status'] == 1) {
	
mysqli_query($con,"UPDATE users SET account_status = 0 WHERE user_id = '{$user_details['user_id']}'");
mysqli_query($con,"INSERT INTO ban_logs (user_id,reason) VALUES ('{$user_details['user_id']}','$reason')");

echo '<div class="alert alert-dismissable alert-success">
<button type="button" class="close" data-dismiss="alert">X</button>
User banned successfully.
</div>';
	
} else {
	
mysqli_query($con,"UPDATE users SET account_status = 1 WHERE user_id = '{$user_details['user_id']}'");
mysqli_query($con,"DELETE FROM ban_logs WHERE user_id = '{$user_details['user_id']}'");

echo '<div class="alert alert-dismissable alert-success">
<button type="button" class="close" data-dismiss="alert">X</button>
User unbanned successfully.
</div>';
	
}

}

}

} elseif(isset($_GET['deleteuser'])) {
	
$input = filterData($_GET['user']);
$matcher = filterData($_GET['matcher']);

$matcher_array = array("user_name","user_id");

if($input == "") {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
Input field is blank.
</div>';	
	
} elseif(in_array($matcher,$matcher_array) == FALSE) {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
Invalid match settings. Ah.
</div>';
	
} else {

if($matcher == "user_name") {
$user_query = mysqli_query($con,"SELECT user_id, user_name FROM users WHERE user_name = '{$input}' LIMIT 1");
} else {
$user_query = mysqli_query($con,"SELECT user_id, user_name FROM users WHERE user_id = '{$input}' LIMIT 1");	
}

if(mysqli_num_rows($user_query) < 0.99) {
	
echo '<div class="alert alert-dismissable alert-danger">
<button type="button" class="close" data-dismiss="alert">X</button>
No user found. Take a look at the inputs.
</div>';
	
} else {

$user_details = mysqli_fetch_array($user_query);

mysqli_query($con,"DELETE FROM users WHERE user_id = '{$user_details['user_id']}'");

echo '<div class="alert alert-dismissable alert-success">
<button type="button" class="close" data-dismiss="alert">X</button>
User has been deleted successfully.
</div>';

}

}

} elseif(isset($_POST['submit_change'])) {
	
$user_id = filterData($_POST['user_id']);
$user_name = filterData($_POST['user_name']);
$user_email = filterData($_POST['user_email']);
$type = filterData($_POST['type']);
$avatar_url = filterData($_POST['avatar_url']);
$forum_posts = filterData($_POST['forum_posts']);
	
mysqli_query($con,"UPDATE users SET user_name = '$user_name', user_email = '$user_email', account_group = '$type', avatar_url = '$avatar_url', forum_posts = '{$forum_posts}' WHERE user_id = '$user_id'");

echo '<div class="alert alert-dismissable alert-success">
<button type="button" class="close" data-dismiss="alert">X</button>
User has been updated.
</div>';

}

?>

<br>
<br>

<h3>Load User Details:</h3>

<form method="get" action="admin">

<b>Input Query:</b>
<input type="text" name="user" class="form-control" placeholder="Will be used for finding the user.">
<br>
<b>What's that?:</b>
<select name="matcher" class="form-control">
<option value="user_name">A User Name</option>
<option value="user_id">A User ID</option>
</select>
<br>
<input type="submit" name="fetchusrdetails" class="btn btn-primary" value="Find and Display">

</form>

<hr>

<h3>Edit a User:</h3>

<form method="get" action="admin">

<b>Input Query:</b>
<input type="text" name="user" class="form-control" placeholder="Will be used for finding the user.">
<br>
<b>What's that?:</b>
<select name="matcher" class="form-control">
<option value="user_name">A User Name</option>
<option value="user_id">A User ID</option>
</select>
<br>
<input type="submit" name="editusrdetails" class=" btn btn-primary" value="Find and Edit">

</form>

<hr>

<h3>Ban/Unban User:</h3>

<form method="get" action="admin" onsubmit="return confirm('Are you sure that you want to ban/unban this user?');">

<b>Input Query:</b>
<input type="text" name="user" class="form-control" placeholder="Will be used for finding the user (User will be unbanned if already banned).">
<br>
<b>What's that?:</b>
<select name="matcher" class="form-control">
<option value="user_name">A User Name</option>
<option value="user_id">A User ID</option>
</select>
<br>
<b>Reason:</b>
<input type="text" name="reason" class="form-control" placeholder="What is the reason of this ban?">
<br>
<input type="submit" name="unbanuser" class=" btn btn-primary" value="Ban/Unban User">

</form>

<hr>

<h3>Delete A User:</h3>

<form method="get" action="admin" onsubmit="return confirm('Are you sure that you want to delete this user?');">

<b>Input Query:</b>
<input type="text" name="user" class="form-control" placeholder="Will be used for finding the user.">
<br>
<b>What's that?:</b>
<select name="matcher" class="form-control">
<option value="user_name">A User Name</option>
<option value="user_id">A User ID</option>
</select>
<br>
<input type="submit" name="deleteuser" class="btn btn-primary" value="Delete User">

</form>

</div> 
</div>
</section>