<? 
$users = get_users([
	'meta_query' => [
		'relation' => 'OR',
		[
			'key' => 'profit',
			'compare' => '>',
			'value' => '0',
		],
		[
			'key' => 'refund',
			'compare' => '>',
			'value' => '0',
		],
		[
			'key' => 'refund_over',
			'compare' => '>',
			'value' => '0',
		],
	]
]);

global $all_profit, $all_refund, $all_refund_over;
$all_profit = $all_refund = $all_refund_over = 0;
?>
<div class="flex-column d-flex">
	<div class="order-2">
		<table class="table m-b-3 tablesaw tablesaw-swipe" data-tablesaw-mode="swipe">
			<?
				get_template_part('template-parts/all-debt/content-header');
				foreach($users as $user) {
					get_template_part('template-parts/all-debt/content', 'users', ['user' => $user->ID, 'num' => $n]);
					$n++;
				}
			?>
		</table>
	</div>
	<div class="order-1 m-b-3">
		<? get_template_part('template-parts/all-debt/content', 'summary'); ?>
	</div>
</div>