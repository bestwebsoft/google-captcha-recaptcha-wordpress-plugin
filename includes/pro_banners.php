<?php
/**
 * Display banners on settings page
 * @package Google Captcha(reCAPTCHA) by BestWebSoft
 * @since 1.27
 */

/**
 * Show ads for PRO
 * @param		string     $func        function to call
 * @return		void
 */
if ( ! function_exists( 'gglcptch_pro_block' ) ) {
	function gglcptch_pro_block( $func, $show_cross = true, $display_always = false ) {
		global $gglcptch_plugin_info, $wp_version, $gglcptch_options;
		if ( $display_always || ! bws_hide_premium_options_check( $gglcptch_options ) ) { ?>
			<div class="bws_pro_version_bloc gglcptch_pro_block <?php echo $func;?>" title="<?php _e( 'This options is available in Pro version of plugin', 'google-captcha' ); ?>">
				<div class="bws_pro_version_table_bloc">
					<?php if ( $show_cross ) { ?>
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'google-captcha' ); ?>"></button>
					<?php } ?>
					<div class="bws_table_bg"></div>
					<div class="bws_pro_version">
						<?php call_user_func( $func ); ?>
					</div>
				</div>
				<div class="bws_pro_version_tooltip">
					<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/google-captcha/?k=b850d949ccc1239cab0da315c3c822ab&pn=109&v=<?php echo $gglcptch_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google Captcha Pro (reCAPTCHA)">
						<?php _e( 'Upgrade to Pro', 'google-captcha' ); ?>
					</a>
					<div class="clear"></div>
				</div>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'gglcptch_whitelist_banner' ) ) {
	function gglcptch_whitelist_banner() { ?>
		<table class="form-table bws_pro_version">
			<tr>
				<td valign="top"><?php _e( 'Reason', 'google-captcha' ); ?>
					<input disabled type="text" style="margin: 10px 0;"/><br />
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed formats", 'google-captcha' ); ?>:&nbsp;<code>192.168.0.1, 192.168.0., 192.168., 192., 192.168.0.1/8, 123.126.12.243-185.239.34.54</code></span><br />
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for IPs: a comma", 'google-captcha' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'google-captcha' ); ?> (<code>;</code>), <?php _e( 'ordinary space, tab, new line or carriage return', 'google-captcha' ); ?></span><br />
					<span class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for reasons: a comma", 'google-captcha' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'google-captcha' ); ?> (<code>;</code>), <?php _e( 'tab, new line or carriage return', 'google-captcha' ); ?></span>
				</td>
			</tr>
		</table>
	<?php }
}

if ( ! function_exists( 'gglcptch_supported_plugins_banner' ) ) {
	function gglcptch_supported_plugins_banner() { ?>
		<label><input disabled="disabled" type="checkbox" disabled="disabled"> Subscriber by BestWebSoft</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled"> Contact Form 7</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled"> BuddyPress Registration form</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled"> BuddyPress Comments form</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled"> BuddyPress "Create a Group" form</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled"> WooCommerce Login form</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled"> WooCommerce Register form</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled"> WooCommerce Lost Password form</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled"> WooCommerce Checkout Billing form</label>
	<?php }
}

if ( ! function_exists( 'gglcptch_additional_settings_banner' ) ) {
	function gglcptch_additional_settings_banner() {
		global $gglcptch_options;
		$gglcptch_sizes_v2 = array(
			'normal'	=> __( 'Normal', 'google-captcha' ),
			'compact'	=> __( 'Compact', 'google-captcha' )
		); ?>
		<table class="form-table bws_pro_version">
			<tr valign="top">
				<th scope="row"><?php _e( 'reCAPTCHA Language', 'google-captcha' ); ?></th>
				<td>
					<select disabled="disabled">
						<option selected="selected">English (US)</option>
					</select>
					<div style="margin: 5px 0 0;">
						<input disabled="disabled" id="gglcptch_use_multilanguage_locale" type="checkbox" />
						<label for="gglcptch_use_multilanguage_locale"><?php _e( 'Use the current site language', 'google-captcha' ); ?></label>&nbsp;<span class="bws_info">(<?php _e( 'Using', 'google-captcha' ); ?> Multilanguage by BestWebSoft)</span>
					</div>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<?php _e( 'reCAPTCHA Size', 'google-captcha' ); ?>
					<br/><span class="bws_info">(<?php _e( 'for version', 'google-captcha' ); ?> 2)</span>
				</th>
				<td><fieldset>
					<?php foreach ( $gglcptch_sizes_v2 as $value => $name ) {
						$link = plugins_url( 'google-captcha/images' );
						$link .= $value == 'normal' ? '/recaptcha_v2_normal' : '/recaptcha_v2_compact';
						$tooltip = bws_add_help_box(
							'<img src="' . $link . '_light.png" class="gglcptch_size_sample gglcptch_size_sample_light' . ( 'light' == $gglcptch_options['theme_v2'] ? '"' : ' hidden"' ) . ' />' .
							'<img src="' . $link . '_dark.png" class="gglcptch_size_sample gglcptch_size_sample_dark' . ( 'dark' == $gglcptch_options['theme_v2'] ? '"' : ' hidden"' ) . ' />',
							'bws-auto-width'
						);
						printf(
							'<div class="gglcptch_size_v2"><label><input disabled="disabled" type="radio" %s> %s</label>%s</div>',
							$name == 'Normal' ? ' checked="checked"' : '',
							$name,
							$tooltip
						);
					} ?>
					</fieldset>
				</td>
			</tr>
		</table>
	<?php }
}
