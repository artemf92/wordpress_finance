<? 
global $wpdb;

$arGroups = $wpdb->get_results("SELECT * FROM `wp_promag_groups`;");
// debug($arGroups);
?>
<form class="form-filter">
  <div class="row">
    <div class="col-md-4">
      <div class="mb-3">
        <label for="investor" class="form-label">
          <? echo __('Группа:') ?>
        </label>
        <select name="group" class="form-select form-control" aria-label="Default select example">
          <option selected disabled><?= __('Выберите группу') ?></option>
          <? foreach($arGroups as $group) { ?>
          <option value="<?=$group->id?>" <?=isset($_GET['group']) && $_GET['group'] == $group->id?'selected':''?>><?= $group->group_name ?></option>
          <? } ?>
        </select>
      </div>
    </div>
    <div class="col-md-2">
      <div class="mb-3">
        <label for="investor" class="form-label">
          <? echo __('Дата:') ?>
        </label>
        <br>
        <input type="date" class="form-input form-control" min="2025-01-29" name="date" value="<?=isset($_GET['date']) ? $_GET['date']:""?>">
      </div>
    </div>
    <div class="col-md-3">
      <div class="d-flex">
        <button type="submit" class="btn btn-info btn-md d-block m-a-2">
          <i class="fa-solid fa-filter"></i>
          <?= __('Показать') ?>
        </button>
      </div>
      </div>
  </div>
  <hr>
</form>