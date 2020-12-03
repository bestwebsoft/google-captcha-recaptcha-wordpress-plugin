( function( $ ) {
	$( document ).ready( function() {

        $( '#gglcptch_score_v3' ).change( function() {
            var score;
            score = $( this ).val();
            $( '#gglcptch_score_out_v3' ).text( score );
        } ).trigger( 'change' );

		$( 'input[name="gglcptch_recaptcha_version"]' ).change( function() {
			var versions = $( 'input[name="gglcptch_recaptcha_version"]' );
			versions.each( function() {
				if ( $( this ).is( ':checked' ) ) {
					$( '.gglcptch_theme_' + $( this ).val() ).show();
                    $( '.gglcptch_score_' + $( this ).val() ).show();
                    $( '.gglcptch_badge_v3' ).show();
					$( '.gglcptch_submit_' + $( this ).val() ).show();
				} else {
					$( '.gglcptch_theme_' + $( this ).val() ).hide();
                    $( '.gglcptch_score_' + $( this ).val() ).hide();
					$( '.gglcptch_badge_' + $( this ).val() ).hide();
					$( '.gglcptch_submit_' + $( this ).val() ).hide();
				}
			} );
		} ).trigger( 'change' );

		$( 'input[name="gglcptch_private_key"], input[name="gglcptch_public_key"]' ).change( function() {
			$( '.gglcptch_verified, #gglcptch-test-keys, #gglcptch-test-block' ).hide();
		} );

		$( '.gglcptch-settings-accordion' ).accordion(
			{
				collapsible: true,
				heightStyle: "content"
			}
		);

		/* Prevent jQuery accordion collapsing on link click */
		$( ".gglcptch-settings-accordion a" ).on( "click", function( event ) {
			event.stopPropagation();
		} );

		/**
		 * Handle the "Allow list" tab on the plugins option page
		 */
		$( 'button[name="gglcptch_show_allowlist_form"]' ).click( function() {
			$( this ).closest( 'form' ).hide();
			$( '.gglcptch_allowlist_form' ).show();
			return false;
		} );

		/*  add my ip to the allowlist */
		$( 'input[name="gglcptch_add_to_allowlist_my_ip"]' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				var my_ip = $( 'input[name="gglcptch_add_to_allowlist_my_ip_value"]' ).val();
				$( 'input[name="gglcptch_add_to_allowlist"]' ).val( my_ip ).attr( 'readonly', 'readonly' );
			} else {
				$( 'input[name="gglcptch_add_to_allowlist"]' ).val( '' ).removeAttr( 'readonly' );
			}
		} );
	} );

	$( document ).on( 'click', '#gglcptch-test-keys a', function( e ) {
		e.preventDefault();

		if ( ! $( '#gglcptch-test-block' ).length ) {
			$( '#gglcptch-test-keys' ).after( '<div id="gglcptch-test-block"></div>' );
		}

		$( '.gglcptch-test-results' ).remove();
		$( '#gglcptch-test-block' ).load( $( this ).prop( 'href' ), function() {
			$( '.gglcptch_v2, .gglcptch_invisible' ).each( function() {
				var container = $( this ).find( '.gglcptch_recaptcha' ).attr( 'id' );
				if ( $( this ).is( ':visible' ) ) {
					gglcptch.display( container );
					if ( $( this ).hasClass( 'gglcptch_invisible' ) ) {
						var gglcptch_index = $( this ).find( '.gglcptch_recaptcha' ).data( 'gglcptch_index' );
						grecaptcha.execute( gglcptch_index );
					}
				}
			} );
		} );

		e.stopPropagation();
		$( '#gglcptch-test-keys' ).hide();
        setTimeout( function(){
            if( $( '#gglcptch_test_keys_verification' ).length > 0 ) {
                $( '#gglcptch_test_keys_verification' ).removeAttr('disabled');
	            $( '#gglcptch_test_keys_verification' ).removeClass( 'cptch_loading' );
                $( '.bws_form input[type="submit"]' ).removeAttr( 'disabled' );
            }
        }, 8000 );
		return false;
	} );

	$( document ).on( 'click', '#gglcptch_test_keys_verification', function( e ) {
		e.preventDefault();
		$.ajax( {
			async   : false,
			cache   : false,
			type    : 'POST',
			url     : ajaxurl,
			headers : {
				'Content-Type' : 'application/x-www-form-urlencoded'
			},
			data    : {
				action: 'gglcptch_test_keys_verification',
				recaptcha_challenge_field : $( '#recaptcha_challenge_field' ).val(),
				recaptcha_response_field  : $( '#recaptcha_response_field' ).val(),
				'g-recaptcha-response'  : $( '.g-recaptcha-response' ).val(),
				_wpnonce : $( '[name="gglcptch_test_keys_verification-nonce"]' ).val()
			},
			success: function( data ) {
				$( '#gglcptch-test-block' ).after( data );
				$( '#gglcptch-test-block' ).html( '' );
				if ( $( '.gglcptch-test-results' ).hasClass( 'updated' ) ) {
					$( '.gglcptch_verified' ).show();
				} else {
					$( '.gglcptch_verified' ).hide();
					if (
						'v2' == $( 'input[name="gglcptch_recaptcha_version"]:checked' ).val() ||
						'invisible' == $( 'input[name="gglcptch_recaptcha_version"]:checked' ).val()
					) {
						$( '#gglcptch-test-keys' ).show();
					}
				}
			}
		} );

		e.stopPropagation();
		return false;
	} );
} )( jQuery );
