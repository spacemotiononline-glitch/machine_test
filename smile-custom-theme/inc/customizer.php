<?php
/**
 * Customizer settings for practice details.
 *
 * @package Lifestyle_Smiles
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the "Practice details" section.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function smile_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'smile_practice',
		array(
			'title'    => __( 'Practice details', 'lifestyle-smiles' ),
			'priority' => 30,
		)
	);

	$fields = array(
		'phone'            => array( __( 'Phone number', 'lifestyle-smiles' ), 'sanitize_text_field', 'text' ),
		'address'          => array( __( 'Address', 'lifestyle-smiles' ), 'sanitize_text_field', 'text' ),
		'booking_url'      => array( __( 'Booking / suitability CTA URL', 'lifestyle-smiles' ), 'smile_sanitize_url_or_anchor', 'text' ),
		'notify_email'     => array( __( 'Enquiry notification email (defaults to the site admin email)', 'lifestyle-smiles' ), 'sanitize_email', 'email' ),
		'disclaimer_url'   => array( __( 'Disclaimer page URL', 'lifestyle-smiles' ), 'smile_sanitize_url_or_anchor', 'text' ),
		'privacy_url'      => array( __( 'Privacy statement page URL', 'lifestyle-smiles' ), 'smile_sanitize_url_or_anchor', 'text' ),
		'meta_description' => array( __( 'Meta description', 'lifestyle-smiles' ), 'sanitize_text_field', 'textarea' ),
	);

	$defaults = smile_defaults();

	foreach ( $fields as $key => $field ) {
		$wp_customize->add_setting(
			'smile_' . $key,
			array(
				'default'           => $defaults[ $key ],
				'sanitize_callback' => $field[1],
			)
		);
		$wp_customize->add_control(
			'smile_' . $key,
			array(
				'label'   => $field[0],
				'section' => 'smile_practice',
				'type'    => $field[2],
			)
		);
	}

	$wp_customize->add_setting(
		'smile_hero_image',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'smile_hero_image',
			array(
				'label'   => __( 'Hero image', 'lifestyle-smiles' ),
				'section' => 'smile_practice',
			)
		)
	);
}
add_action( 'customize_register', 'smile_customize_register' );

/**
 * Accept a full URL or an on-page anchor such as #suitability.
 *
 * @param string $value Raw value.
 * @return string
 */
function smile_sanitize_url_or_anchor( $value ) {
	$value = trim( (string) $value );
	if ( '' !== $value && '#' === $value[0] ) {
		return '#' . sanitize_html_class( ltrim( $value, '#' ) );
	}
	return esc_url_raw( $value );
}
