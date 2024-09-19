<? 
$f_user_id = isset($_REQUEST['f_user_id']) && $_REQUEST['f_user_id'] > 0 ? $_REQUEST['f_user_id'] : $currentUserID;

if ($f_user_id) {
  $query['meta_query'][] =
    [
      'key' => 'settings_investor',
      'value'   => $f_user_id,
      'compare' => '=',
    ];
}
$users = getUsersForExport();

if (!empty($users)) { ?>
<div class="col-md-3">
  <div class="mb-3">
    <label for="f_user_id">
      <?= esc_html('Участник:') ?>
    </label>
    <div class="">
      <select name="f_user_id" id="f_user_id" class="form-select form-control" multiple>
        <option value="<?=$currentUserID?>"><?=get_userdata($currentUserID)->display_name?></option>
        <optgroup label="<?=esc_html('Участники'.current_user_can('manager') ? ' группы':'')?>">
          <? foreach($users as $user) { ?>
            <option value="<?=$user->ID?>" <?=$f_user_id == $user->ID ? 'selected':''?>><?= $user->display_name ?> </option>
          <? } ?>
        </optgroup>
      </select>
    </div>
  </div>
</div>
<? } ?>