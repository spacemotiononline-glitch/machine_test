<?php
/**
 * "Why Lifestyle Smiles?" section.
 *
 * @package Lifestyle_Smiles
 */

$services = array(
	__( 'General and restorative dentistry', 'lifestyle-smiles' ),
	__( 'Digital smile planning', 'lifestyle-smiles' ),
	__( 'Composite bonding', 'lifestyle-smiles' ),
	__( 'Porcelain veneers', 'lifestyle-smiles' ),
	__( 'Invisalign', 'lifestyle-smiles' ),
	__( 'Dental Implants', 'lifestyle-smiles' ),
	__( 'All-on-4 Plus', 'lifestyle-smiles' ),
);
?>
<section class="section section--green why" id="why" aria-labelledby="why-title">
	<div class="container">
		<h2 id="why-title" class="heading heading--lg heading--center">
			<?php esc_html_e( 'Why', 'lifestyle-smiles' ); ?>
			<em><?php esc_html_e( 'Lifestyle Smiles?', 'lifestyle-smiles' ); ?></em>
		</h2>
		<p class="why__lead"><?php esc_html_e( 'Lifestyle Smiles provides comprehensive dental care with a focus on appropriate, evidence-based cosmetic treatment.', 'lifestyle-smiles' ); ?></p>

		<div class="why__grid">
			<div>
				<h3 class="why__label"><?php esc_html_e( 'Our team offers experience in:', 'lifestyle-smiles' ); ?></h3>
				<ul class="star-list">
					<?php foreach ( $services as $service ) : ?>
						<li><?php echo smile_icon( 'star' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $service ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</div>

			<div class="approach">
				<h3 class="approach__title"><?php esc_html_e( 'Our Approach to Care', 'lifestyle-smiles' ); ?></h3>
				<p><?php esc_html_e( 'Each patient is assessed individually to determine whether treatment is suitable, and to ensure options are explained clearly.', 'lifestyle-smiles' ); ?></p>
				<div class="approach__box">
					<h4><?php esc_html_e( 'For same-day treatments', 'lifestyle-smiles' ); ?></h4>
					<p><?php esc_html_e( 'We focus on conservative approaches, careful planning, and outcomes aligned with your dental health and goals.', 'lifestyle-smiles' ); ?></p>
				</div>
			</div>
		</div>

		<div class="section__cta">
			<?php smile_cta_button( __( 'Check Your Suitability', 'lifestyle-smiles' ) ); ?>
		</div>
	</div>
</section>
