<?php
/**
 * Suitability enquiry form handler (admin-post.php).
 *
 * The form emails the practice via wp_mail(). Photos are attached from the
 * PHP temp upload and are NOT stored in the media library or on disk after
 * the email is sent.
 *
 * @package Lifestyle_Smiles
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_post_nopriv_smile_suitability', 'smile_handle_suitability' );
add_action( 'admin_post_smile_suitability', 'smile_handle_suitability' );

/**
 * Photo slots accepted by the form.
 *
 * @return array
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
 * Process the submission.
 */
function smile_handle_suitability() {
	if ( 'POST' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : '' ) ) {
		smile_form_redirect( 'error', 'method' );
	}

	$nonce = isset( $_POST['smile_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['smile_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'smile_suitability' ) ) {
		smile_form_redirect( 'error', 'nonce' );
	}

	// Honeypot: real users never fill this in.
	if ( ! empty( $_POST['smile_website'] ) ) {
		smile_form_redirect( 'success', 'ok' );
	}

	// Basic rate limit: one submission per IP per 60 seconds.
	$ip_raw = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$key    = 'smile_rl_' . md5( $ip_raw );
	if ( get_transient( $key ) ) {
		smile_form_redirect( 'error', 'rate' );
	}

	$first  = isset( $_POST['first_name'] ) ? sanitize_text_field( wp_unslash( $_POST['first_name'] ) ) : '';
	$last   = isset( $_POST['last_name'] ) ? sanitize_text_field( wp_unslash( $_POST['last_name'] ) ) : '';
	$dob    = isset( $_POST['dob'] ) ? sanitize_text_field( wp_unslash( $_POST['dob'] ) ) : '';
	$gender = isset( $_POST['gender'] ) ? sanitize_text_field( wp_unslash( $_POST['gender'] ) ) : '';
	$code   = isset( $_POST['phone_code'] ) ? sanitize_text_field( wp_unslash( $_POST['phone_code'] ) ) : '';
	$phone  = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	$phone_digits = preg_replace( '/\D/', '', $phone );

	if ( '' === $first || '' === $last ) {
		smile_form_redirect( 'error', 'name' );
	}
	if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $dob, $m ) || ! checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) || $dob > gmdate( 'Y-m-d' ) ) {
		smile_form_redirect( 'error', 'dob' );
	}
	if ( ! in_array( $gender, array( 'male', 'female', 'other' ), true ) ) {
		smile_form_redirect( 'error', 'gender' );
	}
	if ( ! in_array( $code, array_keys( smile_phone_codes() ), true ) || strlen( $phone_digits ) < 6 || strlen( $phone_digits ) > 12 ) {
		smile_form_redirect( 'error', 'phone' );
	}
	if ( ! is_email( $email ) ) {
		smile_form_redirect( 'error', 'email' );
	}

	// Validate photos.
	$allowed_mimes = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'png'          => 'image/png',
		'webp'         => 'image/webp',
	);
	$max_bytes     = 5 * MB_IN_BYTES;
	$attachments   = array();
	$temp_files    = array();
	$tmp_dir       = trailingslashit( get_temp_dir() );

	foreach ( smile_photo_slots() as $field => $label ) {
		if ( empty( $_FILES[ $field ]['tmp_name'] ) || ! empty( $_FILES[ $field ]['error'] ) ) {
			smile_cleanup( $temp_files );
			smile_form_redirect( 'error', 'photos' );
		}

		$tmp  = $_FILES[ $field ]['tmp_name']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- path from PHP, validated below.
		$name = isset( $_FILES[ $field ]['name'] ) ? sanitize_file_name( wp_unslash( $_FILES[ $field ]['name'] ) ) : $field . '.jpg';
		$size = isset( $_FILES[ $field ]['size'] ) ? (int) $_FILES[ $field ]['size'] : 0;

		if ( ! is_uploaded_file( $tmp ) || $size <= 0 || $size > $max_bytes ) {
			smile_cleanup( $temp_files );
			smile_form_redirect( 'error', 'photo_size' );
		}

		$check = wp_check_filetype_and_ext( $tmp, $name, $allowed_mimes );
		if ( empty( $check['type'] ) || empty( $check['ext'] ) || ! @getimagesize( $tmp ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			smile_cleanup( $temp_files );
			smile_form_redirect( 'error', 'photo_type' );
		}

		$dest = $tmp_dir . wp_unique_filename( $tmp_dir, sanitize_file_name( $field . '-' . wp_generate_password( 6, false ) . '.' . $check['ext'] ) );
		if ( ! move_uploaded_file( $tmp, $dest ) ) {
			smile_cleanup( $temp_files );
			smile_form_redirect( 'error', 'photos' );
		}
		$temp_files[] = $dest;
		$attachments[] = $dest;
	}

	$to = smile_get_option( 'notify_email' );
	$to = is_email( $to ) ? $to : get_option( 'admin_email' );

	$subject = sprintf(
		/* translators: %s: patient name */
		__( 'New suitability enquiry: %s', 'lifestyle-smiles' ),
		$first . ' ' . $last
	);

	$codes = smile_phone_codes();
	$body  = implode(
		"\n",
		array(
			'First name: ' . $first,
			'Last name: ' . $last,
			'Date of birth: ' . $dob,
			'Gender: ' . $gender,
			'Phone: ' . $code . ' ' . $phone_digits,
			'Email: ' . $email,
			'Country code region: ' . $codes[ $code ],
			'',
			'Four smile photos are attached.',
		)
	);

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'Reply-To: ' . str_replace( array( "\r", "\n" ), '', $first . ' ' . $last ) . ' <' . $email . '>',
	);

	$sent = wp_mail( $to, $subject, $body, $headers, $attachments );

	smile_cleanup( $temp_files );

	if ( ! $sent ) {
		smile_form_redirect( 'error', 'mail' );
	}

	set_transient( $key, 1, MINUTE_IN_SECONDS );
	smile_form_redirect( 'success', 'ok' );
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
 * Allowed phone country codes.
 *
 * @return array Code => region label.
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
 * Map a status code to a user-facing message.
 *
 * @param string $status Status.
 * @param string $code   Code.
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
		'photos'     => __( 'Please upload all four smile photos.', 'lifestyle-smiles' ),
		'photo_size' => __( 'Each photo must be 5 MB or smaller.', 'lifestyle-smiles' ),
		'photo_type' => __( 'Photos must be JPG, PNG or WebP images.', 'lifestyle-smiles' ),
		'mail'       => __( 'We could not send your enquiry. Please call the practice instead.', 'lifestyle-smiles' ),
	);

	return isset( $messages[ $code ] ) ? $messages[ $code ] : __( 'Something went wrong. Please try again.', 'lifestyle-smiles' );
}
