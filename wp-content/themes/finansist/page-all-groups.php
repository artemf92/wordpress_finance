<?php
/**
 * Theme: Flat Bootstrap
 * 
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @package flat-bootstrap
 */

get_header(); 
?>

<?php if (current_user_can('administrator') || current_user_can('manager')) { 
	get_template_part( 'content', 'header' ); 
} ?>

<?php get_sidebar( 'home' ); ?>

<div class="container">
<div id="main-grid" class="row">

	<div id="primary" class="content-area col-md-12">
		<main id="main" class="site-main" role="main">

			<? if (!current_user_can('administrator') && !current_user_can('manager')) {
				get_template_part( 'template-parts/page', 'block' );
			} else {
					echo do_shortcode('[profilegrid_groups]');
			} ?>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?//php get_sidebar(); ?>
		
</div><!-- .row -->
</div><!-- .container -->

<?php get_footer(); ?>
