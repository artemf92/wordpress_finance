<?php
/**
 * Template Post Type: post, page, product, projects
 * 
 * The template used for displaying page content for fullwidth pages. It contains 
 * everything after the_content()
 *
 * @package flat-bootstrap
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div id="xsbf-entry-content" class="entry-content">

		<? get_template_part('template-parts/transactions/navbar', 'transactions') ?>

		<!-- Tab panes -->
		<div class="tab-content">

			<? get_template_part('template-parts/transactions/tab', 'info') ?>
			<? get_template_part('template-parts/transactions/tab', 'history') ?>
			<? get_template_part('template-parts/transactions/tab', 'investors') ?>
			<? get_template_part('template-parts/transactions/tab', 'transactions') ?>
			<?// get_template_part('template-parts/transactions/tab', 'stages') ?>

		</div>

		<?//php get_template_part( 'content', 'page-nav' ); ?>

		<?php edit_post_link( __( '<span class="glyphicon glyphicon-edit"></span> Edit', 'flat-bootstrap' ), '<div class="container"><footer class="entry-meta"><div class="edit-link">', '</div></div></footer>' ); ?>
	</div><!-- .entry-content -->
	
</article><!-- #post-## -->
