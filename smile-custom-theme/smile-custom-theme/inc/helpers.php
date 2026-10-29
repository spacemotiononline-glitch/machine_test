<?php
/**
 * Template helpers: options, asset URLs, navigation, icons and buttons.
 *
 * @package Lifestyle_Smiles
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default values for every Customizer setting.
 *
 * @return array
 */
function smile_defaults() {
	return array(
		'phone'            => '',
		'booking_url'      => '#suitability',
		'notify_email'     => '',
		'address'          => 'Shop 2, 28 Carpenter St, Brighton',
		'hero_image'       => '',
		'disclaimer_url'   => '#',
		'privacy_url'      => '#',
		'meta_description' => 'Same-day smile glow up at Lifestyle Smiles, Brighton. Check whether cosmetic dental treatment is suitable for you with a short online assessment.',
	);
}

/**
 * Get a theme option with fallback to its default.
 *
 * @param string $key Option key (without the "smile_" prefix).
 * @return string
 */
function smile_get_option( $key ) {
	$defaults = smile_defaults();
	$default  = isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
	return (string) get_theme_mod( 'smile_' . $key, $default );
}

/**
 * URL of a file in the theme's assets directory.
 *
 * @param string $path Relative path inside /assets.
 * @return string
 */
function smile_asset( $path ) {
	return get_template_directory_uri() . '/assets/' . ltrim( $path, '/' );
}

/**
 * URL used by the "Check Your Suitability" CTAs.
 *
 * @return string
 */
function smile_cta_url() {
	$url = smile_get_option( 'booking_url' );
	return $url ? $url : '#suitability';
}

/**
 * Opening hours, shown in the footer and exposed to search engines.
 *
 * Each row has a visible label and an optional schema.org OpeningHoursSpecification.
 *
 * @return array
 */
function smile_opening_hours() {
	return array(
		array(
			'label' => __( 'Mon – Fri, 8:00am – 5:30pm', 'lifestyle-smiles' ),
			'spec'  => array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' ),
				'opens'     => '08:00',
				'closes'    => '17:30',
			),
		),
		array(
			'label' => __( 'Sat - By appointment only', 'lifestyle-smiles' ),
			'spec'  => null,
		),
		array(
			'label' => __( 'Sun - Closed', 'lifestyle-smiles' ),
			'spec'  => null,
		),
	);
}

/**
 * Primary navigation. Uses the assigned menu, otherwise anchor links.
 */
function smile_primary_nav() {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'primary-nav__list',
				'depth'          => 1,
			)
		);
		return;
	}

	// Placeholder targets until real destinations are assigned in Appearance > Menus.
	$items = array(
		__( 'Suitability', 'lifestyle-smiles' )  => '#suitability',
		__( 'Consultation', 'lifestyle-smiles' ) => '#why',
		__( 'Journey', 'lifestyle-smiles' )      => '#stories',
		__( 'Pricing', 'lifestyle-smiles' )      => '#suitability',
		__( 'About Us', 'lifestyle-smiles' )     => '#about',
	);

	echo '<ul class="primary-nav__list">';
	foreach ( $items as $label => $href ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $href ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * SVG elements and attributes allowed in the theme's inline icons.
 *
 * @return array
 */
function smile_svg_allowed() {
	$paint = array(
		'fill'            => true,
		'stroke'          => true,
		'stroke-width'    => true,
		'stroke-linecap'  => true,
		'stroke-linejoin' => true,
	);

	return array(
		'svg'    => array_merge(
			$paint,
			array(
				'class'       => true,
				'width'       => true,
				'height'      => true,
				'viewbox'     => true,
				'aria-hidden' => true,
				'focusable'   => true,
			)
		),
		'path'   => array_merge( $paint, array( 'd' => true ) ),
		'circle' => array_merge(
			$paint,
			array(
				'cx' => true,
				'cy' => true,
				'r'  => true,
			)
		),
	);
}

/**
 * Inline SVG icons used by the page sections.
 *
 * @param string $name Icon name.
 * @return string SVG markup, sanitised with smile_svg_allowed().
 */
function smile_icon( $name ) {
	$icons = array(
		'check'    => '<svg class="icon" width="20" height="20" viewBox="0 0 20 20" aria-hidden="true" focusable="false"><circle cx="10" cy="10" r="8.5" fill="none" stroke="currentColor" stroke-width="1"/><path d="M6.4 10.2l2.4 2.4 4.8-5" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'star'     => '<svg class="icon" width="22" height="22" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2.8l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 16.8l-5.4 2.9 1-6.1-4.4-4.3 6.1-.9z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>',
		'heart'    => '<svg class="icon" width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" d="M12 20.5S3.5 15 3.5 9A4.5 4.5 0 0 1 12 7a4.5 4.5 0 0 1 8.5 2c0 6-8.5 11.5-8.5 11.5z"/></svg>',
		'document' => '<svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" d="M6 3h8l4 4v14H6zM14 3v4h4M9 12h6M9 15h6M9 9h2"/></svg>',
		'image'    => '<svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" d="M4 5h16v14H4zM4 16l5-5 4 4 3-3 4 4"/><circle cx="9" cy="9" r="1.2" fill="currentColor"/></svg>',
		'steth'    => '<svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" d="M6 3v6a4 4 0 0 0 8 0V3M10 13v2a4 4 0 0 0 8 0v-1"/><circle cx="18" cy="12" r="2" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>',
		'upload'   => '<svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M12 16V5M7 9l5-5 5 5M5 19h14"/></svg>',
		'arrow'    => '<svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	return wp_kses( $icons[ $name ], smile_svg_allowed() );
}

/**
 * Print a gold gradient CTA button.
 *
 * @param string $label Button text.
 * @param string $url   Destination. Defaults to the suitability CTA URL.
 */
function smile_cta_button( $label, $url = '' ) {
	$url = $url ? $url : smile_cta_url();
	printf(
		'<a class="btn" href="%1$s"><span>%2$s</span>%3$s</a>',
		esc_url( $url ),
		esc_html( $label ),
		smile_icon( 'arrow' )
	);
}
