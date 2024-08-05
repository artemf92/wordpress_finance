<?php
$arInvestors = get_field('investory')['investors'];
$data = array_filter($arInvestors, function($_investor) {
  global $USER_ID;
  // $user = wp_get_current_user();
  return $_investor['investor'] == $USER_ID;
});
$data = array_pop($data);

$sum = get_formatted_number(get_post_meta($post->ID, 'settings_project_sum', true));
$invested = get_formatted_number($data['invest']);
$invested_over = get_formatted_number($data['invest_over']);
$profit = get_formatted_number(get_post_meta($post->ID, 'settings_project_profit', true), '%');
$time = get_post_full_time();

echo '<tr>';
echo '  <th scope="row">'.$args['num'].'</th>';
echo '  <td><a href="'.get_the_permalink().'" />'.get_the_title().'</a></td>';
echo '  <td>'.esc_html('Активен').'</td>';
echo '  <td>'.$sum.'</td>';
echo '  <td>'.$invested . ' / '.$invested_over.'</td>';
echo '  <td>'.$profit.'</td>';
echo '  <td>'.$time.'</td>';
echo '</tr>';