<style>

.h5 {
	background-color: white;
}
</style>

<div class="container">

<?php

$page = filterData($_GET['page']);

if($page == "settings") { ?>
	
<div class="page-header">	
<h1>Settings:</h1>
</div>

<?php

if(isset($_POST['update_settings'])) {
	
foreach($_POST as $key => $value) {
	
$key = filterData($key);
$value = filterData($_POST[$key]);

mysqli_query($con,"UPDATE settings SET value = '{$value}' WHERE name = '{$key}'");

}

echo '<div class="alert alert-dismissable alert-success">
Updated the settings ^.^
</div>';	
	
}

?>

<form action="" method="post">

<h4>Website Name:</h4>
<input type="text" name="website_name" value="<?php echo settings('website_name'); ?>" class="form-control">
<br>
<h4>Website URL:</h4>
<input type="text" name="website_url" value="<?php echo settings('website_url'); ?>" class="form-control">
<br>
<h4>Remember-Me (Stay Logged-In) Length (in days):</h4>
<input type="text" name="StayLoggedDAYS" value="<?php echo settings('StayLoggedDAYS'); ?>" class="form-control">
<br>
<h4>Google ReCaptcha:</h4>
<select name="googleReCaptcha" class="form-control">
<option value="0">Disabled</option>
<?php
if(settings('googleReCaptcha') == 1) {
echo '<option value="1" selected>Enabled</option>';	
} else {
echo '<option value="1">Enabled</option>';
}
?>
</select>
<br>
<h4>Registration Email Confirmation:</h4>
<select name="emailConfirmation" class="form-control">
<option value="0">Disabled</option>
<?php
if(settings('emailConfirmation') == 1) {
echo '<option value="1" selected>Enabled</option>';	
} else {
echo '<option value="1">Enabled</option>';
}
?>
</select>
<br>
<h4>ReCaptcha Public (Site) Key:</h4>
<input type="text" name="googleRecaptcha_PUBLICkey" value="<?php echo settings('googleRecaptcha_PUBLICkey'); ?>" class="form-control">
<br>
<h4>ReCaptcha Secret (Server) Key:</h4>
<input type="text" name="googleRecaptcha_SECRETkey" value="<?php echo settings('googleRecaptcha_SECRETkey'); ?>" class="form-control">
<br>
<h4>Rapid 2FA:</h4>
<select name="rapid2Fa" class="form-control">
<option value="0">Disabled</option>
<?php
if(settings('rapid2Fa') == 1) {
echo '<option value="1" selected>Enabled</option>';	
} else {
echo '<option value="1">Enabled</option>';
}
?>
</select>
<h4>Rapid 2FA API Key:</h4>
<input type="text" name="rapid2Fa_ApiKey" value="<?php echo settings('rapid2Fa_ApiKey'); ?>" class="form-control">
<br>
<h4>Rapid 2FA API Secret:</h4>
<input type="text" name="rapid2Fa_ApiSecret" value="<?php echo settings('rapid2Fa_ApiSecret'); ?>" class="form-control">
<br>
<br>

Your Rapid 2FA callback will be: <?php echo $website_url; ?>rapid2fa-callback.php?hash=*

<br>
<br>

<input type="submit" class="form-control btn-danger" value="Update" name="update_settings">

</form>
	
<?php } elseif($page == "all_users") { ?>

<div class="page-header">	
<h1>All Users:</h1>
</div>

<?php

$rc = mysqli_query($con,"SELECT COUNT(user_id) AS id FROM users ORDER BY user_id DESC");
$numrows = mysqli_fetch_array($rc);

$refs = 50;
$total_pages = ceil($numrows['id'] / $refs);

if(isset($_GET['offset']) && is_numeric($_GET['offset'])) {
$req_page = (int) filterData($_GET['offset']);
} else {
$req_page = 1;
}

if($req_page > $total_pages) {
   $req_page = $total_pages;
}

if($req_page < 1) {
   $req_page = 1;
}

$offset = ($req_page - 1) * $refs;

$query = mysqli_query($con,"SELECT user_id,user_name,user_email,registration_datetime FROM users ORDER BY user_id DESC LIMIT $offset, $refs");

if(mysqli_num_rows($query) > 0.9) {
	
echo '<table class="table table-striped table-hover">
  <thead>
    <tr class="danger">    
    <th>Username</th>
    <th>Email</th>
    <th>Registration Datetime</th>
    <th>Control</th>
    </tr>
  </thead>
  <tbody>';
  
while($usr = mysqli_fetch_array($query)) {	 
 
echo '<tr class="success">';
echo  '<td>' . $usr['user_name'] . '</td>';	
echo  '<td>' . $usr['user_email'] . '</td>';
echo  '<td>' . $usr['registration_datetime'] . '</td>';
echo '<td><a href="admin?fetchusrdetails=1&user=' . $usr['user_id'] . '&matcher=user_id">View</a> | <a href="admin?editusrdetails=1&user=' . $usr['user_id'] . '&matcher=user_id">Edit</a> | <a href="admin?deleteuser=1&user=' . $usr['user_id'] . '&matcher=user_id" onclick="return confirm(\'Are you sure that you want to delete this user?\');">Delete</a></td>';
echo '</tr>';
  
}
  
echo '</tbody>
</table>';

echo '<br>';
echo '<br>';

echo '<div align="center"><ul class="pagination">';

if($req_page > 1) {
	$prev = $req_page - 1;
  echo '<li><a href="?page=all_users&offset=' . $prev . '">Previous Page</a></li>';
}

echo '<li class="disabled active"><a href="?page=all_users&offset=' . $req_page . '">Current Page: ' . $req_page . '</a></li>';

if($req_page < $total_pages) {

$next = $req_page + 1;

echo '<li><a href="?page=all_users&offset=' . $next . '">Next Page</a></li>';

}
  
echo '</ul></div>';
	
} else {
	
echo '<div class="alert alert-dismissable alert-warning">
  <button type="button" class="close" data-dismiss="alert">X</button>
Bad thing! No user has registered.
</div>';
	
}

?>
	
<?php } elseif($page == "banned_users") { ?>

<div class="page-header">	
<h1>Banned Users:</h1>
</div>

<?php

$rc = mysqli_query($con,"SELECT COUNT(user_id) AS id FROM users WHERE account_status = 0 ORDER BY user_id DESC");
$numrows = mysqli_fetch_array($rc);

$refs = 50;
$total_pages = ceil($numrows['id'] / $refs);

if(isset($_GET['offset']) && is_numeric($_GET['offset'])) {
$req_page = (int) filterData($_GET['offset']);
} else {
$req_page = 1;
}

if($req_page > $total_pages) {
   $req_page = $total_pages;
}

if($req_page < 1) {
   $req_page = 1;
}

$offset = ($req_page - 1) * $refs;

$query = mysqli_query($con,"SELECT user_id,user_name FROM users WHERE account_status = 0 ORDER BY user_id DESC LIMIT $offset, $refs");

if(mysqli_num_rows($query) > 0.9) {
	
echo '<table class="table table-striped table-hover">
  <thead>
    <tr class="danger">    
    <th>Username</th>
    <th>Reason</th>
    <th>Control</th>
    </tr>
  </thead>
  <tbody>';
  
while($usr = mysqli_fetch_array($query)) {	 
 
echo '<tr class="success">';
echo  '<td>' . $usr['user_name'] . '</td>';
$load_reason = mysqli_query($con,"SELECT reason FROM ban_logs WHERE user_id = '{$usr['user_id']}'");
$reason = mysqli_fetch_array($load_reason);
echo  '<td>' . $reason['reason'] . '</td>';
echo '<td><a href="admin?unbanuser=1&user=' . $usr['user_id'] . '&matcher=user_id">Unban</a> | <a href="admin?editusrdetails=1&user=' . $usr['user_id'] . '&matcher=user_id">Edit</a> | <a href="admin?deleteuser=1&user=' . $usr['user_id'] . '&matcher=user_id" onclick="return confirm(\'Are you sure that you want to delete this user?\');">Delete</a></td>';
echo '</tr>';
  
}
  
echo '</tbody>
</table>';

echo '<br>';
echo '<br>';

echo '<div align="center"><ul class="pagination">';

if($req_page > 1) {
$prev = $req_page - 1;
echo '<li><a href="?page=banned_users&offset=' . $prev . '">Previous Page</a></li>';
}

echo '<li class="disabled active"><a href="?page=banned_users&offset=' . $req_page . '">Current Page: ' . $req_page . '</a></li>';

if($req_page < $total_pages) {

$next = $req_page + 1;

echo '<li><a href="?page=banned_users&offset=' . $next . '">Next Page</a></li>';

}
  
echo '</ul></div>';
	
} else {
	
echo '<div class="alert alert-dismissable alert-warning">
  <button type="button" class="close" data-dismiss="alert">X</button>
Good thing! No user has been banned.
</div>';
	
}

?>

<?php } elseif($page == "admins") { ?>

<div class="page-header">
<h1>Administrators</h1>
</div>

<?php

$load = mysqli_query($con,"SELECT user_id,user_name,forum_posts FROM users WHERE admin_powers = 1");

if(mysqli_num_rows($load) < 0.99) {
	
echo '<div class="alert alert-dismissable alert-success">
Whoops, no admin to show c:
</div>';
	
} else {

echo '<table class="table table-striped table-hover">
  <thead>
    <tr class="danger">
    <th>User ID</th>
    <th>User Name</th>
	<th>Manage</th>
    </tr>
</thead>
<tbody>';

while($mod = mysqli_fetch_array($load)) {	
	
echo '<tr class="success">';
echo '<td>' . $mod['user_id'] . '</td>';
echo '<td>' . $mod['user_name'] . '</td>';
echo '<td><a onclick="return confirm(\'Are you sure?\');" href="admin_cmd?page=admins&delete_mod=' . $mod['user_id'] . '">Downgrade</a></td>';
echo '</tr>';	
	
}

echo '</tbody>
</table>';

}

echo "<hr>";

if(isset($_GET['delete_mod'])) {
	
$id = filterData($_GET['delete_mod']);

mysqli_query($con,"UPDATE users SET admin_powers = 0 WHERE user_id = '{$id}' AND admin_powers = 1");

echo '<div class="alert alert-dismissable alert-success">
  <button type="button" class="close" data-dismiss="alert">X</button>
Eureka! The mod has been downgraded successfully.
</div>';	
	
}

if(isset($_POST['submit_new'])) {
	
$user_name = filterData($_POST['user_name']);
	
mysqli_query($con,"UPDATE users SET admin_powers = 1 WHERE user_name = '{$user_name}' AND admin_powers = 0");
	
echo '<div class="alert alert-dismissable alert-success">
  <button type="button" class="close" data-dismiss="alert">X</button>
Eureka! The user has been upgraded successfully.
</div>';	
	
}

echo '<br>';
echo '<br>';

echo '<form action="" method="post">';
echo '<input type="text" name="user_name" placeholder="What\'s the username you wish to upgrade to admin?" class="form-control">';
echo '<br>';
echo '<input type="submit" name="submit_new" value="Submit" class="form-control btn btn-danger">';
echo '</form>';

?>

<?php } elseif($page == "mass_mail") { ?>

<div class="page-header">
<h1>Mass-Mail</h1>
</div>

<?php

if(isset($_POST['submit_new'])) {

$subject = $_POST['subject'];
$body = $_POST['body'];

$q = mysqli_query($con,"SELECT user_email FROM users WHERE user_verified = 1");

$x = 0;

while($queue = mysqli_fetch_array($q)) {

$mail = new PHPMailer();

if(EMAIL_USE_SMTP) {

$mail->IsSMTP();

$mail->SMTPAuth = EMAIL_SMTP_AUTH;
if(defined(EMAIL_SMTP_ENCRYPTION)) {
$mail->SMTPSecure = EMAIL_SMTP_ENCRYPTION;
}

$mail->Host = EMAIL_SMTP_HOST;
$mail->Username = EMAIL_SMTP_USERNAME;
$mail->Password = EMAIL_SMTP_PASSWORD;
$mail->Port = EMAIL_SMTP_PORT;
} else {
$mail->IsMail();
}
  
$mail->Subject = $subject;
$mail->SMTPDebug = false;
$mail->do_debug = 0;
$mail->MsgHTML($body);
$address = $queue['user_email'];
$mail->AddAddress($address);
$mail->Send();

$x = $x + 1;

}

echo '<div class="alert alert-dismissable alert-success">
Email sent to ' . $x . ' users!
</div>';

}

echo '<form action="" method="post">';

echo '<input type="text" name="subject" placeholder="What\'s the subject?" class="form-control">';
echo '<br>';
echo '<textarea class="form-control" name="body" placeholder="Your message to the users" rows="6"></textarea>';
echo '<br>';
echo '<input type="submit" name="submit_new" value="Submit" class="form-control btn btn-danger">';
echo '</form>';

}

?>

</div>

<br>
<br>