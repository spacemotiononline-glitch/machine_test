<?php
/**
 * Patient stories (horizontally scrollable cards).
 *
 * @package Lifestyle_Smiles
 */

// The Figma frame shows the same patient story twice; replace with distinct stories as they become available.
$stories = array(
	array(
		'image'  => smile_asset( 'images/patient-story.jpg' ),
		'alt'    => __( 'Before and after photos of a smiling patient', 'lifestyle-smiles' ),
		'note'   => __( 'After photo taken approximately 7 months after procedure.', 'lifestyle-smiles' ),
		'dentist' => 'Dr. Rosana Prada',
		'reg'    => 'DEN0002551420',
	),
	array(
		'image'  => smile_asset( 'images/patient-story.jpg' ),
		'alt'    => __( 'Before and after photos of a smiling patient', 'lifestyle-smiles' ),
		'note'   => __( 'After photo taken approximately 7 months after procedure.', 'lifestyle-smiles' ),
		'dentist' => 'Dr. Rosana Prada',
		'reg'    => 'DEN0002551420',
	),
);
?>
<section class="section section--white stories" id="stories" aria-labelledby="stories-title">
	<div class="container">
		<h2 id="stories-title" class="heading heading--lg heading--center">
			<?php esc_html_e( 'Patient', 'lifestyle-smiles' ); ?>
			<em><?php esc_html_e( 'Stories', 'lifestyle-smiles' ); ?></em>
		</h2>

		<div class="stories__track" role="group" aria-label="<?php esc_attr_e( 'Patient stories', 'lifestyle-smiles' ); ?>" tabindex="0">
			<?php foreach ( $stories as $story ) : ?>
				<article class="story-card">
					<img class="story-card__logo" src="<?php echo esc_url( smile_asset( 'images/logo.png' ) ); ?>" alt="" width="265" height="65" loading="lazy">
					<img class="story-card__photo" src="<?php echo esc_url( $story['image'] ); ?>" alt="<?php echo esc_attr( $story['alt'] ); ?>" width="541" height="387" loading="lazy" decoding="async">
					<p class="story-card__note"><?php echo esc_html( $story['note'] ); ?></p>
					<p class="story-card__by"><?php esc_html_e( 'Treatment done by:', 'lifestyle-smiles' ); ?></p>
					<p class="story-card__dentist">
						<?php echo esc_html( $story['dentist'] ); ?><br>
						<?php
						/* translators: %s: registration number */
						printf( esc_html__( 'Reg no. %s', 'lifestyle-smiles' ), esc_html( $story['reg'] ) );
						?>
					</p>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="stories__dots" role="group" aria-label="<?php esc_attr_e( 'Choose a patient story', 'lifestyle-smiles' ); ?>">
			<?php foreach ( $stories as $i => $story ) : ?>
				<button type="button" class="stories__dot" data-story-dot="<?php echo esc_attr( $i ); ?>" aria-current="<?php echo 0 === $i ? 'true' : 'false'; ?>">
					<span class="screen-reader-text">
						<?php
						/* translators: %d: story number */
						printf( esc_html__( 'Show story %d', 'lifestyle-smiles' ), (int) $i + 1 );
						?>
					</span>
				</button>
			<?php endforeach; ?>
		</div>

		<p class="note-bar note-bar--italic"><?php esc_html_e( 'These photos show a real patient of Lifestyle Smiles with their written consent; images are genuine, and taken under consistent conditions without filters, have not been digitally altered with AI tools, or artificial lighting effects.', 'lifestyle-smiles' ); ?></p>
	</div>
</section>
