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
    'before' => $to,
    'inclusive' => true
  ];
}
?>
<div class="filter-input">
  <label for="from" class="form-label filter-input__btn">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filter" viewBox="0 0 16 16">
      <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
    </svg>
  </label>
  <div class="d-flex flex-column filter-input__control">
    <div class="m-b-1">
      <span><?=__('От')?>:&nbsp;</span>
      <input type="date" name="from" id="from" class="form-control input-date" value="<?=$from?>" placeholder="От" onchange="clearYearMonth(this);filterSelect(this)">
    </div>
    <div class="">
      <span><?=__('До')?>:&nbsp;</span>
      <input type="date" name="to" class="form-control input-date" value="<?=$to?>" placeholder="До" onchange="clearYearMonth(this);filterSelect(this)">
    </div>
  </div>
</div>