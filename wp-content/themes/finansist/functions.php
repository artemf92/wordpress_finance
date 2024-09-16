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
	$private_pages = [113, 115, 117, 120]; //113 - активные проекты, 115 - архив проектов, 117 - все транзакции, 120 - доходность портфеля
	if ($postID) {
		if (!is_user_logged_in() && in_array($postID, $private_pages)) {
			wp_redirect('/auth/');
		}
	}
}

function wph_noadmin() {
	if ( is_admin() && !current_user_can('administrator') && !current_user_can('manager') && !wp_doing_ajax() ) {
		wp_redirect(home_url());
		exit;
	} }
add_action('init', 'wph_noadmin');

if ( !current_user_can('administrator') && !current_user_can('manager')) {
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

function getMonth($num) {
	$month = array(
		1  => 'Январь',
		2  => 'Февраль',
		3  => 'Март',
		4  => 'Апрель',
		5  => 'Май', 
		6  => 'Июнь',
		7  => 'Июль',
		8  => 'Август',
		9  => 'Сентябрь',
		10 => 'Октябрь',
		11 => 'Ноябрь',
		12 => 'Декабрь'
	);

	return $month[$num];
}

function getProfitvalue($year) {
	global $wpdb;

	$capitalOnHand = $portfolio = $capitalInvested = $monthly_totals_percent = $medianPerYear = $rows = [];
	$profit_per_year = 0;

	$userID = getUserID();

	$transactionsRefund = transactionsForCurrentUser($year, 3); // Получаем все транзакции по возврату

	foreach ($transactionsRefund as $month => $transaction_data) {
		$monthly_refund_transactions[$month] = array_sum(array_column($transaction_data, 'value'));
	}

	$profitData = getProfitUserInfo($userID);

	foreach ($profitData as $pd) {
		$field_date = $pd['date'];
		$field_money = $pd['user_money'];
		$field_contributed = $pd['user_contributed'];
		$field_overdep = $pd['user_overdep'];

		$dateObject = \DateTime::createFromFormat('Y-m-d', $field_date);

		$month = $dateObject->format('n');

		if (!isset($portfolio[$month])) {
			$portfolio[$month] = $field_money + $field_contributed + $field_overdep + $monthly_refund_transactions[$month];
			$capitalInvested[$month] = $field_contributed + $field_overdep;
			$capitalOnHand[$month] = $field_money + $monthly_refund_transactions[$month];
		}

	}

	$profitTransactions = transactionsForCurrentUser($year, $month, 4, true); // Получаем все транзакции по доходу

	// Перебор транзакций и суммирование значений по месяцам.
	foreach ($profitTransactions as $month => $transaction_data) {
		$monthly_totals_profit[$month] = array_sum(array_column($transaction_data, 'value'));
		$profit_per_year += $monthly_totals_profit[$month];
	}

	for ($i = 1; $i <= 12; $i++) {
		if (isset($monthly_totals_profit[$i]) && isset($capitalInvested[$i]) && isset($capitalOnHand[$i])) {
			$monthly_totals_percent[$i] = $monthly_totals_profit[$i] / ($capitalInvested[$i] + $capitalOnHand[$i]) * 100;
		}
	}

	// Построение таблицы с месяцами и суммами значений.
	$lastMonth = $year == date('Y') ? date('n') - 1 : 12;

	for($m = 1; $m <= $lastMonth; $m++) {
		if (isset($monthly_totals_percent[$m])) {
			$medianPerYear[] = round($monthly_totals_percent[$m], 2);
			$russian_month = getMonth($m);
			$rows['rows'][] = [
				'year' => $year,
				'month' => $russian_month,
				'portfolio' => get_formatted_number($portfolio[$m]),
				'capital_in' => get_formatted_number($capitalInvested[$m]),
				'capital_has' => get_formatted_number($capitalOnHand[$m]),
				'total' => get_formatted_number($monthly_totals_profit[$m]),
				'percent' => isset($monthly_totals_percent[$m]) && $monthly_totals_percent[$m] != 50 ? round($monthly_totals_percent[$m], 2) : ''
			];
		}
	}
	$rows ['medianPerYear'] = round(array_sum($medianPerYear) / count($medianPerYear), 2) . ' %';

	return $rows;
}

function transactionsForCurrentUser($year, $type, $prevPeriod = false) {

	$monthly_transactions = $periods = [];
	$periods = [];
	

	for ($month = 1; $month <= ($prevPeriod ? 13 : 12); $month++) {
		$y = $year;
		$m = $month;
		if ($prevPeriod) {
			if ($month == 1) {
				$y = $year - 1;
				$m = 12;
			}
			else {
				$m = $month - 1;
			}
		}

		$start_date = $y . '-' . $m . '-01';
		$strStart_date = strtotime($y . '-' . $m . '-01');
		$end_date = date('Y-m-t', $strStart_date) . ' 23:59:59';

		$transactions = getTransactionsByType($type, $start_date, $end_date);

		$transaction_data = [];
		foreach ($transactions as $transaction) {
			$field_value = $transaction['value'];

			$transaction_data[] = [
				'id' => $transaction['id'],
				'value' => $field_value, 
				// 'date' => $transaction['date'],
			];
		}
		if ($prevPeriod) {
			if ($month == 1) {
				$period = 0;
			} else {
				$period = $month - 1;
			}
		} else {
			$period = $month;
		}
		$monthly_transactions[$period] = $transaction_data;
	}


	return $monthly_transactions;
}

function getTransactionsByType($type, $start, $end) {
	global $wpdb;

	$user = getUserID();

	$query = "
			SELECT p.ID, pm_sum.meta_value AS value, p.post_date AS date
			FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->postmeta} pm_user ON p.ID = pm_user.post_id
			INNER JOIN {$wpdb->postmeta} pm_type ON p.ID = pm_type.post_id
			INNER JOIN {$wpdb->postmeta} pm_sum ON p.ID = pm_sum.post_id
			WHERE p.post_type = 'transactions'
			AND p.post_status = 'publish'
			AND pm_user.meta_key = 'settings_investor'
			AND pm_user.meta_value = %d
			AND pm_type.meta_key = 'settings_transaction_type'
			AND pm_type.meta_value = %s
			AND pm_sum.meta_key = 'settings_sum'
			AND p.post_date >= %s
			AND p.post_date <= %s
			ORDER BY p.post_date ASC
	";

	$results = $wpdb->get_results($wpdb->prepare($query, $user, $type, $start, $end));

	$data = [];
	foreach ($results as $row) {
			$data[] = [
					'id' => $row->ID,
					'value' => $row->value,
					// 'date' => date('Y-m-t', strtotime($row->date)),
			];
	}

	return $data;
}

function getProfitUserInfo($userID) {
	global $wpdb;

	$data = [];
	$result = $wpdb->get_results ( 
		"
			SELECT * 
			FROM  af_profit_data
			WHERE user_id = $userID
			ORDER BY ID ASC
		" );

	foreach ( $result as $res )
	{
		$data[] = [
			'date' => $res->date,
			'user_money' => $res->user_money,
			'user_contributed' => $res->user_contributed,
			'user_overdep' => $res->user_overdep,
		];
	}

	return $data;
}

function getUserID() {
	return get_query_var('user_id') !== '' ? get_query_var('user_id') : get_current_user_id();
}

function hasAccess() {
	if (current_user_can('administrator') || current_user_can('manager')) return true;
	
	global $wpdb;
	$obj_id = get_queried_object_id();
	$post_type = get_post_type($obj_id);
	if (current_user_can('accountant')) {
		$accountantID = wp_get_current_user()->ID;
		$userID = getUserID();
		$group_id = get_user_meta($accountantID, 'pm_group', true);

		$usersInGroup = [];

		foreach ($group_id as $key => $g_id) {
			$tmpArr = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value LIKE %s",'pm_group',serialize([$g_id])));
			$usersInGroup = array_merge($usersInGroup, $tmpArr);
		}
		$usersInGroup = array_unique($usersInGroup);
		switch ($post_type) {
			case 'page':
				return in_array($userID, $usersInGroup);
			case 'projects':
				$projects = array_unique(getUsersProjects($usersInGroup));
				return in_array($obj_id, $projects);
			case 'events':
			case 'transactions':
				$projects = array_unique(getUsersProjects($usersInGroup));
				$project_meta = get_post_meta($obj_id, 'settings_project', true);
				return in_array($project_meta, $projects);
			default:
				return false;
		} 
	} else {
		$userID = wp_get_current_user()->ID;

		switch ($post_type) {
			case 'projects':
				$projects = getUsersProjects($userID);
				return in_array($obj_id, $projects);
			case 'events':
			case 'transactions':
				$projects = array_unique(getUsersProjects($userID));
				$project_meta = get_post_meta($obj_id, 'settings_project', true);
				if ($post_type == 'transactions') {
					$transaction_meta = get_post_meta($obj_id, 'settings_investor', true);
					return $transaction_meta == $userID;
				} else if ($project_meta != '') {
					return in_array($project_meta, $projects);
				} else {
					return false;
				}
		}
	}
}

function getUsersProjects($user_ids, $status = '') {
	if (!is_array($user_ids)) {
		$user_ids = [$user_ids];
	}

	$status_query = '';
	if ($status != '') {
		switch ($status) {
			case 'active':
				$s = 1;
				break;
			case 'inactive':
				$s = 5;
				break;
			case 'not_started':
				$s = 0;
				break;
		}
		$status_query = " AND meta_key = 'status' AND meta_value = " . $s;
	}

	global $wpdb;
	$meta_key_like = 'meta_key LIKE %s';
	$user_ids_placeholder = implode(',', array_fill(0, count($user_ids), '%d'));

	$query = $wpdb->prepare("
			SELECT post_id 
			FROM $wpdb->postmeta 
			WHERE $meta_key_like
			AND meta_value IN ($user_ids_placeholder)
	", array_merge(['investory_investors_%_investor'], $user_ids));

	return $wpdb->get_col($query);
}

function getUserInvestedInProjects($user_id, $project_id) {
	$i = 0;
	while($investor = get_post_meta($project_id, 'investory_investors_'.$i.'_investor', true)) {
		if ($user_id == $investor) {
			return [
				'invest' => get_post_meta($project_id, 'investory_investors_'.$i.'_invest', true),
				'invest_over' => get_post_meta($project_id, 'investory_investors_'.$i.'_invest_over', true),
			];
		}
		$i++;
	}
	return false;
}