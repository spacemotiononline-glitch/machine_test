<?php
/**
 * Basic SEO for the landing page: document title, meta description,
 * Open Graph / Twitter tags and Dentist structured data (JSON-LD).
 *
 * Steps aside when a dedicated SEO plugin is active, so tags are not duplicated.
 *
 * @package Lifestyle_Smiles
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a dedicated SEO plugin already outputs these tags.
 *
 * @return bool
 */
function smile_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' )     // Yoast SEO.
		|| defined( 'RANK_MATH_VERSION' ) // Rank Math.
		|| defined( 'AIOSEO_VERSION' )    // All in One SEO.
		|| defined( 'SEOPRESS_VERSION' ); // SEOPress.
}

/**
 * Front-page headline (used in the <title>, og:title and schema name).
 *
 * @return string
 */
function smile_seo_headline() {
	return __( 'Same-Day Smile Glow Up in Brighton', 'lifestyle-smiles' );
}

/**
 * Meta description from the Customizer, trimmed.
 *
 * @return string
 */
function smile_seo_description() {
	return trim( smile_get_option( 'meta_description' ) );
}

/**
 * Image used for social previews: the hero image when set, otherwise the logo.
 *
 * @return string Absolute URL.
 */
function smile_seo_image() {
	$hero = smile_get_option( 'hero_image' );
	return $hero ? $hero : smile_asset( 'images/logo.png' );
}

/**
 * Front-page document title, e.g. "Same-Day Smile Glow Up in Brighton – Lifestyle Smiles".
 *
 * @param array $parts Title parts (title, page, tagline, site).
 * @return array
 */
function smile_document_title_parts( $parts ) {
	if ( is_front_page() && ! smile_seo_plugin_active() ) {
		$parts['title'] = smile_seo_headline();
	}
	return $parts;
}
add_filter( 'document_title_parts', 'smile_document_title_parts' );

/**
 * Meta description, Open Graph and Twitter Card tags for the front page.
 */
function smile_seo_meta_tags() {
	if ( ! is_front_page() || smile_seo_plugin_active() ) {
		return;
	}

	$description = smile_seo_description();
	$image       = smile_seo_image();

	if ( '' !== $description ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
	}

	$og = array(
		'og:type'        => 'website',
		'og:site_name'   => get_bloginfo( 'name' ),
		'og:title'       => smile_seo_headline(),
		'og:description' => $description,
		'og:url'         => home_url( '/' ),
		'og:image'       => $image,
		'og:locale'      => str_replace( '-', '_', get_locale() ),
	);

	foreach ( $og as $property => $content ) {
		if ( '' !== (string) $content ) {
			printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $property ), esc_attr( $content ) );
		}
	}

	printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );
}
add_action( 'wp_head', 'smile_seo_meta_tags', 2 );

/**
 * Dentist structured data (JSON-LD) for the front page.
 */
function smile_seo_structured_data() {
	if ( ! is_front_page() || smile_seo_plugin_active() ) {
		return;
	}

	$hours = array();
	foreach ( smile_opening_hours() as $row ) {
		if ( $row['spec'] ) {
			$hours[] = $row['spec'];
		}
	}

	$data = array(
		'@context'                  => 'https://schema.org',
		'@type'                     => 'Dentist',
		'name'                      => get_bloginfo( 'name' ),
		'description'               => smile_seo_description(),
		'url'                       => home_url( '/' ),
		'image'                     => smile_seo_image(),
		'telephone'                 => smile_get_option( 'phone' ),
		'address'                   => array(
			'@type'          => 'PostalAddress',
			'streetAddress'  => smile_get_option( 'address' ),
		),
		'openingHoursSpecification' => $hours,
	);

	// Drop empty values so Google does not see blank fields.
	$data = array_filter(
		$data,
		function ( $value ) {
			return ! ( '' === $value || array() === $value );
		}
	);

	// JSON_HEX_TAG keeps "</script>" out of the output, so it is safe inside the script element.
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-encoded with JSON_HEX_TAG.
}
add_action( 'wp_head', 'smile_seo_structured_data', 3 );
