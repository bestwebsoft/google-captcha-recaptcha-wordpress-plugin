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
			$( '#gglcptch_theme_notice' ).css( 'display', 'block' );
		});

		var gglcptch_version_not_selected = $( 'input[name="gglcptch_recaptcha_version"]:not(:checked)' ).val();
		$( '.gglcptch_theme_' + gglcptch_version_not_selected ).hide();
		$( 'th .gglcptch_span' ).hide();

		$( 'input[name="gglcptch_recaptcha_version"]').change( function() {
			var gglcptch_version_selected = $( this ).val(),
				gglcptch_version_not_selected = $( 'input[name="gglcptch_recaptcha_version"]:not(:checked)' ).val();
			$( '.gglcptch_theme_' + gglcptch_version_selected ).show();
			$( '.gglcptch_theme_' + gglcptch_version_not_selected ).hide();
		});
	});
})(jQuery);