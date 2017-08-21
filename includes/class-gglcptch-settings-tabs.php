<?php
/**
 * Displays the content on the plugin settings page
 */

require_once( dirname( dirname( __FILE__ ) ) . '/bws_menu/class-bws-settings.php' );

if ( ! class_exists( 'Gglcptch_Settings_Tabs' ) ) {
	class Gglcptch_Settings_Tabs extends Bws_Settings_Tabs {
		private $keys, $versions, $forms, $themes;

		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $gglcptch_options, $gglcptch_plugin_info;

			$tabs = array(
				'settings'      => array( 'label' => __( 'Settings', 'google-captcha' ) ),
				'misc'          => array( 'label' => __( 'Misc', 'google-captcha' ) ),
				'custom_code'   => array( 'label' => __( 'Custom Code', 'google-captcha' ) ),
				'license'       => array( 'label' => __( 'License Key', 'google-captcha' ) )
			);

			parent::__construct( array(
				'plugin_basename'    => $plugin_basename,
				'plugins_info'       => $gglcptch_plugin_info,
				'prefix'             => 'gglcptch',
				'default_options'    => gglcptch_get_default_options(),
				'options'            => $gglcptch_options,
				'is_network_options' => is_network_admin(),
				'tabs'               => $tabs,
				'wp_slug'            => 'google-captcha',
				'pro_page'           => 'admin.php?page=google-captcha-pro.php',
				'bws_license_plugin' => 'google-captcha-pro/google-captcha-pro.php',
				'link_key'           => 'b850d949ccc1239cab0da315c3c822ab',
				'link_pn'            => '109'
			) );

			$this->all_plugins = get_plugins();

			/* Private and public keys */
			$this->keys = array(
				'public' => array(
					'display_name'	=>	__( 'Site Key', 'google-captcha' ),
					'form_name'		=>	'gglcptch_public_key',
					'error_msg'		=>	'',
				),
				'private' => array(
					'display_name'	=>	__( 'Secret Key', 'google-captcha' ),
					'form_name'		=>	'gglcptch_private_key',
					'error_msg'		=>	'',
				),
			);

			$this->versions = array(
				'v1'			=> sprintf( '%s 1', __( 'Version', 'google-captcha' ) ),
				'v2'			=> sprintf( '%s 2', __( 'Version', 'google-captcha' ) ),
				'invisible'		=> __( 'Invisible', 'google-captcha' )
			);

			/* Checked forms */
			$this->forms = array(
				array( 'login_form', __( 'Login form', 'google-captcha' ) ),
				array( 'registration_form', __( 'Registration form', 'google-captcha' ) ),
				array( 'reset_pwd_form', __( 'Reset password form', 'google-captcha' ) ),
				array( 'comments_form', __( 'Comments form', 'google-captcha' ) ),
			);

			/* Google captcha themes */
			$this->themes = array(
				array( 'red', 'Red' ),
				array( 'white', 'White' ),
				array( 'blackglass', 'Blackglass' ),
				array( 'clean', 'Clean' ),
			);

			add_action( get_parent_class( $this ) . '_display_custom_messages', array( $this, 'display_custom_messages' ) );
			add_action( get_parent_class( $this ) . '_display_metabox', array( $this, 'display_metabox' ) );
		}

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {

			/* Save data for settings page */
			if ( empty( $_POST['gglcptch_public_key'] ) ) {
				$this->keys['public']['error_msg'] = __( 'Enter site key', 'google-captcha' );
				$error = __( "WARNING: The captcha will not be displayed until you fill key fields.", 'google-captcha' );
			} else {
				$this->keys['public']['error_msg'] = '';
			}

			if ( empty( $_POST['gglcptch_private_key'] ) ) {
				$this->keys['private']['error_msg'] = __( 'Enter secret key', 'google-captcha' );
				$error = __( "WARNING: The captcha will not be displayed until you fill key fields.", 'google-captcha' );
			} else {
				$this->keys['private']['error_msg'] = '';
			}

			if ( $_POST['gglcptch_public_key'] != $this->options['public_key'] || $_POST['gglcptch_private_key'] != $this->options['private_key'] )
				$this->options['keys_verified'] = false;

			if ( $_POST['gglcptch_recaptcha_version'] != $this->options['recaptcha_version'] ) {
				$this->options['keys_verified'] = false;
				$this->options['need_keys_verified_check'] = true;
			}

			$this->options['whitelist_message']	=	stripslashes( esc_html( $_POST['gglcptch_whitelist_message'] ) );
			$this->options['public_key']			=	trim( stripslashes( esc_html( $_POST['gglcptch_public_key'] ) ) );
			$this->options['private_key']		=	trim( stripslashes( esc_html( $_POST['gglcptch_private_key'] ) ) );
			$this->options['login_form']			=	isset( $_POST['gglcptch_login_form'] ) ? 1 : 0;
			$this->options['registration_form']	=	isset( $_POST['gglcptch_registration_form'] ) ? 1 : 0;
			$this->options['reset_pwd_form']		=	isset( $_POST['gglcptch_reset_pwd_form'] ) ? 1 : 0;
			$this->options['comments_form']		=	isset( $_POST['gglcptch_comments_form'] ) ? 1 : 0;
			$this->options['contact_form']		=	isset( $_POST['gglcptch_contact_form'] ) ? 1 : 0;
			$this->options['recaptcha_version']	=	in_array( $_POST['gglcptch_recaptcha_version'], array( 'v1', 'v2', 'invisible' ) ) ? $_POST['gglcptch_recaptcha_version']: 'v2';
			$this->options['theme']				=	stripslashes( esc_html( $_POST['gglcptch_theme'] ) );
			$this->options['theme_v2']			=	stripslashes( esc_html( $_POST['gglcptch_theme_v2'] ) );

			if ( function_exists( 'get_editable_roles' ) ) {
				foreach ( get_editable_roles() as $role => $fields ) {
					$this->options[ $role ] = isset( $_POST[ 'gglcptch_' . $role ] ) ? 1 : 0;
				}
			}

			update_option( 'gglcptch_options', $this->options );
			$message = __( "Settings saved.", 'google-captcha' );

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 * Displays 'settings' menu-tab
		 * @access public
		 * @param void
		 * @return void
		 */
		public function tab_settings() {
			global $wp_version;
			$is_main_site = is_main_site( get_current_blog_id() ); ?>
			<h3 class="bws_tab_label"><?php _e( 'Google Captcha Settings', 'google-captcha' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_tab_sub_label"><?php _e( 'Authentication', 'google-captcha' ); ?></div>
			<div class="bws_info"><?php _e( 'Register your website with Google to get required API keys and enter them below.', 'google-captcha' ); ?> <a target="_blank" href="https://www.google.com/recaptcha/admin#list"><?php _e( 'Get the API Keys', 'google-captcha' ); ?></a></div>
			<table class="form-table">
				<?php foreach ( $this->keys as $key => $fields ) { ?>
					<tr>
						<th><?php echo $fields['display_name']; ?></th>
						<td>
							<input class="regular-text" type="text" name="<?php echo $fields['form_name']; ?>" value="<?php echo $this->options[ $key . '_key' ] ?>" maxlength="200" />
							<label class="gglcptch_error_msg error"><?php echo $fields['error_msg']; ?></label>
							<span class="dashicons dashicons-yes gglcptch_verified <?php if ( ! isset( $this->options['keys_verified'] ) || true !== $this->options['keys_verified'] ) echo 'hidden'; ?>"></span>
						</td>
					</tr>
				<?php }
				if ( ! empty( $this->options['public_key'] ) && ! empty( $this->options['private_key'] ) ) { ?>
					<tr class="hide-if-no-js">
						<th></th>
						<td>
							<div id="gglcptch-test-keys">
								<a class="button button-secondary" href="<?php echo add_query_arg( array( '_wpnonce' => wp_create_nonce( 'gglcptch-test-keys' ), 'action' => 'gglcptch-test-keys', 'is_network' => $this->is_network_options ? '1' : '0' ), admin_url( 'admin-ajax.php' ) ); ?>"><?php _e( 'Test ReCaptcha' , 'google-captcha' ); ?></a>
							</div>
						</td>
					</tr>
				<?php } ?>
			</table>
			<div class="bws_tab_sub_label"><?php _e( 'General', 'google-captcha' ); ?></div>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Enable ReCaptcha for', 'google-captcha' ); ?></th>
					<td>
						<fieldset>
							<p>
								<i><?php _e( 'WordPress default', 'google-captcha' ); ?></i>
							</p>
							<br>
							<?php foreach ( $this->forms as $form ) {
								$form_attr = ( '1' == $this->options[ $form[0] ] ) ? 'checked="checked"' : '';
								$form_notice = '';

								if ( ( $form[0] == 'registration_form' || $form[0] == 'reset_pwd_form' ) && ! $is_main_site ) {
									$form_notice .= '<span class="bws_info">(' . __( 'This option is available only for network or for main blog.', 'google-captcha' ) . ')</span>';
									$form_attr = 'disabled="disabled" readonly="readonly"';
								} ?>
								<label>
									<input type="checkbox" name="<?php echo 'gglcptch_' . $form[0]; ?>" value="1" <?php echo $form_attr; ?> />
									 <?php echo $form[1]; ?>
								</label>
								<?php echo $form_notice; ?>
								<br />
							<?php } ?>
							<hr>
							<p>
								<i><?php _e( 'External Plugins', 'google-captcha' ); ?></i>
							</p>
							<br>
							<?php /* Check Contact Form by BestWebSoft */
							$plugin_info = gglcptch_plugin_status( array( 'contact-form-plugin/contact_form.php', 'contact-form-pro/contact_form_pro.php' ), $this->all_plugins, $this->is_network_options );
							$plugin_name = 'Contact Form';
							$attrs = $plugin_notice = '';
							if ( 'deactivated' == $plugin_info['status'] ) {
								$attrs = 'disabled="disabled"';
								$plugin_notice = '<a href="' . self_admin_url( 'plugins.php' ) . '">' . __( 'Activate', 'google-captcha' ) . '</a>';
							} elseif ( 'not_installed' == $plugin_info['status'] ) {
								$attrs = 'disabled="disabled"';
								$plugin_notice = '<a href="https://bestwebsoft.com/products/wordpress/plugins/contact-form/?k=0a750deb99a8e5296a5432f4c9cb9b55&pn=109&v=' . $this->plugins_info["Version"] . '&wp_v=' . $wp_version . '" target="_blank">' . __( 'Install Now', 'google-captcha' ) . '</a>';
							}
							if ( $attrs == '' && ( is_plugin_active( 'contact-form-multi-pro/contact-form-multi-pro.php' ) || is_plugin_active( 'contact-form-multi/contact-form-multi.php' ) ) )
								$plugin_notice = ' (' . __( 'Enable for adding captcha to forms on their settings pages.', 'google-captcha' ) . ')';

							if ( '1' == $this->options['contact_form'] && $attrs == '' ) {
								$attrs .= ' checked="checked"';
							} ?>
							<label><input type="checkbox" <?php echo $attrs; ?> name="gglcptch_contact_form" value="contact_form" /> <?php echo $plugin_name; ?></label>
							<span class="bws_info"> <?php echo $plugin_notice; ?></span>
							<hr>
						</fieldset>
					</td>
				</tr>
			</table>
			<?php if ( ! $this->hide_pro_tabs ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'google-captcha' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<?php gglcptch_supported_plugins_banner(); ?>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php } ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Hide ReCaptcha in Comments Form for', 'google-captcha' ); ?></th>
					<td>
						<fieldset>
							<?php if ( function_exists( 'get_editable_roles' ) ) {
								foreach ( get_editable_roles() as $role => $fields ) : ?>
									<label><input type="checkbox" name="<?php echo 'gglcptch_' . $role; ?>" value=<?php echo $role; if ( isset( $this->options[ $role ] ) && '1' == $this->options[ $role ] ) echo ' checked'; ?>> <?php echo $fields['name']; ?></label><br/>
								<?php endforeach;
							} ?>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'ReCaptcha Version', 'google-captcha' ); ?></th>
					<td>
						<fieldset>
							<?php foreach ( $this->versions as $version => $version_name ) { ?>
								<label>
									<input type="radio" name="gglcptch_recaptcha_version" value="<?php echo $version; ?>" <?php checked( $version, $this->options['recaptcha_version'] ); ?>> <?php echo $version_name; ?>
								</label>
								<br/>
							<?php } ?>
						</fieldset>
					</td>
				</tr>
				<tr class="gglcptch_theme_v1" valign="top">
					<th scope="row">
						<?php _e( 'Theme', 'google-captcha' ); ?>
					</th>
					<td>
						<select name="gglcptch_theme">
							<?php foreach ( $this->themes as $theme ) { ?>
								<option value="<?php echo $theme[0]; ?>" <?php selected( $theme[0], $this->options['theme'] ); ?>><?php echo $theme[1]; ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr class="gglcptch_theme_v2" valign="top">
					<th scope="row">
						<?php _e( 'Theme', 'google-captcha' ); ?>
					</th>
					<td>
						<select name="gglcptch_theme_v2">
							<option value="light" <?php selected( 'light', $this->options['theme_v2'] ); ?>>Light</option>
							<option value="dark" <?php selected( 'dark', $this->options['theme_v2'] ); ?>>Dark</option>
						</select>
					</td>
				</tr>
			</table>
			<?php if ( ! $this->hide_pro_tabs ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'google-captcha' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<?php gglcptch_additional_settings_banner(); ?>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php } ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Whitelist Notification', 'google-captcha' ); ?></th>
					<td>
						<textarea name="gglcptch_whitelist_message"><?php echo $this->options['whitelist_message']; ?></textarea>
						<div class="bws_info"><?php _e( 'This message will be displayed instead of the ReCaptcha.', 'google-captcha' ); ?></div>
					</td>
				</tr>
			</table>
		<?php }

		/**
		 * Display custom error\message\notice
		 * @access public
		 * @param  $save_results - array with error\message\notice
		 * @return void
		 */
		public function display_custom_messages( $save_results ) {
			if ( $this->options['recaptcha_version'] == 'v1' ) { ?>
				<div class="updated inline bws-notice"><p><strong><?php _e( "Only one ReCaptcha can be displayed on the page, it's related to ReCaptcha version 1 features.", 'google-captcha' ); ?></strong></p></div>
			<?php }
			if ( ! empty( $this->options['need_keys_verified_check'] ) ) { ?>
				<div class="updated inline bws-notice"><p><strong><?php _e( 'ReCaptcha version was changed. Please submit "Test ReCaptcha" and regenerate Site and Secret keys if necessary.', 'google-captcha' ); ?></strong></p></div>
			<?php }
		}

		/**
		 * Display custom metabox
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function display_metabox() { ?>
			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Google Captcha Shortcode', 'google-captcha' ); ?>
				</h3>
				<div class="inside">
					<?php _e( "Add Google Captcha to your posts or pages using the following shortcode:", 'google-captcha' ); ?>
					<?php bws_shortcode_output( '[bws_google_captcha]' ); ?>
				</div>
			</div>
		<?php }
	}
}