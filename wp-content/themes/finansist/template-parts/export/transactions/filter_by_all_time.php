<? 
$all_time = isset($_REQUEST['all_time']) && $_REQUEST['all_time'] != '' ? $_REQUEST['all_time'] : '';

if ($all_time && (current_user_can('administrator') || current_user_can('manager'))) {
  unset($query['meta_query']);
  unset($query['date_query']);
}

if (current_user_can('administrator') || current_user_can('manager')) { ?>
      <div class="col-md-2">
        <div class="mb-3">
          <div class="">
            <label for="all_time" class="form-label">
              <input type="checkbox" name="all_time" id="all_time" <?=$all_time ? 'checked':''?> onchange="clearAllTimeInput(this)">
              <? echo __('Все время:') ?>
            </label>
          </div>
        </div>
      </div>
      <? } ?>