<? 
global $group_name, $totalMoney, $totalContributed, $totalOverdep, $totalRefund, $capitalNotIvested, $portfolio, $totalCapitalNotInvested, $totalPortfolio;
$user = $args['user'];
$date = $args['date'];

$userData = get_userdata($user);
$num = $args['num']+1;

if (!$date) {
  $userMoney = get_field('money', 'user_' . $user);
  $userRefund = get_field('refund', 'user_' . $user);
  $userContributed = get_field('contributed', 'user_' . $user);
  $userOverdep = get_field('overdep', 'user_' . $user);
} else {
  global $wpdb;
  $result = $wpdb->get_results("SELECT * FROM af_users_data WHERE user_id={$user} AND date=\"{$date}\"", 'ARRAY_A');

  $userMoney = $result[0]['user_money'];
  $userRefund = $result[0]['user_refund'];
  $userContributed = $result[0]['user_contributed'];
  $userOverdep = $result[0]['user_overdep'];
}

$capitalNotIvested = $userMoney + $userRefund;
$portfolio = $capitalNotIvested + $userContributed;

$totalMoney += $userMoney;
$totalContributed += $userContributed;
$totalRefund += $userRefund;
$totalOverdep += $userOverdep;
$totalCapitalNotInvested += $capitalNotIvested;
$totalPortfolio += $portfolio;
?>
<tr>
  <td><?=$num?></td>
  <td class="<?= in_array('accountant', $userData->roles) ? 'crown':'' ?>">
    <a href="/user/<?=$user?>/"><?= userDisplayName($userData) ?> 
      <? if (in_array('accountant', $userData->roles)) { ?>
        <span data-tooltip="<?=__('Бухгалтер') ?>"></span>
      <? } ?>
    </a>
  </td>
  <!-- <td><?= $group_name ?></td> -->
  <td class="centered"><?= get_formatted_number($userMoney) ?></td>
  <td class="centered"><?= get_formatted_number($userRefund) ?></td>
  <td class="centered"><?= get_formatted_number($userContributed) ?></td>
  <td class="centered"><?= get_formatted_number($userOverdep) ?></td>
  <td class="centered"><?= get_formatted_number($capitalNotIvested) ?></td>
  <td class="centered"><?= get_formatted_number($portfolio) ?></td>
</tr>