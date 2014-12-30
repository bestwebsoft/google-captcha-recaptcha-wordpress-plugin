(function($) {
	$(document).ready( function() {
		$( '#gglcptch_settings_form input' ).bind( "change click select", function() {
			if ( $( this ).attr( 'type' ) != 'submit' ) {
				$( '.updated.fade' ).css( 'display', 'none' );
				$( '#gglcptch_settings_notice' ).css( 'display', 'block' );
			};
		});
		$( '#gglcptch_settings_form select' ).bind( "change", function() {
			$( '.updated.fade' ).css( 'display', 'none' );
			$( '#gglcptch_settings_notice' ).css( 'display', 'block' );
		});

		if ( 'v1' == $( 'input[name="gglcptch_recaptcha_version"]:checked' ).val() ) {
			$( '#gglcptch_theme_v2' ).hide();
			$( '#gglcptch_theme_v1' ).show();
		} else {
			$( '#gglcptch_theme_v2' ).show();
			$( '#gglcptch_theme_v1' ).hide();
		}
		$( 'th .gglcptch_span' ).hide();
		$( 'input[name="gglcptch_recaptcha_version"]').change( function() {
			if ( 'v1' == $( this ).filter(':checked').val() ) {
				$( '#gglcptch_theme_v2' ).hide();
				$( '#gglcptch_theme_v1' ).show();
			} else {
				$( '#gglcptch_theme_v2' ).show();
				$( '#gglcptch_theme_v1' ).hide();
			}
		});
	});
})(jQuery);