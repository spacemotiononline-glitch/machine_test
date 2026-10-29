<?php
/**
 * Hero section.
 *
 * @package Lifestyle_Smiles
 */

$hero = smile_get_option( 'hero_image' );
?>
<section class="hero" aria-labelledby="hero-title">
	<div class="container">
		<?php if ( $hero ) : ?>
			<div class="hero__media">
				<img src="<?php echo esc_url( $hero ); ?>" alt="<?php esc_attr_e( 'Lifestyle Smiles same-day smile results', 'lifestyle-smiles' ); ?>" width="1200" height="240" fetchpriority="high" decoding="async">
			</div>
		<?php endif; ?>
		<h1 id="hero-title" class="heading heading--xl">
			<?php esc_html_e( 'Same-Day Smile', 'lifestyle-smiles' ); ?>
			<em><?php esc_html_e( 'Glow Up', 'lifestyle-smiles' ); ?></em>
		</h1>
		<p class="hero__lead">
			<?php esc_html_e( 'Ready for a big event?', 'lifestyle-smiles' ); ?><br>
			<?php esc_html_e( 'Get a same-day smile glow up and transform your look in just one visit.', 'lifestyle-smiles' ); ?>
		</p>
		<?php smile_cta_button( __( 'Check Your Suitability', 'lifestyle-smiles' ) ); ?>
	</div>
</section>
