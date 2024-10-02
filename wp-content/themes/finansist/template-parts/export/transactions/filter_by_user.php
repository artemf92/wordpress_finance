<? 
global $isPageTransactions;
$selectedUsers = [];
$users = getUsersForExport();

if (isset($atts['project_id']) && $atts['project_id']) {
  $usersInGroup = getAdminGroupUsers();
  $investors = get_field('investory', $atts['project_id'])['investors'];
  $tmpUsers = array_column(array_filter($investors, function($inv) use ($usersInGroup) {
    return in_array($inv['investor'], $usersInGroup);
  }), 'investor');
  $users = array_filter($users, function($inv2) use ($tmpUsers) {
    return in_array($inv2->ID, $tmpUsers);
  });
}
if (isset($atts['user_id']) && $atts['user_id']) {
  $users = get_users(['include' => explode(', ', $atts['user_id'])]);
}

if (!empty($users)) { 
  foreach($users as $us) {
      if (in_array($us->ID, (array)$f_user_id)) $selectedUsers[] = $us->display_name;
  }
  if (in_array($currentUserID, (array)$f_user_id) && !in_array(get_userdata($currentUserID)->display_name, $selectedUsers)) {
    $selectedUsers[] = get_userdata($currentUserID)->display_name;
  } 
?>
<div class="filter-input">
  <label for="f_user_id" class="form-label filter-input__btn" style="max-width:200px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filter" viewBox="0 0 16 16">
      <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
    </svg>
    <span class="values"><?= implode(', ', (array)$selectedUsers)  ?></span>
  </label>
  <div class="filter-input__control">
    <select name="f_user_id[]" id="f_user_id" class="form-select form-control" onchange="filterSelect(this)" multiple>
      <? if ($isPageTransactions) { ?>
      <option value="<?=$currentUserID?>" <?=in_array($currentUserID, (array)$f_user_id) ? 'selected':''?>><?=get_userdata($currentUserID)->display_name?></option>
      <? } ?>
      <optgroup label="<?= esc_html_e('Участники' . (current_user_can('manager') ? ' группы' : '')) ?>">
        <? foreach($users as $user) { ?>
          <option value="<?=$user->ID?>" <?=in_array($user->ID, (array)$f_user_id) ? 'selected':''?>><?= $user->display_name ?> </option>
        <? } ?>
      </optgroup>
    </select>
  </div>
</div>
<? } ?>