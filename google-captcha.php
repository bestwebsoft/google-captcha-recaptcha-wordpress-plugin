<?php
/*
Plugin Name: Google Captcha (reCAPTCHA) by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: Plugin Google Captcha intended to prove that the visitor is a human being and not a spam robot.
Author: BestWebSoft
Text Domain: google-captcha
Domain Path: /languages
Version: 1.20
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
		add_submenu_page( 'bws_plugins', __( 'Google Captcha Settings', 'google-captcha' ), 'Google Captcha', 'manage_options', 'google-captcha.php', 'gglcptch_settings_page' );
	}
}

if ( ! function_exists( 'gglcptch_plugins_loaded' ) ) {
	function gglcptch_plugins_loaded() {
		/* Internationalization, first(!)  */
		load_plugin_textdomain( 'google-captcha', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists( 'gglcptch_init' ) ) {
	function gglcptch_init() {
		global $gglcptch_options, $gglcptch_allow_url_fopen, $gglcptch_plugin_info;

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		if ( empty( $gglcptch_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$gglcptch_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $gglcptch_plugin_info, '3.8', '3.1' );

		/* Get options from the database */
		$gglcptch_options = get_option( 'gglcptch_options' );

		/* Get option from the php.ini */
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
		global $gglcptch_options, $bws_plugin_info, $gglcptch_plugin_info, $gglcptch_allow_url_fopen, $gglcptch_default_options;

		$gglcptch_default_options = array(
			'public_key'				=> '',
			'private_key'				=> '',
			'login_form'				=> '1',
			'registration_form'			=> '1',
			'reset_pwd_form'			=> '1',
			'comments_form'				=> '1',
			'contact_form'				=> '0',
			'theme'						=> 'red',
			'theme_v2'					=> 'light',
			'recaptcha_version'			=> ( $gglcptch_allow_url_fopen ) ? 'v2' : 'v1',
			'plugin_option_version'		=> $gglcptch_plugin_info["Version"],
			'first_install'				=>	strtotime( "now" ),
			'display_settings_notice'	=> 1
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
			$gglcptch_default_options['display_settings_notice'] = 0;
			$gglcptch_options = array_merge( $gglcptch_default_options, $gglcptch_options );
			$gglcptch_options['plugin_option_version'] = $gglcptch_plugin_info["Version"];
			/* show pro features */
			$gglcptch_options['hide_premium_options'] = array();
			update_option( 'gglcptch_options', $gglcptch_options );
		}
	}
}

/* Display settings page */
if ( ! function_exists( 'gglcptch_settings_page' ) ) {
	function gglcptch_settings_page() {
		global $gglcptch_options, $gglcptch_plugin_info, $wp_version, $gglcptch_allow_url_fopen, $gglcptch_default_options;
		
		$plugin_basename = plugin_basename( __FILE__ );
		$message = $error = '';

		if ( ! isset( $_GET['action'] ) ) {

			$all_plugins = get_plugins();

			$gglcptch_sizes_v2 = array( 
				'normal'	=> __( 'Normal', 'google-captcha' ),
				'compact'	=> __( 'Compact', 'google-captcha' )
			);

			/* Private and public keys */
			$gglcptch_keys = array(
				'public' => array(
					'display_name'	=>	__( 'Site key', 'google-captcha' ),
					'form_name'		=>	'gglcptch_public_key',
					'error_msg'		=>	'',
				),
				'private' => array(
					'display_name'	=>	__( 'Secret Key', 'google-captcha' ),
					'form_name'		=>	'gglcptch_private_key',
					'error_msg'		=>	'',
				),
			);

			/* Checked forms */
			$gglcptch_forms = array(
				array( 'login_form', __( 'Login form', 'google-captcha' ) ),
				array( 'registration_form', __( 'Registration form', 'google-captcha' ) ),
				array( 'reset_pwd_form', __( 'Reset password form', 'google-captcha' ) ),
				array( 'comments_form', __( 'Comments form', 'google-captcha' ) ),
			);

			/* Google captcha themes */
			$gglcptch_themes = array(
				array( 'red', 'Red' ),
				array( 'white', 'White' ),
				array( 'blackglass', 'Blackglass' ),
				array( 'clean', 'Clean' ),
			);

			/* Save data for settings page */
			if ( isset( $_POST['gglcptch_form_submit'] ) && check_admin_referer( $plugin_basename, 'gglcptch_nonce_name' ) ) {
				if ( isset( $_POST['bws_hide_premium_options'] ) ) {
					$hide_result = bws_hide_premium_options( $gglcptch_options );
					$gglcptch_options = $hide_result['options'];
				}

				if ( ! $_POST['gglcptch_public_key'] || '' == $_POST['gglcptch_public_key'] ) {
					$gglcptch_keys['public']['error_msg'] = __( 'Enter site key', 'google-captcha' );
					$error = __( "WARNING: The captcha will not display while you don't fill key fields.", 'google-captcha' );
				} else
					$gglcptch_keys['public']['error_msg'] = '';

				if ( ! $_POST['gglcptch_private_key'] || '' == $_POST['gglcptch_private_key'] ) {
					$gglcptch_keys['private']['error_msg'] = __( 'Enter secret key', 'google-captcha' );
					$error = __( "WARNING: The captcha will not display while you don't fill key fields.", 'google-captcha' );
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
				$message = __( 'Settings saved', 'google-captcha' );
			}

			if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
				$gglcptch_options = $gglcptch_default_options;
				update_option( 'gglcptch_options', $gglcptch_options );
				$message = __( 'All plugin settings were restored.', 'google-captcha' );
			}
		} 

		$bws_hide_premium_options_check = bws_hide_premium_options_check( $gglcptch_options );

		/* GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
			$go_pro_result = bws_go_pro_tab_check( $plugin_basename, 'gglcptch_options' );
			if ( ! empty( $go_pro_result['error'] ) )
				$error = $go_pro_result['error'];
			elseif ( ! empty( $go_pro_result['message'] ) )
				$message = $go_pro_result['message'];
		} ?>
		<div class="wrap">
			<h2><?php _e( 'Google Captcha Settings', 'google-captcha' ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( ! isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-captcha.php"><?php _e( 'Settings', 'google-captcha' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/products/google-captcha/faq/" target="_blank"><?php _e( 'FAQ', 'google-captcha' ); ?></a>
				<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) echo ' nav-tab-active'; ?> bws_go_pro_tab" href="admin.php?page=google-captcha.php&amp;action=go_pro"><?php _e( 'Go PRO', 'google-captcha' ); ?></a>
			</h2>
			<?php bws_show_settings_notice(); ?>
			<div class="updated fade" <?php if ( "" == $message ) echo 'style="display:none"'; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="error" <?php if ( "" == $error ) echo 'style="display:none"'; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php if ( ! empty( $hide_result['message'] ) ) { ?>
				<div class="updated fade"><p><strong><?php echo $hide_result['message']; ?></strong></p></div>
			<?php }
			if ( ! $gglcptch_allow_url_fopen && $gglcptch_options['recaptcha_version'] == 'v2' ) {
				printf( '<div class="error"><p><strong>%s</strong> <a href="http://php.net/manual/en/filesystem.configuration.php" target="_blank">%s</a></p></div>',
					__( 'Google Captcha version 2 will not work correctly, since the option "allow_url_fopen" is disabled in the PHP settings of your hosting.', 'google-captcha' ),
					__( 'Read more.', 'google-captcha' )
				);
			}
			if ( ! isset( $_GET['action'] ) ) { 
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( $plugin_basename );
				} else { ?>
					<p><?php _e( 'If you would like to add the Google Captcha to your own form, just copy and paste this shortcode to your form:', 'google-captcha' ); ?> <span class="bws_code">[bws_google_captcha]</span></p>
					<form class="bws_form" method="post" action="admin.php?page=google-captcha.php">
						<h3><?php _e( 'Authentication', 'google-captcha' ); ?></h3>
						<p><?php printf( __( 'Before you are able to do something, you must to register %s here %s', 'google-captcha' ), '<a target="_blank" href="https://www.google.com/recaptcha/admin#list">','</a>.' ); ?></p>
						<p><?php _e( 'Enter site key and secret key, that you get after registration.', 'google-captcha' ); ?></p>
						<table id="gglcptch-keys" class="form-table">
							<?php foreach ( $gglcptch_keys as $key => $fields ) { ?>
								<tr valign="top">
									<th scope="row"><?php echo $fields['display_name']; ?></th>
									<td>
										<input type="text" name="<?php echo $fields['form_name']; ?>" value="<?php echo $gglcptch_options[ $key . '_key' ] ?>" maxlength="200" />
										<label class="gglcptch_error_msg"><?php echo $fields['error_msg']; ?></label>
									</td>
								</tr>
							<?php } ?>
						</table>
						<h3><?php _e( 'Options', 'google-captcha' ); ?></h3>
						<table class="form-table">
							<tr valign="top">
								<th scope="row"><?php _e( 'Enable reCAPTCHA for', 'google-captcha' ); ?></th>
								<td>
									<?php foreach ( $gglcptch_forms as $form ) : ?>
										<label><input type="checkbox" name="<?php echo 'gglcptch_' . $form[0]; ?>" value=<?php echo $form[0]; if ( '1' == $gglcptch_options[ $form[0] ] ) echo ' checked'; ?>> <?php echo $form[1]; ?></label><br />
									<?php endforeach;
									if ( isset( $all_plugins['contact-form-plugin/contact_form.php'] ) || isset( $all_plugins['contact-form-pro/contact_form_pro.php'] ) ) :
										if ( is_plugin_active( 'contact-form-plugin/contact_form.php' ) || is_plugin_active( 'contact-form-pro/contact_form_pro.php' ) ) : ?>
											<label><input type="checkbox" name="gglcptch_contact_form" value="contact_form"<?php if ( '1' == $gglcptch_options['contact_form'] ) echo ' checked'; ?>> Contact Form</label>
											<span class="bws_info">(<?php _e( 'powered by', 'google-captcha' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>)</span><br />
										<?php else : ?>
											<label><input type="checkbox" disabled name="gglcptch_contact_form" value="contact_form"<?php if ( '1' == $gglcptch_options['contact_form'] ) echo ' checked'; ?>> Contact Form</label>
											<span class="bws_info">(<?php _e( 'powered by', 'google-captcha' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>) <a href="<?php echo admin_url( 'plugins.php' ); ?>"><?php _e( 'Activate', 'google-captcha' ); ?> Contact Form</a></span><br />
										<?php endif;
									else : ?>
										<label><input type="checkbox" disabled name="gglcptch_contact_form" value="contact_form"<?php if ( '1' == $gglcptch_options['contact_form'] ) echo ' checked'; ?>> Contact Form</label>
										<span class="bws_info">(<?php _e( 'powered by', 'google-captcha' ); ?> <a href="http://bestwebsoft.com/products/">bestwebsoft.com</a>) <a href="http://bestwebsoft.com/products/contact-form/?k=d70b58e1739ab4857d675fed2213cedc&pn=75&v=<?php echo $gglcptch_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>"><?php _e( 'Download', 'google-captcha' ); ?> Contact Form</a></span><br />
									<?php endif; ?>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e( 'Hide reCAPTCHA in Comments form for', 'google-captcha' ); ?></th>
								<td>
									<?php foreach ( get_editable_roles() as $role => $fields) : ?>
										<label><input type="checkbox" name="<?php echo 'gglcptch_' . $role; ?>" value=<?php echo $role; if ( isset( $gglcptch_options[ $role ] ) && '1' == $gglcptch_options[ $role ] ) echo ' checked'; ?>> <?php echo $fields['name']; ?></label><br/>
									<?php endforeach; ?>
								</td>
							</tr>
							<tr valign="top">
								<th scope="row"><?php _e( 'reCAPTCHA version', 'google-captcha' ); ?></th>
								<td>
									<fieldset>
										<label><input type="radio" name="gglcptch_recaptcha_version" value="v1"<?php if ( 'v1' == $gglcptch_options['recaptcha_version'] ) echo ' checked="checked"'; ?>> <?php _e( 'version', 'google-captcha' ); ?> 1</label><br/>
										<label><input type="radio" name="gglcptch_recaptcha_version" value="v2"<?php if ( 'v2' == $gglcptch_options['recaptcha_version'] ) echo ' checked="checked"'; ?>> <?php _e( 'version', 'google-captcha' ); ?> 2</label>
									</fieldset>
								</td>
							</tr>
							<tr class="gglcptch_theme_v1" valign="top">
								<th scope="row">
									<?php _e( 'reCAPTCHA theme', 'google-captcha' ); ?>
									<br/><span class="bws_info">(<?php _e( 'for version', 'google-captcha' ); ?> 1)</span>
								</th>
								<td>
									<select name="gglcptch_theme">
										<?php foreach ( $gglcptch_themes as $theme ) : ?>
											<option value=<?php echo $theme[0]; if ( $theme[0] == $gglcptch_options['theme'] ) echo ' selected'; ?>> <?php echo $theme[1]; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr class="gglcptch_theme_v2" valign="top">
								<th scope="row">
									<?php _e( 'reCAPTCHA theme', 'google-captcha' ); ?>
									<br/><span class="bws_info">(<?php _e( 'for version', 'google-captcha' ); ?> 2)</span>
								</th>
								<td>
									<select name="gglcptch_theme_v2">
										<option value="light" <?php if ( 'light' == $gglcptch_options['theme_v2'] ) echo ' selected'; ?>>light</option>
										<option value="dark" <?php if ( 'dark' == $gglcptch_options['theme_v2'] ) echo ' selected'; ?>>dark</option>
									</select>
								</td>
							</tr>
						</table>
						<?php if ( ! $bws_hide_premium_options_check ) { ?>
							<div class="bws_pro_version_bloc">
								<div class="bws_pro_version_table_bloc">
									<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'google-captcha' ); ?>"></button>
									<div class="bws_table_bg"></div>
									<table class="form-table bws_pro_version">
										<tr valign="top">
											<th scope="row"><?php _e( 'reCAPTCHA language', 'google-captcha' ); ?></th>
											<td>
												<select disabled name="gglcptch_language">
													<option value="en" selected="selected">English (US)</option>
												</select>
												<div style="margin: 5px 0 0;">
													<?php $gglcptch_multilanguage = $gglcptch_use_multilanguage = $gglcptch_multilanguage_message = '';
													if ( array_key_exists( 'multilanguage/multilanguage.php', $all_plugins ) || array_key_exists( 'multilanguage-pro/multilanguage-pro.php', $all_plugins ) ) {
														if ( is_plugin_active( 'multilanguage/multilanguage.php' ) || is_plugin_active( 'multilanguage-pro/multilanguage-pro.php' ) ) {
															$gglcptch_use_multilanguage = ( $gglcptch_options["use_multilanguage_locale"] == 1 ) ? 'checked="checked"' : '';
														} else {
															$gglcptch_multilanguage = 'disabled="disabled"';
															$gglcptch_multilanguage_message = sprintf( '<a href="plugins.php">%s Multilanguage</a>', __( 'Activate', 'google-captcha' ) );
														}
													} else {
														$gglcptch_multilanguage = 'disabled="disabled"';
														$gglcptch_multilanguage_message = sprintf( '<a href="http://bestwebsoft.com/products/multilanguage/?k=390f8e0d92066f2b73a14429d02dcee7&pn=281&v=%s&wp_v=%s">%s Multilanguage</a>', $gglcptch_plugin_info["Version"], $wp_version, __( 'Download', 'google-captcha' )  );
													} ?>
													<input id="gglcptch_use_multilanguage_locale" type="checkbox" name="gglcptch_use_multilanguage_locale" value="1" <?php printf( '%s %s', $gglcptch_use_multilanguage, $gglcptch_multilanguage ) ?> /> 
													<label for="gglcptch_use_multilanguage_locale"><?php _e( 'Use the current site language', 'google-captcha' ); ?></label>&nbsp;<span class="bws_info">(<?php _e( 'Using', 'google-captcha' ); ?> Multilanguage by BestWebSoft) <?php echo $gglcptch_multilanguage_message; ?></span>
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
													printf(
														'<div class="gglcptch_size_v2"><label><input disabled type="radio" name="gglcptch_size_v2" value="%s"%s> %s</label></div>',
														$value,
														$name == 'Normal' ? ' checked="checked"' : '',
														$name
													);
												} ?>
												</fieldset>
											</td>
										</tr>
										<tr valign="top">
											<th scope="row">
												<strong>Contact Form 7</strong><br/>
												<?php _e( 'Enable CAPTCHA', 'google-captcha' ); ?> 
											</th>
											<td>
												<?php if ( array_key_exists( 'contact-form-7/wp-contact-form-7.php', $all_plugins ) ) {
													if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) { ?>
														<br/><input  disabled='disabled' type="checkbox" name="gglcptchpr_cf7" value="1" />
													<?php } else { ?>
														<span class="bws_info"><?php _e( 'You should', 'google-captcha' ); ?> <a href="<?php echo admin_url( 'plugins.php' ); ?>"><?php _e( 'activate', 'google-captcha' ); ?> Contact Form 7</a> <?php _e( 'to use this functionality', 'google-captcha' ); ?></span><br/>
														<input disabled='disabled' type="checkbox" name="gglcptchpr_cf7" value="1" />
													<?php }
												} else { ?>
													<span class="bws_info"><?php _e( 'You should', 'google-captcha' ); ?> <a target="_blank" href="http://wordpress.org/plugins/contact-form-7/"><?php _e( 'download', 'google-captcha' ); ?> Contact Form 7</a> <?php _e( 'to use this functionality', 'google-captcha' ); ?></span><br/>
													<input disabled='disabled' type="checkbox" name="gglcptchpr_cf7" value="1" />
												<?php } ?>
											</td>
										</tr>	
									</table>
								</div>
								<div class="bws_pro_version_tooltip">
									<div class="bws_info">
										<?php _e( 'Unlock premium options by upgrading to Pro version', 'google-captcha' ); ?>
									</div>
									<a class="bws_button" href="http://bestwebsoft.com/products/google-captcha/?k=b850d949ccc1239cab0da315c3c822ab&pn=109&v=<?php echo $gglcptch_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google Captcha Pro (reCAPTCHA)">
										<?php _e( 'Learn More', 'google-captcha' ); ?>
									</a>	
									<div class="clear"></div>					
								</div>
							</div>
						<?php } ?>
						<p class="submit">
							<input id="bws-submit-button" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'google-captcha' ); ?>" name="gglcptch_save_changes" />
							<input type="hidden" name="gglcptch_form_submit" value="submit" />
							<?php wp_nonce_field( $plugin_basename, 'gglcptch_nonce_name' ); ?>
						</p>						
					</form>
					<?php bws_form_restore_default_settings( $plugin_basename );
				}				
			} elseif ( 'go_pro' == $_GET['action'] ) {
				bws_go_pro_tab_show( $bws_hide_premium_options_check, $gglcptch_plugin_info, $plugin_basename, 'google-captcha.php', 'google-captcha-pro.php', 'google-captcha-pro/google-captcha-pro.php', 'google-captcha', 'b850d949ccc1239cab0da315c3c822ab', '109', isset( $go_pro_result['pro_plugin_is_activated'] ) );
			} 
			bws_plugin_reviews_block( $gglcptch_plugin_info['Name'], 'google-captcha' ); ?>
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
					__( 'To use Google Captcha you must get the keys from', 'google-captcha' ),
					__ ( 'here', 'google-captcha' ),
					__ ( 'and enter them on the', 'google-captcha' ),
					admin_url( '/admin.php?page=google-captcha.php' ),
					__( 'plugin setting page', 'google-captcha' )
				);
			}
			$content .= '</div>';
			$gglcptch_count++;
			return $content;
		}
		if ( isset( $gglcptch_options['recaptcha_version'] ) && 'v2' == $gglcptch_options['recaptcha_version'] ) {
			$content .= '<style type="text/css" media="screen">
					#gglcptch_error {
						color: #F00;
					}
				</style>
				<script type="text/javascript">
					var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '",
					gglcptch_error_msg = "' . __( 'Error: You have entered an incorrect CAPTCHA value.', 'google-captcha' ) . '";
				</script>';
			$content .= '<div class="g-recaptcha" data-sitekey="' . $publickey . '" data-theme="' . $gglcptch_options['theme_v2'] . '"></div>
			<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>
			<noscript>
				<div style="width: 302px;">
					<div style="width: 302px; height: 422px; position: relative;">
						<div style="width: 302px; height: 422px; position: absolute;">
							<iframe src="https://www.google.com/recaptcha/api/fallback?k=' . $publickey . '" frameborder="0" scrolling="no" style="width: 302px; height:422px; border-style: none;"></iframe>
						</div>
					</div>
					<div style="border-style: none; bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px; background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px; height: 60px; width: 300px;">
						<textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 40px; border: 1px solid #c1c1c1; margin: 10px 25px !important; padding: 0px; resize: none; "></textarea>
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
				__( 'Error: You have entered an incorrect CAPTCHA value.', 'google-captcha' )
			);
			if ( is_ssl() )
				$content .= gglcptch_recaptcha_get_html( $publickey, '', true );
			else
				$content .= gglcptch_recaptcha_get_html( $publickey );
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
			$reCaptcha = new gglcptch_ReCaptcha( $privatekey );
			$gglcptch_g_recaptcha_response = isset( $_POST["g-recaptcha-response"] ) ? $_POST["g-recaptcha-response"] : '';
			$resp = $reCaptcha->verifyResponse( $_SERVER["REMOTE_ADDR"], $gglcptch_g_recaptcha_response );

			if ( $resp != null && $resp->success )
				return $user;
			else {
				wp_clear_auth_cookie();
				$error = new WP_Error();
				$error->add( 'gglcptch_error', '<strong>' . __( 'Error', 'google-captcha' ) . '</strong>: ' . __( 'You have entered an incorrect CAPTCHA value.', 'google-captcha' ) );
				return $error;
			}
		} elseif ( isset( $_POST['recaptcha_challenge_field'] ) && isset( $_POST['recaptcha_response_field'] ) ) {
			require_once( 'lib/recaptchalib.php' );
			$gglcptch_recaptcha_challenge_field = isset( $_POST['recaptcha_challenge_field'] ) ? $_POST['recaptcha_challenge_field'] : '';
			$gglcptch_recaptcha_response_field = isset( $_POST['recaptcha_response_field'] ) ? $_POST['recaptcha_response_field'] : '';
			$resp = gglcptch_recaptcha_check_answer( $privatekey, $_SERVER['REMOTE_ADDR'], $gglcptch_recaptcha_challenge_field, $gglcptch_recaptcha_response_field );

			if ( ! $resp->is_valid ) {
				wp_clear_auth_cookie();
				$error = new WP_Error();
				$error->add( 'gglcptch_error', '<strong>' . __( 'Error', 'google-captcha' ) . '</strong>: ' . __( 'You have entered an incorrect CAPTCHA value.', 'google-captcha' ) );
				return $error;
			} else {
				return $user;
			}				
		} else {
			if ( isset( $_REQUEST['log'] ) && isset( $_REQUEST['pwd'] ) ) {
				/* captcha was not found in _REQUEST */
				$error = new WP_Error();
				$error->add( 'gglcptch_error', '<strong>' . __( 'Error', 'google-captcha' ) . '</strong>: ' . __( 'You have entered an incorrect CAPTCHA value.', 'google-captcha' ) );
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
			$reCaptcha = new gglcptch_ReCaptcha( $privatekey );
			$gglcptch_g_recaptcha_response = isset( $_POST["g-recaptcha-response"] ) ? $_POST["g-recaptcha-response"] : '';
			$resp = $reCaptcha->verifyResponse( $_SERVER["REMOTE_ADDR"], $gglcptch_g_recaptcha_response );

			if ( $resp != null && $resp->success )
				return;
			else
				wp_die( __( 'Error: You have entered an incorrect CAPTCHA value. Click the BACK button on your browser, and try again.', 'google-captcha' ) );
		} else {
			require_once( 'lib/recaptchalib.php' );
			$gglcptch_recaptcha_challenge_field = isset( $_POST['recaptcha_challenge_field'] ) ? $_POST['recaptcha_challenge_field'] : '';
			$gglcptch_recaptcha_response_field = isset( $_POST['recaptcha_response_field'] ) ? $_POST['recaptcha_response_field'] : '';
			$resp = gglcptch_recaptcha_check_answer( $privatekey, $_SERVER['REMOTE_ADDR'], $gglcptch_recaptcha_challenge_field, $gglcptch_recaptcha_response_field );
			if ( ! $resp->is_valid ) {
				wp_die( __( 'Error: You have entered an incorrect CAPTCHA value. Click the BACK button on your browser, and try again.', 'google-captcha' ) );
			} else
				return;
		}
	}
}

/* display google captcha in Contact form */
if ( ! function_exists( 'gglcptch_cf_display' ) ) {
    function gglcptch_cf_display( $content = "" ) {
        return $content . gglcptch_display();
    }
}

if ( ! function_exists( 'gglcptch_action_links' ) ) {
	function gglcptch_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			static $this_plugin;
			if ( ! $this_plugin )
				$this_plugin = plugin_basename(__FILE__);

			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=google-captcha.php">' . __( 'Settings', 'google-captcha' ) . '</a>';
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
				$links[]	=	'<a href="admin.php?page=google-captcha.php">' . __( 'Settings', 'google-captcha' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/google-captcha/faq/" target="_blank">' . __( 'FAQ', 'google-captcha' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'google-captcha' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists ( 'gglcptch_plugin_banner' ) ) {
	function gglcptch_plugin_banner() {
		global $hook_suffix, $gglcptch_plugin_info, $gglcptch_options;	
		if ( 'plugins.php' == $hook_suffix ) {
			if ( isset( $gglcptch_options['first_install'] ) && strtotime( '-1 week' ) > $gglcptch_options['first_install'] )
				bws_plugin_banner( $gglcptch_plugin_info, 'gglcptch', 'google-captcha', '676d9558f9786ab41d7de35335cf5c4d', '109', '//ps.w.org/google-captcha/assets/icon-128x128.png' );
			
			bws_plugin_banner_to_settings( $gglcptch_plugin_info, 'gglcptch_options', 'google-captcha', 'admin.php?page=google-captcha.php' );
		}
	}
}

/* Check Google Captcha in shortcode and contact form */
if ( ! function_exists( 'gglcptch_captcha_check' ) ) {
	function gglcptch_captcha_check() {
		$gglcptch_options = get_option( 'gglcptch_options' );
		$privatekey = $gglcptch_options['private_key'];

		if ( isset( $gglcptch_options['recaptcha_version'] ) && 'v2' == $gglcptch_options['recaptcha_version'] ) {
			require_once( 'lib_v2/recaptchalib.php' );
			$reCaptcha = new gglcptch_ReCaptcha( $privatekey );
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
			$resp = gglcptch_recaptcha_check_answer( $privatekey, $_SERVER['REMOTE_ADDR'], $gglcptch_recaptcha_challenge_field, $gglcptch_recaptcha_response_field );
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
		global $wpdb;
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( 'gglcptch_options' );
			}
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'gglcptch_options' );
		}
	}
}

add_action( 'admin_menu', 'google_capthca_admin_menu' );

add_action( 'init', 'gglcptch_init' );
add_action( 'admin_init', 'gglcptch_admin_init' );

add_action( 'plugins_loaded', 'gglcptch_plugins_loaded' );

add_action( 'admin_enqueue_scripts', 'gglcptch_add_style' );
add_action( 'wp_enqueue_scripts', 'gglcptch_add_script' );

add_shortcode( 'bws_google_captcha', 'gglcptch_display' );

add_filter( 'plugin_action_links', 'gglcptch_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'gglcptch_links', 10, 2 );

add_action( 'admin_notices', 'gglcptch_plugin_banner' );

add_action( 'wp_ajax_gglcptch_captcha_check', 'gglcptch_captcha_check' );
add_action( 'wp_ajax_nopriv_gglcptch_captcha_check', 'gglcptch_captcha_check' );

register_uninstall_hook( __FILE__, 'gglcptch_delete_options' );