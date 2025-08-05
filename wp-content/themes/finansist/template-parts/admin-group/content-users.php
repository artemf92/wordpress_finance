<? 
global $group_name, $totalMoney, $totalContributed, $totalOverdep, $totalRefund;
$user = $args['user'];
$userData = get_userdata($user);
$num = $args['num']+1;
$userMoney = get_field('money', 'user_' . $user);
$userContributed = get_field('contributed', 'user_' . $user);
$userOverdep = get_field('overdep', 'user_' . $user);
$userRefund = get_field('refund', 'user_' . $user);
$totalMoney += $userMoney;
$totalContributed += $userContributed;
$totalOverdep += $userOverdep;
$totalRefund += $userRefund;
?>
<tr>
  <td><?=$num?></td>
  <td><a href="/user/<?=$user?>/"><?= userDisplayName($userData) ?></a></td>
  <!-- <td><?= $group_name ?></td> -->
  <td><?= in_array('accountant', $userData->roles) ? 'Бухгалтер':'' ?></td>
  <td class="centered"><?= get_formatted_number($userMoney) ?></td>
  <td class="centered"><?= get_formatted_number($userRefund) ?></td>
  <td class="centered"><?= get_formatted_number($userContributed) ?></td>
  <td class="centered"><?= get_formatted_number($userOverdep) ?></td>
</tr>