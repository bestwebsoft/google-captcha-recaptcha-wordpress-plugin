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
		<div class="bws_info" style="line-height: 2;"><?php _e( "Allowed formats", 'google-captcha' ); ?>:&nbsp;<code>192.168.0.1, 192.168.0., 192.168., 192., 192.168.0.1/8, 123.126.12.243-185.239.34.54</code></div>
		<div class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for IPs: a comma", 'google-captcha' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'google-captcha' ); ?> (<code>;</code>), <?php _e( 'ordinary space, tab, new line or carriage return.', 'google-captcha' ); ?></div>	
		<?php _e( 'Reason', 'google-captcha' ); ?><br>
		<textarea disabled></textarea>					
		<div class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for reasons: a comma", 'google-captcha' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'google-captcha' ); ?> (<code>;</code>), <?php _e( 'tab, new line or carriage return.', 'google-captcha' ); ?></div>
	<?php }
}

if ( ! function_exists( 'gglcptch_supported_plugins_banner' ) ) {
	function gglcptch_supported_plugins_banner() { ?>
		<table class="form-table bws_pro_version">			
			<tr valign="top">
				<th scope="row"></th>
				<td>
					<p>
						<i><?php _e( 'External Plugins', 'google-captcha' ); ?></i>
					</p>
					<br>
					<fieldset>
						<label><input disabled="disabled" type="checkbox" disabled="disabled"> Subscriber</label><br>
						<label><input disabled="disabled" type="checkbox" disabled="disabled"> Contact Form 7</label>
					</fieldset>
					<hr>
					<p>
						<i>BuddyPress</i>
					</p>
					<br>
					<fieldset>
						<label><input disabled="disabled" type="checkbox" disabled="disabled"> <?php _e( 'Registration form', 'google-captcha' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" disabled="disabled"> <?php _e( 'Comments form', 'google-captcha' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" disabled="disabled"> <?php _e( 'Create a Group form', 'google-captcha' ); ?></label>
					</fieldset>
					<hr>
					<p>
						<i>WooCommerce</i>
					</p>
					<br>
					<fieldset>
						<label><input disabled="disabled" type="checkbox" disabled="disabled"> <?php _e( 'Login form', 'google-captcha' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" disabled="disabled"> <?php _e( 'Registration form', 'google-captcha' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" disabled="disabled"> <?php _e( 'Lost password form', 'google-captcha' ); ?></label><br>
						<label><input disabled="disabled" type="checkbox" disabled="disabled"> <?php _e( 'Checkout form', 'google-captcha' ); ?></label>
					</fieldset>
				</td>
			</tr>
		</table>
	<?php }
}

if ( ! function_exists( 'gglcptch_additional_settings_banner' ) ) {
	function gglcptch_additional_settings_banner() { ?>
		<table class="form-table bws_pro_version">			
			<tr class="gglcptch_theme_v2" valign="top">
				<th scope="row">
					<?php _e( 'Size', 'google-captcha' ); ?>
				</th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="radio" checked><?php _e( 'Normal', 'google-captcha' ); ?></label><br />
						<label><input disabled="disabled" type="radio"><?php _e( 'Compact', 'google-captcha' ); ?></label>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Language', 'google-captcha' ); ?></th>
				<td>
					<select disabled="disabled">
						<option selected="selected">English</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'Multilanguage', 'google-captcha' ); ?></th>
				<td>
					<input disabled="disabled" type="checkbox" /> 
					<span class="bws_info"><?php _e( 'Enable to switch language automatically on multilingual website using Multilanguage plugin.', 'google-captcha' ); ?></span>
				</td>
			</tr>
		</table>
	<?php }
}
