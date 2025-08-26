<?php
/**
 * Contains the extending functionality
 *
 * @since 1.32
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! function_exists( 'gglcptch_get_forms' ) ) {
	/**
	 * All forms for reCaptcha
	 *
	 * @return  array    $gglcptch_forms   Forms for reCaptcha.
	 */
	function gglcptch_get_forms() {
		global $gglcptch_forms;

		$default_forms = array(
			'login_form'           => array( 'form_name' => __( 'Login form', 'google-captcha' ) ),
			'registration_form'    => array( 'form_name' => __( 'Registration form', 'google-captcha' ) ),
			'reset_pwd_form'       => array( 'form_name' => __( 'Reset password form', 'google-captcha' ) ),
			'password_form'        => array( 'form_name' => __( 'Protected post password form', 'google-captcha' ) ),
			'comments_form'        => array( 'form_name' => __( 'Comments form', 'google-captcha' ) ),
			'contact_form'         => array( 'form_name' => 'Contact Form' ),
			'testimonials'         => array( 'form_name' => __( 'Testimonials', 'google-captcha' ) ),
			'frm_contact_form'     => array( 'form_name' => __( 'Formidable Contact Form', 'google-captcha' ) ),
			'bws_login_form'       => array( 'form_name' => __( 'BWS Login form', 'google-captcha' ) ),
			'bws_register_form'    => array( 'form_name' => __( 'BWS Register form', 'google-captcha' ) ),
			'bws_forgot_pass_form' => array( 'form_name' => __( 'BWS Forgot Password form', 'google-captcha' ) ),
		);

		$custom_forms   = apply_filters( 'gglcptch_add_custom_form', array() );
		$gglcptch_forms = array_merge( $default_forms, $custom_forms );

		foreach ( $gglcptch_forms as $form_slug => $form_data ) {
			$gglcptch_forms[ $form_slug ]['form_notice'] = gglcptch_get_form_notice( $form_slug );
		}

		$gglcptch_forms = apply_filters( 'gglcptch_forms', $gglcptch_forms );

		return $gglcptch_forms;
	}
}

if ( ! function_exists( 'gglcptch_get_sections' ) ) {
	/**
	 * Google captcha sections for dashboard setting page
	 *
	 * @return  array    $gglcptch_sections   Google captcha sections.
	 */
	function gglcptch_get_sections() {
		global $gglcptch_sections;

		$default_sections = array(
			'standard' => array(
				'name'  => __( 'WordPress default', 'google-captcha' ),
				'forms' => array(
					'login_form',
					'registration_form',
					'reset_pwd_form',
					'password_form',
					'comments_form',
				),
			),
			'external' => array(
				'name'  => __( 'External Plugins', 'google-captcha' ),
				'forms' => array(
					'contact_form',
					'testimonials',
					'frm_contact_form'
				),
			),
			'bws_login_register' => array(
				'name'  => 'BWS Login Register',
				'forms' => array(
					'bws_login_form',
					'bws_register_form',
					'bws_forgot_pass_form'
				),
			),
		);

		$custom_forms = apply_filters( 'gglcptch_add_custom_form', array() );

		$custom_sections   = ( empty( $custom_forms ) ) ? array() : array(
			'custom' => array(
				'name'  => __( 'Custom Forms', 'google-captcha' ),
				'forms' => array_keys( $custom_forms ),
			),
		);
		$gglcptch_sections = array_merge( $default_sections, $custom_sections );

		foreach ( $gglcptch_sections as $section_slug => $section_data ) {
			$gglcptch_sections[ $section_slug ]['section_notice'] = gglcptch_get_section_notice( $section_slug );
		}

		$gglcptch_sections = apply_filters( 'gglcptch_sections', $gglcptch_sections );

		return $gglcptch_sections;
	}
}

if ( ! function_exists( 'gglcptch_add_lmtttmpts_forms' ) ) {
	/**
	 * Add reCaptcha forms to the Limit Attempts plugin
	 *
	 * @param   array $forms   (Optional) Forms array.
	 * @return  array $forms   Forms array.
	 */
	function gglcptch_add_lmtttmpts_forms( $forms = array() ) {
		if ( ! is_array( $forms ) ) {
			$forms = array();
		}

		$forms['gglcptch'] = array(
			'name'  => __( 'reCaptcha Plugin', 'google-captcha' ),
			'forms' => array(),
		);

		$recaptcha_forms = gglcptch_get_forms();

		foreach ( $recaptcha_forms as $form_slug => $form_data ) {
			$forms['gglcptch']['forms'][ "{$form_slug}_captcha_check" ] = $form_data;
			if ( empty( $form_data['form_notice'] ) ) {
				$forms['gglcptch']['forms'][ "{$form_slug}_captcha_check" ]['form_notice'] = gglcptch_get_section_notice( $form_slug );
			}
		}

		return $forms;
	}
}

if ( ! function_exists( 'gglcptch_get_section_notice' ) ) {
	/**
	 * Display section notice
	 *
	 * @access public
	 * @param  string $section_slug    Section slug for notice.
	 * @return array    The action results.
	 */
	function gglcptch_get_section_notice( $section_slug = '' ) {
		$section_notice = '';

		$plugins          = array(
			'bws_login_register' => 'bws-login-register/bws-login-register.php'
		);

		$plugins        = apply_filters( 'gglcptch_custom_plugin_section_notice', $plugins );

		$is_network_admin = is_network_admin();

		if ( isset( $plugins[ $section_slug ] ) ) {
			$slug        = explode( '/', $plugins[ $section_slug ] );
			$slug        = $slug[0];
			$plugin_info = gglcptch_plugin_status( $plugins[ $section_slug ], get_plugins(), $is_network_admin );
			if ( 'deactivated' === $plugin_info['status'] ) {
				$section_notice = '<a href="' . self_admin_url( 'plugins.php' ) . '">' . __( 'Activate', 'google-captcha' ) . '</a>';
			} elseif ( 'not_installed' === $plugin_info['status'] ) {
				$section_notice = sprintf( '<a href="http://wordpress.org/plugins/%s/" target="_blank">%s</a>', $slug, __( 'Install Now', 'google-captcha' ) );
			}
		}

		return apply_filters( 'gglcptch_section_notice', $section_notice, $section_slug );
	}
}

if ( ! function_exists( 'gglcptch_get_form_notice' ) ) {
	/**
	 * Add Settings and Support links
	 *
	 * @param   string $form_slug   Form slug.
	 * @return  string apply_filters result.
	 */
	function gglcptch_get_form_notice( $form_slug = '' ) {
		global $wp_version, $gglcptch_plugin_info;
		$form_notice = '';

		$plugins = array(
			'contact_form'     => array( 'contact-form-plugin/contact_form.php', 'contact-form-pro/contact_form_pro.php', 'contact-form-plus/contact-form-plus.php' ),
			'testimonials'     => array( 'bws-testimonials/bws-testimonials.php', 'bws-testimonials-pro/bws-testimonials-pro.php' ),
			'frm_contact_form' => array( 'formidable/formidable.php', 'formidable-pro/formidable-pro.php' ),
		);

		if ( isset( $plugins[ $form_slug ] ) ) {
			$plugin_info = gglcptch_plugin_status( $plugins[ $form_slug ], get_plugins(), is_network_admin() );

			if ( 'deactivated' === $plugin_info['status'] ) {
				$form_notice = '<a href="' . self_admin_url( 'plugins.php' ) . '">' . __( 'Activate', 'google-captcha' ) . '</a>';
			} elseif ( 'not_installed' === $plugin_info['status'] ) {
				if ( 'contact_form' === $form_slug ) {
					$form_notice = '<a href="https://bestwebsoft.com/products/wordpress/plugins/contact-form/?k=fa26df3911ebcd90c3e85117d6dd0ce0&pn=281&v=' . $gglcptch_plugin_info['Version'] . '&wp_v=' . $wp_version . '" target="_blank">' . __( 'Install Now', 'google-captcha' ) . '</a>';
				} else {
					$form_notice = '<a href="https://bestwebsoft.com/products/wordpress/plugins/bws-testimonials/?k=451513a59dcd9844db90b567473022ce&pn=281&v=' . $gglcptch_plugin_info['Version'] . '&wp_v=' . $wp_version . '" target="_blank">' . __( 'Install Now', 'google-captcha' ) . '</a>';
				}
			}
		}
		return apply_filters( 'gglcptch_form_notice', $form_notice, $form_slug );
	}
}

if ( ! function_exists( 'gglcptch_add_actions' ) ) {
	/**
	 * Add Settings and Support links
	 */
	function gglcptch_add_actions() {
		global $gglcptch_options;

		$is_user_logged_in = is_user_logged_in();

		if ( ! empty( $gglcptch_options['login_form'] ) || ! empty( $gglcptch_options['reset_pwd_form'] ) || ! empty( $gglcptch_options['registration_form'] ) ) {

			if ( gglcptch_is_recaptcha_required( 'login_form', $is_user_logged_in ) ) {
				add_action( 'login_form', 'gglcptch_login_display' );
				add_action( 'authenticate', 'gglcptch_login_check', 21, 1 );
			}

			if ( gglcptch_is_recaptcha_required( 'registration_form', $is_user_logged_in ) ) {
				if ( ! is_multisite() ) {
					add_action( 'register_form', 'gglcptch_login_display', 99 );
					add_action( 'registration_errors', 'gglcptch_register_check', 10, 1 );
				} else {
					add_action( 'signup_extra_fields', 'gglcptch_signup_display' );
					add_action( 'signup_blogform', 'gglcptch_signup_display' );
					add_filter( 'wpmu_validate_user_signup', 'gglcptch_signup_check', 10, 3 );
				}
			}

			if ( gglcptch_is_recaptcha_required( 'reset_pwd_form', $is_user_logged_in ) ) {
				add_action( 'lostpassword_form', 'gglcptch_login_display' );
				add_action( 'allow_password_reset', 'gglcptch_lostpassword_check' );
			}
		}

		/* Add Google Captcha to Protected post password */
		if ( gglcptch_is_recaptcha_required( 'password_form', $is_user_logged_in ) ) {
			add_filter( 'the_password_form', 'gglcptch_password_form_display', 10, 2 );
			add_filter( 'post_password_expires', 'gglcptch_password_form_cookie' );
			add_filter( 'post_password_required', 'gglcptch_password_form_check', 10, 2 );
		}

		/* Add Google Captcha to WP comments */
		if ( gglcptch_is_recaptcha_required( 'comments_form', $is_user_logged_in ) ) {
			add_action( 'comment_form_after_fields', 'gglcptch_commentform_display' );
			add_action( 'comment_form_logged_in_after', 'gglcptch_commentform_display' );
			add_action( 'pre_comment_on_post', 'gglcptch_commentform_check' );
		}

		/* Add Google Captcha to Contact Form by BestWebSoft */
		if ( gglcptch_is_recaptcha_required( 'contact_form', $is_user_logged_in ) ) {
			add_filter( 'cntctfrm_display_captcha', 'gglcptch_display', 10, 1 );
			add_filter( 'cntctfrm_check_form', 'gglcptch_contact_form_check' );
		}

		/* Add Google Captcha to Testimonials by BestWebSoft */
		if ( gglcptch_is_recaptcha_required( 'testimonials', $is_user_logged_in ) ) {
			add_filter( 'tstmnls_display_recaptcha', 'gglcptch_display', 10, 0 );
		}

		/* Add Google Captcha to BWS Login Register */
		if ( gglcptch_is_recaptcha_required( 'bws_login_form', $is_user_logged_in ) || gglcptch_is_recaptcha_required( 'bws_register_form', $is_user_logged_in ) || gglcptch_is_recaptcha_required( 'bws_forgot_pass_form', $is_user_logged_in ) ) {
			add_filter( 'lgnrgstrfrm_add_field', 'gglcptch_add_login_register_forms', 10, 2 );
			add_filter( 'lgnrgstrfrm_check_field', 'gglcptch_check_login_register_forms', 10 );
		}

		do_action( 'gglcptch_add_plus_actions', $is_user_logged_in );
	}
}

if ( ! function_exists( 'gglcptch_echo_recaptcha' ) ) {
	/**
	 * Echo google captcha
	 *
	 * @param   string $content   Content without captcha.
	 */
	function gglcptch_echo_recaptcha( $content = '' ) {
		echo gglcptch_display( $content );
	}
}

if ( ! function_exists( 'gglcptch_login_display' ) ) {
	/**
	 * Add google captcha to the login form
	 *
	 * @return  bool    true
	 */
	function gglcptch_login_display() {

		global $gglcptch_options;

		if ( isset( $gglcptch_options['recaptcha_version'] ) ) {
			if ( 'v2' === $gglcptch_options['recaptcha_version'] ) {
				$from_width = 302; ?>
				<style type="text/css" media="screen">
					.login-action-login #loginform,
					.login-action-lostpassword #lostpasswordform,
					.login-action-register #registerform {
						width: <?php echo esc_attr( $from_width ); ?>px !important;
					}
					#login_error,
					.message {
						width: <?php echo absint( esc_attr( $from_width ) ) + 20; ?>px !important;
					}
					.login-action-login #loginform .gglcptch,
					.login-action-lostpassword #lostpasswordform .gglcptch,
					.login-action-register #registerform .gglcptch {
						margin-bottom: 10px;
					}
				</style>
				<?php
			}
		}
		echo gglcptch_display();
		return true;
	}
}

if ( ! function_exists( 'gglcptch_login_check' ) ) {
	/**
	 * Check google captcha in login form
	 *
	 * @param   object $user   User object or errros array.
	 * @return  object    $user   User object or errros array.
	 */
	function gglcptch_login_check( $user ) {
		global $gglcptch_check;
		if ( gglcptch_is_woocommerce_page() ) {
			return $user;
		}
		if ( is_wp_error( $user ) && isset( $user->errors['empty_username'] ) && isset( $user->errors['empty_password'] ) ) {
			return $user;
		}
		/* Skip check if connecting to XMLRPC */
		if ( defined( 'XMLRPC_REQUEST' ) ) {
			return $user;
		}

		$gglcptch_check = gglcptch_check( 'login_form' );

		if ( ! $gglcptch_check['response'] ) {
			if ( 'VERIFICATION_FAILED' === $gglcptch_check['reason'] ) {
				wp_clear_auth_cookie();
			}
			$error_code      = ( is_wp_error( $user ) ) ? $user->get_error_code() : 'incorrect_password';
			$errors          = new WP_Error( $error_code, __( 'Authentication failed.', 'google-captcha' ) );
			if ( isset( $gglcptch_check['errors'] ) ) {
				$gglcptch_errors = $gglcptch_check['errors']->errors;
				foreach ( $gglcptch_errors as $code => $messages ) {
					foreach ( $messages as $message ) {
						$errors->add( $code, $message );
					}
				}
			}
			$gglcptch_check['errors'] = $errors;
			return $gglcptch_check['errors'];
		}
		return $user;
	}
}

if ( ! function_exists( 'gglcptch_register_check' ) ) {
	/**
	 * Check google captcha in registration form
	 *
	 * @param   bool $allow   Flag for captcha result.
	 * @return  bool  $allow   Flag for captcha result.
	 */
	function gglcptch_register_check( $allow ) {
		if ( gglcptch_is_woocommerce_page() ) {
			return $allow;
		}
		/* Skip check if connecting to XMLRPC */
		if ( defined( 'XMLRPC_REQUEST' ) ) {
			return $allow;
		}

		$gglcptch_check = gglcptch_check( 'registration_form' );
		if ( ! $gglcptch_check['response'] ) {
			return $gglcptch_check['errors'];
		}
		$_POST['g-recaptcha-response-check'] = true;
		return $allow;
	}
}

if ( ! function_exists( 'gglcptch_lostpassword_check' ) ) {
	/**
	 * Check google captcha in lostpassword form
	 *
	 * @param   bool $allow   Flag for captcha result.
	 * @return  bool  $allow   Flag for captcha result.
	 */
	function gglcptch_lostpassword_check( $allow ) {
		if ( gglcptch_is_woocommerce_page() || ( isset( $_POST['g-recaptcha-response-check'] ) && true === $_POST['g-recaptcha-response-check'] ) ) {
			return $allow;
		}
		$gglcptch_check = gglcptch_check( 'reset_pwd_form' );
		if ( ! $gglcptch_check['response'] ) {
			return $gglcptch_check['errors'];
		}
		return $allow;
	}
}

if ( ! function_exists( 'gglcptch_password_form_display' ) ) {
	/**
	 * Add google captcha to the protected post password form
	 *
	 * @param   string $output   Form content.
	 * @param   object $post     Post object.
	 * @return  string  $expire   Form content.
	 */
	function gglcptch_password_form_display( $output, $post = null ) {
		$recaptcha = gglcptch_display();
		if ( '' !== $recaptcha && isset( $_COOKIE['gglcptch_password_form_errors'] ) ) {
			$output = str_replace( '</form>', '<div class="error gglcptch-password-form-error"><p>' . wp_kses_post( wp_unslash( $_COOKIE['gglcptch_password_form_errors'] ) ) . '</p></div>' . $recaptcha . '</form>', $output );
		} else {
			$output = str_replace( '</form>', $recaptcha . '</form>', $output );
		}
		return $output;
	}
}

if ( ! function_exists( 'gglcptch_password_form_cookie' ) ) {
	/**
	 * Add google captcha to the protected post password form
	 *
	 * @param   array $expire   Expire time.
	 * @return  array  $expire   Expire time.
	 */
	function gglcptch_password_form_cookie( $expire ) {
		if ( isset( $_POST['g-recaptcha-response'] ) ) {
			$gglcptch_check = gglcptch_check( 'password_form' );
			if ( ! $gglcptch_check['response'] ) {
				setcookie( 'gglcptch_password_form_errors', $gglcptch_check['errors']->get_error_message( 'gglcptch_error' ), time() + 300, COOKIEPATH, COOKIE_DOMAIN, false );
			} else {
				setcookie( 'gglcptch_password_form_errors', '', -1, COOKIEPATH, COOKIE_DOMAIN, false );
			}
		}
		return $expire;
	}
}

if ( ! function_exists( 'gglcptch_password_form_check' ) ) {
	/**
	 * Check google captcha in protected post password form
	 *
	 * @param   bool   $required   Flag for required password form.
	 * @param   object $post       Post object.
	 * @return  bool    $required   Flag for required password form.
	 */
	function gglcptch_password_form_check( $required, $post ) {
		if ( isset( $_COOKIE['gglcptch_password_form_errors'] ) ) {
			$required = true;
		}
		return $required;
	}
}

if ( ! function_exists( 'gglcptch_signup_display' ) ) {
	/**
	 * Add google captcha to the multisite login form
	 *
	 * @param   array $errors   Login form errors.
	 */
	function gglcptch_signup_display( $errors ) {
		$error_message = $errors->get_error_message( 'gglcptch_error' );
		if ( ! empty( $error_message ) ) {
			printf( '<p class="error gglcptch_error">%s</p>', wp_kses_post( $error_message ) );
		}
		$error_message = $errors->get_error_message( 'lmttmpts_error' );
		if ( ! empty( $error_message ) ) {
			printf( '<p class="error lmttmpts_error">%s</p>', wp_kses_post( $error_message ) );
		}
		echo gglcptch_display();
	}
}

if ( ! function_exists( 'gglcptch_signup_check' ) ) {
	/**
	 * Check google captcha in multisite login form
	 *
	 * @param   array $result   Result/Error.
	 * @return  array $result  Result/Error.
	 */
	function gglcptch_signup_check( $result ) {
		global $current_user;
		if ( is_admin() && ! defined( 'DOING_AJAX' ) && ! empty( $current_user->data->ID ) ) {
			return $result;
		}
		$gglcptch_check = gglcptch_check( 'registration_form' );
		if ( ! $gglcptch_check['response'] ) {
			$result['errors'] = $gglcptch_check['errors'];
			return $result;
		}
		return $result;
	}
}

if ( ! function_exists( 'gglcptch_commentform_display' ) ) {
	/**
	 * Add google captcha to the comment form
	 *
	 * @return  bool true
	 */
	function gglcptch_commentform_display() {
		if ( gglcptch_is_hidden_for_role() ) {
			return;
		}
		echo gglcptch_display();
		return true;
	}
}

if ( ! function_exists( 'gglcptch_commentform_check' ) ) {
	/**
	 * Check JS enabled for comment form
	 */
	function gglcptch_commentform_check() {
		$gglcptch_check = gglcptch_check( 'comments_form' );
		if ( ! $gglcptch_check['response'] ) {
			$message          = gglcptch_get_message( $gglcptch_check['reason'] ) . '<br />';
			if ( ! empty( $gglcptch_check['errors'] ) && is_wp_error( $gglcptch_check['errors'] ) ){
				$lmttmpts_error = $gglcptch_check['errors']->get_error_message( 'lmttmpts_error' );
				if ( ! empty( $lmttmpts_error ) ) {
					$message .= sprintf( 
						'<strong>%s</strong>:&nbsp;%s<br />',
						__( 'Error', 'google-captcha' ),
						$gglcptch_check['errors']->get_error_message( 'lmttmpts_error' )
					);
				}
			}
			
			$error_message = sprintf(
				'<strong>%s</strong>:&nbsp;%s&nbsp;%s',
				__( 'Error', 'google-captcha' ),
				$message,
				__( 'Click the BACK button on your browser and try again.', 'google-captcha' )
			);
			wp_die( wp_kses_post( $error_message ) );
		}
	}
}

if ( ! function_exists( 'gglcptch_contact_form_check' ) ) {
	/**
	 * Check google captcha in BWS Contact Form
	 *
	 * @param   bool $allow   (Optional) reCaptcha for contact form.
	 * @return  bool $allow   Result check.
	 */
	function gglcptch_contact_form_check( $allow = true ) {
		if ( ! $allow || is_string( $allow ) || is_wp_error( $allow ) ) {
			return $allow;
		}
		$gglcptch_check = gglcptch_check( 'contact_form' );
		if ( ! $gglcptch_check['response'] ) {
			return $gglcptch_check['errors'];
		}
		return $allow;
	}
}

if ( ! function_exists( 'gglcptch_testimonials_check' ) ) {
	/**
	 * Check google captcha in BWS Testimonial
	 *
	 * @param   bool $allow   (Optional) reCaptcha for testimonials.
	 * @return  bool $allow   Result check.
	 */
	function gglcptch_testimonials_check( $allow = true ) {
		global $gglcptch_check;
		if ( ! $allow || is_string( $allow ) || is_wp_error( $allow ) ) {
			return $allow;
		}
		$gglcptch_check = gglcptch_check( 'testimonials' );
		if ( ! $gglcptch_check['response'] ) {
			return $gglcptch_check['errors'];
		}
		return $allow;
	}
}

if ( ! function_exists( 'gglcptch_add_login_register_forms' ) ) {
	/**
	 * Check google captcha in BWS Testimonial
	 *
	 * @param   string $content Content.
	 * @param   string $form    Form name.
	 * @return  string $content Content after update.
	 */
	function gglcptch_add_login_register_forms( $content, $form ) {
		$is_user_logged_in = is_user_logged_in();
		if ( gglcptch_is_recaptcha_required( 'bws_login_form', $is_user_logged_in ) && 'login' === $form ) {
			return gglcptch_display( array(), $content );
		} elseif ( gglcptch_is_recaptcha_required( 'bws_register_form', $is_user_logged_in ) && 'register' === $form ) {
			return gglcptch_display( array(), $content );
		} elseif( gglcptch_is_recaptcha_required( 'bws_forgot_pass_form', $is_user_logged_in ) && 'forgot_password' === $form ) {
			return gglcptch_display( array(), $content );
		}
		return $content;
	}
}

if ( ! function_exists( 'gglcptch_check_login_register_forms' ) ) {
	/**
	 * Check google captcha in BWS Testimonial
	 *
	 * @param   string $form          Form name.
	 * @return  string $error_message Error for reCaptcha.
	 */
	function gglcptch_check_login_register_forms( $form ) {
		global $gglcptch_options;
		$gglcptch_check = gglcptch_check( 'bws_' . $form . '_form' );
		if ( ! $gglcptch_check['response'] ) {
			$error_message = implode( '<br>', $gglcptch_check['errors']->get_error_messages() );
			return $error_message;
		}
		return '';
	}
}

