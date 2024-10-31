<?php

update_field('settings', ['project' => get_post_meta($post->ID, 'settings_project', true)], $post->ID);

$project = get_post_meta($post->ID, 'settings_project', true);
$userID = get_post_meta($post->ID, 'settings_investor', true);
$userData = get_userdata($userID);
$sum = get_formatted_number(get_post_meta($post->ID, 'settings_sum', true));
$time = get_post_full_time();

echo '<tr data-project-id="'.$post->ID.'">';
echo '  <th scope="row">'.$args['num'].'</th>';
echo '  <td><a href="/user/'.$userID.'/" target="_blank">' . userDisplayName(get_user_by('ID', $userID)) . '</a></td>';
echo '  <td>'.preg_replace('(\(проект .*?\))', '', get_the_title()).'</td>';
echo '  <td>'.$sum.'</td>';
echo '  <td>'.$time.'</td>';
echo '</tr>';