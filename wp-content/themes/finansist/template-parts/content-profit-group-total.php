<?php
$data = $args['data'];

echo '<tr>';
echo '  <td class="bg-primary text-white" colspan="6">' . esc_html('Среднемесячная доходность:') . '</td>';
echo '  <td class="bg-primary text-white">' . get_formatted_number($data['portfolio']) .'</td>';
echo '  <td class="bg-primary text-white">' . get_formatted_number($data['capital_in']) .'</td>';
echo '  <td class="bg-primary text-white">' . get_formatted_number($data['capital_has']) .'</td>';
echo '  <td class="bg-primary text-white">' . get_formatted_number($data['total_profit']) .'</td>';
echo '  <td class="bg-primary text-white">' . $data['percent'] .'</td>';
echo '</tr>';