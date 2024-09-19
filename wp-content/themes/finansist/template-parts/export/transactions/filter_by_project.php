<? 
$projectID = isset($_REQUEST['project_id']) && $_REQUEST['project_id'] > 0 ? $_REQUEST['project_id'] : '';

if ($projectID) {
  $query['meta_query'][] =
    [
      'key' => 'settings_project',
      'value'   => $projectID,
      'compare' => '=',
    ];
} 

$projects = getProjectsForExport($currentUserID);

if (!empty($projects)) { ?>
<div class="col-md-3 col-md-offset-1">
  <div class="mb-3">
    <div class="">
      <label for="projectID">
        <?= esc_html('Проект:') ?>
      </label>
      <select name="project_id" id="project_id" class="form-select form-control">
        <option value="" selected disabled><?=esc_html('Выбрать проект')?></option>
        <? foreach($projects as $project) { ?>
        <option value="<?=$project->ID?>" <?=$projectID == $project->ID ? 'selected':''?>><?= $project->post_title ?>
        </option>
        <? } ?>
      </select>
    </div>
  </div>
</div>
<? } ?>