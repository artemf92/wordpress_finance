<?php
$data = $args['data'];

echo '<tr>';
echo '  <td class="bg-primary text-white" colspan="6">' . esc_html('Среднемесячная доходность за календарный год:') . '</td>';
echo '  <td class="bg-primary text-white">' . $data .'</td>';
echo '</tr>';