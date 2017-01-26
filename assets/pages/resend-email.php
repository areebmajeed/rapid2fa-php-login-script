<section id="form-page">
<div class="container">
<div class="row">
<div class="col-md-6 col-md-offset-3 well" id="duh">

<div align="center">
<h2><b>Confirm your account email</b></h2>
</div>

<br>
<br>

<form action="resend-email" method="post">

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

<input type="submit" name="resend_email" value="Resend" placeholder="Submit" class="btn btn-primary">

</form>

</div>
</div>
</div>
</section>