<?php
$data = $args['data'];

echo '<tr>';
echo '  <td class="bg-primary text-white" colspan="6">' . esc_html('Доходность за год (средняя):') . '</td>';
echo '  <td class="bg-primary text-white">' . $data .'</td>';
echo '</tr>';