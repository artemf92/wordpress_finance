<? 
$selectedTransactionTypes = 
isset($_REQUEST['transaction_type']) && !empty($_REQUEST['transaction_type']) 
  ? $_REQUEST['transaction_type'] 
  : '';

if ($selectedTransactionTypes) {
  $query['meta_query'][] = [
    'key' => 'settings_transaction_type',
    'value' => $selectedTransactionTypes,
    'compare' => 'IN',
  ];
}

$transactionTypesValues = [];
$fields = acf_get_fields(50);
foreach($fields[0]['sub_fields'] as $field) {
  if ($field['name'] == 'transaction_type') {
    $transactionTypesValues = $field;
  }
}

$resultTransactionTypes = array_intersect_key($transactionTypesValues['choices'], array_flip((array)$selectedTransactionTypes));
?>
<div class="filter-input">
  <label for="transaction_type" class="form-label filter-input__btn" style="max-width:230px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filter" viewBox="0 0 16 16">
      <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
    </svg>
    <span class="values"><?= implode(', ', (array)$resultTransactionTypes)?></span>
  </label>
  <div class="filter-input__control">
    <select name="transaction_type[]" class="form-select form-control" aria-label="<?= __('Тип транзакции:') ?>" onchange="filterSelect(this)" multiple>
      <?/*<option disabled><?= __('Выбрать тип') ?></option>*/?>
      <? foreach($transactionTypesValues['choices'] as $k => $type) { ?>
      <option value="<?=$k?>"
        <?=isset($selectedTransactionTypes) && in_array($k, (array)$selectedTransactionTypes) ? 'selected' : ''?>>
        <?= $type ?>
      </option>
      <? } ?>
    </select>
  </div>
</div>