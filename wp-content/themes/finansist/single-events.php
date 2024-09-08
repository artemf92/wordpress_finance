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

if (!is_user_logged_in()) {
	wp_redirect('/auth/');
}

get_header(); ?>

<?php get_template_part( 'content', 'header' ); ?>

<div class="container">
<div id="main-grid" class="row">

	<div id="primary" class="content-area col-md-12">
		<main id="main" class="site-main" role="main">

			<?php get_template_part( 'template-parts/events/content', 'page-events' ); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				// if ( comments_open() || '0' != get_comments_number() )
				// 	comments_template();
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

</div><!-- .row -->
</div><!-- .container -->

<?php get_footer(); ?>