<? 
global $group_name;
$user = $args['user'];
$userData = get_userdata($user);
$num = $args['num']+1;
$user_groups = get_user_meta($user, 'pm_group', true);
$groups_text = [];
if (is_array($user_groups)) {
  global $wpdb;
  foreach($user_groups as $group) {
    $group_db =  $wpdb->get_col($wpdb->prepare("SELECT group_name FROM `wp_promag_groups` WHERE id = " . $group));
    $group_name = $group_db[0];
    $groups_text[] = '<a href="/default-user-group/?gid='.$group.'">'.$group_name.'</a>';
  }
}
?>
<tr>
  <td><?=$num?></td>
  <td><a href="/user/<?=$user?>/"><?= userDisplayName($userData) ?></a></td>
  <td><?= implode(', ', $groups_text) ?></td>
  <td><?= in_array('accountant', $userData->roles) ? 'Бухгалтер':'' ?></td>
  <td class="centered"><?= get_formatted_number(get_field('money', 'user_' . $user)) ?></td>
  <td class="centered"><?= get_formatted_number(get_field('contributed', 'user_' . $user)) ?></td>
  <td class="centered"><?= get_formatted_number(get_field('overdep', 'user_' . $user)) ?></td>
</tr>