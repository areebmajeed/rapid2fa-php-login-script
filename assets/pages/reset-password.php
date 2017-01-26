<section id="form-page">
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3 well" id="duh">

<div align="center">
<h2><b>Reset your account password</b></h2>
</div>

<br>
<br>

<?php

if(isset($_GET['reset_user']) && isset($_GET['code']) && $status_code == TRUE) {
	
?>
	
<form action="reset-password?reset_user=<?php echo $user_id; ?>&code=<?php echo $reset_hash; ?>" method="post">

<div class="form-group">
<label class="control-label" for="pwd">New Password</label>
<input type="password" name="password" class="form-control" id="pwd" placeholder="New password please">
</div>	
	
<div class="form-group">
<label class="control-label" for="pwd">New Password (Repeat)</label>
<input type="password" name="password_repeat" class="form-control" id="pwd" placeholder="Repeat it">
</div>
	
<input type="submit" name="change_reset_password" value="Reset" placeholder="Submit" class="btn btn-primary">

</form>
	
<?php
	
} else {
	
?>

<form action="reset-password" method="post">

<div class="form-group">
<label class="control-label" for="username">What's your username?</label>
<input type="text" name="username" class="form-control" id="username" placeholder="What's your username?">
</div>

<?php

if(settings("googleReCaptcha") == 1) {

echo "<script>
var onloadCallback = function() {
grecaptcha.render('captcha', {
'sitekey' : '" . settings("googleRecaptcha_PUBLICkey") . "',
'hl' : 'en-GB'
});
};
</script>
	
<script src='https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit' async defer></script>";

echo '<div id="captcha"></div>';
	
}

?>

<input type="submit" name="reset_password" value="Reset" placeholder="Submit" class="btn btn-primary">

</form>

<?php

}

?>

</div>
</div>
</div>
</section>
