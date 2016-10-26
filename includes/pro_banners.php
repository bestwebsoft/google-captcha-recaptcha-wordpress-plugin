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
					<div class="bws_info"><?php _e( 'Unlock premium options by upgrading to Pro version', 'google-captcha' ); ?></div>
					<a class="bws_button" href="http://bestwebsoft.com/products/wordpress/plugins/google-captcha/?k=b850d949ccc1239cab0da315c3c822ab&pn=109&v=<?php echo $gglcptch_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google Captcha Pro (reCAPTCHA)">
						<?php _e( 'Learn More', 'google-captcha' ); ?>
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
		<label><input disabled="disabled" type="checkbox" disabled="disabled" name="gglcptch_sbscrbr" value="1"> Subscriber by BestWebSoft</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled" name="gglcptch_cf7" value="1"> Contact Form 7</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled" name="gglcptch_buddypress_register" value="1"> BuddyPress Registration form</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled" name="gglcptch_buddypress_comments" value="1"> BuddyPress Comments form</label><br>
		<label><input disabled="disabled" type="checkbox" disabled="disabled" name="gglcptch_buddypress_group" value="1"> BuddyPress "Create a Group" form</label>
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
				<th scope="row"><?php _e( 'reCAPTCHA language', 'google-captcha' ); ?></th>
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
					<?php _e( 'reCAPTCHA size', 'google-captcha' ); ?>
					<br/><span class="bws_info">(<?php _e( 'for version', 'google-captcha' ); ?> 2)</span>
				</th>
				<td><fieldset>
					<?php foreach ( $gglcptch_sizes_v2 as $value => $name ) {
						$tooltip = sprintf(
							'<div class="bws_help_box dashicons dashicons-editor-help" style="vertical-align: middle;z-index:2;"><div class="bws_hidden_help_text" style="z-index: 3;"><img src="%1$s%2$s%3$s"%5$s><img src="%1$s%2$s%4$s"%6$s></div></div><br/>',
							plugins_url( 'google-captcha/images'),
							$name == 'Normal' ? '/recaptcha_v2_normal' : '/recaptcha_v2_compact',
							'_light.png',
							'_dark.png',
							' class="gglcptch_size_sample gglcptch_size_sample_light' . ( 'light' == $gglcptch_options['theme_v2'] ? '"' : ' hidden"' ),
							' class="gglcptch_size_sample gglcptch_size_sample_dark' . ( 'dark' == $gglcptch_options['theme_v2'] ? '"' : ' hidden"' )
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
