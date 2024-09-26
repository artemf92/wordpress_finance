<? 
$users = getUsersForExport();

if (!empty($users)) { ?>
<div class="filter-input">
  <label for="f_user_id" class="form-label filter-input__btn" style="max-width:200px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filter" viewBox="0 0 16 16">
      <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
    </svg>
    <span class="values"></span>
  </label>
  <div class="filter-input__control">
    <select name="f_user_id[]" id="f_user_id" class="form-select form-control" onchange="filterSelect(this)" multiple>
      <option value="<?=$currentUserID?>" <?=in_array($currentUserID, (array)$f_user_id) ? 'selected':''?>><?=get_userdata($currentUserID)->display_name?></option>
      <optgroup label="<?= esc_html_e('Участники' . (current_user_can('manager') ? ' группы' : '')) ?>">
        <? foreach($users as $user) { ?>
          <option value="<?=$user->ID?>" <?=in_array($user->ID, (array)$f_user_id) ? 'selected':''?>><?= $user->display_name ?> </option>
        <? } ?>
      </optgroup>
    </select>
  </div>
</div>
<? } ?>