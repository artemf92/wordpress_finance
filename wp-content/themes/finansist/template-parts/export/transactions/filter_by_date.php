<? 
$from = isset($_REQUEST['from']) && $_REQUEST['from'] != '' ? $_REQUEST['from'] : '';
$to = isset($_REQUEST['to']) && $_REQUEST['to'] != '' ? $_REQUEST['to'] : '';

if ($from) {
  $query['date_query'][] = [
    'after' => $from,
    'inclusive' => true
  ];
}

if ($to) {
  $query['date_query'][] = [
    'after' => $to,
    'inclusive' => true
  ];
}
?>
<div class="col-md-3">
  <div class="mb-3">
    <label for="from" class="form-label">
      <? echo __('Время создания:') ?>
    </label>
    <div class="">
      <span><?=__('От')?>:</span>
      <input type="date" name="from" id="from" class="form-control input-date" value="<?=$from?>" placeholder="От" onchange="clearYearMonth(this)">
      <br>
      <br>
      <span><?=__('До')?>:</span>
      <input type="date" name="to" class="form-control input-date" value="<?=$to?>" placeholder="До" onchange="clearYearMonth(this)">
    </div>
  </div>
</div>