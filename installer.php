<?php

require_once('functions/Core.php');
require_once('config/config.php');

$con = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

if(mysqli_connect_errno()) {
	
echo "Failed to connect to MySQL: " . mysqli_connect_error();

echo "<br>";
echo "<br>";

echo 'Please check your config.php details again and refresh this page.';

} else {
	
if(isset($_GET['override'])) {
	
$sql = "sql.txt";

$templine;
$lines = file($sql);
foreach ($lines as $line) {
// Skip it if it's a comment
if (substr($line, 0, 2) == '--' || $line == '')
    continue;

// Add this line to the current segment
$templine .= $line;
// If it has a semicolon at the end, it's the end of the query
if (substr(trim($line), -1, 1) == ';') {
mysqli_query($con,$templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysqli_error($con) . '<br>');
$templine = '';
}
}

unlink("installer.php");
unlink("sql.txt");

echo '<b>The installation has been completed.</b>';

echo "<br>";
echo "<br>";

echo '<b><font color="red">Please delete installer.php and sql.txt from your server/cPanel FTP.</font></b>';

echo "<br>";
echo "<br>";

echo 'You can login into the <b><a href="admin">Admin Panel</a></b> by following details:';

echo "<br>";
echo "<br>";

echo '<b>User: </b>admin';
echo "<br>";
echo '<b>Password: </b>password';
	
} else {
	
echo 'I could easily connect to the database server. Do you wish to continue? <b><a href="?override=1">Yes, let\'s install!</b></b>';
	
	
}
	
}

mysqli_close($con);

?>