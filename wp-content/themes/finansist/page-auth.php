<?php
/**
 * Page Name: Page - registration
 *
 * @package flat-bootstrap
 */
if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout') {
	wp_logout();
	wp_redirect('/auth/');
}
if (is_user_logged_in()) {
	wp_redirect('/user/');
}
get_header('clear'); ?>

	<div id="primary" class="content-area-wide full-height">
		<main id="main" class="site-main" role="main">
			<h1 style="text-align: center;"><?= __('Авторизация') ?></h1>

			<?// echo do_shortcode('[wppb-login]')?>
			<? echo do_shortcode('[profilegrid_login]')?>

		</main><!-- #main -->
	</div><!-- #primary -->

	<?php //get_sidebar(); ?>

<?php get_footer('clear'); ?>
