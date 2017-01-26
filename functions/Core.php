<?php

function UserLoggedIn() {

if(!empty($_SESSION['user_name']) && ($_SESSION['user_logged_in'] == 1)) {

return true;
	
} else {
	
return false;

}
	
}

function fetch_content($url) {

$ch = curl_init();
$timeout = 5;
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
$data = curl_exec($ch);
curl_close($ch);
return $data;

}

function verifyCaptcha() {

$recaptcha_secret = settings("googleRecaptcha_SECRETkey");
$response = fetch_content("https://www.google.com/recaptcha/api/siteverify?secret=". $recaptcha_secret . "&response=" . $_POST['g-recaptcha-response']);
$response = json_decode($response, true);	

if($response["success"] === true) {
	
return true;
	
} else {
	
return false;
	
}
	
}

function settings($config) {

global $con;
$con_f = $con;

$query = mysqli_query($con_f,"SELECT value FROM settings WHERE name = '{$config}'");
$data = mysqli_fetch_array($query);

return $data['value'];

}

function filterData($input) {
	
global $con;
$con_f = $con;
 
$search = array(
'@<script[^>]*?>.*?</script>@si',  
'@<[\/\!]*?[^<>]*?>@si',           
'@<style[^>]*?>.*?</style>@siU',   
'@<![\s\S]*?--[ \t\n\r]*>@'       
);

$wipe = array(

"+union+",
"%20union%20",
"/union/*",
' union ',
"union",
"sql",
"mysql",
"database",
"cookie",
"coockie",
"select",
"from",
"where",
"benchmark",
"concat",
"table",
"into",
"by",
"values",
"exec",
"shell",
"truncate",
"wget",
"/**/"

);
 
$output = preg_replace($search, '', $input);
$output = str_replace($wipe,'',$output);

return mysqli_real_escape_string($con_f,trim($output));

}

function get_IP() {
    // check for shared internet/ISP IP
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    // check for IPs passing through proxies
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // check if multiple ips exist in var
        if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
                if (validate_ip($ip))
                    return $ip;
            }
        } else {
            if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED']))
        return $_SERVER['HTTP_X_FORWARDED'];
    if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
        return $_SERVER['HTTP_FORWARDED_FOR'];
    if (!empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED']))
        return $_SERVER['HTTP_FORWARDED'];

    // return unreliable ip since all else failed
    return $_SERVER['REMOTE_ADDR'];
}


function validate_ip($ip) {
    if (strtolower($ip) === 'unknown')
        return false;

    // generate ipv4 network address
    $ip = ip2long($ip);

    // if the ip is set and not equivalent to 255.255.255.255
    if ($ip !== false && $ip !== -1) {
        // make sure to get unsigned long representation of ip
        // due to discrepancies between 32 and 64 bit OSes and
        // signed numbers (ints default to signed in PHP)
        $ip = sprintf('%u', $ip);
        // do private network range checking
        if ($ip >= 0 && $ip <= 50331647) return false;
        if ($ip >= 167772160 && $ip <= 184549375) return false;
        if ($ip >= 2130706432 && $ip <= 2147483647) return false;
        if ($ip >= 2851995648 && $ip <= 2852061183) return false;
        if ($ip >= 2886729728 && $ip <= 2887778303) return false;
        if ($ip >= 3221225984 && $ip <= 3221226239) return false;
        if ($ip >= 3232235520 && $ip <= 3232301055) return false;
        if ($ip >= 4294967040) return false;
    }
    return true;
}

function sendMail($to,$subject,$body) {
	
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
$address = $to;
$mail->AddAddress($address);
$mail->Send();
	
}

function checkAdmin() {
	
$status = false;

if(UserLoggedIn() == true) {
	
$powers = loadAccDetails("user_id",$_SESSION['user_id'],"admin_powers");
	
if($powers == 1) {
	
$status = true;
	
}

}

return $status;
	
}

function loadAccDetails($input_field,$input,$output_field) {

global $con;
$con_f = $con;

$query = mysqli_query($con_f,"SELECT $output_field FROM users WHERE $input_field = '{$input}'");
$data = mysqli_fetch_array($query);

return $data[$output_field];
	
}