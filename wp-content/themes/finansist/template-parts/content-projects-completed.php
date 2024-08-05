<?php

$settings = get_field('settings_project');
$arInvestors = get_field('investory')['investors'];
$data = array_filter($arInvestors, function($_investor) {
  global $USER_ID;
  // $user = wp_get_current_user();
  return $_investor['investor'] == $USER_ID;
});
$data = array_pop($data);

$sum = get_formatted_number($settings['sum']);
$invested = get_formatted_number($data['invest']);
$invested_over = get_formatted_number($data['invest_over']);
$profit = get_formatted_number($settings['profit'], '%');
$time = get_post_full_time();

echo '<tr>';
echo '  <th scope="row">'.$args['num'].'</th>';
echo '  <td><a href="'.get_the_permalink().'" />'.get_the_title().'</a></td>';
echo '  <td>'.esc_html('Завершен').'</td>';
echo '  <td>'.$profit.'</td>';
echo '  <td>'.$time.'</td>';
echo '</tr>';