<?php
/**
 * Site header.
 *
 * @package Lifestyle_Smiles
 */

$phone      = smile_get_option( 'phone' );
$phone_href = preg_replace( '/[^\d+]/', '', $phone );
$cta_url    = smile_cta_url();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'lifestyle-smiles' ); ?></a>

<header class="site-header">
	<div class="container site-header__inner">
		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'Lifestyle Smiles home', 'lifestyle-smiles' ); ?>">
			<img src="<?php echo esc_url( smile_asset( 'images/logo.png' ) ); ?>" alt="Lifestyle Smiles" width="265" height="65">
		</a>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'lifestyle-smiles' ); ?></span>
			<span class="nav-toggle__bars" aria-hidden="true"></span>
		</button>

		<div class="site-header__panel" id="primary-nav">
			<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Primary', 'lifestyle-smiles' ); ?>">
				<?php smile_primary_nav(); ?>
			</nav>
			<?php if ( $phone ) : ?>
				<a class="pill pill--phone" href="tel:<?php echo esc_attr( $phone_href ); ?>">
					<svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.57 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1z"/></svg>
					<span><?php echo esc_html( $phone ); ?></span>
				</a>
			<?php endif; ?>
			<a class="pill pill--gold" href="<?php echo esc_url( $cta_url ); ?>"><?php esc_html_e( 'Book Consultation', 'lifestyle-smiles' ); ?></a>
		</div>
	</div>
</header>
