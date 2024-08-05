<?php

$settings = get_field('settings');
if (empty($settings)) {
  update_field('settings', [
    'project' => get_post_meta($post->ID, 'settings_project', true),
    'sum' => get_post_meta($post->ID, 'settings_sum', true),
    'transaction_type' => get_post_meta($post->ID, 'settings_transaction_type', true),
  ]);
  $settings = get_field('settings');
};
// $sum = get_formatted_number($settings['sum']);
// $invested = get_formatted_number($data['invest']);
// $invested_over = get_formatted_number($data['invest_over']);
// $profit = get_formatted_number($settings['profit'], '%');
$time = get_post_full_time();
$transaction_type = $settings['transaction_type']['value'] === '0' 
  ? __('Создание проекта (проект на рассмотрении)')
  : $settings['transaction_type']['label'];

echo '<tr>';
echo '  <td>'.$args['num'].'</td>';
// echo '  <td>'.$transaction_type.'</td>';
echo '  <td data-tablesaw-priority="persist"><a href="'.get_the_permalink().'">'.preg_replace('(\(проект .*?\))', '', get_the_title()).'</a></td>';
echo '  <td>'.get_formatted_number($settings['sum']).'</td>';
echo '  <td>'.$time.'</td>';
// echo '  <td><a href="'.get_the_permalink().'" />'.get_the_title().'</a></td>';
// echo '  <td>'.esc_html('Активен').'</td>';
echo '</tr>';