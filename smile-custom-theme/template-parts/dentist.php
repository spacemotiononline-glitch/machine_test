<?php
/**
 * "Meet Dr Julie Taing" section.
 *
 * @package Lifestyle_Smiles
 */

$focus = array(
	__( 'Clear communication', 'lifestyle-smiles' ),
	__( 'Minimally invasive treatment where appropriate', 'lifestyle-smiles' ),
	__( 'Maintaining natural tooth structure', 'lifestyle-smiles' ),
);
?>
<section class="section section--white dentist" id="about" aria-labelledby="dentist-title">
	<div class="container">
		<div class="dentist__grid">
			<figure class="dentist__figure">
				<img class="rounded-photo" src="<?php echo esc_url( smile_asset( 'images/dr-julie.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Dr Julie Taing, cosmetic dentist at Lifestyle Smiles', 'lifestyle-smiles' ); ?>" width="374" height="374" loading="lazy" decoding="async">
				<figcaption>
					<h2 id="dentist-title" class="heading heading--md">
						<?php esc_html_e( 'Meet', 'lifestyle-smiles' ); ?>
						<em><?php esc_html_e( 'Dr Julie Taing', 'lifestyle-smiles' ); ?></em>
					</h2>
					<p class="dentist__role"><?php esc_html_e( 'Cosmetic Dentist', 'lifestyle-smiles' ); ?></p>
				</figcaption>
			</figure>

			<div class="dentist__body">
				<p><?php esc_html_e( 'Dr Julie Taing is a general dentist at Lifestyle Smiles with an interest in cosmetic and restorative dentistry, including composite bonding, veneers and smile enhancement.', 'lifestyle-smiles' ); ?></p>
				<p class="dentist__focus"><em><?php esc_html_e( 'She provides patient-centred care with a focus on:', 'lifestyle-smiles' ); ?></em></p>
				<ul class="pill-list">
					<?php foreach ( $focus as $item ) : ?>
						<li><?php echo smile_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $item ); ?></span></li>
					<?php endforeach; ?>
				</ul>
				<blockquote class="quote">
					<p><?php esc_html_e( '"Dr Julie works closely with patients to understand their concerns and to determine suitable treatment options based on their individual needs and oral health."', 'lifestyle-smiles' ); ?></p>
				</blockquote>
				<?php smile_cta_button( __( 'Apply Now', 'lifestyle-smiles' ) ); ?>
			</div>
		</div>
	</div>
</section>
