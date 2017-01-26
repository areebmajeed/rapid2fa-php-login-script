<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title><?php echo $website_name; ?> | <?php echo @$pageName; ?></title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<link rel="stylesheet" href="assets/css/style.css">
<link href="https://fonts.googleapis.com/css?family=Questrial|Roboto+Condensed|Stalemate" rel="stylesheet">
</head>

<body>

<div class="navbar navbar-default navbar-fixed-top">
<div class="container">
<div class="navbar-header">
<a href="index" class="navbar-brand"><b><?php echo $website_name; ?></b></a>
<button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
</div>

<div class="navbar-collapse collapse" id="navbar-main">
<ul class="nav navbar-nav">
<li><a href="sample-page"><i class="glyphicon glyphicon-th-list"></i> &nbsp; Sample Page</a></li>
<?php if(checkAdmin() == true){?><li><a href="admin"><i class="glyphicon glyphicon-eye-open"></i> &nbsp; Admin</a></li><?php } ?>
</ul>

<ul class="nav navbar-nav navbar-right">

<?php if(UserLoggedIn() == true) { ?>
<li><a href="edit-profile"><i class="glyphicon glyphicon-wrench"></i> &nbsp; Edit Profile</a></li>
<li><a href="?logout"><i class="glyphicon glyphicon-off"></i> &nbsp; Logout</a></li>
<?php

} else {
	
?>

<li><a href="login"><i class="glyphicon glyphicon-user"></i> &nbsp; Login</a></li>
<li><a href="register"><i class="glyphicon glyphicon-user"></i> &nbsp; Register</a></li>

<?php	

}

?>

</ul>

</div>
</div>
</div>

<div id="wrapper">

<?php

if(empty($core_system_messages) == false) {
echo '<div class="container">';
foreach($core_system_messages as $message) {
echo '<div class="alert alert-dismissable alert-info">' . $message . '</div>';
}
echo '</div>';

}

?>