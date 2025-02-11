<? 
global $totalMoney, $totalContributed, $totalOverdep, $totalRefund, $totalCapitalNotInvested, $totalPortfolio;
?>
<tr style="background-color: #e2e2e2;">
  <td colspan="2"><strong>Итог:</strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalMoney) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalRefund) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalContributed) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalOverdep) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalCapitalNotInvested) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalPortfolio) ?></strong></td>
</tr> 