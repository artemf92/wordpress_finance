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

<?php if (!current_user_can('contributor')) { 
	get_template_part( 'content', 'header' ); 
} ?>

<?php get_sidebar( 'home' ); ?>

<div class="container">
<div id="main-grid" class="row">

	<div id="primary" class="content-area col-md-12">
		<main id="main" class="site-main" role="main">
			<? isProjectManager(get_the_ID(), getUserID()) ?>
			<? if (current_user_can('contributor') && !current_user_can('project_manager') ) {
				get_template_part( 'template-parts/page', 'block' );
			} else {
				get_template_part('template-parts/analytics/content', 'statistics');
				get_template_part('template-parts/analytics/content', 'reports');
			} ?>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?//php get_sidebar(); ?>
		
</div><!-- .row -->
</div><!-- .container -->

<?php get_footer(); ?>
