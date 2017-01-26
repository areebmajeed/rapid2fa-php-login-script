<section id="form-page">

<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3 well" id="duh">

<div align="center">
<h2><b>Register an account</b></h2>
</div>

<br>
<br>

<form action="register" method="post">

<div class="form-group">
<label class="control-label" for="username">Account Username</label>
<input type="text" name="username" class="form-control" id="username" placeholder="What's your username?">
</div>

<div class="form-group">
<label class="control-label" for="email">Account Email</label>
<input type="text" name="email" class="form-control" id="email" placeholder="What's your email?">
</div>

<div class="form-group">
<label class="control-label" for="password">Account Password</label>
<input type="password" name="password" class="form-control" id="password" placeholder="What's your password?">
</div>

<div class="form-group">
<label class="control-label" for="password_repeat">Account Password (Repeat)</label>
<input type="password" name="password_repeat" class="form-control" id="password_repeat" placeholder="Please repeat what you wrote above.">
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

<input type="submit" name="register" value="Register" placeholder="Submit" class="btn btn-primary">

<br>
<br>

Already a user? Login to your <b><a href="login">account</a></b>.

</form>

</div>
</div>
</div>
</section>