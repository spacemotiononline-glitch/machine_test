<?php
/**
 * "A Natural Refresh" section.
 *
 * @package Lifestyle_Smiles
 */

$items = array(
	__( 'Small chips/ Broken teeth', 'lifestyle-smiles' ),
	__( 'Stained fillings/Old composite bonding', 'lifestyle-smiles' ),
	__( 'Minor unevenness in front teeth', 'lifestyle-smiles' ),
	__( 'Yellow teeth', 'lifestyle-smiles' ),
	__( 'Missing teeth', 'lifestyle-smiles' ),
	__( 'Too small/too big teeth', 'lifestyle-smiles' ),
);
?>
<section class="section section--cream refresh" id="refresh" aria-labelledby="refresh-title">
	<div class="container">
		<div class="refresh__grid">
			<div class="refresh__media">
				<h2 id="refresh-title" class="heading heading--lg">
					<?php esc_html_e( 'A Natural Refresh Before Your', 'lifestyle-smiles' ); ?>
					<em><?php esc_html_e( 'Big Moment', 'lifestyle-smiles' ); ?></em>
				</h2>
				<img class="rounded-photo" src="<?php echo esc_url( smile_asset( 'images/couple.jpg' ) ); ?>" alt="<?php esc_attr_e( 'A smiling bride and groom close together at their wedding', 'lifestyle-smiles' ); ?>" width="548" height="354" loading="lazy" decoding="async">
			</div>

			<div class="refresh__list">
				<p class="refresh__intro"><?php esc_html_e( 'This treatment may be suitable for people with', 'lifestyle-smiles' ); ?></p>
				<div class="panel">
					<ul class="check-list">
						<?php foreach ( $items as $item ) : ?>
							<li><?php echo smile_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $item ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</div>

		<p class="note-bar"><?php esc_html_e( 'When appropriate, we can achieve a natural, refined result without committing to more complex treatment.', 'lifestyle-smiles' ); ?></p>

		<div class="section__cta">
			<?php smile_cta_button( __( 'Check Your Suitability', 'lifestyle-smiles' ) ); ?>
		</div>
	</div>
</section>
