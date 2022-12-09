<?php
/**
 * Display banners on settings page
 *
 * @package reCaptcha by BestWebSoft
 * @since 1.27
 */

/**
 * Show ads for PRO
 *
 * @param       string     $func        function to call
 * @return      void
 */
if ( ! function_exists( 'gglcptch_pro_block' ) ) {
	function gglcptch_pro_block( $func, $show_cross = true, $display_always = false ) {
		global $gglcptch_plugin_info, $wp_version, $gglcptch_options;
		if ( $display_always || ! bws_hide_premium_options_check( $gglcptch_options ) ) { ?>
			<div class="bws_pro_version_bloc gglcptch_pro_block <?php echo esc_attr( $func ); ?>" title="<?php esc_html_e( 'This options is available in Pro version of plugin', 'google-captcha' ); ?>">
				<div class="bws_pro_version_table_bloc">
					<?php if ( $show_cross ) { ?>
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'google-captcha' ); ?>" value="1"></button>
					<?php } ?>
					<div class="bws_table_bg"></div>
					<div class="bws_pro_version">
						<?php call_user_func( $func ); ?>
					</div>
				</div>
				<div class="bws_pro_version_tooltip">
					<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/google-captcha/?k=b850d949ccc1239cab0da315c3c822ab&pn=109&v=<?php echo esc_attr( $gglcptch_plugin_info['Version'] ); ?>&wp_v=<?php echo esc_attr( $wp_version ); ?>" target="_blank" title="reCaptcha Pro">
						<?php esc_html_e( 'Upgrade to Pro', 'google-captcha' ); ?>
					</a>
					<div class="clear"></div>
				</div>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'gglcptch_allowlist_banner' ) ) {
	function gglcptch_allowlist_banner() {
		?>
		<div class="bws_info" style="line-height: 2;"><?php esc_html_e( 'Allowed formats', 'google-captcha' ); ?>:&nbsp;<code>192.168.0.1, 192.168.0., 192.168., 192., 192.168.0.1/8, 123.126.12.243-185.239.34.54</code></div>
		<div class="bws_info" style="line-height: 2;"><?php esc_html_e( 'Allowed separators for IPs: a comma', 'google-captcha' ); ?> (<code>,</code>), <?php esc_html_e( 'semicolon', 'google-captcha' ); ?> (<code>;</code>), <?php esc_html_e( 'ordinary space, tab, new line or carriage return.', 'google-captcha' ); ?></div>
		<?php esc_html_e( 'Reason', 'google-captcha' ); ?><br>
		<textarea disabled></textarea>
		<div class="bws_info" style="line-height: 2;"><?php esc_html_e( 'Allowed separators for reasons: a comma', 'google-captcha' ); ?> (<code>,</code>), <?php esc_html_e( 'semicolon', 'google-captcha' ); ?> (<code>;</code>), <?php esc_html_e( 'tab, new line or carriage return.', 'google-captcha' ); ?></div>
		<?php
	}
}

if ( ! function_exists( 'gglcptch_supported_plugins_banner' ) ) {
	function gglcptch_supported_plugins_banner() {
		$pro_forms    = array(
			'cf7'                         => array( 'form_name' => 'Contact Form 7' ),
			'si_contact_form'             => array( 'form_name' => 'Fast Secure Contact Form' ),
			'jetpack_contact_form'        => array( 'form_name' => __( 'Jetpack Contact Form', 'google-captcha' ) ),
			'sbscrbr'                     => array( 'form_name' => 'Subscriber' ),
			'mailchimp'                   => array( 'form_name' => 'MailChimp for Wordpress' ),
			'bbpress_new_topic_form'      => array( 'form_name' => __( 'bbPress New Topic form', 'google-captcha' ) ),
			'bbpress_reply_form'          => array( 'form_name' => __( 'bbPress Reply form', 'google-captcha' ) ),
			'buddypress_register'         => array( 'form_name' => __( 'BuddyPress Registration form', 'google-captcha' ) ),
			'buddypress_comments'         => array( 'form_name' => __( 'BuddyPress Comments form', 'google-captcha' ) ),
			'buddypress_group'            => array( 'form_name' => __( 'BuddyPress Add New Group form', 'google-captcha' ) ),
			'woocommerce_login'           => array( 'form_name' => __( 'WooCommerce Login form', 'google-captcha' ) ),
			'woocommerce_register'        => array( 'form_name' => __( 'WooCommerce Registration form', 'google-captcha' ) ),
			'woocommerce_lost_password'   => array( 'form_name' => __( 'WooCommerce Reset password form', 'google-captcha' ) ),
			'woocommerce_checkout'        => array( 'form_name' => __( 'WooCommerce Checkout form', 'google-captcha' ) ),
			'wpforo_login_form'           => array( 'form_name' => __( 'wpForo Login form', 'google-captcha' ) ),
			'wpforo_register_form'        => array( 'form_name' => __( 'wpForo Registration form', 'google-captcha' ) ),
			'wpforo_new_topic_form'       => array( 'form_name' => __( 'wpForo New Topic form', 'google-captcha' ) ),
			'wpforo_reply_form'           => array( 'form_name' => __( 'wpForo Reply form', 'google-captcha' ) ),
			'ninja_form'                  => array( 'form_name' => __( 'Ninja Forms', 'google-captcha' ) ),
			'divi_contact_form'           => array( 'form_name' => __( 'Divi Contact Form', 'google-captcha' ) ),
			'divi_login'                  => array( 'form_name' => __( 'Divi Login Form', 'google-captcha' ) ),
			'gravity_forms'               => array( 'form_name' => __( 'Gravity Forms', 'google-captcha' ) ),
			'wpforms'                     => array( 'form_name' => __( 'WPForms', 'google-captcha' ) ),
			'ultimate_member_login'       => array( 'form_name' => __( 'Ultimate Member Login form', 'google-captcha' ) ),
			'ultimate_member_register'    => array( 'form_name' => __( 'Ultimate Member Registration form', 'google-captcha' ) ),
			'ultimate_member_profile'     => array( 'form_name' => __( 'Ultimate Member Profile form', 'google-captcha' ) ),
			'caldera_forms'               => array( 'form_name' => 'Caldera Forms' ),
			'elementor_contact_form'      => array( 'form_name' => __( 'Elementor Contact Form', 'google-captcha' ) ),
			'memberpress_checkout'        => array( 'form_name' => __( 'MemberPress checkout form', 'google-captcha' ) ),
			'memberpress_login'           => array( 'form_name' => __( 'MemberPress login form', 'google-captcha' ) ),
			'memberpress_forgot_password' => array( 'form_name' => __( 'MemberPress forgot password form', 'google-captcha' ) ),
			'learndash_login_form'        => array( 'form_name' => __( 'LearnDash login form', 'google-captcha' ) ),
			'learndash_registration_form' => array( 'form_name' => __( 'LearnDash registration form', 'google-captcha' ) ),
			'bboss_registration_form'     => array( 'form_name' => __( 'BuddyBoss registration form', 'google-captcha' ) ),
		);
		$pro_sections = array(
			'external'        => array(
				'name'  => __( 'External Plugins', 'google-captcha' ),
				'forms' => array(
					'gravity_forms',
					'ninja_form',
					'jetpack_contact_form',
					'mailchimp',
					'sbscrbr',
					'wpforms',
					'cf7',
					'si_contact_form',
					'caldera_forms',
					'elementor_contact_form',
				),
			),
			'woocommerce'     => array(
				'name'  => 'WooCommerce',
				'forms' => array(
					'woocommerce_login',
					'woocommerce_register',
					'woocommerce_lost_password',
					'woocommerce_checkout',
				),
			),
			'buddypress'      => array(
				'name'  => 'BuddyPress',
				'forms' => array(
					'buddypress_register',
					'buddypress_comments',
					'buddypress_group',
				),
			),
			'divi'            => array(
				'name'  => 'Divi',
				'forms' => array(
					'divi_contact_form',
					'divi_login',
				),
			),
			'bbpress'         => array(
				'name'  => 'bbPress',
				'forms' => array(
					'bbpress_new_topic_form',
					'bbpress_reply_form',
				),
			),
			'wpforo'          => array(
				'name'  => 'Forums - wpForo',
				'forms' => array(
					'wpforo_login_form',
					'wpforo_register_form',
					'wpforo_new_topic_form',
					'wpforo_reply_form',
				),
			),
			'ultimate_member' => array(
				'name'  => 'Ultimate Member',
				'forms' => array(
					'ultimate_member_login',
					'ultimate_member_register',
					'ultimate_member_profile',
				),
			),
			'memberpress'     => array(
				'name'  => 'MemberPress',
				'forms' => array(
					'memberpress_checkout',
					'memberpress_login',
					'memberpress_forgot_password',
				),
			),
			'learndash'       => array(
				'name'  => 'LearnDash',
				'forms' => array(
					'learndash_login_form',
					'learndash_registration_form',
				),
			),
			'buddyboss'       => array(
				'name'  => 'BuddyBoss',
				'forms' => array(
					'bboss_registration_form',
				),
			),
		);
		?>
		<table class="form-table bws_pro_version" style="margin-right: 10px; width: calc( 100% - 10px );">
			<tbody style="display: table-row-group;">
				<tr valign="top">
					<th scope="row"></th>
					<td style="padding-top: 30px;">
						<?php
						foreach ( $pro_sections as $section_slug => $section ) {

							if ( empty( $section['name'] ) || empty( $section['forms'] ) || ! is_array( $section['forms'] ) ) {
								continue;
							}
							?>
							<!--[if !IE]> -->
							<div class="gglcptch-settings-accordion">
							<!-- <![endif]-->
								<p class="gglcptch_section_header">
									<i><?php echo esc_html( $section['name'] ); ?></i><br />
								</p>
								<fieldset class="gglcptch_section_forms">
									<?php foreach ( $section['forms'] as $form_slug ) { ?>
										<label>
											<input type="checkbox" <?php disabled( true ); ?> /> <?php echo esc_html( $pro_forms[ $form_slug ]['form_name'] ); ?>
										</label>
										<br />
									<?php } ?>
									<hr />
								</fieldset>
							<!--[if !IE]> -->
							</div> <!-- .gglcptch-settings-accordion -->
							<!-- <![endif]-->
						<?php } ?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
}

if ( ! function_exists( 'gglcptch_additional_settings_banner_general' ) ) {
	function gglcptch_additional_settings_banner_general() {
		?>
		<table class="form-table bws_pro_version">
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Language', 'google-captcha' ); ?></th>
				<td>
					<select disabled="disabled">
						<option selected="selected">English</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Multilanguage', 'google-captcha' ); ?></th>
				<td>
					<input disabled="disabled" type="checkbox" />
					<span class="bws_info"><?php esc_html_e( 'Enable to switch language automatically on multilingual website using the Multilanguage plugin.', 'google-captcha' ); ?></span>
				</td>
			</tr>
		</table>
		<?php
	}
}

if ( ! function_exists( 'gglcptch_additional_settings_banner_appearance' ) ) {
	function gglcptch_additional_settings_banner_appearance() {
		?>
		<table class="form-table bws_pro_version">
			<tr class="" valign="top">
				<th scope="row">
					<?php esc_html_e( 'Size', 'google-captcha' ); ?>
				</th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="radio" checked><?php esc_html_e( 'Normal', 'google-captcha' ); ?></label><br />
						<label><input disabled="disabled" type="radio"><?php esc_html_e( 'Compact', 'google-captcha' ); ?></label>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
	}
}

if ( ! function_exists( 'gglcptch_additional_settings_banner_display' ) ) {
	function gglcptch_additional_settings_banner_display() {
		?>
		<table class="form-table bws_pro_version">
			<tr class="gglcptch_score_v3" valign="top">
				<th scope="row">
					<?php esc_html_e( 'Display for', 'google-captcha' ); ?>
				</th>
				<td>
					<fieldset>
						<label><input disabled="disabled" type="radio" checked><?php esc_html_e( 'Only form', 'google-captcha' ); ?></label><br />
						<label><input disabled="disabled" type="radio"><?php esc_html_e( 'All pages', 'google-captcha' ); ?></label>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
	}
}
