<? 
global $isPageProject;
if (!empty($projects)) { 
$selectedProjects = [];

foreach($projects as $prj) {
  if (in_array($prj->ID, (array)$projectIDs)) $selectedProjects[] = $prj->post_title;
}
?>
<div class="filter-input">
  <label for="projectID" class="form-label filter-input__btn" style="max-width:320px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filter" viewBox="0 0 16 16">
      <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
    </svg>
    <span class="values"><?= implode(', ', (array)$selectedProjects)  ?></span>
  </label>
  <div class="filter-input__control">
    <select name="project_id[]" id="project_id" class="form-select form-control" onchange="filterSelect(this)" multiple>
      <?/*<option value="" selected disabled><?=esc_html('Выбрать проект')?></option>*/?>
      <? foreach($projects as $project) { ?>
      <option value="<?=$project->ID?>" <?=in_array($project->ID, (array)$projectIDs) ? 'selected':''?>><?= $project->post_title ?>
      </option>
      <? } ?>
    </select>
  </div>
</div>
<? } ?>