<?php
/**
 * "Check Your Suitability Online" section with the enquiry form.
 *
 * @package Lifestyle_Smiles
 */

// Read-only status flags from the post-redirect-get; values are whitelisted before use.
// phpcs:disable WordPress.Security.NonceVerification.Recommended
$status = isset( $_GET['smile_form'] ) ? sanitize_key( wp_unslash( $_GET['smile_form'] ) ) : '';
$code   = isset( $_GET['smile_code'] ) ? sanitize_key( wp_unslash( $_GET['smile_code'] ) ) : '';
// phpcs:enable
$status = in_array( $status, array( 'success', 'error' ), true ) ? $status : '';

$steps = array(
	array( 'document', __( 'Complete a Short Questionnaire', 'lifestyle-smiles' ), __( 'Share your details for a callback within 24 hours', 'lifestyle-smiles' ) ),
	array( 'image', __( 'Upload Four Smile Photos', 'lifestyle-smiles' ), __( 'Upload front, left side, right side, and tilted smile photos.', 'lifestyle-smiles' ) ),
	array( 'steth', __( 'Clinical Review', 'lifestyle-smiles' ), __( 'Our team will review your information and advise whether this treatment may be suitable.', 'lifestyle-smiles' ) ),
);
?>
<section class="section section--cream suitability" id="suitability" aria-labelledby="suitability-title">
	<div class="container">
		<div class="suitability__grid">
			<div class="suitability__intro">
				<h2 id="suitability-title" class="heading heading--lg">
					<?php esc_html_e( 'Check Your Suitability', 'lifestyle-smiles' ); ?>
					<em><?php esc_html_e( 'Online', 'lifestyle-smiles' ); ?></em>
				</h2>
				<p><?php esc_html_e( 'If you are considering', 'lifestyle-smiles' ); ?> <strong><?php esc_html_e( 'a same-day smile treatment', 'lifestyle-smiles' ); ?></strong><?php esc_html_e( ', you can begin with an online assessment.', 'lifestyle-smiles' ); ?></p>

				<ol class="steps">
					<?php foreach ( $steps as $i => $step ) : ?>
						<li class="step-card<?php echo 0 === $i ? ' is-active' : ''; ?>" data-step-card="<?php echo esc_attr( $i + 1 ); ?>">
							<span class="step-card__icon"><?php echo smile_icon( $step[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="step-card__num">
								<?php
								/* translators: %d: step number */
								printf( esc_html__( 'Step %d:', 'lifestyle-smiles' ), (int) $i + 1 );
								?>
							</span>
							<h3><?php echo esc_html( $step[1] ); ?></h3>
							<p><?php echo esc_html( $step[2] ); ?></p>
						</li>
					<?php endforeach; ?>
				</ol>
			</div>

			<div class="form-card">
				<?php if ( $status ) : ?>
					<p class="form-notice form-notice--<?php echo esc_attr( $status ); ?>" role="<?php echo 'error' === $status ? 'alert' : 'status'; ?>">
						<?php echo esc_html( smile_form_message( $status, $code ) ); ?>
					</p>
				<?php endif; ?>

				<form id="suitability-form" class="suitability-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" novalidate>
					<input type="hidden" name="action" value="smile_suitability">
					<?php wp_nonce_field( 'smile_suitability', 'smile_nonce' ); ?>
					<div class="hp" aria-hidden="true">
						<label for="smile_website"><?php esc_html_e( 'Leave this field empty', 'lifestyle-smiles' ); ?></label>
						<input type="text" id="smile_website" name="smile_website" tabindex="-1" autocomplete="off">
					</div>

					<fieldset class="form-step" data-step="1">
						<legend class="form-step__head">
							<span class="step-card__icon"><?php echo smile_icon( 'document' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="step-card__num"><?php esc_html_e( 'Step 1:', 'lifestyle-smiles' ); ?></span>
							<span class="form-step__title"><?php esc_html_e( 'Complete a Short Questionnaire', 'lifestyle-smiles' ); ?></span>
							<span class="form-step__sub"><?php esc_html_e( 'Share your details for a callback within 24 hours', 'lifestyle-smiles' ); ?></span>
						</legend>

						<div class="field">
							<label for="first_name"><?php esc_html_e( 'First name', 'lifestyle-smiles' ); ?>*</label>
							<input type="text" id="first_name" name="first_name" placeholder="<?php esc_attr_e( 'Enter your first name', 'lifestyle-smiles' ); ?>" autocomplete="given-name" required>
						</div>
						<div class="field">
							<label for="last_name"><?php esc_html_e( 'Last name', 'lifestyle-smiles' ); ?>*</label>
							<input type="text" id="last_name" name="last_name" placeholder="<?php esc_attr_e( 'Enter your last name', 'lifestyle-smiles' ); ?>" autocomplete="family-name" required>
						</div>
						<div class="field">
							<label for="dob"><?php esc_html_e( 'Date of birth', 'lifestyle-smiles' ); ?>*</label>
							<input type="date" id="dob" name="dob" max="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>" autocomplete="bday" required>
						</div>
						<fieldset class="field field--radio">
							<legend><?php esc_html_e( 'Gender', 'lifestyle-smiles' ); ?>*</legend>
							<div class="radio-row">
								<label><input type="radio" name="gender" value="male" required><span><?php esc_html_e( 'Male', 'lifestyle-smiles' ); ?></span></label>
								<label><input type="radio" name="gender" value="female"><span><?php esc_html_e( 'Female', 'lifestyle-smiles' ); ?></span></label>
								<label><input type="radio" name="gender" value="other"><span><?php esc_html_e( 'Other', 'lifestyle-smiles' ); ?></span></label>
							</div>
						</fieldset>
						<div class="field">
							<label for="phone"><?php esc_html_e( 'Phone number', 'lifestyle-smiles' ); ?>*</label>
							<div class="phone-row">
								<label class="screen-reader-text" for="phone_code"><?php esc_html_e( 'Country code', 'lifestyle-smiles' ); ?></label>
								<select id="phone_code" name="phone_code">
									<?php foreach ( smile_phone_codes() as $dial => $region ) : ?>
										<option value="<?php echo esc_attr( $dial ); ?>" <?php selected( '+61', $dial ); ?>><?php echo esc_html( $dial ); ?></option>
									<?php endforeach; ?>
								</select>
								<input type="tel" id="phone" name="phone" placeholder="<?php esc_attr_e( 'Enter number', 'lifestyle-smiles' ); ?>" autocomplete="tel-national" inputmode="tel" required>
							</div>
						</div>
						<div class="field">
							<label for="email"><?php esc_html_e( 'Email', 'lifestyle-smiles' ); ?>*</label>
							<input type="email" id="email" name="email" placeholder="<?php esc_attr_e( 'Enter your email address', 'lifestyle-smiles' ); ?>" autocomplete="email" required>
						</div>
					</fieldset>

					<fieldset class="form-step" data-step="2">
						<legend class="form-step__head">
							<span class="step-card__icon"><?php echo smile_icon( 'image' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="step-card__num"><?php esc_html_e( 'Step 2:', 'lifestyle-smiles' ); ?></span>
							<span class="form-step__title"><?php esc_html_e( 'Upload Four Smile Photos', 'lifestyle-smiles' ); ?></span>
							<span class="form-step__sub"><?php esc_html_e( 'JPG, PNG or WebP, up to 5 MB each.', 'lifestyle-smiles' ); ?></span>
						</legend>
						<?php foreach ( smile_photo_slots() as $field => $label ) : ?>
							<div class="field">
								<label for="<?php echo esc_attr( $field ); ?>"><?php echo esc_html( $label ); ?>*</label>
								<input type="file" id="<?php echo esc_attr( $field ); ?>" name="<?php echo esc_attr( $field ); ?>" accept="image/jpeg,image/png,image/webp" required>
							</div>
						<?php endforeach; ?>
					</fieldset>

					<p class="form-error" id="form-error" role="alert" hidden></p>

					<div class="form-actions">
						<button type="button" class="btn btn--sm" data-form-back hidden><span><?php esc_html_e( 'Back', 'lifestyle-smiles' ); ?></span></button>
						<button type="button" class="btn btn--sm" data-form-next><span><?php esc_html_e( 'Next', 'lifestyle-smiles' ); ?></span><?php echo smile_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						<button type="submit" class="btn btn--sm" data-form-submit><span><?php esc_html_e( 'Send enquiry', 'lifestyle-smiles' ); ?></span></button>
					</div>
					<p class="form-secure"><?php esc_html_e( 'Your information is transmitted securely.', 'lifestyle-smiles' ); ?></p>
				</form>
			</div>
		</div>
	</div>
</section>
