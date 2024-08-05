<?php

update_field('settings', ['project' => get_post_meta($post->ID, 'settings_project', true)], $post->ID);

$settings = get_field('settings');
$project = $settings['project'];

// $arInvestors = get_field('investory')['investors'];
// $data = array_filter($arInvestors, function($_investor) {
//   $user = wp_get_current_user();
//   return $_investor['investor'] == $user->ID;
// });
// $data = array_pop($data);

$sum = get_formatted_number($settings['sum']);
// $invested = get_formatted_number($data['invest']);
// $invested_over = get_formatted_number($data['invest_over']);
// $profit = get_formatted_number($settings['profit'], '%');
$time = get_post_full_time();

echo '<tr data-transaction-id="'.$post->ID.'">';
echo '  <td scope="row">'.$args['num'].'</td>';
echo '  <td><a href="/transactions/'.$post->ID.'/">'.get_the_title().'</a></td>';
if ($project) {
  echo '  <td><a href="'.get_the_permalink($project).'" />'.get_the_title($project).'</a></td>';
} else {
  echo '  <td></td>';
}
echo '  <td>'.$sum.'</td>';
echo '  <td>'.$time.'</td>';
echo '  <td scope="row">'.$post->ID.'</td>';
echo '</tr>';