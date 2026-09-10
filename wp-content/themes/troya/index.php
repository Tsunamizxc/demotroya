<?php
/**
 * Fallback index.
 *
 * @package Troya
 */

get_header();
?>
<section class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : ?>
				<?php the_post(); ?>
				<article <?php post_class(); ?>>
					<h1 class="heading"><?php the_title(); ?></h1>
					<div class="text"><?php the_content(); ?></div>
				</article>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
