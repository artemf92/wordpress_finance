<?php
$data = $args['data'];
$key = $args['key'];
$debug = strpos($args['class'], 'debug') !== false;
$userData = get_userdata($data['user_id']);

echo '<tr'.($debug ? ' data-debug':'').'>';
echo '  <td>' . $key .'</td>';
echo '  <td class="'.( in_array('accountant', $userData->roles) ? 'crown':'').'"><a href="/user/' . $data['user_id'] . '">' . $data['login'];
if (in_array('accountant', $userData->roles)) {
  echo '<span data-tooltip="' . __('Бухгалтер') . '"></span>';
}
echo '</a>';
echo '</td>';
echo '  <td>' . $data['portfolio_formatted'] .'</td>';
echo '  <td>' . $data['capital_in_formatted'] . '</td>';
echo '  <td>' . $data['capital_has_formatted'] . '</td>';
echo '  <td>' . $data['total_profit_formatted'] . '</td>';
echo '  <td>' . $data['percent'] . '</td>';
echo '</tr>';