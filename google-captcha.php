<?php
/*
Plugin Name: Google Captcha (reCAPTCHA) by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: Plugin Google Captcha intended to prove that the visitor is a human being and not a spam robot.
Author: BestWebSoft
Version: 1.17
Author URI: http://bestwebsoft.com/
License: GPLv3 or later
*/

/*  © Copyright 2015  BestWebSoft  ( http://support.bestwebsoft.com )

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
		bws_add_general_menu( plugin_basename( __FILE__ ) );
		add_submenu_page( 'bws_plugins', __( 'Google Captcha Settings', 'google_captcha' ), 'Google Captcha', 'manage_options', 'google-captcha.php', 'gglcptch_settings_page' );
	}
}

if ( ! function_exists( 'gglcptch_init' ) ) {
	function gglcptch_init() {
		global $gglcptch_options, $gglcptch_allow_url_fopen, $gglcptch_plugin_info;

		load_plugin_textdomain( 'google_captcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_functions.php' );

		if ( empty( $gglcptch_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$gglcptch_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version  */
		bws_wp_version_check( plugin_basename( __FILE__ ), $gglcptch_plugin_info, "3.0" );

		/* Get options from the database */
		$gglcptch_options = get_option( 'gglcptch_options' );

		/* Get option from the php.ini */
		if ( isset( $gglcptch_options['recaptcha_version'] ) && $gglcptch_options['recaptcha_version'] == 'v2' )
			$gglcptch_allow_url_fopen = ( ini_get( 'allow_url_fopen' ) != 1 ) ? false : true;

		/* Add hooks */
		if ( '1' == $gglcptch_options['login_form'] ) {
			add_action( 'login_form', 'gglcptch_login_display' );
			add_action( 'authenticate', 'gglcptch_login_check', 21, 1 );
		}

		if ( '1' == $gglcptch_options['comments_form'] ) {
			add_action( 'comment_form_after_fields', 'gglcptch_commentform_display' );
			add_action( 'comment_form_logged_in_after', 'gglcptch_commentform_display' );
			add_action( 'pre_comment_on_post', 'gglcptch_commentform_check' );
		}

		if ( '1' == $gglcptch_options['reset_pwd_form'] ) {
			add_action( 'lostpassword_form', 'gglcptch_login_display' );
			add_action( 'lostpassword_post', 'gglcptch_lostpassword_check' );
		}

		if ( '1' == $gglcptch_options['registration_form'] ) {
			add_action( 'register_form', 'gglcptch_login_display' );
			add_action( 'register_post', 'gglcptch_lostpassword_check' );
			/* for multisite */
			add_action( 'signup_extra_fields', 'gglcptch_login_display' );
		}
		
		if ( '1' == $gglcptch_options['contact_form'] ) {
			add_filter( 'cntctfrm_display_captcha', 'gglcptch_cf_display' );
			add_filter( 'cntctfrmpr_display_captcha', 'gglcptch_cf_display' );
		}
	}
}

if ( ! function_exists( 'gglcptch_admin_init' ) ) {
	function gglcptch_admin_init() {
		global $bws_plugin_info, $gglcptch_plugin_info;

		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '109', 'version' => $gglcptch_plugin_info["Version"] );

		/* Call register settings function */
		if ( isset( $_GET['page'] ) && "google-captcha.php" == $_GET['page'] )
			register_gglcptch_settings();
	}
}

/* Add google captcha styles */
if ( ! function_exists( 'gglcptch_add_style' ) ) {
	function gglcptch_add_style() {
		if ( isset( $_REQUEST['page'] ) && 'google-captcha.php' == $_REQUEST['page'] ) {
			wp_enqueue_style( 'gglcptch_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'gglcptch_admin_script', plugins_url( 'js/admin_script.js', __FILE__ ), array( 'jquery' ) );
		}
	}
}

/* Add google captcha scripts */
if ( ! function_exists( 'gglcptch_add_script' ) ) {
	function gglcptch_add_script() {
		wp_enqueue_script( 'gglcptch_script', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery' ) );
		wp_localize_script( 'gglcptch_script', 'gglcptch_vars', array( 'nonce' => wp_create_nonce( 'gglcptch_recaptcha_nonce' ) ) );
	}
}
/* Google catpcha settings */
if ( ! function_exists( 'register_gglcptch_settings' ) ) {
	function register_gglcptch_settings() {
		global $gglcptch_options, $bws_plugin_info, $gglcptch_plugin_info;

		$gglcptch_default_options = array(
			'public_key'			=> '',
			'private_key'			=> '',
			'login_form'			=> '1',
			'registration_form'		=> '1',
			'reset_pwd_form'		=> '1',
			'comments_form'			=> '1',
			'contact_form'			=> '0',
			'theme'					=> 'red',
			'theme_v2'				=> 'light',
			'recaptcha_version'		=> 'v1',
			'plugin_option_version'	=> $gglcptch_plugin_info["Version"]
		);

		foreach ( get_editable_roles() as $role => $fields ) {
			$gglcptch_default_options[ $role ] = '0';
		}

		/* Install the option defaults */
		if ( ! get_option( 'gglcptch_options' ) )
			add_option( 'gglcptch_options', $gglcptch_default_options );
		/* Get options from the database */
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
		global $gglcptch_options, $gglcptch_plugin_info, $wp_version, $gglcptch_allow_url_fopen;

		/* Private and public keys */
		$gglcptch_keys = array(
			'public' => array(
				'display_name'	=>	__( 'Site key', 'google_captcha' ),
				'form_name'		=>	'gglcptch_public_key',
				'error_msg'		=>	'',
			),
			'private' => array(
				'display_name'	=>	__( 'Secret Key', 'google_captcha' ),
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
				$gglcptch_keys['public']['error_msg'] = __( 'Enter site key', 'google_captcha' );
				$error = __( "WARNING: The captcha will not display while you don't fill key fields.", 'google_captcha' );
			} else
				$gglcptch_keys['public']['error_msg'] = '';

			if ( ! $_POST['gglcptch_private_key'] || '' == $_POST['gglcptch_private_key'] ) {
				$gglcptch_keys['private']['error_msg'] = __( 'Enter secret key', 'google_captcha' );
				$error = __( "WARNING: The captcha will not display while you don't fill key fields.", 'google_captcha' );
			} else
				$gglcptch_keys['private']['error_msg'] = '';

			$gglcptch_options['public_key']			=	trim( stripslashes( esc_html( $_POST['gglcptch_public_key'] ) ) );
			$gglcptch_options['private_key']		=	trim( stripslashes( esc_html( $_POST['gglcptch_private_key'] ) ) );
			$gglcptch_options['login_form']			=	isset( $_POST['gglcptch_login_form'] ) ? 1 : 0;
			$gglcptch_options['registration_form']	=	isset( $_POST['gglcptch_registration_form'] ) ? 1 : 0;
			$gglcptch_options['reset_pwd_form']		=	isset( $_POST['gglcptch_reset_pwd_form'] ) ? 1 : 0;
			$gglcptch_options['comments_form']		=	isset( $_POST['gglcptch_comments_form'] ) ? 1 : 0;
			$gglcptch_options['contact_form']		=	isset( $_POST['gglcptch_contact_form'] ) ? 1 : 0;
			$gglcptch_options['recaptcha_version']	=	$_POST['gglcptch_recaptcha_version'];
			$gglcptch_options['theme']				=	$_POST['gglcptch_theme'];
			$gglcptch_options['theme_v2']			=	$_POST['gglcptch_theme_v2'];

			foreach ( get_editable_roles() as $role => $fields ) {
				$gglcptch_options[ $role ] = isset( $_POST[ 'gglcptch_' . $role ] ) ? 1 : 0;
			}

			update_option( 'gglcptch_options', $gglcptch_options );

			if ( ! $gglcptch_allow_url_fopen && $gglcptch_options['recaptcha_version'] == 'v2' )
				$gglcptch_allow_url_fopen = ( ini_get( 'allow_url_fopen' ) != 1 ) ? false : true;
		} ?>
		<div class="wrap">
			<h2><?php _e( 'Google Captcha Settings', 'google_captcha' ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active" href="admin.php?page=google-captcha.php"><?php _e( 'Settings', 'google_captcha' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/products/google-captcha/faq" target="_blank"><?php _e( 'FAQ', 'google_captcha' ); ?></a>
			</h2>
			<div id="gglcptch_settings_notice" class="updated fade" style="display:none"><p><strong><?php _e( "Notice:", 'google_captcha' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'google_captcha' ); ?></p></div>
			<div class="updated fade" <?php if ( ! isset( $_POST['gglcptch_save_changes'] ) || "" != $error ) echo 'style="display:none"'; ?>><p><strong><?php _e( 'Settings saved', 'google_captcha' ); ?></strong></p></div>
			<div class="error" <?php if ( "" == $error ) echo 'style="display:none"'; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php if ( ! $gglcptch_allow_url_fopen && $gglcptch_options['recaptcha_version'] == 'v2' ) {
				printf( '<div class="error"><p><strong>%s</strong> <a href="http://php.net/manual/en/filesystem.configuration.php" target="_blank">%s</a></p></div>',
					__( 'Google Captcha version 2 will not work correctly, since the option "allow_url_fopen" is disabled in the PHP settings of your hosting.', 'google_captcha' ),
					__( 'Read more.', 'google_captcha' )
				);
			} ?>
			<p><?php _e( 'If you would like to add the Google Captcha to your own form, just copy and paste this shortcode to your post or page:', 'google_captcha' ); echo ' [bws_google_captcha]'; ?></p>
			<form id="gglcptch_settings_form" method="post" action="admin.php?page=google-captcha.php">
				<h3><?php _e( 'Authentication', 'google_captcha' ); ?></h3>
				<p><?php printf( __( 'Before you are able to do something, you must to register %s here %s', 'google_captcha' ), '<a target="_blank" href="https://www.google.com/recaptcha/admin#list">','</a>.' ); ?></p>
				<p><?php _e( 'Enter site key and secret key, that you get after registration.', 'google_captcha' ); ?></p>
				<table id="gglcptch-keys" class="form-table">
					<?php foreach ( $gglcptch_keys as $key => $fields ) : ?>
						<tr valign="top">
							<th scope="row"><?php echo $fields['display_name']; ?></th>
							<td>
								<input type="text" name="<?php echo $fields['form_name']; ?>" value="<?php echo $gglcptch_options[ $key . '_key' ] ?>" maxlength="200" />
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
								<label><input type="checkbox" name="<?php echo 'gglcptch_' . $form[0]; ?>" value=<?php echo $form[0]; if ( '1' == $gglcptch_options[ $form[0] ] ) echo ' checked'; ?>> <?php echo $form[1]; ?></label><br />
							<?php endforeach;
							$gglcptch_all_plugins = get_plugins();
							$gglcptch_cntctfrm_installed = ( isset( $gglcptch_all_plugins['contact-form-plugin/contact_form.php'] ) || isset( $gglcptch_all_plugins['contact-form-pro/contact_form_pro.php'] ) ) ? true : false;
							$gglcptch_cntctfrm_activated = ( is_plugin_active( 'contact-form-plugin/contact_form.php' ) || is_plugin_active( 'contact-form-pro/contact_form_pro.php' ) ) ? true : false;
							if ( $gglcptch_cntctfrm_installed ) :
								if ( $gglcptch_cntctfrm_activated ) : ?>
									<label><input type="checkbox" name="gglcptch_contact_form" value="contact_form"<?php if ( '1' == $gglcptch_options['contact_form'] ) echo ' checked'; ?>> <?php _e( 'Contact form', 'google_captcha' ); ?></label>
									<span class="gglcptch_span">(<?php _e( 'powered by', 'google_captcha' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>)</span><br />
								<?php else : ?>
									<label><input type="checkbox" disabled name="gglcptch_contact_form" value="contact_form"<?php if ( '1' == $gglcptch_options['contact_form'] ) echo ' checked'; ?>> <?php _e( 'Contact form', 'google_captcha' ); ?></label>
									<span class="gglcptch_span">(<?php _e( 'powered by', 'google_captcha' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>) <a href="<?php echo bloginfo("url"); ?>/wp-admin/plugins.php"><?php _e( 'Activate contact form', 'google_captcha' ); ?></a></span><br />
								<?php endif;
							else : ?>
								<label><input type="checkbox" disabled name="gglcptch_contact_form" value="contact_form"<?php if ( '1' == $gglcptch_options['contact_form'] ) echo ' checked'; ?>> <?php _e( 'Contact form', 'google_captcha' ); ?></label>
								<span class="gglcptch_span">(<?php _e( 'powered by', 'google_captcha' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>) <a href="http://bestwebsoft.com/products/contact-form/?k=d70b58e1739ab4857d675fed2213cedc&pn=75&v=<?php echo $gglcptch_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>"><?php _e( 'Download contact form', 'google_captcha' ); ?></a></span><br />
							<?php endif; ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Hide captcha for:', 'google_captcha' ); ?></th>
						<td>
							<?php foreach ( get_editable_roles() as $role => $fields) : ?>
								<label><input type="checkbox" name="<?php echo 'gglcptch_' . $role; ?>" value=<?php echo $role; if ( '1' == $gglcptch_options[ $role ] ) echo ' checked'; ?>> <?php echo $fields['name']; ?></label><br/>
							<?php endforeach; ?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'reCAPTCHA version:', 'google_captcha' ); ?></th>
						<td>
							<label><input type="radio" name="gglcptch_recaptcha_version" value="v1"<?php if ( 'v1' == $gglcptch_options['recaptcha_version'] ) echo ' checked="checked"'; ?>> <?php _e( 'version', 'google_captcha' ); ?> 1</label><br/>
							<label><input type="radio" name="gglcptch_recaptcha_version" value="v2"<?php if ( 'v2' == $gglcptch_options['recaptcha_version'] ) echo ' checked="checked"'; ?>> <?php _e( 'version', 'google_captcha' ); ?> 2</label>
						</td>
					</tr>
					<tr id="gglcptch_theme_v1" valign="top">
						<th scope="row">
							<?php _e( 'Theme:', 'google_captcha' ); ?>
							<br/><span class="gglcptch_span">(<?php _e( 'for reCAPTCHA version', 'google_captcha' ); ?> 1)</span>
						</th>
						<td>
							<select name="gglcptch_theme">
								<?php foreach ( $gglcptch_themes as $theme ) : ?>
									<option value=<?php echo $theme[0]; if ( $theme[0] == $gglcptch_options['theme'] ) echo ' selected'; ?>> <?php echo $theme[1]; ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr id="gglcptch_theme_v2" valign="top">
						<th scope="row">
							<?php _e( 'Theme:', 'google_captcha' ); ?>
							<br/><span class="gglcptch_span">(<?php _e( 'for reCAPTCHA version', 'google_captcha' ); ?> 2)</span>
						</th>
						<td>
							<select name="gglcptch_theme_v2">
								<option value="light" <?php if ( 'light' == $gglcptch_options['theme_v2'] ) echo ' selected'; ?>>light</option>
								<option value="dark" <?php if ( 'dark' == $gglcptch_options['theme_v2'] ) echo ' selected'; ?>>dark</option>
							</select>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'google_captcha' ); ?>" name="gglcptch_save_changes" />
				</p>
				<?php wp_nonce_field( plugin_basename( __FILE__ ), 'gglcptch_nonce_name' ); ?>
			</form>
			<?php bws_plugin_reviews_block( $gglcptch_plugin_info['Name'], 'google-captcha' ); ?>
		</div>
	<?php }
}

/* Checking current user role */
if ( ! function_exists( 'gglcptch_check_role' ) ) {
	function gglcptch_check_role() {
		global $current_user, $gglcptch_options;
		if ( ! is_user_logged_in() )
			return false;
		if ( ! empty( $current_user->roles[0] ) ) {
			$role = $current_user->roles[0];
			if ( '1' == $gglcptch_options[ $role ] )
				return true;
			else
				return false;
		} else
			return false;
	}
}

/* Display google captcha via shortcode */
if ( ! function_exists( 'gglcptch_display' ) ) {
	function gglcptch_display( $content = false ) {
		if ( gglcptch_check_role() )
			return;
		global $gglcptch_options, $gglcptch_count, $gglcptch_allow_url_fopen;
		if ( empty( $gglcptch_count ) ) {
			$gglcptch_count = 1;
		}
		if ( $gglcptch_count > 1 ) {
			return $content;
		}
		if ( ! $gglcptch_allow_url_fopen && isset( $gglcptch_options['recaptcha_version'] ) && $gglcptch_options['recaptcha_version'] == 'v2' ) {
			$content .= '<div class="gglcptch allow_url_fopen_off"></div>';
			$gglcptch_count++;
			return $content;
		}
		$publickey = $gglcptch_options['public_key'];
		$privatekey = $gglcptch_options['private_key'];
		$content .= '<div class="gglcptch">';
		if ( ! $privatekey || ! $publickey ) {
			if ( current_user_can( 'manage_options' ) ) {
				$content .= sprintf(
					'<strong>%s <a target="_blank" href="https://www.google.com/recaptcha/admin#list">%s</a> %s <a target="_blank" href="%s">%s</a>.</strong>',
					__( 'To use Google Captcha you must get the keys from', 'google_captcha' ),
					__ ( 'here', 'google_captcha' ),
					__ ( 'and enter them on the', 'google_captcha' ),
					admin_url( '/admin.php?page=google-captcha.php' ),
					__( 'plugin setting page', 'google_captcha' )
				);
			}
			$content .= '</div>';
			$gglcptch_count++;
			return $content;
		}
		if ( isset( $gglcptch_options['recaptcha_version'] ) && 'v2' == $gglcptch_options['recaptcha_version'] ) {
			require_once( 'lib_v2/recaptchalib.php' );
			$reCaptcha = new ReCaptcha( $privatekey );
			$content .= '<style type="text/css" media="screen">
					#gglcptch_error {
						color: #F00;
					}
				</style>
				<script type="text/javascript">
					var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '",
					gglcptch_error_msg = "' . __( 'Error: You have entered an incorrect CAPTCHA value.', 'google_captcha' ) . '";
				</script>';
			$content .= '<div class="g-recaptcha" data-sitekey="' . $publickey . '" data-theme="' . $gglcptch_options['theme_v2'] . '"></div>
			<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>
			<noscript>
				<div style="width: 302px; height: 352px;">
					<div style="width: 302px; height: 352px; position: relative;">
						<div style="width: 302px; height: 352px; position: absolute;">
							<iframe src="https://www.google.com/recaptcha/api/fallback?k=' . $publickey . '" frameborder="0" scrolling="no" style="width: 302px; height:352px; border-style: none;"></iframe>
						</div>
						<div style="width: 250px; height: 80px; position: absolute; border-style: none; bottom: 21px; left: 25px; margin: 0px; padding: 0px; right: 25px;">
							<textarea id="g-recaptcha-response" name="g-recaptcha-response"	class="g-recaptcha-response" style="width: 250px; height: 80px; border: 1px solid #c1c1c1; margin: 0px; padding: 0px; resize: none;" value=""></textarea>
						</div>
					</div>
				</div>
			</noscript>';
		} else {
			require_once( 'lib/recaptchalib.php' );
			$content .= sprintf(
				'<style type="text/css" media="screen">
					#recaptcha_response_field {
						max-height: 35px;
					}
					#gglcptch_error {
						color: #F00;
					}
					.gglcptch table#recaptcha_table {
					    table-layout: auto;
					}
				</style>
				<script type="text/javascript">
					var RecaptchaOptions = { theme : "%s" },
					ajaxurl = "%s",
					gglcptch_error_msg = "%s";
				</script>',
				$gglcptch_options['theme'],
				admin_url( 'admin-ajax.php' ),
				__( 'Error: You have entered an incorrect CAPTCHA value.', 'google_captcha' )
			);
			if ( is_ssl() )
				$content .= recaptcha_get_html( $publickey, '', true );
			else
				$content .= recaptcha_get_html( $publickey );
		}
		$content .= '</div>';
		$gglcptch_count++;
		return $content;
	}
}

/* Add google captcha to the login form */
if ( ! function_exists( 'gglcptch_login_display' ) ) {
	function gglcptch_login_display() {
		global $gglcptch_options;
		if ( isset( $gglcptch_options['recaptcha_version'] ) && 'v2' == $gglcptch_options['recaptcha_version'] ) {
			$from_width = 302;
		} else {
			$from_width = 320;
			if ( 'clean' == $gglcptch_options['theme'] )
				$from_width = 450;
		} ?>
		<style type="text/css" media="screen">
			#loginform,
			#lostpasswordform,
			#registerform {
				width: <?php echo $from_width; ?>px !important;
			}
			.message {
				width: <?php echo $from_width + 20; ?>px !important;
			}
			#loginform .gglcptch {
				margin-bottom: 10px;
			}
		</style>
		<?php echo gglcptch_display();
		return true;
	}
}

/* Check google captcha in login form */
if ( ! function_exists( 'gglcptch_login_check' ) ) {
	function gglcptch_login_check( $user ) {
		global $gglcptch_options, $gglcptch_allow_url_fopen;

		if ( ! $gglcptch_allow_url_fopen && isset( $gglcptch_options['recaptcha_version'] ) && $gglcptch_options['recaptcha_version'] == 'v2' ) {
			return $user;
		}

		$publickey = $gglcptch_options['public_key'];
		$privatekey = $gglcptch_options['private_key'];

		if ( ! $privatekey || ! $publickey ) {
			return $user;
		}

		if ( isset( $_REQUEST['g-recaptcha-response'] ) && isset( $gglcptch_options['recaptcha_version'] ) && 'v2' == $gglcptch_options['recaptcha_version'] ) {
			require_once( 'lib_v2/recaptchalib.php' );
			$reCaptcha = new ReCaptcha( $privatekey );
			$gglcptch_g_recaptcha_response = isset( $_POST["g-recaptcha-response"] ) ? $_POST["g-recaptcha-response"] : '';
			$resp = $reCaptcha->verifyResponse( $_SERVER["REMOTE_ADDR"], $gglcptch_g_recaptcha_response );

			if ( $resp != null && $resp->success )
				return $user;
			else {
				wp_clear_auth_cookie();
				$error = new WP_Error();
				$error->add( 'gglcptch_error', '<strong>' . __( 'Error', 'google_captcha' ) . '</strong>: ' . __( 'You have entered an incorrect CAPTCHA value.', 'google_captcha' ) );
				return $error;
			}
		} elseif ( isset( $_POST['recaptcha_challenge_field'] ) && isset( $_POST['recaptcha_response_field'] ) ) {
			require_once( 'lib/recaptchalib.php' );
			$gglcptch_recaptcha_challenge_field = isset( $_POST['recaptcha_challenge_field'] ) ? $_POST['recaptcha_challenge_field'] : '';
			$gglcptch_recaptcha_response_field = isset( $_POST['recaptcha_response_field'] ) ? $_POST['recaptcha_response_field'] : '';
			$resp = recaptcha_check_answer( $privatekey, $_SERVER['REMOTE_ADDR'], $gglcptch_recaptcha_challenge_field, $gglcptch_recaptcha_response_field );

			if ( ! $resp->is_valid ) {
				wp_clear_auth_cookie();
				$error = new WP_Error();
				$error->add( 'gglcptch_error', '<strong>' . __( 'Error', 'google_captcha' ) . '</strong>: ' . __( 'You have entered an incorrect CAPTCHA value.', 'google_captcha' ) );
				return $error;
			} else {
				return $user;
			}				
		} else {
			if ( isset( $_REQUEST['log'] ) && isset( $_REQUEST['pwd'] ) ) {
				/* captcha was not found in _REQUEST */
				$error = new WP_Error();
				$error->add( 'gglcptch_error', '<strong>' . __( 'Error', 'google_captcha' ) . '</strong>: ' . __( 'You have entered an incorrect CAPTCHA value.', 'google_captcha' ) );
				return $error;
			} else {
				/* it is not a submit */
				return $user;
			}
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

/* Check google captcha in lostpassword form */
if ( ! function_exists( 'gglcptch_lostpassword_check' ) ) {
	function gglcptch_lostpassword_check() {
		global $gglcptch_options, $gglcptch_allow_url_fopen;

		if ( ! $gglcptch_allow_url_fopen && isset( $gglcptch_options['recaptcha_version'] ) && $gglcptch_options['recaptcha_version'] == 'v2' ) {
			return;
		}

		$publickey	=	$gglcptch_options['public_key'];
		$privatekey	=	$gglcptch_options['private_key'];

		if ( ! $privatekey || ! $publickey )
			return;

		if ( isset( $gglcptch_options['recaptcha_version'] ) && 'v2' == $gglcptch_options['recaptcha_version'] ) {
			require_once( 'lib_v2/recaptchalib.php' );
			$reCaptcha = new ReCaptcha( $privatekey );
			$gglcptch_g_recaptcha_response = isset( $_POST["g-recaptcha-response"] ) ? $_POST["g-recaptcha-response"] : '';
			$resp = $reCaptcha->verifyResponse( $_SERVER["REMOTE_ADDR"], $gglcptch_g_recaptcha_response );
			if ( $resp != null && $resp->success )
				return;
			else
				wp_die( __( 'Error: You have entered an incorrect CAPTCHA value. Click the BACK button on your browser, and try again.', 'google_captcha' ) );
		} else {
			require_once( 'lib/recaptchalib.php' );
			$gglcptch_recaptcha_challenge_field = isset( $_POST['recaptcha_challenge_field'] ) ? $_POST['recaptcha_challenge_field'] : '';
			$gglcptch_recaptcha_response_field = isset( $_POST['recaptcha_response_field'] ) ? $_POST['recaptcha_response_field'] : '';
			$resp = recaptcha_check_answer( $privatekey, $_SERVER['REMOTE_ADDR'], $gglcptch_recaptcha_challenge_field, $gglcptch_recaptcha_response_field );
			if ( ! $resp->is_valid ) {
				wp_die( __( 'Error: You have entered an incorrect CAPTCHA value. Click the BACK button on your browser, and try again.', 'google_captcha' ) );
			} else
				return;
		}
	}
}

/* display google captcha in Contact form */
if ( ! function_exists( 'gglcptch_cf_display' ) ) {
	function gglcptch_cf_display() {
		return gglcptch_display();
	}
}

if ( ! function_exists( 'gglcptch_action_links' ) ) {
	function gglcptch_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			static $this_plugin;
			if ( ! $this_plugin )
				$this_plugin = plugin_basename(__FILE__);

			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=google-captcha.php">' . __( 'Settings', 'google_captcha' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

if ( ! function_exists( 'gglcptch_links' ) ) {
	function gglcptch_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[]	=	'<a href="admin.php?page=google_captcha.php">' . __( 'Settings', 'google_captcha' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/google-captcha/faq/" target="_blank">' . __( 'FAQ', 'google_captcha' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'google_captcha' ) . '</a>';
		}
		return $links;
	}
}

/* Check Google Captcha in shortcode and contact form */
if ( ! function_exists( 'gglcptch_captcha_check' ) ) {
	function gglcptch_captcha_check() {
		$gglcptch_options = get_option( 'gglcptch_options' );
		$privatekey = $gglcptch_options['private_key'];

		if ( isset( $gglcptch_options['recaptcha_version'] ) && 'v2' == $gglcptch_options['recaptcha_version'] ) {
			require_once( 'lib_v2/recaptchalib.php' );
			$reCaptcha = new ReCaptcha( $privatekey );
			$gglcptch_g_recaptcha_response = isset( $_POST["g-recaptcha-response"] ) ? $_POST["g-recaptcha-response"] : '';
			$resp = $reCaptcha->verifyResponse( $_SERVER["REMOTE_ADDR"], $gglcptch_g_recaptcha_response );
			if ( $resp != null && $resp->success )
				echo "success";
			else
				echo "error";
		} else {
			require_once( 'lib/recaptchalib.php' );
			$gglcptch_recaptcha_challenge_field = isset( $_POST['recaptcha_challenge_field'] ) ? $_POST['recaptcha_challenge_field'] : '';
			$gglcptch_recaptcha_response_field = isset( $_POST['recaptcha_response_field'] ) ? $_POST['recaptcha_response_field'] : '';
			$resp = recaptcha_check_answer( $privatekey, $_SERVER['REMOTE_ADDR'], $gglcptch_recaptcha_challenge_field, $gglcptch_recaptcha_response_field );
			if ( ! $resp->is_valid )
				echo "error";
			else
				echo "success";
		}
		die();
	}
}

/* Check JS enabled for comment form  */
if ( ! function_exists( 'gglcptch_commentform_check' ) ) {
	function gglcptch_commentform_check() {
		if ( isset( $_POST['gglcptch_test_enable_js_field'] ) ) {
			if ( wp_verify_nonce( $_POST['gglcptch_test_enable_js_field'], 'gglcptch_recaptcha_nonce' ) ) 
				return;
			else {
				if ( gglcptch_check_role() )
					return;
				gglcptch_lostpassword_check();
			}
		} else {
			if ( gglcptch_check_role() )
				return;
			gglcptch_lostpassword_check();
		}
	}
}

if ( ! function_exists( 'gglcptch_delete_options' ) ) {
	function gglcptch_delete_options() {
		delete_option( 'gglcptch_options' );
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

add_action( 'wp_ajax_gglcptch_captcha_check', 'gglcptch_captcha_check' );
add_action( 'wp_ajax_nopriv_gglcptch_captcha_check', 'gglcptch_captcha_check' );

register_uninstall_hook( __FILE__, 'gglcptch_delete_options' );
?>