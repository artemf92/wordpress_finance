<?php
global $totalSumm;

update_field('settings', ['project' => get_post_meta($post->ID, 'settings_project', true)], $post->ID);

$view = $args['view'] ? explode(',', $args['view']) : explode(',', 'num,name,project,investor,amount,date,id') ;
$settings = get_field('settings');
$project = $settings['project'];
$investorID = get_post_meta($post->ID, 'settings_investor', true);
$investor = get_user_by('ID', $investorID);

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
$showLink = !current_user_can('contributor');
$totalSumm += $settings['sum'];

echo '<tr data-transaction-id="'.$post->ID.'">';
if (in_array('num', $view)) {
  echo '  <td scope="row" class="td-num">'.$args['num'].'</td>';
}
if (in_array('name', $view)) {
  // echo '  <td class="td-transaction"><a href="/transactions/'.$post->ID.'/">'.get_the_title().'</a></td>';
  echo '  <td class="td-transaction">'.($showLink ? '<a href="/transactions/'.$post->ID.'/">':'').preg_replace('/\(по проекту .*?»\)|\(проект .*?»\)/u', '', get_the_title()).($showLink ? '</a>':'').'</td>';
}
if (in_array('project', $view)) {
  if ($project) {
    echo '  <td class="td-project"><a href="'.get_the_permalink($project).'">'.get_the_title($project).'</a></td>';
  } else {
    echo '  <td class="td-project"></td>';
  }
}
if (in_array('investor', $view) && !current_user_can('contributor')) {
  echo '  <td class="td-investor"><a href="/user/'.$investorID.'/">'.userDisplayName($investor).'</a></td>';
}
if (in_array('amount', $view)) {
  echo '  <td class="td-amount">'.$sum.'</td>';
}
if (in_array('date', $view)) {
  echo '  <td class="td-date">'.$time.'</td>';
}
if (in_array('id', $view)) {
  echo '  <td scope="row" class="td-id">'.$post->ID.'</td>';
}
echo '</tr>';