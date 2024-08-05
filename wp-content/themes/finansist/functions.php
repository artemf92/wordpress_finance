<?php

add_action( 'wp_enqueue_scripts', 'my_scripts_method' );
function my_scripts_method(){
	wp_enqueue_style( 'fancybox', get_stylesheet_directory_uri() . '/assets/css/libs/fancybox.css');
	wp_enqueue_style( 'tablesaw', get_stylesheet_directory_uri() . '/assets/css/libs/tablesaw.css');
	
	wp_enqueue_script( 'fancybox', get_stylesheet_directory_uri() . '/assets/js/libs/fancybox.umd.js');
	wp_enqueue_script( 'tablesaw', get_stylesheet_directory_uri() . '/assets/js/libs/tablesaw.jquery.js', ['jquery'], time());
	// wp_enqueue_script( 'tablesaw-init', get_stylesheet_directory_uri() . '/assets/js/libs/tablesaw-init.js', ['jquery'], time());
	wp_enqueue_script( 'scripts', get_stylesheet_directory_uri() . '/assets/js/scripts.js', [], time());
	
	wp_enqueue_script( 'ajax-scripts', get_stylesheet_directory_uri() . '/assets/js/ajax.js', ['scripts'], time());

	wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . '/assets/css/custom.css', [
		'bootstrap',
		'bootstrap-utilities',
		'theme-base',
		'theme-flat',
		'flat-bootstrap'
	], time());
}

require __DIR__ . '/inc/functions.php';
require __DIR__ . '/inc/rewrite.php';

// Post types 
require __DIR__ . '/inc/post_types/projects/projects.php';
require __DIR__ . '/inc/post_types/projects/add_new.php';
require __DIR__ . '/inc/post_types/projects/start.php';
require __DIR__ . '/inc/post_types/projects/stop.php';
require __DIR__ . '/inc/post_types/projects/profit.php';
require __DIR__ . '/inc/post_types/projects/restart.php';

require __DIR__ . '/inc/post_types/events/events.php';

require __DIR__ . '/inc/post_types/transactions/transactions.php';
/////////////////////

require __DIR__ . '/inc/auth/auth.php';
require __DIR__ . '/inc/profile/profile.php';
require __DIR__ . '/inc/profile/checkout.php';


require __DIR__ . '/inc/active_projects/active_projects.php';
require __DIR__ . '/inc/archive_projects/archive_projects.php';
require __DIR__ . '/inc/all_projects/all_projects.php';
require __DIR__ . '/inc/transactions/transactions.php';
require __DIR__ . '/inc/investors/investors.php';

require __DIR__ . '/inc/admin-group/admin-group.php';
require __DIR__ . '/inc/admin-group/active_projects.php';
require __DIR__ . '/inc/admin-group/users.php';

if ($_SERVER['REQUEST_URI'] === '/') {
	header('Location: http://'.$_SERVER['HTTP_HOST'].'/user/');
	exit();
}

function check_private($postID) {
	$private_pages = [113, 115, 117, 120];
	if ($postID) {
		if (!is_user_logged_in() && in_array($postID, $private_pages)) {
			wp_redirect('/auth/');
		}
	}
}

function wph_noadmin() {
	if ( is_admin() && !current_user_can('administrator') && !current_user_can('accountant') && !wp_doing_ajax() ) {
		wp_redirect(home_url());
		exit;
	} }
add_action('init', 'wph_noadmin');

if ( !current_user_can('administrator') && !current_user_can('accountant')) {
	add_filter( 'show_admin_bar', '__return_false' ); 
}

function custom_rewrite_rule() {
	add_rewrite_rule('^user/([0-9]+)/?', 'index.php?pagename=user&user_id=$matches[1]', 'top');
}
add_action('init', 'custom_rewrite_rule', 10, 0);

function custom_query_vars($vars) {
	$vars[] = 'user_id';
	return $vars;
}
add_filter('query_vars', 'custom_query_vars');


add_action( 'wp_enqueue_scripts', 'custom_ajax_data', 99 );
function custom_ajax_data(){

	wp_localize_script( 'ajax-scripts', 'finajax',
		array(
			'url' => admin_url('admin-ajax.php')
		)
	);

}

add_action('wp_head', function() {
	if (
			(is_page('all_groups') || is_page('all_projects')) 
			&& !current_user_can('administrator') && !current_user_can('manager') && !current_user_can('accountant')
		) {
			wp_redirect('/auth/');
	}
}, 10, 0);

function wp_delete_post_link($link = 'Удалить', $before = '', $after = '')
{
	global $post;
	if ( $post->post_type == 'page' ) {
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return;
		} else {
			if ( !current_user_can( 'edit_post', $post->ID ) )
				return;
	}
	$link = "<a href='" . get_delete_post_link($post->ID) . "' data-post-delete data-post-type=" . $post->post_type . " data-backurl=". htmlentities($_SERVER['HTTP_REFERER'])." class='btn btn-danger' >".$link."</a>";
	echo $before . $link . $after;
}

add_action('wp_footer', 'show_deleted_modal', 90);
function show_deleted_modal() {
	if (isset($_GET['delete'])) {
		switch ($_GET['delete']) {
			case 'success':
				if ($_GET['deleted_type'] == 'events') {
					$message = 'Транзакции успешно удалены';
				} else if ($_GET['deleted_type'] == 'transactions') {
					$message = 'Транзакция успешно удалена';
				} else if ($_GET['deleted_type'] == 'projects') {
					$message = 'Проект успешно удален';
				} else {
					$message = 'Запись успешно удалена';
				}?>
				<script type="text/javascript">
					jQuery(document).ready(function($){
						
						if (typeof Fancybox != undefined) {
							const fancybox = new Fancybox([
								{
									src: `
									<div class="align-center d-flex flex-column">
										<i class="bg-white border-1 border-primary color-lightgreen fa-4x fa-check fa-solid m-x-3 m-y-3"></i>
										<p><?=$message?></p>
									</div>`,
									type: "html",
								},
							]);

							const url = new URLSearchParams(location.search)
							url.delete('delete')
							url.delete('deleted_type')
	
							history.pushState('', '', '?' + url.toString())
	
							setTimeout(() => {
								Fancybox.close()
							}, 1200);
						}

					})
				</script>
				<?
				break;
		}
	}
}
