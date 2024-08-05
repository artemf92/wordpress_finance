<?php
$settings = get_field('settings_project');
global $userID;
$arInvestors = get_field('investory')['investors'];
$data = array_filter($arInvestors, function($_investor) {
  global $userID;
  // $user = wp_get_current_user();
  return $_investor['investor'] == $userID;
});
$data = array_pop($data);

$sum = get_formatted_number($settings['sum']);
$invested = get_formatted_number($data['invest']);
$invested_over = get_formatted_number($data['invest_over']);
$profit = get_formatted_number($settings['profit'], '%');
$time = get_post_full_time();

echo '<tr>';
echo '  <td scope="row">'.$args['num'].'</td>';
echo '  <td><a href="/user/'.$userID.'/">'.get_userdata($userID)->display_name.'</a></td>';
echo '  <td><a href="'.get_the_permalink().'" />'.get_the_title().'</a></td>';
// echo '  <td>'.esc_html('Активен').'</td>';
echo '  <td>'.$sum.'</td>';
echo '  <td>'.$invested . ' / '.$invested_over.'</td>';
echo '  <td>'.$profit.'</td>';
echo '  <td>'.$time.'</td>';
echo '</tr>';