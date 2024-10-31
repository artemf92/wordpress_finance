<? 
global $group_name;
$user = $args['user'];
$userData = get_userdata($user);
$num = $args['num']+1;
?>
<tr>
  <td><?=$num?></td>
  <td><a href="/user/<?=$user?>/"><?= userDisplayName($userData) ?></a></td>
  <!-- <td><?= $group_name ?></td> -->
  <td><?= in_array('accountant', $userData->roles) ? 'Бухгалтер':'' ?></td>
  <td class="centered"><?= get_formatted_number(get_field('money', 'user_' . $user)) ?></td>
  <td class="centered"><?= get_formatted_number(get_field('contributed', 'user_' . $user)) ?></td>
  <td class="centered"><?= get_formatted_number(get_field('overdep', 'user_' . $user)) ?></td>
</tr>