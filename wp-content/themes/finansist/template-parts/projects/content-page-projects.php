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

		<? get_template_part('template-parts/projects/navbar', 'projects') ?>

		<!-- Tab panes -->
		<div class="tab-content">

			<? get_template_part('template-parts/projects/tab', 'info') ?>
			<? get_template_part('template-parts/projects/tab', 'history') ?>
			<? if (current_user_can('administrator') || current_user_can('mananger') || current_user_can('accountant') || isProjectManager(get_the_ID(), getUserID())) {
				get_template_part('template-parts/projects/tab', 'investors');
				get_template_part('template-parts/projects/tab', 'transactions');
			} ?>
			<?// get_template_part('template-parts/projects/tab', 'stages') ?>	

		</div>

		<?php edit_post_link( __( '<span class="glyphicon glyphicon-edit"></span> Изменить проект', 'flat-bootstrap' ), '<div class="container"><footer class="entry-meta"><div class="edit-link">', '</div></div></footer>' ); ?>
	</div><!-- .entry-content -->
	
</article><!-- #post-## -->
