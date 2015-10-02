(function($) {
	$(document).ready( function() {
		var gglcptch_version_not_selected = $( 'input[name="gglcptch_recaptcha_version"]:not(:checked)' ).val();
		$( '.gglcptch_theme_' + gglcptch_version_not_selected ).hide();

		$( 'input[name="gglcptch_recaptcha_version"]').change( function() {
			var gglcptch_version_selected = $( this ).val(),
				gglcptch_version_not_selected = $( 'input[name="gglcptch_recaptcha_version"]:not(:checked)' ).val();
			$( '.gglcptch_theme_' + gglcptch_version_selected ).show();
			$( '.gglcptch_theme_' + gglcptch_version_not_selected ).hide();
		});
	});
})(jQuery);