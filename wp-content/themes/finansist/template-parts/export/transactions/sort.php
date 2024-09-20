<?
$sortby = isset($_REQUEST['sort']) && $_REQUEST['sort'] != '' ? $_REQUEST['sort']:'ID';

$sortVariants = [
  [
    'value' => 'new',
    'title' => 'Новые'
  ], 
  [
    'value' => 'old',
    'title' => 'Старые'
  ], 
  [
    'value' => 'max_amount',
    'title' => 'По сумме ↓'
  ], 
  [
    'value' => 'min_amount',
    'title' => 'По сумме ↑',
  ], 
];

if ($sortby == 'old') {
  $query['order'] = 'ASC';
} else if ($sortby == 'max_amount') {
  $query['meta_key'] = 'settings_sum';
  $query['orderby'] = 'meta_value_num';
  $query['order'] = 'DESC';
} else if ($sortby == 'min_amount') {
  $query['meta_key'] = 'settings_sum';
  $query['orderby'] = 'meta_value_num';
  $query['order'] = 'ASC';

}
?>
<div class="col-md-3 col-md-offset-8">
  <div class="mb-3">
    <div class="">
      <label for="sort" class="d-flex align-items-center">
        <span><?= esc_html('Сортировка:') ?>&nbsp;&nbsp;&nbsp;</span>
        <select name="sort" id="sort" class="form-select form-control" onchange="$(this).closest('form').trigger('submit')">
          <? foreach($sortVariants as $item) { ?>
          <option value="<?=$item['value']?>" <?=$item['value'] == $sortby ? 'selected':''?>><?= $item['title'] ?>
          </option>
          <? } ?>
        </select>
      </label>
    </div>
  </div>
</div>