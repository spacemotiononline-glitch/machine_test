<?php
/**
 * Site footer.
 *
 * @package Lifestyle_Smiles
 */

$address = smile_get_option( 'address' );
$maps    = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $address );
?>
<footer class="site-footer">
	<div class="container">
		<div class="site-footer__grid">
			<div class="site-footer__brand">
				<img src="<?php echo esc_url( smile_asset( 'images/logo.png' ) ); ?>" alt="Lifestyle Smiles" width="265" height="65" loading="lazy">
				<p><?php esc_html_e( 'Serving the Brighton community for over 50 years with compassionate, comprehensive dental care.', 'lifestyle-smiles' ); ?></p>
			</div>

			<div class="site-footer__col">
				<h2 class="site-footer__heading"><?php esc_html_e( 'Location', 'lifestyle-smiles' ); ?></h2>
				<p><?php echo esc_html( $address ); ?></p>
				<a class="site-footer__link" href="<?php echo esc_url( $maps ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Get Directions', 'lifestyle-smiles' ); ?>
					<span class="screen-reader-text"><?php esc_html_e( '(opens in a new tab)', 'lifestyle-smiles' ); ?></span>
					<span aria-hidden="true">&rarr;</span>
				</a>
			</div>

			<div class="site-footer__col">
				<h2 class="site-footer__heading"><?php esc_html_e( 'Contact Details', 'lifestyle-smiles' ); ?></h2>
				<p><?php esc_html_e( 'Opening Hours', 'lifestyle-smiles' ); ?></p>
				<p><?php esc_html_e( 'Mon – Fri, 8:00am – 5:30pm', 'lifestyle-smiles' ); ?></p>
				<p><?php esc_html_e( 'Sat - By appointment only', 'lifestyle-smiles' ); ?></p>
				<p><?php esc_html_e( 'Sun - Closed', 'lifestyle-smiles' ); ?></p>
			</div>
		</div>

		<p class="site-footer__legal">
			<?php
			printf(
				/* translators: 1: year, 2: site name */
				esc_html__( 'Copyright © %1$s %2$s. All Rights Reserved.', 'lifestyle-smiles' ),
				esc_html( wp_date( 'Y' ) ),
				esc_html( get_bloginfo( 'name' ) )
			);
			?>
			|
			<a href="<?php echo esc_url( smile_get_option( 'disclaimer_url' ) ); ?>"><?php esc_html_e( 'Disclaimer', 'lifestyle-smiles' ); ?></a>
			|
			<a href="<?php echo esc_url( smile_get_option( 'privacy_url' ) ); ?>"><?php esc_html_e( 'Privacy Statement', 'lifestyle-smiles' ); ?></a>
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
