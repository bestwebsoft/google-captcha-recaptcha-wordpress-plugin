<?php
/*
Plugin Name: Google Captcha (reCAPTCHA)
Plugin URI: http://bestwebsoft.com/plugin/
Description: Plugin Google Captcha intended to prove that the visitor is a human being and not a spam robot.
Author: BestWebSoft
Version: 1.05
Author URI: http://bestwebsoft.com/
License: GPLv3 or later
*/

/*  © Copyright 2014  BestWebSoft  ( http://support.bestwebsoft.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Add menu page */
if ( ! function_exists( 'google_capthca_admin_menu' ) ) {
	function google_capthca_admin_menu() {
		global $bstwbsftwppdtplgns_options, $wpmu, $bstwbsftwppdtplgns_added_menu;
		$bws_menu_info = get_plugin_data( plugin_dir_path( __FILE__ ) . "bws_menu/bws_menu.php" );
		$bws_menu_version = $bws_menu_info["Version"];
		$base = plugin_basename( __FILE__ );

		if ( ! isset( $bstwbsftwppdtplgns_options ) ) {
			if ( 1 == $wpmu ) {
				if ( ! get_site_option( 'bstwbsftwppdtplgns_options' ) )
					add_site_option( 'bstwbsftwppdtplgns_options', array(), '', 'yes' );
				$bstwbsftwppdtplgns_options = get_site_option( 'bstwbsftwppdtplgns_options' );
			} else {
				if ( ! get_option( 'bstwbsftwppdtplgns_options' ) )
					add_option( 'bstwbsftwppdtplgns_options', array(), '', 'yes' );
				$bstwbsftwppdtplgns_options = get_option( 'bstwbsftwppdtplgns_options' );
			}
		}

		if ( isset( $bstwbsftwppdtplgns_options['bws_menu_version'] ) ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			unset( $bstwbsftwppdtplgns_options['bws_menu_version'] );
			update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options, '', 'yes' );
			require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] ) || $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] < $bws_menu_version ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options, '', 'yes' );
			require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_added_menu ) ) {
			$plugin_with_newer_menu = $base;
			foreach ( $bstwbsftwppdtplgns_options['bws_menu']['version'] as $key => $value ) {
				if ( $bws_menu_version < $value && is_plugin_active( $base ) ) {
					$plugin_with_newer_menu = $key;
				}
			}
			$plugin_with_newer_menu = explode( '/', $plugin_with_newer_menu );
			$wp_content_dir = defined( 'WP_CONTENT_DIR' ) ? basename( WP_CONTENT_DIR ) : 'wp-content';
			if ( file_exists( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' ) )
				require_once( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' );
			else
				require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
			$bstwbsftwppdtplgns_added_menu = true;
		}

		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', plugins_url( "images/px.png", __FILE__ ), 1001 );
		add_submenu_page( 'bws_plugins', __( 'Google Captcha Settings', 'google_captcha' ), __( 'Google Captcha', 'google_captcha' ), 'manage_options', 'google-captcha.php', 'gglcptch_settings_page' );
	}
}

/* Function check if plugin is compatible with current WP version  */
if ( ! function_exists ( 'gglcptch_version_check' ) ) {
	function gglcptch_version_check() {
		global $wp_version, $gglcptch_plugin_info;
		$require_wp		=	"3.0"; /* Wordpress at least requires version */
		$plugin			=	plugin_basename( __FILE__ );
	 	if ( version_compare( $wp_version, $require_wp, "<" ) ) {
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
				wp_die( "<strong>" . $gglcptch_plugin_info['Name'] . " </strong> " . __( 'requires', 'google_captcha' ) . " <strong>WordPress " . $require_wp . "</strong> " . __( 'or higher, that is why it has been deactivated! Please upgrade WordPress and try again.', 'google_captcha') . "<br /><br />" . __( 'Back to the WordPress', 'google_captcha') . " <a href='" . get_admin_url( null, 'plugins.php' ) . "'>" . __( 'Plugins page', 'google_captcha') . "</a>." );
			}
		}
	}
}

if ( ! function_exists( 'gglcptch_init' ) ) {
	function gglcptch_init() {
		load_plugin_textdomain( 'google_captcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		global $wpmu, $gglcptch_options;
		/* Get options from the database */
		if ( 1 == $wpmu )
			$gglcptch_options = get_site_option( 'gglcptch_options' );
		else
			$gglcptch_options = get_option( 'gglcptch_options' );

		/* Add hooks */
		if ( '1' == $gglcptch_options['login_form'] ) {
			add_action( 'login_form', 'gglcptch_login_display' );
			add_filter( 'login_redirect', 'gglcptch_login_check' );
		}

		if ( '1' == $gglcptch_options['comments_form'] ) {
			add_action( 'comment_form_after_fields', 'gglcptch_commentform_display' );
			add_action( 'comment_form_logged_in_after', 'gglcptch_commentform_display' );
			add_filter( 'preprocess_comment', 'gglcptch_commentform_check' );
		}

		if ( '1' == $gglcptch_options['reset_pwd_form'] ) {
			add_action( 'lostpassword_form', 'gglcptch_login_display' );
			add_action( 'lostpassword_post', 'gglcptch_lostpassword_check' );
		}

		if ( '1' == $gglcptch_options['registration_form'] ) {
			add_action( 'register_form', 'gglcptch_login_display' );
			add_action( 'register_post', 'gglcptch_lostpassword_check' );
		}
		if ( '1' == $gglcptch_options['contact_form'] ) {
			add_filter( 'cntctfrm_display_captcha', 'gglcptch_display' );
			add_filter( 'cntctfrmpr_display_captcha', 'gglcptch_display' );
		} elseif ( '0' == $gglcptch_options['contact_form'] ) {
			remove_filter( 'cntctfrm_display_captcha', 'gglcptch_display' );
			remove_filter( 'cntctfrmpr_display_captcha', 'gglcptch_display' );
		}
	}
}

if ( ! function_exists( 'gglcptch_admin_init' ) ) {
	function gglcptch_admin_init() {
		 global $bws_plugin_info, $gglcptch_plugin_info;

 		$gglcptch_plugin_info = get_plugin_data( __FILE__, false );

		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '109', 'version' => $gglcptch_plugin_info["Version"] );

		/* Check version on WordPress */
		gglcptch_version_check();

		/* Call register settings function */
		if ( isset( $_GET['page'] ) && "google-captcha.php" == $_GET['page'] )
			register_gglcptch_settings();
	}
}

/* Add google captcha styles */
if ( ! function_exists( 'gglcptch_add_style' ) ) {
	function gglcptch_add_style() {
		if ( isset( $_REQUEST['page'] ) && 'google-captcha.php' == $_REQUEST['page'] )
			wp_enqueue_style( 'gglcptch_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

/* Add google captcha scripts */
if ( ! function_exists( 'gglcptch_add_script' ) ) {
	function gglcptch_add_script() {
		wp_enqueue_script( 'gglcptch_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
	}
}
/* Google catpcha settings */
if ( ! function_exists( 'register_gglcptch_settings' ) ) {
	function register_gglcptch_settings() {
		global $wpmu, $gglcptch_options, $bws_plugin_info, $gglcptch_plugin_info;

		$gglcptch_default_options = array(
			'public_key'			=>	'',
			'private_key'			=>	'',
			'login_form'			=>	'1',
			'registration_form'		=>	'1',
			'reset_pwd_form'		=>	'1',
			'comments_form'			=>	'1',
			'contact_form'			=>	'0',
			'theme'					=>	'red',
			'plugin_option_version'	=>	$gglcptch_plugin_info["Version"]
		);

		foreach ( get_editable_roles() as $role => $fields ) {
			$gglcptch_default_options[ $role ] = '0';
		}

		/* Install the option defaults */
		if ( 1 == $wpmu ) {
			if ( ! get_site_option( 'gglcptch_options' ) ) {
				add_site_option( 'gglcptch_options', $gglcptch_default_options, '', 'yes' );
			}
		} else {
			if ( ! get_option( 'gglcptch_options' ) ) {
				add_option( 'gglcptch_options', $gglcptch_default_options, '', 'yes' );
			}
		}

		/* Get options from the database */
		if ( 1 == $wpmu )
			$gglcptch_options = get_site_option( 'gglcptch_options' );
		else
			$gglcptch_options = get_option( 'gglcptch_options' );

		/* Array merge incase this version has added new options */
		if ( ! isset( $gglcptch_options['plugin_option_version'] ) || $gglcptch_options['plugin_option_version'] != $gglcptch_plugin_info["Version"] ) {
			$gglcptch_options = array_merge( $gglcptch_default_options, $gglcptch_options );
			$gglcptch_options['plugin_option_version'] = $gglcptch_plugin_info["Version"];
			update_option( 'gglcptch_options', $gglcptch_options );
		}
	}
}

/* Display settings page */
if ( ! function_exists( 'gglcptch_settings_page' ) ) {
	function gglcptch_settings_page() {
		global $gglcptch_options;

		/* Private and public keys */
		$gglcptch_keys = array(
			'public' => array(
				'display_name'	=>	__( 'Public Key', 'google_captcha' ),
				'form_name'		=>	'gglcptch_public_key',
				'error_msg'		=>	'',
			),
			'private' => array(
				'display_name'	=>	__( 'Private Key', 'google_captcha' ),
				'form_name'		=>	'gglcptch_private_key',
				'error_msg'		=>	'',
			),
		);

		/* Checked forms */
		$gglcptch_forms = array(
			array( 'login_form', __( 'Login form', 'google_captcha' ) ),
			array( 'registration_form', __( 'Registration form', 'google_captcha' ) ),
			array( 'reset_pwd_form', __( 'Reset password form', 'google_captcha' ) ),
			array( 'comments_form', __( 'Comments form', 'google_captcha' ) ),
		);

		/* Google captcha themes */
		$gglcptch_themes = array(
			array( 'red', 'Red' ),
			array( 'white', 'White' ),
			array( 'blackglass', 'Blackglass' ),
			array( 'clean', 'Clean' ),
		);

		$error = '';
		/* Save data for settings page */
		if ( isset( $_POST['gglcptch_save_changes'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'gglcptch_nonce_name' ) ) {
			if ( ! $_POST['gglcptch_public_key'] || '' == $_POST['gglcptch_public_key'] ) {
				$gglcptch_keys['public']['error_msg'] = __( 'Enter public key', 'google_captcha' );
				$error = __( "WARNING: The captcha will not display while you don't fill key fields.", 'google_captcha' );
			} else
				$gglcptch_keys['public']['error_msg'] = '';

			if ( ! $_POST['gglcptch_private_key'] || '' == $_POST['gglcptch_private_key'] ) {
				$gglcptch_keys['private']['error_msg'] = __( 'Enter private key', 'google_captcha' );
				$error = __( "WARNING: The captcha will not display while you don't fill key fields.", 'google_captcha' );
			} else
				$gglcptch_keys['private']['error_msg'] = '';

			$gglcptch_options['public_key']			=	$_POST['gglcptch_public_key'];
			$gglcptch_options['private_key']		=	$_POST['gglcptch_private_key'];
			$gglcptch_options['login_form']			=	isset( $_POST['gglcptch_login_form'] ) ? 1 : 0;
			$gglcptch_options['registration_form']	=	isset( $_POST['gglcptch_registration_form'] ) ? 1 : 0;
			$gglcptch_options['reset_pwd_form']		=	isset( $_POST['gglcptch_reset_pwd_form'] ) ? 1 : 0;
			$gglcptch_options['comments_form']		=	isset( $_POST['gglcptch_comments_form'] ) ? 1 : 0;
			$gglcptch_options['contact_form']		=	isset( $_POST['gglcptch_contact_form'] ) ? 1 : 0;
			$gglcptch_options['theme']				=	$_POST['gglcptch_theme'];

			foreach ( get_editable_roles() as $role => $fields ) {
				$gglcptch_options[ $role ] = isset( $_POST[ 'gglcptch_' . $role ] ) ? 1 : 0;
			}

			update_option( 'gglcptch_options', $gglcptch_options );
		} ?>
		<div class="wrap">
			<h2><?php _e( 'Google Captcha Settings', 'google_captcha' ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active" href="admin.php?page=google-captcha.php"><?php _e( 'Settings', 'google_captcha' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/plugin/google-captcha/#faq" target="_blank"><?php _e( 'FAQ', 'google_captcha' ); ?></a>
			</h2>
			<div class="updated fade" <?php if ( ! isset( $_POST['gglcptch_save_changes'] ) || "" != $error ) echo 'style="display:none"'; ?>><p><strong><?php _e( 'Settings saved', 'google_captcha' ); ?></strong></p></div>
			<div class="error" <?php if ( "" == $error ) echo 'style="display:none"'; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<p><?php _e( 'If you would like to add the Google Captcha to your own form, just copy and paste this shortcode to your post or page:', 'google_captcha' ); echo ' [bws_google_captcha]'; ?></p>
			<form method="post" action="admin.php?page=google-captcha.php">
				<h3><?php _e( 'Authentication', 'google_captcha' ); ?></h3>
				<p><?php printf( __( 'Before you are able to do something, you must to register %s here %s', 'google_captcha' ), '<a href="https://www.google.com/recaptcha/admin/create">','</a>.' ); ?></p>
				<p><?php _e( 'Enter public and private keys, that you get after registration.', 'google_captcha' ); ?></p>
				<table id="gglcptch-keys" class="form-table">
					<?php foreach ( $gglcptch_keys as $key => $fields ) : ?>
						<tr valign="top">
							<th scope="row"><?php echo $fields['display_name']; ?></th>
							<td>
								<input type="text" name="<?php echo $fields['form_name']; ?>" value="<?php echo $gglcptch_options[ $key . '_key' ] ?>" />
								<label class="gglcptch_error_msg"><?php echo $fields['error_msg']; ?></label>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<h3><?php _e( 'Options', 'google_captcha' ); ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php _e( 'Enable Google Captcha for:', 'google_captcha' ); ?></th>
						<td>
							<?php foreach ( $gglcptch_forms as $form ) : ?>
								<label><input type="checkbox" name="<?php echo 'gglcptch_' . $form[0]; ?>" value=<?php echo $form[0]; if ( '1' == $gglcptch_options[ $form[0] ] ) echo ' checked'; ?>><?php echo $form[1]; ?></label><br />
							<?php endforeach;
							$all_plugins = get_plugins();
							$active_plugins = get_option( 'active_plugins' );
							if ( isset( $all_plugins['contact-form-plugin/contact_form.php'] ) || isset( $all_plugins['contact-form-pro/contact_form_pro.php'] ) ) :
								if ( in_array( 'contact-form-plugin/contact_form.php', $active_plugins ) || in_array( 'contact-form-pro/contact_form_pro.php', $active_plugins ) ) : ?>
									<label><input type="checkbox" name="gglcptch_contact_form" value="contact_form"<?php if ( '1' == $gglcptch_options['contact_form'] ) echo ' checked'; ?>><?php _e( 'Contact form', 'google_captcha' ); ?></label>
									<span style="color: #888888;font-size: 10px;">(<?php _e( 'powered by', 'google_captcha' ); ?> <a href="http://bestwebsoft.com/plugin/">bestwebsoft.com</a>)</span><br />
								<?php else : ?>
									<label><input type="checkbox" disabled name="gglcptch_contact_form" value="contact_form"<?php if ( '1' == $gglcptch_options['contact_form'] ) echo ' checked'; ?>><?php _e( 'Contact form', 'google_captcha' ); ?></label>
									<span style="color: #888888;font-size: 10px;">(<?php _e( 'powered by', 'google_captcha' ); ?> <a href="http://bestwebsoft.com/plugin/">bestwebsoft.com</a>) <a href="<?php echo bloginfo("url"); ?>/wp-admin/plugins.php"><?php _e( 'Activate contact form', 'google_captcha' ); ?></a></span><br />
								<?php endif;
							else : ?>
								<label><input type="checkbox" disabled name="gglcptch_contact_form" value="contact_form"<?php if ( '1' == $gglcptch_options['contact_form'] ) echo ' checked'; ?>><?php _e( 'Contact form', 'google_captcha' ); ?></label>
								<span style="color: #888888;font-size: 10px;">(<?php _e( 'powered by', 'google_captcha' ); ?> <a href="http://bestwebsoft.com/plugin/">bestwebsoft.com</a>) <a href="http://bestwebsoft.com/plugin/contact-form-pro/?k=d70b58e1739ab4857d675fed2213cedc&pn=75&v=<?php echo $plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>"><?php _e( 'Download contact form', 'google_captcha' ); ?></a></span><br />
							<?php endif; ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Hide captcha for:', 'google_captcha' ); ?></th>
						<td>
							<?php foreach ( get_editable_roles() as $role => $fields) : ?>
								<label><input type="checkbox" name="<?php echo 'gglcptch_' . $role; ?>" value=<?php echo $role; if ( '1' == $gglcptch_options[ $role ] ) echo ' checked'; ?>><?php echo $fields['name']; ?></label><br/>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Theme:', 'google_captcha' ); ?></th>
						<td>
							<select name="gglcptch_theme">
								<?php foreach ( $gglcptch_themes as $theme ) : ?>
									<option value=<?php echo $theme[0]; if ( $theme[0] == $gglcptch_options['theme'] ) echo ' selected'; ?> ><?php echo $theme[1]; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'google_captcha' ); ?>" name="gglcptch_save_changes" />
				</p>
				<?php wp_nonce_field( plugin_basename( __FILE__ ), 'gglcptch_nonce_name' ); ?>
			</form>
			<div class="bws-plugin-reviews">
				<div class="bws-plugin-reviews-rate">
					<?php _e( 'If you enjoy our plugin, please give it 5 stars on WordPress', 'google_captcha' ); ?>: 
					<a href="http://wordpress.org/support/view/plugin-reviews/google-captcha" target="_blank" title="Google Captcha reviews"><?php _e( 'Rate the plugin', 'google_captcha' ); ?></a>
				</div>
				<div class="bws-plugin-reviews-support">
					<?php _e( 'If there is something wrong about it, please contact us', 'google_captcha' ); ?>: 
					<a href="http://support.bestwebsoft.com">http://support.bestwebsoft.com</a>
				</div>
			</div>
		</div>
	<?php }
}

/* Checking current user role */
if ( ! function_exists( 'gglcptch_check_role' ) ) {
	function gglcptch_check_role() {
		global $current_user, $gglcptch_options;
		if ( ! is_user_logged_in() )
			return false;
		$role = $current_user->roles[0];
		if ( '1' == $gglcptch_options[ $role ] )
			return true;
		else
			return false;
	}
}

/* Display google captcha via shortcode */
if ( ! function_exists( 'gglcptch_display' ) ) {
	function gglcptch_display() {
		if ( gglcptch_check_role() )
			return;
		global $gglcptch_options;
		require_once( 'lib/recaptchalib.php' );
		$publickey = $gglcptch_options['public_key'];
		$privatekey = $gglcptch_options['private_key']; ?>
		<style type="text/css" media="screen">
		#recaptcha_response_field {
			max-height: 35px;
		}
		</style>
		<script type='text/javascript'>
			var RecaptchaOptions = { theme : "<?php echo $gglcptch_options['theme']; ?>" },
			gglcptch_path = "<?php echo plugins_url( 'google_captcha_check.php', __FILE__ ); ?>",
			gglcptch_error_msg = "<?php _e( 'Error: You have entered an incorrect CAPTCHA value.', 'google_captcha' ); ?>",
			gglcptch_private_key = "<?php echo $privatekey; ?>";
		</script>
		<?php
		if ( ! $privatekey || ! $publickey ) {
				if ( current_user_can( 'manage_options' ) ) { ?>
					<div>
						<strong>
							<?php _e( 'To use Google Captcha you must get the keys from', 'google_captcha' ); ?> <a target="_blank" href="https://www.google.com/recaptcha/admin/create"><?php _e ( 'here', 'google_captcha' ); ?></a> <?php _e ( 'and enter them on the', 'google_captcha' ); ?> <a target="_blank" href="<?php echo admin_url( '/admin.php?page=google-captcha.php' ); ?>" ><?php _e ( 'plugin setting page', 'google_captcha' ); ?></a>.
						</strong>
					</div>
			<?php }
			return false;
		}
		if ( is_ssl() )
			return recaptcha_get_html( $publickey, '', true );
		else
			return recaptcha_get_html( $publickey );
	}
}

/* Add google captcha to the login form */
if ( ! function_exists( 'gglcptch_login_display' ) ) {
	function gglcptch_login_display() {
		global $gglcptch_options;
		$from_width = 320;
		if ( 'clean' == $gglcptch_options['theme'] )
			$from_width = 450; ?>
		<style type="text/css" media="screen">
		#loginform,
		#lostpasswordform,
		#registerform {
			width: <?php echo $from_width; ?>px !important;
		}
		.message {
			width: <?php echo $from_width + 20; ?>px !important;
		}
		</style>
		<?php echo gglcptch_display();
		return true;
	}
}

/* Check google captcha in login form */
if ( ! function_exists( 'gglcptch_login_check' ) ) {
	function gglcptch_login_check() {
		if ( isset( $_POST['wp-submit'] ) ) {
			global $gglcptch_options;
			require_once( 'lib/recaptchalib.php' );
			$publickey = $gglcptch_options['public_key'];
			$privatekey = $gglcptch_options['private_key'];

			if ( ! $privatekey || ! $publickey )
				return 'wp-admin';

			$resp = recaptcha_check_answer( $privatekey,
										$_SERVER['REMOTE_ADDR'],
										$_POST['recaptcha_challenge_field'],
										$_POST['recaptcha_response_field'] );
			if ( ! $resp->is_valid ) {
				wp_clear_auth_cookie();
				wp_die( __( 'Error: You have entered an incorrect CAPTCHA value. Click the BACK button on your browser, and try again.', 'google_captcha' ) );
			} else
				return 'wp-admin';
		}
	}
}

/* Add google captcha to the comment form */
if ( ! function_exists( 'gglcptch_commentform_display' ) ) {
	function gglcptch_commentform_display() {
		if ( gglcptch_check_role() )
			return;
		echo gglcptch_display();
		return true;
	}
}

/* Check google captcha in comment form */
if ( ! function_exists( 'gglcptch_commentform_check' ) ) {
	function gglcptch_commentform_check( $comment ) {
		global $gglcptch_options;
		if ( gglcptch_check_role() )
			return $comment;

		/* Skip captcha for comment replies from the admin menu */
		if ( isset( $_REQUEST['action'] ) && 'replyto-comment' == $_REQUEST['action'] &&
		( check_ajax_referer( 'replyto-comment', '_ajax_nonce', false ) || check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment', false ) ) ) {
			/* Skip capthca */
			return $comment;
		}
		/* Skip captcha for trackback or pingback */
		if ( '' != $comment['comment_type'] && 'comment' != $comment['comment_type'] ) {
			return $comment;
		}
		require_once( 'lib/recaptchalib.php' );
		$publickey = $gglcptch_options['public_key'];
		$privatekey = $gglcptch_options['private_key'];
		$resp = recaptcha_check_answer( $privatekey,
									$_SERVER['REMOTE_ADDR'],
									$_POST['recaptcha_challenge_field'],
									$_POST['recaptcha_response_field'] );
		if ( ! $resp->is_valid )
			wp_die( __( 'Error: You have entered an incorrect CAPTCHA value. Click the BACK button on your browser, and try again.', 'google_captcha' ) );
		else
			return $comment;
	}
}

/* Check google captcha in lostpassword form */
if ( ! function_exists( 'gglcptch_lostpassword_check' ) ) {
	function gglcptch_lostpassword_check() {
		global $gglcptch_options;
		require_once( 'lib/recaptchalib.php' );
		$publickey	=	$gglcptch_options['public_key'];
		$privatekey	=	$gglcptch_options['private_key'];

		if ( ! $privatekey || ! $publickey )
			return;

		$resp = recaptcha_check_answer( $privatekey,
									$_SERVER['REMOTE_ADDR'],
									$_POST['recaptcha_challenge_field'],
									$_POST['recaptcha_response_field'] );
		if ( ! $resp->is_valid ) {
			wp_die( __( 'Error: You have entered an incorrect CAPTCHA value. Click the BACK button on your browser, and try again.', 'google_captcha' ) );
		} else
			return;
	}
}

if ( ! function_exists( 'gglcptch_action_links' ) ) {
	function gglcptch_action_links( $links, $file ) {
		/* Static so we don't call plugin_basename on every plugin row. */
		static $this_plugin;
		if ( ! $this_plugin )
			$this_plugin = plugin_basename(__FILE__);

		if ( $file == $this_plugin ){
			$settings_link = '<a href="admin.php?page=google-captcha.php">' . __( 'Settings', 'google_captcha' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
}

if ( ! function_exists( 'gglcptch_links' ) ) {
	function gglcptch_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			$links[]	=	'<a href="admin.php?page=google_captcha.php">' . __( 'Settings', 'google_captcha' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/google-captcha/faq/" target="_blank">' . __( 'FAQ', 'google_captcha' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'google_captcha' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists( 'gglcptch_delete_options' ) ) {
	function gglcptch_delete_options() {
		delete_option( 'gglcptch_options' );
		delete_site_option( 'gglcptch_options' );
	}
}

add_action( 'admin_menu', 'google_capthca_admin_menu' );
add_action( 'init', 'gglcptch_init' );
add_action( 'admin_init', 'gglcptch_admin_init' );
add_action( 'admin_enqueue_scripts', 'gglcptch_add_style' );
add_action( 'wp_enqueue_scripts', 'gglcptch_add_script' );

add_shortcode( 'bws_google_captcha', 'gglcptch_display' );

add_filter( 'plugin_action_links', 'gglcptch_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'gglcptch_links', 10, 2 );

register_uninstall_hook( __FILE__, 'gglcptch_delete_options' );
?>