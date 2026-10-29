<?php
/**
 * Suitability enquiry form handler (admin-post.php).
 *
 * Validates the submission, attaches the four photos to an email sent with
 * wp_mail() and then deletes the temporary files. Photos are not kept on the server.
 *
 * @package Lifestyle_Smiles
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SMILE_MAX_PHOTO_BYTES', 5 * MB_IN_BYTES );

add_action( 'admin_post_nopriv_smile_suitability', 'smile_handle_suitability' );
add_action( 'admin_post_smile_suitability', 'smile_handle_suitability' );

/**
 * Photo slots accepted by the form.
 *
 * @return array Field name => label.
 */
function smile_photo_slots() {
	return array(
		'photo_front'  => __( 'Front smile', 'lifestyle-smiles' ),
		'photo_left'   => __( 'Left side', 'lifestyle-smiles' ),
		'photo_right'  => __( 'Right side', 'lifestyle-smiles' ),
		'photo_tilted' => __( 'Tilted smile', 'lifestyle-smiles' ),
	);
}

/**
 * Image types accepted for photos.
 *
 * @return array
 */
function smile_allowed_photo_mimes() {
	return array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'webp'         => 'image/webp',
	);
}

/**
 * Treatment concerns offered in step 1.
 *
 * @return array Slug => label.
 */
function smile_concerns() {
	return array(
		'chips'   => __( 'Small chips/ Broken teeth', 'lifestyle-smiles' ),
		'stained' => __( 'Stained fillings/Old composite bonding', 'lifestyle-smiles' ),
		'uneven'  => __( 'Minor unevenness in front teeth', 'lifestyle-smiles' ),
		'yellow'  => __( 'Yellow teeth', 'lifestyle-smiles' ),
		'missing' => __( 'Missing teeth', 'lifestyle-smiles' ),
		'size'    => __( 'Too small/too big teeth', 'lifestyle-smiles' ),
		'other'   => __( 'Others', 'lifestyle-smiles' ),
	);
}

/**
 * Urgency options (placeholder wording until confirmed by the practice).
 *
 * @return array Slug => label.
 */
function smile_urgency_options() {
	return array(
		'asap'      => __( 'As soon as possible', 'lifestyle-smiles' ),
		'two-weeks' => __( 'Within 2 weeks', 'lifestyle-smiles' ),
		'one-month' => __( 'Within a month', 'lifestyle-smiles' ),
		'exploring' => __( 'Just exploring options', 'lifestyle-smiles' ),
	);
}

/**
 * Allowed phone country codes.
 *
 * @return array Dial code => region label.
 */
function smile_phone_codes() {
	return array(
		'+61' => 'Australia',
		'+64' => 'New Zealand',
		'+44' => 'United Kingdom',
		'+1'  => 'United States / Canada',
		'+65' => 'Singapore',
		'+91' => 'India',
	);
}

/**
 * Redirect back to the form with a status code.
 *
 * @param string $status success|error.
 * @param string $code   Machine-readable reason.
 */
function smile_form_redirect( $status, $code = '' ) {
	$back = wp_get_referer();
	$back = $back ? $back : home_url( '/' );
	$back = remove_query_arg( array( 'smile_form', 'smile_code' ), $back );
	$back = add_query_arg(
		array(
			'smile_form' => $status,
			'smile_code' => $code,
		),
		$back
	);
	wp_safe_redirect( $back . '#suitability' );
	exit;
}

/**
 * Read a text field from the POST body.
 *
 * @param string $key Field name.
 * @return string Sanitised value, or '' when missing.
 */
function smile_post_text( $key ) {
	return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
}

/**
 * Process the submission (runs on admin-post.php).
 */
function smile_handle_suitability() {
	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
	if ( 'POST' !== $method ) {
		smile_form_redirect( 'error', 'method' );
	}

	// The nonce is checked before any other POST data is read.
	if ( ! wp_verify_nonce( smile_post_text( 'smile_nonce' ), 'smile_suitability' ) ) {
		smile_form_redirect( 'error', 'nonce' );
	}

	// Honeypot: real users never fill this in. Report success so bots learn nothing.
	if ( ! empty( smile_post_text( 'smile_website' ) ) ) {
		smile_form_redirect( 'success', 'ok' );
	}

	// Basic rate limit: one submission per IP per minute.
	$ip       = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$rate_key = 'smile_rl_' . md5( $ip );
	if ( get_transient( $rate_key ) ) {
		smile_form_redirect( 'error', 'rate' );
	}

	$details = smile_validate_details();
	if ( is_wp_error( $details ) ) {
		smile_form_redirect( 'error', $details->get_error_code() );
	}

	$photos = smile_store_photos();
	if ( is_wp_error( $photos ) ) {
		smile_form_redirect( 'error', $photos->get_error_code() );
	}

	$sent = wp_mail(
		smile_notify_address(),
		smile_email_subject( $details ),
		smile_email_body( $details ),
		smile_email_headers( $details ),
		$photos
	);

	smile_cleanup( $photos );

	if ( ! $sent ) {
		smile_form_redirect( 'error', 'mail' );
	}

	set_transient( $rate_key, 1, MINUTE_IN_SECONDS );
	smile_form_redirect( 'success', 'ok' );
}

/**
 * Validate and sanitise the text fields.
 *
 * @return array|WP_Error Sanitised details, or a WP_Error whose code is the form error code.
 */
function smile_validate_details() {
	$details = array(
		'first'    => smile_post_text( 'first_name' ),
		'last'     => smile_post_text( 'last_name' ),
		'dob'      => smile_post_text( 'dob' ),
		'gender'   => smile_post_text( 'gender' ),
		'code'     => smile_post_text( 'phone_code' ),
		'phone'    => preg_replace( '/\D/', '', smile_post_text( 'phone' ) ),
		'email'    => sanitize_email( smile_post_text( 'email' ) ),
		'urgency'  => isset( $_POST['urgency'] ) ? sanitize_key( wp_unslash( $_POST['urgency'] ) ) : '',
		'concerns' => smile_post_concerns(),
	);

	if ( '' === $details['first'] || '' === $details['last'] ) {
		return new WP_Error( 'name' );
	}

	$dob = $details['dob'];
	if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $dob, $m ) || ! checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) || $dob > gmdate( 'Y-m-d' ) ) {
		return new WP_Error( 'dob' );
	}

	if ( ! in_array( $details['gender'], array( 'male', 'female', 'other' ), true ) ) {
		return new WP_Error( 'gender' );
	}

	$digits = strlen( $details['phone'] );
	if ( ! array_key_exists( $details['code'], smile_phone_codes() ) || $digits < 6 || $digits > 12 ) {
		return new WP_Error( 'phone' );
	}

	if ( ! is_email( $details['email'] ) ) {
		return new WP_Error( 'email' );
	}

	if ( empty( $details['concerns'] ) ) {
		return new WP_Error( 'concerns' );
	}

	if ( ! array_key_exists( $details['urgency'], smile_urgency_options() ) ) {
		return new WP_Error( 'urgency' );
	}

	return $details;
}

/**
 * Read the concern checkboxes, keeping only known slugs.
 *
 * @return array
 */
function smile_post_concerns() {
	if ( ! isset( $_POST['concerns'] ) || ! is_array( $_POST['concerns'] ) ) {
		return array();
	}

	$submitted = array_filter(
		array_map(
			function ( $value ) {
				return is_string( $value ) ? sanitize_key( $value ) : '';
			},
			wp_unslash( $_POST['concerns'] )
		)
	);

	return array_values( array_intersect( $submitted, array_keys( smile_concerns() ) ) );
}

/**
 * Validate and store the four uploaded photos in the temp directory.
 *
 * @return array|WP_Error Absolute paths of the stored photos, or a WP_Error.
 */
function smile_store_photos() {
	$tmp_dir = trailingslashit( get_temp_dir() );
	$stored  = array();

	foreach ( array_keys( smile_photo_slots() ) as $field ) {
		$path = smile_store_photo( $field, $tmp_dir );
		if ( is_wp_error( $path ) ) {
			smile_cleanup( $stored );
			return $path;
		}
		$stored[] = $path;
	}

	return $stored;
}

/**
 * Validate one uploaded photo and move it out of PHP's upload area.
 *
 * @param string $field   Form field name.
 * @param string $tmp_dir Destination directory.
 * @return string|WP_Error Absolute path, or a WP_Error.
 */
function smile_store_photo( $field, $tmp_dir ) {
	if ( empty( $_FILES[ $field ]['tmp_name'] ) || ! empty( $_FILES[ $field ]['error'] ) ) {
		return new WP_Error( 'photos' );
	}

	$file = $_FILES[ $field ];
	$tmp  = $file['tmp_name']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- path from PHP's upload handler; verified with is_uploaded_file() below.
	$name = isset( $file['name'] ) ? sanitize_file_name( wp_unslash( $file['name'] ) ) : $field . '.jpg';
	$size = isset( $file['size'] ) ? (int) $file['size'] : 0;

	if ( ! is_uploaded_file( $tmp ) || $size <= 0 || $size > SMILE_MAX_PHOTO_BYTES ) {
		return new WP_Error( 'photo_size' );
	}

	$check = wp_check_filetype_and_ext( $tmp, $name, smile_allowed_photo_mimes() );
	if ( empty( $check['type'] ) || empty( $check['ext'] ) || ! wp_getimagesize( $tmp ) ) {
		return new WP_Error( 'photo_type' );
	}

	$filename = sanitize_file_name( $field . '-' . wp_generate_password( 6, false ) . '.' . $check['ext'] );
	$dest     = $tmp_dir . wp_unique_filename( $tmp_dir, $filename );

	if ( ! move_uploaded_file( $tmp, $dest ) ) {
		return new WP_Error( 'photos' );
	}

	return $dest;
}

/**
 * Address that receives enquiries: the Customizer setting, or the site admin email.
 *
 * @return string
 */
function smile_notify_address() {
	$to = smile_get_option( 'notify_email' );
	return is_email( $to ) ? $to : get_option( 'admin_email' );
}

/**
 * @param array $details Sanitised details.
 * @return string
 */
function smile_email_subject( $details ) {
	return sprintf(
		/* translators: %s: patient name */
		__( 'New suitability enquiry: %s', 'lifestyle-smiles' ),
		$details['first'] . ' ' . $details['last']
	);
}

/**
 * Plain-text body of the enquiry email.
 *
 * @param array $details Sanitised details.
 * @return string
 */
function smile_email_body( $details ) {
	$concerns = smile_concerns();
	$labels   = array_map(
		function ( $slug ) use ( $concerns ) {
			return $concerns[ $slug ];
		},
		$details['concerns']
	);

	$urgencies = smile_urgency_options();
	$codes     = smile_phone_codes();

	return implode(
		"\n",
		array(
			'First name: ' . $details['first'],
			'Last name: ' . $details['last'],
			'Date of birth: ' . $details['dob'],
			'Gender: ' . $details['gender'],
			'Phone: ' . $details['code'] . ' ' . $details['phone'],
			'Email: ' . $details['email'],
			'Country code region: ' . $codes[ $details['code'] ],
			'Concerns: ' . implode( ', ', $labels ),
			'Urgency: ' . $urgencies[ $details['urgency'] ],
			'',
			'Four smile photos are attached.',
		)
	);
}

/**
 * @param array $details Sanitised details.
 * @return array
 */
function smile_email_headers( $details ) {
	$name = str_replace( array( "\r", "\n" ), '', $details['first'] . ' ' . $details['last'] );

	return array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . $name . ' <' . $details['email'] . '>',
	);
}

/**
 * Delete temporary attachment files.
 *
 * @param array $files Absolute paths.
 */
function smile_cleanup( $files ) {
	foreach ( $files as $file ) {
		if ( is_string( $file ) && file_exists( $file ) ) {
			wp_delete_file( $file );
		}
	}
}

/**
 * Map a status code to a user-facing message.
 *
 * @param string $status Status (success|error).
 * @param string $code   Error or success code.
 * @return string
 */
function smile_form_message( $status, $code ) {
	if ( 'success' === $status ) {
		return __( 'Thank you. Your details and photos have been sent. Our team will be in touch within 24 hours.', 'lifestyle-smiles' );
	}

	$messages = array(
		'nonce'      => __( 'Your session expired. Please reload the page and try again.', 'lifestyle-smiles' ),
		'rate'       => __( 'Please wait a minute before submitting again.', 'lifestyle-smiles' ),
		'name'       => __( 'Please enter your first and last name.', 'lifestyle-smiles' ),
		'dob'        => __( 'Please enter a valid date of birth.', 'lifestyle-smiles' ),
		'gender'     => __( 'Please select a gender option.', 'lifestyle-smiles' ),
		'phone'      => __( 'Please enter a valid phone number.', 'lifestyle-smiles' ),
		'email'      => __( 'Please enter a valid email address.', 'lifestyle-smiles' ),
		'concerns'   => __( 'Please select at least one concern.', 'lifestyle-smiles' ),
		'urgency'    => __( 'Please select your urgency.', 'lifestyle-smiles' ),
		'photos'     => __( 'Please upload all four smile photos.', 'lifestyle-smiles' ),
		'photo_size' => __( 'Each photo must be 5 MB or smaller.', 'lifestyle-smiles' ),
		'photo_type' => __( 'Photos must be JPG, PNG or WebP images.', 'lifestyle-smiles' ),
		'mail'       => __( 'We could not send your enquiry. Please call the practice instead.', 'lifestyle-smiles' ),
	);

	return isset( $messages[ $code ] ) ? $messages[ $code ] : __( 'Something went wrong. Please try again.', 'lifestyle-smiles' );
}
