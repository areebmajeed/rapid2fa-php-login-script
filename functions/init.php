<?php

$session_name = 'login_session';
$secure = false;
$httponly = true;
ini_set('session.use_only_cookies', 1);
ini_set('session.entropy_file', '/dev/urandom');
ini_set('session.entropy_length', '512');

$StayLoggedDAYS = settings("StayLoggedDAYS");
$StayLoggedDAYS = 30+60*60*24*$StayLoggedDAYS;

define("COOKIE_RUNTIME", $StayLoggedDAYS);

$cookieParams = session_get_cookie_params();
session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
session_name($session_name);
session_start();

$website_name = settings("website_name");
$website_url = settings("website_url");
$file_name = basename(filterData($_SERVER['PHP_SELF']));

if(isset($_GET['logout'])) {
	
if(isset($_COOKIE['remember_me'])) {
	
unset($_COOKIE['remember_me']);
setcookie('remember_me', "", time() - COOKIE_RUNTIME*2, "/", COOKIE_DOMAIN);
	
}

$_SESSION = array();
session_destroy();
$core_system_messages[] = "You have been successfully logged out from the website.";
	
}

elseif(isset($_POST['login'])) {
	
$username = filterData($_POST['username']);
$password = filterData($_POST['password']);

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"SELECT user_id,user_name,password_hash,user_email,user_verified,failed_logins,last_failed_login,account_group,account_status FROM users WHERE user_name = ? LIMIT 1");
mysqli_stmt_bind_param($stmt,"s",$username);
mysqli_stmt_execute($stmt);
$load_user = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$user = mysqli_fetch_array($load_user);

if(settings("googleReCaptcha") == 1 && verifyCaptcha() == false) {
	
$core_system_messages[] = "My bad! You haven't filled the captcha properly.";
	
} elseif($username == "") {
	
$core_system_messages[] = "The username has been left blank.";
	
} elseif(strlen($username) > 32) {
	
$core_system_messages[] = "The username length is limited to 32 characters.";
	
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $username)) {

$core_system_messages[] = "The username cannot contain special characters.";

} elseif($password == "") {
	
$core_system_messages[] = "The password has been left blank.";
	
} elseif(mysqli_num_rows($load_user) < 0.99) {
	
$core_system_messages[] = "Invalid login details, please try again.";

} elseif(!password_verify($password, $user['password_hash'])) {
	
$core_system_messages[] = "Invalid login details, please try again.";

$last_failed_login = time();
mysqli_query($con,"UPDATE users SET failed_logins = failed_logins + 1, last_failed_login = '{$last_failed_login}' WHERE user_name = '{$user['user_name']}'");

} elseif($user['user_verified'] == 0) {
	
$core_system_messages[] = "Your account is not verified yet. Please check your email for our email.";
	
} elseif($user['account_status'] == 0) {

$reason = mysqli_query($con,"SELECT reason FROM ban_logs WHERE user_id = '{$user['user_id']}'");
$reason = mysqli_fetch_array($reason);
	
$core_system_messages[] = "Your account has been suspended. <b>Reason:</b> " . $reason['reason'];
	
} elseif($user['failed_logins'] >= 6 && $user['last_failed_login'] > (time() - 900)) {
	
$core_system_messages[] = "Too many failed attempts. Please wait for 15 minutes.";
	
} else {

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['user_name'] = $user['user_name'];
$_SESSION['user_email'] = $user['user_email'];
$_SESSION['user_logged_in'] = 1;

$datetime = date("Y-m-d H:i:s");

if(!isset($_POST['remember_me'])) {
	
$rememberme_token = null;

} else {

$rememberme_token = hash('sha256',mt_rand());
$hash = $rememberme_token . COOKIE_SECRET_KEY;
$token = $user['user_id'] . ":" . hash('sha256',$hash) . ":" . $rememberme_token;
setcookie('remember_me', $token, time() + COOKIE_RUNTIME, "/", COOKIE_DOMAIN);

}

$datetime = date("Y-m-d H:i:s");

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"UPDATE users SET last_logged_in = ?, failed_logins = 0, rememberme_token = ? WHERE user_name = ?");
mysqli_stmt_bind_param($stmt,"sss",$datetime,$rememberme_token,$user['user_name']);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

}	
	
}

elseif(isset($_COOKIE['remember_me']) && !isset($_SESSION['user_logged_in'])) {
	
$cookie = filterData($_COOKIE['remember_me']);

list($user_id,$hash,$token) = explode(":",$cookie);

$construct = $token . COOKIE_SECRET_KEY;
$construct = hash('sha256',$construct);

if(is_numeric($user_id) && $hash == $construct) {

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"SELECT user_id,user_name,user_email FROM users WHERE rememberme_token = ? LIMIT 1");
mysqli_stmt_bind_param($stmt,"s",$token);
mysqli_stmt_execute($stmt);
$load_user = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$user = mysqli_fetch_array($load_user);
	
$_SESSION['user_id'] = $user['user_id'];
$_SESSION['user_name'] = $user['user_name'];
$_SESSION['user_email'] = $user['user_email'];
$_SESSION['user_logged_in'] = 1;

$rememberme_token = hash('sha256',mt_rand());
$hash = $rememberme_token . COOKIE_SECRET_KEY;
$token = $user['user_id'] . ":" . hash('sha256',$hash) . ":" . $rememberme_token;
setcookie('remember_me', $token, time() + COOKIE_RUNTIME, "/", COOKIE_DOMAIN);

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"UPDATE users SET rememberme_token = ? WHERE user_name = ?");
mysqli_stmt_bind_param($stmt,"ss",$rememberme_token,$user['user_name']);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
	
} else {
	
$_SESSION = array();
session_destroy();

unset($_COOKIE['remember_me']);
setcookie('remember_me', "", time() - COOKIE_RUNTIME*2, "/", COOKIE_DOMAIN);

$core_system_messages[] = "Your session has expired. Please login once again.";
	
}

}
	
elseif(isset($_POST['register'])) {
	
$username = filterData($_POST['username']);
$email = filterData($_POST['email']);
$password = filterData($_POST['password']);
$password_repeat = filterData($_POST['password_repeat']);

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"SELECT COUNT(user_id) as id FROM users WHERE user_name = ? LIMIT 1");
mysqli_stmt_bind_param($stmt,"s",$username);
mysqli_stmt_execute($stmt);
$load_user = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$confirm_user = mysqli_fetch_array($load_user);

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"SELECT COUNT(user_id) as id FROM users WHERE user_email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt,"s",$email);
mysqli_stmt_execute($stmt);
$load_user = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$confirm_email = mysqli_fetch_array($load_user);

if(settings("googleReCaptcha") == 1 && verifyCaptcha() == false) {
	
$core_system_messages[] = "My bad! You haven't filled the captcha properly.";
	
} elseif($username == "") {
	
$core_system_messages[] = "The username has been left blank.";
	
} elseif(strlen($username) > 32) {
	
$core_system_messages[] = "The username length is limited to 32 characters.";
	
} elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $username)) {

$core_system_messages[] = "The username cannot contain special characters.";

} elseif($email == "") {
	
$core_system_messages[] = "The email has been left blank.";
	
} elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {

$core_system_messages[] = "The email is not valid.";

} elseif($password == "") {
	
$core_system_messages[] = "The password has been left blank.";
	
} elseif(strlen($password) > 30) {
	
$core_system_messages[] = "The password length is limited to 30 characters.";
	
} elseif($password_repeat == "") {
	
$core_system_messages[] = "The password (repeat) has been left blank.";
	
} elseif($password != $password_repeat) {
	
$core_system_messages[] = "The password doesn't match with the repeating field.";
	
} elseif($confirm_user['id'] > 0.99) {
	
$core_system_messages[] = "A user with that username already exists.";
	
} elseif($confirm_email['id'] > 0.99) {
	
$core_system_messages[] = "A user with that email already exists.";
	
} else {
	
if(settings("emailConfirmation") == 1) {
	
$core_system_messages[] = "Your account has been registered, please check your email account for a confirmation mail.";
$user_verified = 0;
$activation_hash = hash('sha256',mt_rand());

$subject = "Confirm your email at {$website_name}";
$body = "Hello there!

<br>
<br>

Thank you for registering at {$website_name}. However, before you getting running on the site, you've to confirm your email address. Click <b><a href='{$website_url}?confirm_email={$activation_hash}'>here</a></b> to confirm your account, or copy the link below directly to confirm your email address.

<br>
<br>

<b>Confirmation link: {$website_url}?confirm_email={$activation_hash}</b>

<br>
<br>

Regards,
<br>
{$website_name}

<br>
<br>

<small>If you didn't apply for an account, please ignore this email and you won't be bugged again.</small>";
	
} else {
	
$core_system_messages[] = "Your account has been registered, you can login into your account now.";
$user_verified = 1;
$activation_hash = null;

$subject = "You have successfully registered at {$website_name}";
$body = "Hello there!

<br>
<br>

Thank you for registering at {$website_name}. You don't require to confirm your email, you can immediately login with your username and the password you chose during the registration.

<br>
<br>

Regards,
<br>
{$website_name}

<br>
<br>

<small>If you didn't apply for an account, please contact us.</small>";
	
}
	
sendMail($email,$subject,$body);

$password_hash = password_hash($password, PASSWORD_DEFAULT, array('cost' => 12));
$registration_datetime = date("Y-m-d H:i:s");
$registration_ip = get_IP();
	
$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"INSERT INTO users (user_name,user_email,password_hash,user_verified,activation_hash,registration_datetime,registration_ip) VALUES (?,?,?,?,?,?,?)");
mysqli_stmt_bind_param($stmt,"sssisss",$username,$email,$password_hash,$user_verified,$activation_hash,$registration_datetime,$registration_ip);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
	
}	
	
}

elseif(isset($_GET['confirm_email'])) {
	
$activation_hash = filterData($_GET['confirm_email']);

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"SELECT COUNT(user_id) as id,user_id FROM users WHERE activation_hash = ? AND activation_hash <> '' AND user_verified = 0 LIMIT 1");
mysqli_stmt_bind_param($stmt,"s",$activation_hash);
mysqli_stmt_execute($stmt);
$load_user = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$user = mysqli_fetch_array($load_user);

if($user['id'] < 0.99) {
	
$core_system_messages[] = "The account has been already confirmed.";
	
} else {
	
$user = mysqli_fetch_array($load);

mysqli_query($con,"UPDATE users SET user_verified = 1, activation_hash = null WHERE user_id = '{$user['user_id']}'");	

$core_system_messages[] = "Account successfully confirmed. You may login now.";
	
}	
	
} 

elseif(isset($_POST['reset_password'])) {
	
$username = filterData($_POST['username']);

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"SELECT COUNT(user_id) as id,user_id,user_email FROM users WHERE user_name = ? LIMIT 1");
mysqli_stmt_bind_param($stmt,"s",$username);
mysqli_stmt_execute($stmt);
$load_user = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$user = mysqli_fetch_array($load_user);	

if(settings("googleReCaptcha") == 1 && verifyCaptcha() == false) {
	
$core_system_messages[] = "My bad! You haven't filled the captcha properly.";
	
} elseif($username == "") {
	
$core_system_messages[] = "Your username is left blank.";
	
} elseif($user['id'] < 0.99) {
	
$core_system_messages[] = "Your account was not found.";
	
} else {
	
$user = mysqli_fetch_array($load_username);
	
$reset_hash = hash('sha256',mt_rand());
$user_id = $user['user_id'];
$email = $user['user_email'];

mysqli_query($con,"UPDATE users SET reset_hash = '{$reset_hash}' WHERE user_id = '{$user_id}'");

$subject = "Reset your {$website_name} password";
$body = "Hello there!

<br>
<br>

It looks like you have lost your account password. If you have recently tried to reset your password at our site, please click <b><a href='{$website_url}reset-password?reset_user={$user_id}&code={$reset_hash}'>here</a></b> or copy the link below to reset a new password.

<br>
<br>

<b>Reset URL:</b> {$website_url}reset-password?reset_user={$user_id}&code={$reset_hash}

<br>
<br>

Regards,
<br>
{$website_name}";
	
sendMail($email,$subject,$body);
	
$core_system_messages[] = "Check your email for a reset link.";	
	
}	
	
}

elseif($file_name == "reset-password.php" && isset($_GET['reset_user']) && isset($_GET['code'])) {
	
$status_code = FALSE;
	
$user_id = filterData($_GET['reset_user']);
$reset_hash = filterData($_GET['code']);

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"SELECT COUNT(user_id) as id,user_id FROM users WHERE reset_hash = ? AND reset_hash <> '' AND user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt,"si",$reset_hash,$user_id);
mysqli_stmt_execute($stmt);
$load_user = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$user = mysqli_fetch_array($load_user);

if($user['id'] < 0.99) {
	
$core_system_messages[] = "Invalid password reset link.";	

} else {
	
$status_code = TRUE;

if(isset($_POST['change_reset_password'])) {
	
$password = filterData($_POST['password']);
$password_repeat = filterData($_POST['password_repeat']);

if($password == "") {
	
$core_system_messages[] = "The password has been left blank.";
	
} elseif(strlen($password) > 30) {
	
$core_system_messages[] = "The password length is limited to 30 characters.";
	
} elseif($password_repeat == "") {
	
$core_system_messages[] = "The password (repeat) has been left blank.";
	
} elseif($password != $password_repeat) {
	
$core_system_messages[] = "The password doesn't match with the repeating field.";
	
} else {
	
$user = mysqli_fetch_array($load);

$password_hash = password_hash($password, PASSWORD_DEFAULT, array('cost' => 12));

mysqli_query($con,"UPDATE users SET password_hash = '{$password_hash}', reset_hash = null WHERE user_id = '{$user['user_id']}'");

$core_system_messages[] = "Your password has been successfully changed.";
	
} 
	
}	
	
}	
	
}

elseif(isset($_POST['resend_email'])) {
	
$username = filterData($_POST['username']);

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"SELECT COUNT(user_id) as id,user_id,user_email,activation_hash FROM users WHERE user_name = ? AND user_verified = 0 AND activation_hash <> '' LIMIT 1");
mysqli_stmt_bind_param($stmt,"s",$username);
mysqli_stmt_execute($stmt);
$load_user = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$user = mysqli_fetch_array($load_user);

if(settings("googleReCaptcha") == 1 && verifyCaptcha() == false) {
	
$core_system_messages[] = "My bad! You haven't filled the captcha properly.";
	
} elseif($username == "") {
	
$core_system_messages[] = "Your username is left blank.";
	
} elseif($user['id'] < 0.99) {
	
$core_system_messages[] = "Your account was not found, or is already active.";
	
} else {
	
$email = $user['user_email'];
$activation_hash = $user['activation_hash'];

$subject = "Confirm your email at {$website_name}";
$body = "Hello there!

<br>
<br>

It looks like you didn't receive our confirmation link last time and requested it another time. Please copy and paste the link below to activate your account at our site.

<br>
<br>

<b>Confirmation link: {$website_url}?confirm_email={$activation_hash}</b>

<br>
<br>

Regards,
<br>
{$website_name}

<br>
<br>

<small>If you didn't apply for an account, please ignore this email and you won't be bugged again.</small>";
	
sendMail($email,$subject,$body);
	
$core_system_messages[] = "Check your email for the confirmation link.";	
	
}
	
}

elseif(isset($_POST['change_email_details'])) {

$email = filterData($_POST['email']);

if($email == "") {
	
$core_system_messages[] = "Your email is left blank.";
	
} elseif($email == $_SESSION['user_email']) {
	
$core_system_messages[] = "No change was made.";
	
} elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {

$core_system_messages[] = "The email is not valid.";

} else {
	
$confirm_code = hash('sha256',mt_rand());
	
	
$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"UPDATE email_updates SET email = ?, confirm_code = ? WHERE user_id = ?");
mysqli_stmt_bind_param($stmt,"ssi",$email,$confirm_code,$_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$r = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

if($r == 0) {
	
$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"INSERT INTO email_updates (user_id,email,confirm_code) VALUES (?,?,?)");
mysqli_stmt_bind_param($stmt,"iss",$_SESSION['user_id'],$email,$confirm_code);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
	
}

$subject = "Confirm your email change";

$body = "Hello there!

<br>
<br>

It looks like you've added a new email address to your account at the site. Please confirm the change by copying the link below.

<br>
<br>

<b>Confirmation link: {$website_url}?confirm_email_change={$confirm_code}</b>

<br>
<br>

Regards,
<br>
{$website_name}";

sendMail($email,$subject,$body);

$core_system_messages[] = "Check your email for the confirmation link.";	
	
}
	
}

elseif(isset($_POST['change_password_details'])) {

$password = filterData($_POST['password']);
$password_repeat = filterData($_POST['password_repeat']);

if($password == "") {
	
$core_system_messages[] = "You haven't entered a password.";
	
} if($password != "" && strlen($password) > 30) {
	
$core_system_messages[] = "The password length is limited to 30 characters.";
	
} elseif($password != "" && $password_repeat == "") {
	
$core_system_messages[] = "The password (repeat) has been left blank.";
	
} elseif($password != "" && $password != $password_repeat) {
	
$core_system_messages[] = "The password doesn't match with the repeating field.";
	
} else {
	
$password_hash = password_hash($password, PASSWORD_DEFAULT, array('cost' => 12));

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"UPDATE users SET password_hash = ? WHERE user_id = ?");
mysqli_stmt_bind_param($stmt,"si",$password_hash,$_SESSION['user_id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

$core_system_messages[] = "Your password has been changed with immediate effect.";	
	
}
	
}

elseif(isset($_GET['confirm_email_change'])) {
	
$confirm_code = filterData($_GET['confirm_email_change']);

$stmt = mysqli_stmt_init($con);
$stmt = mysqli_prepare($con,"SELECT COUNT(id) as id,user_id,email FROM email_updates WHERE confirm_code = ?");
mysqli_stmt_bind_param($stmt,"s",$confirm_code);
mysqli_stmt_execute($stmt);
$load_user = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);
$user = mysqli_fetch_array($load_user);

if($user['id'] < 0.99) {

$core_system_messages[] = "The email change couldn't be confirmed.";	
	
} else {
	
$data = mysqli_fetch_array($load);

$_SESSION['user_email'] = $data['email'];
mysqli_query($con,"UPDATE users SET user_email = '{$data['email']}' WHERE user_id = '{$data['user_id']}'");
mysqli_query($con,"DELETE FROM email_updates WHERE id = '{$data['id']}'");
	
$core_system_messages[] = "Your new email has been applied now.";	
	
}	
	
}


if(settings('rapid2Fa') == 1 && $file_name == "rapid2fa-callback.php") {
	
require_once('Rapid2FA.php');

try {

$hash = filterData($_GET['hash']);

$client = new Rapid2FA(RAPID2FA_API_KEY,RAPID2FA_API_SECRET);
$validity = $client->handleVerification($_SESSION['user_id'],$hash);

if($validity['status'] == true) {

mysqli_query($con,"UPDATE users SET 2fa_verified = 1 WHERE user_id = {$_SESSION['user_id']}");
header("Location: index");

} else {

if(isset($_COOKIE['remember_me'])) {
	
unset($_COOKIE['remember_me']);
setcookie('remember_me', "", time() - COOKIE_RUNTIME*2, "/", COOKIE_DOMAIN);
	
}

$_SESSION = array();
session_destroy();

header("Location: login");

}

} catch(Exception $e) {

$error = $e->getMessage();

}
	
}

if(settings('rapid2Fa') == 1 && UserLoggedIn() == true && loadAccDetails('user_id',$_SESSION['user_id'],'2fa_verified') == 0) {
	
require_once('Rapid2FA.php');

try {

$client = new Rapid2FA(RAPID2FA_API_KEY,RAPID2FA_API_SECRET);
$session = $client->generate2FASession($_SESSION['user_id']);
$link = $session['hosted_page'];

if($session['2fa_enabled'] == false) {

mysqli_query($con,"UPDATE users SET 2fa_verified = 1 WHERE user_id = {$_SESSION['user_id']}");

} else {

header("Location: " . $link);
mysqli_close($con);
exit();

}

} catch(Exception $e) {

$error = $e->getMessage();

}
	
}