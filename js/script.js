( function( $, gglcptch ) {
	gglcptch = gglcptch || {};

	gglcptch.prepare = function() {
		/*
		 * display reCaptcha for plugin`s block
		 */
		$( '.gglcptch_v1, .gglcptch_v2, .gglcptch_invisible' ).each( function() {
			var container = $( this ).find( '.gglcptch_recaptcha' );
			if (
				container.is( ':empty' ) &&
				( gglcptch.vars.visibility || $( this ).is( ':visible' ) === $( this ).is( ':not(:hidden)' ) )
			) {
				var containerId = container.attr( 'id' );
				gglcptch.display( containerId );
			}
		} );

		/*
		 * display reCaptcha for others blocks
		 * this part is neccessary because
		 * we have disabled the connection to Google reCaptcha API from other plugins
		 * via plugin`s php-functionality
		 */
		if ( 'v2' == gglcptch.options.version || 'invisible' == gglcptch.options.version ) {
			$( '.g-recaptcha' ).each( function() {
				/* reCAPTCHA will be generated into the empty block only */
				if ( $( this ).html() === '' && $( this ).text() === '' ) {

					/* get element`s ID */
					var container = $( this ).attr( 'id' );

					if ( typeof container == 'undefined' ) {
						container = get_id();
						$( this ).attr( 'id', container );
					}

					/* get reCapatcha parameters */
					var sitekey  = $( this ).attr( 'data-sitekey' ),
						theme    = $( this ).attr( 'data-theme' ),
						lang     = $( this ).attr( 'data-lang' ),
						size     = $( this ).attr( 'data-size' ),
						type     = $( this ).attr( 'data-type' ),
						tabindex = $( this ).attr( 'data-tabindex' ),
						callback = $( this ).attr( 'data-callback' ),
						ex_call  = $( this ).attr( 'data-expired-callback' ),
						stoken   = $( this ).attr( 'data-stoken' ),
						params   = [];

					params['sitekey'] = sitekey ? sitekey : gglcptch.options.sitekey;
					if ( !! theme ) {
						params['theme'] = theme;
					}
					if ( !! lang ) {
						params['lang'] = lang;
					}
					if ( !! size ) {
						params['size'] = size;
					}
					if ( !! type ) {
						params['type'] = type;
					}
					if ( !! tabindex ) {
						params['tabindex'] = tabindex;
					}
					if ( !! callback ) {
						params['callback'] = callback;
					}
					if ( !! ex_call ) {
						params['expired-callback'] = ex_call;
					}
					if ( !! stoken ) {
						params['stoken'] = stoken;
					}

					gglcptch.display( container, false, params );
				}
			} );

			/*
			 * count the number of reCAPTCHA blocks in the form
			 */
			$( 'form' ).each( function() {
				if ( $( this ).contents().find( 'iframe[title="recaptcha widget"]' ).length > 1 && ! $( this ).children( '.gglcptch_dublicate_error' ).length ) {
					$( this ).prepend( '<div class="gglcptch_dublicate_error error" style="color: red;">'+ gglcptch.options.error + '</div><br />\n' );
				}
			} );
		}
	};

	gglcptch.display = function( container, v1_add_to_last_element, params ) {
		if ( typeof( container ) == 'undefined' || container == '' || typeof( gglcptch.options ) == 'undefined' ) {
			return;
		}

		function storeEvents( el ) {
			var target = el,
				events = $._data( el.get(0), 'events' );
			/* restoring events */
			if ( typeof events != 'undefined' ) {
				var storedEvents = {};
				$.extend( true, storedEvents, events );
				target.off();
				target.data('storedEvents', storedEvents );
			}
		}

		function restoreEvents( el ) {
			var target = el,
				events = target.data('storedEvents');
			/* restoring events */
			if ( typeof events != 'undefined' ) {
				for ( var event in events ) {
					for ( var i = 0; i < events[event].length; i++ ) {
						target.on( event, events[event][i] );
					}
				}
			}
			/* reset stored events */
			target.removeData( 'storedEvents' );
		}

		function storeOnSubmit( form, gglcptch_index ) {
			form.on( 'submit', function( e ) {
				if ( '' == form.find('.g-recaptcha-response').val() ) {
					e.preventDefault();
					e.stopImmediatePropagation();
					targetObject = $( e.target || e.srcElement || e.targetObject );
					targetEvent = e.type;
					grecaptcha.execute( gglcptch_index );
				}
			} ).find( 'input:submit, button' ).on( 'click', function( e ) {
				if ( '' == form.find('.g-recaptcha-response').val() ) {
					e.preventDefault();
					e.stopImmediatePropagation();
					targetObject = $( e.target || e.srcElement || e.targetObject );
					targetEvent = e.type;
					grecaptcha.execute( gglcptch_index );
				}
			} );
		}

		var gglcptch_version = gglcptch.options.version;
		v1_add_to_last_element = v1_add_to_last_element || false;

		if ( 'v1' == gglcptch_version ) {
			if ( Recaptcha.widget == null || v1_add_to_last_element == true ) {
				Recaptcha.create( gglcptch.options.sitekey, container, { 'theme' : gglcptch.options.theme } );
			}
		}

		if ( 'v2' == gglcptch_version ) {
			var parameters = params ? params : { 'sitekey' : gglcptch.options.sitekey, 'theme' : gglcptch.options.theme },
				gglcptch_index = grecaptcha.render( container, parameters );
			$( '#' + container ).data( 'gglcptch_index', gglcptch_index );
		}

		if ( 'invisible' == gglcptch_version ) {
			var block = $( '#' + container ),
				form = block.closest( 'form' ),
				parameters = params ? params : { 'sitekey' : gglcptch.options.sitekey, 'size' : 'invisible' },
				targetObject = false,
				targetEvent = false;

			if ( form.length ) {
				storeEvents( form );
				form.find( 'button, input:submit' ).each( function() {
					storeEvents( $( this ) );
				} );

				/* Callback function works only in frontend */
				if ( 'gglcptch_admin_settings_page' != form.attr( 'id' ) ) {
					parameters['callback'] = function( token ) {
						form.off();
						restoreEvents( form );
						form.find( 'button, input:submit' ).off().each( function() {
							restoreEvents( $( this ) );
						} );
						if ( targetObject && targetEvent ) {
							targetObject.trigger( targetEvent );
						}
						form.find( 'button, input:submit' ).each( function() {
							storeEvents( $( this ) );
						} );
						storeEvents( form );
						storeOnSubmit( form, gglcptch_index );
						grecaptcha.reset( gglcptch_index );
					};
				}

				var gglcptch_index = grecaptcha.render( container, parameters );
				block.data( { 'gglcptch_index' : gglcptch_index } );

				if ( 'gglcptch_admin_settings_page' != form.attr( 'id' ) ) {
					storeOnSubmit( form, gglcptch_index );
				}
			}
		}
	};

	$( document ).ready( function() {
		var tryCounter = 0,
			/* launching timer so that the function keeps trying to display the reCAPTCHA again and again until google js api is loaded */
			gglcptch_timer = setInterval( function() {
				if ( typeof Recaptcha != "undefined" || typeof grecaptcha != "undefined" ) {
					try {
						gglcptch.prepare();
					} catch ( e ) {
						console.log( 'Unexpected error occurred: ', e );
					}
					clearInterval( gglcptch_timer );
				}
				tryCounter++;
				/* Stop trying after 10 times */
				if ( tryCounter >= 10 ) {
					clearInterval( gglcptch_timer );
				}
			}, 1000 );

		function gglcptch_prepare() {
			if ( typeof Recaptcha != "undefined" || typeof grecaptcha != "undefined" ) {
				try {
					gglcptch.prepare();
				} catch ( err ) {
					console.log( err );
				}
			}
		}

		$( window ).on( 'load', gglcptch_prepare );

		$( '.woocommerce' ).on( 'click', '.woocommerce-tabs', gglcptch_prepare );

		$( '#recaptcha_widget_div' ).on( 'input paste change', '#recaptcha_response_field', cleanError );

		$( 'form' ).not( '[name="loginform"], [name="registerform"], [name="lostpasswordform"], #setupform, .cntctfrmpr_contact_form, .cntctfrm_contact_form, #commentform, #gglcptch_admin_settings_page, #crrntl-user-info' + gglcptch.vars.excluded_forms ).submit( function( e ) {
			var $form = $( this ),
				$gglcptch = $form.find( '.gglcptch' ),
				$captcha = $gglcptch.filter( '.gglcptch_v1' ).find( '.gglcptch_recaptcha:visible' ),
				$captcha_v2 = $gglcptch.filter( '.gglcptch_v2' ).find( '.gglcptch_recaptcha:visible' );
				$captcha_invisible = $gglcptch.filter( '.gglcptch_invisible' ).find( '.gglcptch_recaptcha:visible' );
			if ( $captcha.length ) {
				if ( $gglcptch.find( 'input[name="gglcptch_test_enable_js_field"]:hidden' ).length == 0 ) {
					$gglcptch.append( '<input type="hidden" value="' + gglcptch.vars.nonce + '" name="gglcptch_test_enable_js_field" />' );
				}
				$.ajax( {
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
						e.preventDefault ? e.preventDefault() : ( e.returnValue = false );
						return false;
					}
				} );
				$( '#recaptcha_reload' ).trigger( 'click' );
			} else if ( $captcha_v2.length ) {
				if ( $gglcptch.find( 'input[name="gglcptch_test_enable_js_field"]:hidden' ).length == 0 ) {
					$gglcptch.append( '<input type="hidden" value="' + gglcptch.vars.nonce + '" name="gglcptch_test_enable_js_field" />' );
				}
				$.ajax( {
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
								$( "#gglcptch_error" ).fadeOut( 15000, function() {
									$( "#gglcptch_error" ).remove();
								} );
								$( 'html, body' ).animate( { scrollTop: $captcha_v2.offset().top - 50 }, 500);
							}
							grecaptcha.reset( $captcha_v2.data( 'gglcptch_index' ) );
							e.preventDefault ? e.preventDefault() : ( e.returnValue = false );
							return false;
						}
					},
					error: function( request, status, error ) {
						if ( $captcha_v2.next( '#gglcptch_error' ).length == 0 ) {
							$captcha_v2.after( '<label id="gglcptch_error">' + request.status + ' ' + error + '</label>' );
						}
						grecaptcha.reset( $captcha_v2.data( 'gglcptch_index' ) );
						e.preventDefault ? e.preventDefault() : ( e.returnValue = false );
						return false;
					}
				} );
			} else if ( $captcha_invisible.length ) {
				if ( $gglcptch.find( 'input[name="gglcptch_test_enable_js_field"]:hidden' ).length == 0 ) {
					$gglcptch.append( '<input type="hidden" value="' + gglcptch.vars.nonce + '" name="gglcptch_test_enable_js_field" />' );
				}
				$.ajax( {
					async	: false,
					cache	: false,
					type	: 'POST',
					url		: gglcptch.vars.ajaxurl,
					headers	: {
						'Content-Type' : 'application/x-www-form-urlencoded'
					},
					data	: {
						action: 'gglcptch_captcha_check',
						'g-recaptcha-response'  : $form.find( '.g-recaptcha-response' ).val()
					},
					success: function( data ) {
						if ( data == 'error' ) {
							if ( $captcha_invisible.next( '#gglcptch_error' ).length == 0 ) {
								$captcha_invisible.after( '<label id="gglcptch_error">' + gglcptch.vars.error_msg + '</label>' );
								$( "#gglcptch_error" ).fadeOut( 15000, function() {
									$( "#gglcptch_error" ).remove();
								} );
								$( 'html, body' ).animate( { scrollTop: $captcha_invisible.offset().top - 50 }, 500);
							}
							grecaptcha.reset( $captcha_invisible.data( 'gglcptch_index' ) );
							e.preventDefault ? e.preventDefault() : ( e.returnValue = false );
							return false;
						}
					},
					error: function( request, status, error ) {
						if ( $captcha_invisible.next( '#gglcptch_error' ).length == 0 ) {
							$captcha_invisible.after( '<label id="gglcptch_error">' + request.status + ' ' + error + '</label>' );
						}
						grecaptcha.reset( $captcha_invisible.data( 'gglcptch_index' ) );
						e.preventDefault ? e.preventDefault() : ( e.returnValue = false );
						return false;
					}
				} );
			}
		} );
	} );

	function cleanError() {
		$error = $( this ).parents( '#recaptcha_widget_div' ).next( '#gglcptch_error' );
		if ( $error.length ) {
			$error.remove();
		}
	}

	function get_id() {
		var id = 'gglcptch_recaptcha_' + Math.floor( Math.random() * 1000 );
		if ( $( '#' + id ).length ) {
			id = get_id();
		} else {
			return id;
		}
	}
} )( jQuery, gglcptch );