<?php
/**
 * Landing page template.
 *
 * @package Lifestyle_Smiles
 */

get_header();
?>
<main id="main" class="site-main">
	<?php
	get_template_part( 'template-parts/hero' );
	get_template_part( 'template-parts/refresh' );
	get_template_part( 'template-parts/stories' );
	get_template_part( 'template-parts/why' );
	get_template_part( 'template-parts/dentist' );
	get_template_part( 'template-parts/suitability' );
	?>
</main>
<?php
get_footer();
