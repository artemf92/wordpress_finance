<? 
global $totalMoney, $totalContributed, $totalOverdep;
?>
<tr style="background-color: #e2e2e2;">
  <td colspan="3"><strong>Итог:</strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalMoney) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalContributed) ?></strong></td>
  <td class="centered"><strong><?= get_formatted_number($totalOverdep) ?></strong></td>
</tr>