<?php
/**
 * Theme bootstrap: setup, assets, resource hints.
 *
 * @package Lifestyle_Smiles
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SMILE_VERSION', '1.1.0' );

require_once get_template_directory() . '/inc/helpers.php';
require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/form-handler.php';
require_once get_template_directory() . '/inc/seo.php';

/**
 * Theme support and menus.
 */
function smile_setup() {
	load_theme_textdomain( 'lifestyle-smiles', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'lifestyle-smiles' ),
		)
	);
}
add_action( 'after_setup_theme', 'smile_setup' );

/**
 * Cache-busting version for a file in /assets, based on its modification time.
 *
 * @param string $path Relative path inside /assets.
 * @return string
 */
function smile_asset_version( $path ) {
	$file = get_template_directory() . '/assets/' . ltrim( $path, '/' );
	return file_exists( $file ) ? (string) filemtime( $file ) : SMILE_VERSION;
}

/**
 * Enqueue styles and scripts.
 */
function smile_enqueue_assets() {
	// Playfair Display (headings). Poppins is self-hosted via main.css.
	wp_enqueue_style(
		'smile-fonts',
		'https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400;1,500&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'smile-main',
		smile_asset( 'css/main.css' ),
		array( 'smile-fonts' ),
		smile_asset_version( 'css/main.css' )
	);

	// Loaded in the footer, so it never blocks rendering.
	wp_enqueue_script(
		'smile-main',
		smile_asset( 'js/main.js' ),
		array(),
		smile_asset_version( 'js/main.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'smile_enqueue_assets' );

/**
 * Preconnect to the Google Fonts hosts.
 *
 * @param array  $urls          URLs.
 * @param string $relation_type Relation type.
 * @return array
 */
function smile_resource_hints( $urls, $relation_type ) {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}
	return $urls;
}
add_filter( 'wp_resource_hints', 'smile_resource_hints', 10, 2 );

/**
 * Preload the hero image when one is set (it is the LCP element).
 */
function smile_preload_hero() {
	$hero = smile_get_option( 'hero_image' );
	if ( is_front_page() && $hero ) {
		printf( '<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n", esc_url( $hero ) );
	}
}
add_action( 'wp_head', 'smile_preload_hero', 1 );

/**
 * Add the "js" class before first paint so the JS-enhanced layout does not flash.
 * main.js runs in the footer, so this inline line is the only place it is set.
 */
function smile_js_class() {
	echo "<script>document.documentElement.classList.add('js');</script>\n";
}
add_action( 'wp_head', 'smile_js_class', 0 );
