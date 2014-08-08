<?php
/* Check Google Captcha in shortcode and contact form */
require_once( 'lib/recaptchalib.php' );
if ( defined('ABSPATH') )
    require_once( ABSPATH . 'wp-load.php' );
else
    require_once( '../../../wp-load.php' );
$gglcptch_options = get_option( 'gglcptch_options' );
$privatekey = $gglcptch_options['private_key'];
$resp = recaptcha_check_answer( $privatekey,
										$_SERVER['REMOTE_ADDR'],
										$_POST['recaptcha_challenge_field'],
										$_POST['recaptcha_response_field'] );
if ( ! $resp->is_valid )
	echo "error";
?>