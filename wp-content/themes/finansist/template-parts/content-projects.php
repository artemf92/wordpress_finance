<?php

$sum = get_post_meta($post->ID, 'settings_project_sum', true);
$status = get_field('status');
//$arInvestors = get_post_meta($post->ID, 'investory_investors', true);
$arInvestors = get_field('investory')['investors'];
if (empty($arInvestors)) return;
$data = array_filter($arInvestors, function($_investor) {
  global $USER_ID;
  // $user = wp_get_current_user();
  return $_investor['investor'] == $USER_ID;
});
$data = array_pop($data);

$sum = get_formatted_number($sum);
$invested = get_formatted_number($data['invest']);
$invested_over = get_formatted_number($data['invest_over']);
$profit = get_formatted_number(get_post_meta($post->ID, 'settings_project_profit', true)[0], '%');
$time = get_post_full_time();

echo '<tr>';
echo '  <th scope="row">'.$args['num'].'</th>';
echo '  <td><a href="'.get_the_permalink().'" />'.get_the_title().'</a></td>';
echo '  <td>'.$status['label'].'</td>';
echo '  <td>'.$profit.'</td>';
echo '  <td>'.$time.'</td>';
echo '</tr>';