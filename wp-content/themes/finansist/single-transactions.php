<?php
/**
 * Theme: Flat Bootstrap
 * 
 * The Template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package flat-bootstrap
 */

get_header(); ?>

<?php if (hasAccess()) { 
	get_template_part( 'content', 'header' ); 
} ?>

<div class="container">
<div id="main-grid" class="row">

	<div id="primary" class="content-area col-md-12">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<? if (!hasAccess()) {
				get_template_part( 'template-parts/page', 'block' );
			} else {
				get_template_part( 'template-parts/transactions/content', 'page-transactions' );
			} ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				// if ( comments_open() || '0' != get_comments_number() )
				// 	comments_template();
			?>

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->

</div><!-- .row -->
</div><!-- .container -->

<?php get_footer(); ?>