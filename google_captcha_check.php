<?php
/* Check Google Captcha in shortcode and contact form */
require_once( 'lib/recaptchalib.php' );
$privatekey = $_POST['gglcptch_private_key'];
$resp = recaptcha_check_answer( $privatekey,
										$_SERVER['REMOTE_ADDR'],
										$_POST['recaptcha_challenge_field'],
										$_POST['recaptcha_response_field'] );
if ( ! $resp->is_valid )
	echo "error";
?>