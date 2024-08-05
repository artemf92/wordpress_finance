<?php
/**
 * Page Name: Page - registration
 *
 * @package flat-bootstrap
 */

get_header('clear'); ?>

	<div id="primary" class="content-area-wide full-height">
		<main id="main" class="site-main" role="main">
			<h1><?= __('Регистрация')?></h1>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page-fullwidth' ); ?>

			<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php //get_sidebar(); ?>

<?php get_footer('clear'); ?>
