(function( $ ) {
	$( document ).ready(function() {
		$( '#recaptcha_widget_div #recaptcha_response_field' ).live( 'input paste change', function() {
			$error = $( this ).parents( '#recaptcha_widget_div' ).next( '#gglcptch_error' );
			if( $error.length ) {
				$error.remove();
			}
		});
		$( 'form' ).submit(function( e ) {
			var $form = $( this ),
				$captcha = $form.find( '#recaptcha_widget_div:visible' );
			if ( $captcha.length ) {
				$.ajax({
					async   : false,
					cache   : false,
					type    : 'POST',
					url     : gglcptch_path,
					headers : {
						'Content-Type' : 'application/x-www-form-urlencoded'
					},
					data    : {
						recaptcha_challenge_field : $( '#recaptcha_challenge_field' ).val(),
						recaptcha_response_field  : $( '#recaptcha_response_field' ).val()
					},
					success : function( data ) {
						if ( data == 'error' ) {
							if ( $captcha.next( '#gglcptch_error' ).length == 0 ) {
								$captcha.after( '<label id="gglcptch_error">' + gglcptch_error_msg + '</label>' );
							}
							$( '#recaptcha_reload' ).trigger( 'click' );
							e.preventDefault ? e.preventDefault() : (e.returnValue = false);
							return false;
						}
					}
				});
				$( '#recaptcha_reload' ).trigger( 'click' );
			}
		});
	});
})(jQuery);