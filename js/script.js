( function( $, gglcptch ) {
	gglcptch = gglcptch || {};

	gglcptch.prepare = function() {
		/*
		 * display reCaptcha for plugin`s block
		 */
		$( '.gglcptch_v2, .gglcptch_invisible' ).each( function() {

			var container = $( this ).find( '.gglcptch_recaptcha' );

			if (
				container.is( ':empty' ) &&
				( gglcptch.vars.visibility || $( this ).is( ':visible' ) === $( this ).is( ':not(:hidden)' ) )
			) {
				var containerId = container.attr( 'id' );
				gglcptch.display( containerId );
			}
		} );

		if ( 'v3' == gglcptch.options.version ) {
			grecaptcha.ready( function() {
				grecaptcha.execute( gglcptch.options.sitekey, {action: 'BWS_reCaptcha'}).then(function( token ) {
					document.querySelectorAll( "#g-recaptcha-response" ).forEach( function ( elem ) { elem.value = token } );
					if ( ! $( 'body' ).hasClass( 'wp-admin' ) ) {
						var gglcptchTimeout = setTimeout( function(){
							$( '.gglcptch_error_text' ).each( function(){
								$( this ).show();
								clearTimeout( gglcptchTimeout );
							});
						}, 180000 );
					}
				});
			});
		}

		/*
		 * display reCaptcha for others blocks
		 * this part is neccessary because
		 * we have disabled the connection to Google reCaptcha API from other plugins
		 * via plugin`s php-functionality
		 */
		if ( 'v2' == gglcptch.options.version || 'invisible' == gglcptch.options.version ) {
			/*
			 * count the number of reCAPTCHA blocks in the form
			 */
			$( 'form' ).each( function() {
				if ( $( this ).contents().find( 'iframe[title="recaptcha widget"]' ).length > 1 && ! $( this ).children( '.gglcptch_dublicate_error' ).length ) {
					$( this ).prepend( '<div class="gglcptch_dublicate_error error" style="color: red;">' + gglcptch.options.error + '</div><br />\n' );
				}
			} );
		}
	};

	gglcptch.display = function( container, params ) {
		if ( typeof( container ) == 'undefined' || container == '' || typeof( gglcptch.options ) == 'undefined' ) {
			return;
		}

		if ( 'v2' === gglcptch.options.version && $( '#' + container ).parents( '.gglcptch_custom' ).length > 0 ) {
			if ( $( '#' + container ).find( 'textarea#g-recaptcha-response' ).length == 0 ) {
				setTimeout(
					function(){
						$( '#' + container ).find( 'textarea#g-recaptcha-response' ).remove();
					},
					1000
				);
			} else {
				$( '#' + container ).find( 'textarea#g-recaptcha-response' ).remove();
			}
			$( '.gglcptch.gglcptch_v2 noscript' ).remove();
		}
		
		// add attribute disable to the submit
		if ( 'v2' === gglcptch.options.version && gglcptch.options.disable ) {
			$( '#' + container ).closest( 'form' ).find( 'input:submit, button' ).prop( 'disabled', true );
			if ( 'setupform' === $( '#' + container ).closest( 'form' ).attr( 'id' ) ) {
				setTimeout(
					function(){
						$( '#' + container ).closest( 'form' ).find( 'input:submit, button' ).prop( 'disabled', true );
					},
					200
				);
			}
		}

		function storeEvents( el ) {
			var target = el,
				events = $._data( el.get( 0 ), 'events' );
			/* restoring events */
			if ( typeof events != 'undefined' ) {
				var storedEvents = {};
				$.extend( true, storedEvents, events );
				target.off();
				target.data( 'storedEvents', storedEvents );
			}
			/* storing and removing onclick action */
			if ( 'undefined' != typeof target.attr( 'onclick' ) ) {
				target.attr( 'gglcptch-onclick', target.attr( 'onclick') );
				target.removeAttr( 'onclick' );
			}
		}

		function restoreEvents( el ) {
			var target = el,
				events = target.data( 'storedEvents' );
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
			/* restoring onclick action */
			if ( 'undefined' != typeof target.attr( 'gglcptch-onclick' ) ) {
				target.attr( 'onclick', target.attr( 'gglcptch-onclick' ) );
				target.removeAttr( 'gglcptch-onclick' );
			}
		}

		function storeOnSubmit( form, gglcptch_index ) {
			form.on( 'submit', function( e ) {
				if ( '' == form.find( '.g-recaptcha-response' ).val() ) {
					e.preventDefault();
					e.stopImmediatePropagation();
					targetObject = $( e.target || e.srcElement || e.targetObject );
					targetEvent = e.type;
					grecaptcha.execute( gglcptch_index );
				}
			} ).find( 'input:submit, button' ).on( 'click', function( e ) {
				if ( '' == form.find( '.g-recaptcha-response' ).val() ) {
					e.preventDefault();
					e.stopImmediatePropagation();
					targetObject = $( e.target || e.srcElement || e.targetObject );
					targetEvent = e.type;
					grecaptcha.execute( gglcptch_index );
				}
			} );
		}

		var gglcptch_version = gglcptch.options.version;
		
		if ( 'v2' == gglcptch_version ) {
			if ( $( '#' + container ).parent().width() <= 300 && $( '#' + container ).parent().width() != 0 || $( window ).width() < 400 ) {
				var size = 'compact';
			} else {
				var size = 'normal';
			}
			var parameters = params ? params : { 'sitekey' : gglcptch.options.sitekey, 'theme' : gglcptch.options.theme, 'size' : size },
				block = $( '#' + container ),
				form = block.closest( 'form' );

				/* Callback function works only in frontend */
				if ( ! $( 'body' ).hasClass( 'wp-admin' ) ) {
					parameters['callback'] = function() {
						form.find( 'button, input:submit' ).prop( 'disabled', false );
					};
				}

			var gglcptch_index = grecaptcha.render( container, parameters );
			$( '#' + container ).data( 'gglcptch_index', gglcptch_index );
		} else if ( 'invisible' == gglcptch_version ) {
			var block = $( '#' + container ),
				form = block.closest( 'form' ),
				parameters = params ? params : { 'sitekey' : gglcptch.options.sitekey, 'size' : 'invisible', 'tabindex' : 9999 },
				targetObject = false,
				targetEvent = false;

			if ( form.length ) {
				storeEvents( form );
				form.find( 'button, input:submit' ).each( function() {
					storeEvents( $( this ) );
				} );

				/* Callback function works only in frontend */
				if ( ! $( 'body' ).hasClass( 'wp-admin' ) ) {
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

				if ( ! $( 'body' ).hasClass( 'wp-admin' ) ) {
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