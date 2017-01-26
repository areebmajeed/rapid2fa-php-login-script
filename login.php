<?php

/**
 * PHP Login Script - Rapid 2FA
 *
 * @author Areeb Majeed
 * @copyright 2017 Rapid 2FA
 * @license https://opensource.org/licenses/MIT MIT License
 *
 * @link https://rapid2fa.com/
 */

if(version_compare(PHP_VERSION, '5.3.7', '<')) {
exit('Sorry, this script does not run on a PHP version smaller than 5.3.7!');
} elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
require_once('libraries/password_compatibility_library.php');
}

require_once('functions/Core.php');
require_once('config/config.php');
require_once('libraries/PHPMailer.php');

$con = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

require_once('functions/init.php');

if(UserLoggedIn() == true) {

header("Location: index");

} else {	
	
$pageName = "Login";
	
require_once('assets/inc/frontend_header.php');
include('assets/pages/login.php');
require_once('assets/inc/_footer.php');
	
}

mysqli_close($con);