<?php
/**
 * This functions are used for adding captcha in Formidable Contact Form
 **/

if ( ! function_exists( 'gglcptch_frm_add_basic_field' ) ) {
	/**
	 * Add the field to the top section of the fields
	 *
	 * @param array $fields All fields for Formidable.
	 * @return array $fields Return fields array.
	 */
	function gglcptch_frm_add_basic_field( $fields ) {

		$fields['recaptcha-bws'] = array(
			'name' => 'reCaptcha BWS',
			'icon' => 'frm_icon_font frm_shield_check_icon',
		);

		return $fields;
	}
}

if ( ! function_exists( 'gglcptch_frm_set_defaults' ) ) {
	/**
	 * Set default settings for new field.
	 *
	 * @param array $field_data All fields for Formidable.
	 * @return array $field_data Return fields array.
	 */
	function gglcptch_frm_set_defaults( $field_data ) {
		if ( 'recaptcha-bws' === $field_data['type'] ) {
			$field_data['required']                 = 1;
			$field_data['field_options']['blank']   = gglcptch_get_message( 'RECAPTCHA_EMPTY_RESPONSE' );
			$field_data['field_options']['invalid'] = gglcptch_get_message( 'incorrect' );
		}

		return $field_data;
	}
}

if ( ! function_exists( 'gglcptch_frm_show_the_admin_field' ) ) {
	/**
	 * Show the field in the builder page.
	 *
	 * @param array $field reCaptcha field for display in admin.
	 */
	function gglcptch_frm_show_the_admin_field( $field ) {
		global $gglcptch_options;
		if ( 'recaptcha-bws' !== $field['type'] ) {
			return;
		}
		$weekdays_flag = true;
		if ( isset( $gglcptch_options['weekdays'] ) ) {
			$week_day = gmdate( 'N' );
			$hour     = gmdate( 'G' );
			if ( ! in_array( $week_day, $gglcptch_options['weekdays'] ) || ( ! in_array( $week_day, $gglcptch_options['all_day'] ) && ! in_array( $hour, $gglcptch_options['hours'][ $week_day ] ) ) ) {
				$weekdays_flag = false;
			}
		}
		?>
		[bws_google_captcha]
		<?php
		if ( false === $weekdays_flag ) {
			?>
			<div class="frm_form_fields frm_opt_container" data-ftype="captcha">
				<span class="frm-with-icon frm-not-set frm_note_style">
					<svg class="frmsvg"><use xlink:href="#frm_report_problem_solid_icon"></use></svg><?php echo esc_html__( 'Currently, the reCaptcha will not be displayed according to the Weekdays and Hours settings.', 'google-captcha-pro' ); ?> <a href="<?php echo esc_url( admin_url( 'admin.php?page=google-captcha-pro.php' ) ); ?>" target="_blank"><?php esc_html_e( 'reCaptcha Settings', 'google-captcha-pro' ); ?></a>
				</span>
				<div class="clear"></div>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'gglcptch_frm_show_front_field' ) ) {
	/**
	 * Show the field in form.
	 *
	 * @param array $field reCaptcha field for display in front.
	 * @param array $field_name reCaptcha field name.
	 * @param array $atts reCaptcha atts.
	 */
	function gglcptch_frm_show_front_field( $field, $field_name, $atts ) {
		global $gglcptch_options;
		if ( 'recaptcha-bws' !== $field['type'] ) {
			return;
		}

		$weekdays_flag = true;
		if ( isset( $gglcptch_options['weekdays'] ) ) {
			$week_day = gmdate( 'N' );
			$hour     = gmdate( 'G' );
			if ( ! in_array( $week_day, $gglcptch_options['weekdays'] ) || ( ! in_array( $week_day, $gglcptch_options['all_day'] ) && ! in_array( $hour, $gglcptch_options['hours'][ $week_day ] ) ) ) {
				$weekdays_flag = false;
			}
		}
		$is_user_logged_in = is_user_logged_in();

		if ( false === $weekdays_flag || ! gglcptch_is_recaptcha_required( 'frm_contact_form', $is_user_logged_in ) ) {
			echo '<style>
			#' . esc_attr( $atts['html_id'] ) . '_label,
			#frm_desc_' . esc_attr( $atts['html_id'] ) . '{
				display: none;
			}
			</style>';
		} elseif( 'v2' != $gglcptch_options['recaptcha_version'] ) {
			echo '<style>
			#' . esc_attr( $atts['html_id'] ) . '_label,
			#frm_desc_' . esc_attr( $atts['html_id'] ) . ',
			.frm_form_field br {
				display: none;
			}
			</style>';
			echo gglcptch_display();
		} else {
			echo gglcptch_display();
		}
	}
}

if ( ! function_exists( 'gglcptch_frm_custom_validation' ) ) {
	/**
	 * Add custom validation.
	 *
	 * @param array $errors Errors array for form.
	 * @param array $posted_field Current posted field.
	 * @param array $posted_value Current posted value.
	 */
	function gglcptch_frm_custom_validation( $errors, $posted_field, $posted_value ) {
		global $gglcptch_options;

		if ( 'recaptcha-bws' === $posted_field->type ) {
			$weekdays_flag = true;
			if ( isset( $gglcptch_options['weekdays'] ) ) {
				$week_day = gmdate( 'N' );
				$hour     = gmdate( 'G' );
				if ( ! in_array( $week_day, $gglcptch_options['weekdays'] ) || ( ! in_array( $week_day, $gglcptch_options['all_day'] ) && ! in_array( $hour, $gglcptch_options['hours'][ $week_day ] ) ) ) {
					$weekdays_flag = false;
				}
			}

			if ( isset( $errors[ 'field' . $posted_field->id ] ) ) {
				unset( $errors[ 'field' . $posted_field->id ] );
			}
			$is_user_logged_in = is_user_logged_in();

			if ( gglcptch_is_recaptcha_required( 'frm_contact_form', $is_user_logged_in ) ) {			
				if ( isset( $_POST ) && isset( $_POST['g-recaptcha-response'] ) ) {
					if ( empty( $gglcptch_check ) ) {
						$gglcptch_check = gglcptch_check( 'frm_contact_form' );
						if ( ! $gglcptch_check['response'] ) {
							$errors[ 'field' . $posted_field->id ] = esc_html( $gglcptch_check['errors']->errors['gglcptch_error'][0] );
						}
					} elseif ( ! empty( $gglcptch_check['errors'] ) ) {
						$errors[ 'field' . $posted_field->id ] = esc_html( $gglcptch_check['errors']->errors['gglcptch_error'][0] );
					}
				} elseif ( false === $weekdays_flag ) {
					if ( isset( $errors[ 'field' . $posted_field->id ] ) ) {
						unset( $errors[ 'field' . $posted_field->id ] );
					}
				}
			}
		}
		return $errors;
	}
}

add_filter( 'frm_available_fields', 'gglcptch_frm_add_basic_field' );
//add_filter( 'frm_pro_available_fields', 'gglcptch_frm_add_basic_field' );
add_filter( 'frm_before_field_created', 'gglcptch_frm_set_defaults' );
add_action( 'frm_display_added_fields', 'gglcptch_frm_show_the_admin_field' );
add_action( 'frm_form_fields', 'gglcptch_frm_show_front_field', 10, 3 );
add_filter( 'frm_validate_field_entry', 'gglcptch_frm_custom_validation', 10, 3 );
