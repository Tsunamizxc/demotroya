<?php
/**
 * Default page.
 *
 * @package Troya
 */

get_header();
?>
<section class="section">
	<div class="container">
		<?php while ( have_posts() ) : ?>
			<?php the_post(); ?>
			<article <?php post_class( 'reveal' ); ?>>
				<h1 class="heading gold-line"><?php the_title(); ?></h1>
				<div class="text"><?php the_content(); ?></div>
			</article>
		<?php endwhile; ?>
	</div>
</section>
<?php
get_footer();
