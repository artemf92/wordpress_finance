<?php
/**
 * Page Name: Page - registration
 *
 * @package flat-bootstrap
 */

get_header(); ?>

<?php if (hasAccess()) { 
	get_template_part( 'content', 'header' ); 
} ?>

<div class="container">
	<div id="main-grid" class="row">

		<div id="primary" class="content-area-wide col-md-12">
			<main id="main" class="site-main" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<?php echo do_shortcode('[transactions_all]') ?>

				<?php endwhile; // end of the loop. ?>

			</main><!-- #main -->
		</div><!-- #primary -->

		<?php //get_sidebar(); ?>

	</div><!-- .row -->
</div><!-- .container -->

<?php get_footer(); ?>
