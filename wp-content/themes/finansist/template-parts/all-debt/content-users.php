<? 
global $group_name;
$user = $args['user'];
$userData = get_userdata($user);
$num = $args['num']+1;

global $all_profit, $all_refund, $all_refund_over;
$profit = floatval(get_field('profit', 'user_' . $user));
$refund = floatval(get_field('refund', 'user_' . $user));
$refund_over = floatval(get_field('refund_over', 'user_' . $user));

$all_profit += $profit;
$all_refund += $refund;
$all_refund_over += $refund_over;
?>
<tr>
  <td><?=$num?></td>
  <td><a href="/user/<?=$user?>/"><?= $userData->display_name ?></a></td>
  <td class="text-right"><?= get_formatted_number($profit) ?></td>
  <td class="text-right"><?= get_formatted_number($refund) ?></td>
  <td class="text-right"><?= get_formatted_number($refund_over) ?></td>
</tr>