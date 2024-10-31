<? 
global $isPageTransactions, $isPageProject, $isPageUser;
$selectedUsers = [];
$users = getUsersForExport();

if ($isPageProject && current_user_can('manager')) {
  $investors = array_column(get_field('investory', $atts['project_id'])['investors'], 'investor');
  $users = array_filter($users, function($inv2) use ($investors) {
    return in_array($inv2->ID, $investors);
  });
} else if ($isPageProject && current_user_can('accountant')) {
  $usersInGroup = getAdminGroupUsers();
  $investors = get_field('investory', $atts['project_id'])['investors'];
  $tmpUsers = array_column(array_filter($investors, function($inv) use ($usersInGroup) {
    return in_array($inv['investor'], $usersInGroup);
  }), 'investor');
  $users = array_filter($users, function($inv2) use ($tmpUsers) {
    return in_array($inv2->ID, $tmpUsers);
  });
}
if ($isPageUser) {
  $users = get_users(['include' => explode(', ', $atts['user_id'])]);
}

if (!empty($users)) { 
  foreach($users as $us) {
      if (in_array($us->ID, (array)$f_user_id)) $selectedUsers[] = userDisplayName($us);
  }
  if (in_array($currentUserID, (array)$f_user_id) && !in_array(userDisplayName(get_userdata($currentUserID)), $selectedUsers)) {
    $selectedUsers[] = userDisplayName(get_userdata($currentUserID));
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
        <? if ($isPageTransactions || ($isPageProject && in_array($currentUserID, (array)$f_user_id))) { ?>
        <option value="<?=$currentUserID?>" <?=in_array($currentUserID, (array)$f_user_id) ? 'selected':''?>><?=userDisplayName(get_userdata($currentUserID))?></option>
        <? } ?>
        <? 
        $label = '';
        if ($isPageProject && current_user_can('accountant')) {
          $label = ' проекта';
        } else if ($isPageTransactions && current_user_can('accountant')) {
          $label = ' группы';
        }
        ?>
        <optgroup label="<?= esc_html_e('Участники' . $label) ?>">
          <? foreach($users as $user) { ?>
            <? $disabled = $isPageUser ? ' disabled':'' ?>
            <option value="<?=$user->ID?>" class="<?=$disabled?>" <?=in_array($user->ID, (array)$f_user_id) ? 'selected':''?>><?= userDisplayName($user) ?> </option>
          <? } ?>
        </optgroup>
      </select>
    </div>
  </div>
<? } ?>