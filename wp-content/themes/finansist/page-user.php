<?php
/**
 * Page Name: Page - User
 *
 * @package flat-bootstrap
 */
if (!is_user_logged_in()) {
	wp_redirect('/auth/');
}

$USER_ID = getUserID();

get_header(); ?>

<?php if (hasAccess() || get_query_var('user_id') == '') { 
	get_template_part( 'template-parts/user', 'header' ); 
} ?>

<?php get_sidebar( 'home' ); ?>

<div class="container">
	<div id="main-grid" class="row">

		<div id="primary" class="content-area-wide col-md-12">
			<main id="main" class="site-main" role="main">

				<?php while ( have_posts() ) : the_post(); ?>

					<? if (!hasAccess() && get_query_var('user_id') != '') {
						get_template_part( 'template-parts/user', 'block' );
					} else {
						get_template_part( 'template-parts/user', 'profile' );
					} ?>

					<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() ) :
					?>
					<div class="comments-wrap">
					<?php comments_template(); ?>
					</div><!-- .comments-wrap" -->
					<?php endif; ?>

				<?php endwhile; // end of the loop. ?>

			</main><!-- #main -->
		</div><!-- #primary -->

		<?php //get_sidebar(); ?>

	</div><!-- .row -->
</div><!-- .container -->

<?php get_footer(); ?>
