<? 
$year = isset($_REQUEST['f_year']) && $_REQUEST['f_year'] != '' ? $_REQUEST['f_year'] : '';
$month = isset($_REQUEST['month']) && $_REQUEST['month'] != '' ? $_REQUEST['month'] : '';

if ($year || $month) {
  $from = $to = '';
  $query['date_query'] = [];
}

if ($year) {
  $query['date_query'][] = [
    'year' => $year,
    'inclusive' => true
  ];
}
if ($month) {
  $query['date_query'][] = [
    'month' => $month,
    'inclusive' => true
  ];
}
?>
<label for="year" class="form-label">
  <? echo __('Год:') ?>
</label>
<div class="">
  <select name="f_year" id="f_year" class="form-select form-date form-control" onchange="clearDate(this)" multiple>
    <?/*<option value="" disabled selected><?=esc_html('Выбрать год')?></option>*/?>
    <? for($y = 2020; $y < intval(date('Y')); $y++) { ?>
    <option value="<?=$y?>" <?=$year == $y ? 'selected':''?>><?=$y?></option>
    <? } ?>
  </select>
</div>
</div>
<div class="mb-3">
<label for="month" class="form-label">
  <? echo __('Месяц:') ?>
</label>
<div class="">
  <select name="month" id="month" class="form-select  form-date form-control" onchange="clearDate(this)" multiple>
    <?/*<option value="" selected disabled><?=esc_html('Выбрать месяц')?></option>*/?>
    <? for($m = 1; $m <= 12; $m++) { ?>
    <option value="<?=$m?>" <?=$month == $m ? 'selected':''?>><?= getMonth($m) ?></option>
    <? } ?>
  </select>
</div>