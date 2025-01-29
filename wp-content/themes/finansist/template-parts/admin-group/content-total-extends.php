<? 
global $totalMoney, $totalContributed, $totalOverdep, $totalRefund, $capitalNotIvested, $portfolio;
?>
<tr style="background-color: #e2e2e2;">
  <td colspan="2"><strong>Итог:</strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalMoney) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalRefund) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalContributed) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalOverdep) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($capitalNotIvested) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($portfolio) ?></strong></td>
</tr>