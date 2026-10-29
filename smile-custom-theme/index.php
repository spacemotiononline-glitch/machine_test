<?php
/**
 * Fallback template. The landing page is rendered by front-page.php.
 *
 * @package Lifestyle_Smiles
 */

get_header();
?>
<main id="main" class="site-main container container--narrow">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<?php the_content(); ?>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<h1><?php esc_html_e( 'Nothing found', 'lifestyle-smiles' ); ?></h1>
		<p><?php esc_html_e( 'The page you are looking for does not exist.', 'lifestyle-smiles' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
