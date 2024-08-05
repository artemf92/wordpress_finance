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

<?php get_template_part( 'content', 'header' ); ?>

<?php get_sidebar( 'home' ); ?>

<div class="container">
<div id="main-grid" class="row">

	<div id="primary" class="content-area col-md-12">
		<main id="main" class="site-main" role="main">
			<? 
			$group_id = get_user_meta(get_current_user_id(), 'pm_group', true);
			
			foreach($group_id as $gid) {
				echo do_shortcode('[admin-group gid="'.$gid.'"]') ;
			}
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?//php get_sidebar(); ?>
		
</div><!-- .row -->
</div><!-- .container -->

<?php get_footer(); ?>
