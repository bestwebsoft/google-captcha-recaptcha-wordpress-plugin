(function( $, gglcptch ) {
	gglcptch = gglcptch || {};

	gglcptch.prepare = function() {
		$( '.gglcptch_v1, .gglcptch_v2' ).each( function() {
			var container = $( this ).find( '.gglcptch_recaptcha' ).attr( 'id' );
			gglcptch.display( container );
		});
	};

	gglcptch.display = function( container, v1_add_to_last_element ) {
		if ( typeof( container ) == 'undefined' || container == '' || typeof( gglcptch.options ) == 'undefined' ) {
			return;
		}

		var gglcptch_version = gglcptch.options.version;
		v1_add_to_last_element = v1_add_to_last_element || false;

		if ( gglcptch_version == 'v1' ) {
			if ( Recaptcha.widget == null || v1_add_to_last_element == true ) {
				Recaptcha.create( gglcptch.options.sitekey, container, { 'theme' : gglcptch.options.theme } );
			}
		}

		if ( gglcptch_version == 'v2' ) {
			var gglcptch_index = grecaptcha.render( container, { 'sitekey' : gglcptch.options.sitekey, 'theme' : gglcptch.options.theme } );
			$( '#' + container ).data( 'gglcptch_index', gglcptch_index );
		}
	};

	window.onload = gglcptch.prepare;

	$( document ).ready(function() {

		if ( parseFloat( $.fn.jquery ) >= 1.7 ) {
			$( '#recaptcha_widget_div' ).on( 'input paste change', '#recaptcha_response_field', cleanError );
		} else {
			$( '#recaptcha_widget_div #recaptcha_response_field' ).live( 'input paste change', cleanError );
		}

		$( 'form' ).not( '[name="loginform"], [name="registerform"], [name="lostpasswordform"], #setupform' ).submit( function( e ) {
			var $form = $( this ),
				$gglcptch = $form.find( '.gglcptch' ),
				$captcha = $gglcptch.filter( '.gglcptch_v1' ).find( '.gglcptch_recaptcha:visible' ),
				$captcha_v2 = $gglcptch.filter( '.gglcptch_v2' ).find( '.gglcptch_recaptcha:visible' );
			if ( $captcha.length ) {
				if ( $gglcptch.find( 'input[name="gglcptch_test_enable_js_field"]:hidden' ).length == 0 ) {
					$gglcptch.append( '<input type="hidden" value="' + gglcptch.vars.nonce + '" name="gglcptch_test_enable_js_field" />' );
				}
				$.ajax({
					async   : false,
					cache   : false,
					type    : 'POST',
					url     : gglcptch.vars.ajaxurl,
					headers : {
						'Content-Type' : 'application/x-www-form-urlencoded'
					},
					data    : {
						action: 'gglcptch_captcha_check',
						recaptcha_challenge_field : $( '#recaptcha_challenge_field' ).val(),
						recaptcha_response_field  : $( '#recaptcha_response_field' ).val()
					},
					success: function( data ) {
						if ( data == 'error' ) {
							if ( $captcha.next( '#gglcptch_error' ).length == 0 ) {
								$captcha.after( '<label id="gglcptch_error">' + gglcptch.vars.error_msg + '</label>' );
							}
							$( '#recaptcha_reload' ).trigger( 'click' );
							e.preventDefault ? e.preventDefault() : (e.returnValue = false);
							return false;
						}
					},
					error: function( request, status, error ) {
						if ( $captcha.next( '#gglcptch_error' ).length == 0 ) {
							$captcha.after( '<label id="gglcptch_error">' + request.status + ' ' + error + '</label>' );
						}
						$( '#recaptcha_reload' ).trigger( 'click' );
						e.preventDefault ? e.preventDefault() : (e.returnValue = false);
						return false;
					}
				});
				$( '#recaptcha_reload' ).trigger( 'click' );
			} else if ( $captcha_v2.length ) {
				if ( $gglcptch.find( 'input[name="gglcptch_test_enable_js_field"]:hidden' ).length == 0 ) {
					$gglcptch.append( '<input type="hidden" value="' + gglcptch.vars.nonce + '" name="gglcptch_test_enable_js_field" />' );
				}
				$.ajax({
					async   : false,
					cache   : false,
					type    : 'POST',
					url     : gglcptch.vars.ajaxurl,
					headers : {
						'Content-Type' : 'application/x-www-form-urlencoded'
					},
					data    : {
						action: 'gglcptch_captcha_check',
						'g-recaptcha-response'  : $form.find( '.g-recaptcha-response' ).val()
					},
					success: function( data ) {
						if ( data == 'error' ) {
							if ( $captcha_v2.next( '#gglcptch_error' ).length == 0 ) {
								$captcha_v2.after( '<label id="gglcptch_error">' + gglcptch.vars.error_msg + '</label>' );
								$( "#gglcptch_error" ).fadeOut( 4000, function() {
									$( "#gglcptch_error" ).remove();
								});
								$( 'html, body' ).animate({ scrollTop: $captcha_v2.offset().top - 50 }, 500);
							}
							e.preventDefault ? e.preventDefault() : (e.returnValue = false);
							return false;
						}
					},
					error: function( request, status, error ) {
						if ( $captcha_v2.next( '#gglcptch_error' ).length == 0 ) {
							$captcha_v2.after( '<label id="gglcptch_error">' + request.status + ' ' + error + '</label>' );
						}
						e.preventDefault ? e.preventDefault() : (e.returnValue = false);
						return false;
					}
				});
			}
		});
	});

	function cleanError() {
		$error = $( this ).parents( '#recaptcha_widget_div' ).next( '#gglcptch_error' );
		if ( $error.length ) {
			$error.remove();
		}
	}

})(jQuery, gglcptch);