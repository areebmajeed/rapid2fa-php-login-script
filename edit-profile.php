<?php

/**
*
* "Login-Script" is a copyrighted service from Inculutus Ltd. It remains the property of it's original author: Areeb.
*
*
* This file is part of Login-Script. Please don't reproduce any part of the script without the permissions of Areeb.
*
* Please contact: hello[at]areebmajeed[dot]me for queries.
*
* Copyrighted 2015 - Inculutus (Areeb)
*
*/

if(version_compare(PHP_VERSION, '5.3.7', '<')) {
exit('Sorry, this script does not run on a PHP version smaller than 5.3.7!');
} elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
require_once('libraries/password_compatibility_library.php');
}

require_once('functions/Core.php');
require_once('config/config.php');
require_once('libraries/PHPMailer.php');
require_once('functions/Rapid2FA.php');

$con = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

require_once('functions/init.php');

if(UserLoggedIn() == true) {
	
if(settings('rapid2Fa') == 1 && isset($_GET['2fa'])) {
	
try {

$client = new Rapid2FA(settings('rapid2Fa_ApiKey'),settings('rapid2Fa_ApiSecret'));
$settings = $client->generateSettingsPage($_SESSION['user_id']);
$link = $settings['hosted_page'];

header("Location: " . $link);

} catch(Exception $e) {

//Can be anything, but not ER-05200.
$error = $e->getMessage();

}	
	
} else {

$pageName = "Edit Profile";
	
require_once('assets/inc/frontend_header.php');
include('assets/pages/edit-profile.php');
require_once('assets/inc/_footer.php');

}

} else {	
	
header("Location: login");
	
}

mysqli_close($con);