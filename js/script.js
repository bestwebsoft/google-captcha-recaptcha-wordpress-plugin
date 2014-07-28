(function( $ ) {
	var gglcptch_match = true;
	$( document ).ready(function() {
		$( '#recaptcha_widget_div' ).parent().children( 'input:submit' ).click(function() {
			click_trigger();
			return gglcptch_match;
		});
		$( '#cntctfrm_contact_form, #cntctfrmpr_contact_form' ).find( 'input:submit' ).click(function() {
			click_trigger();
			return gglcptch_match;
		});
	});

	function click_trigger() {
		var req = getXmpHttp();
		/* fields for checking Google Captcha */
		var recaptcha_challenge_field = $( '#recaptcha_challenge_field' ).val();
		var recaptcha_response_field = $( '#recaptcha_response_field' ).val();
		/* opening asynchronous connection */
		req.open( 'POST', gglcptch_path, false );
		req.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
		/* sending POST parameters */
		req.send( 'recaptcha_challenge_field=' + recaptcha_challenge_field + '&recaptcha_response_field=' + recaptcha_response_field + '&gglcptch_private_key=' + gglcptch_private_key );
		
		if ( req.responseText == 'error' ) {
			/* wrong captcha */
			if ( ! $( '#gglcptch_error' ).text() ) {
				$( '#recaptcha_widget_div' ).after( '<label id="gglcptch_error" style="color:#f00;">' + gglcptch_error_msg + '</label>' );
				gglcptch_match = false;
			}
			$('#recaptcha_reload').click();
		} else {
			/* correct catcha */
			gglcptch_match = true;
		}
	}

	/* Creating xmlhttp object */
	function getXmpHttp() {
		var xmlhttp;
		try {
			xmlhttp = new ActiveXObject( 'Msxml2.XMLHTTP' );
		} catch ( e ) {
			try {
				xmlhttp = new ActiveXObject( 'Microsoft.XMLHTTP' );
			} catch ( E ) {
				xmlhttp = false;
			}
		}
		if ( ! xmlhttp && typeof XMLHttpRequest != 'undefined' ) {
			xmlhttp = new XMLHttpRequest();
		}
		return xmlhttp;
	}
})(jQuery);
