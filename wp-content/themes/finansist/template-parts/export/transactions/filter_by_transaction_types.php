<? 
$transactionTypes = 
  isset($_REQUEST['transaction_type']) && !empty($_REQUEST['transaction_type']) 
    ? $_REQUEST['transaction_type'] 
    : '';

if ($transactionTypes) {
  foreach($transactionTypes as $tt) {
    $query['meta_query'][] = [
      'key' => 'settings_transaction_type',
      'value' => $tt,
      'compare' => 'IN',
    ];

  }
}

$transaction_types = [];
$fields = acf_get_fields(50);
foreach($fields[0]['sub_fields'] as $field) {
  if ($field['name'] == 'transaction_type') {
    $transaction_types = $field;
  }
}
?>
<div class="col-md-4">
  <div class="mb-3">
    <label for="transaction_type" class="form-label">
      <? echo __('Тип транзакции:') ?>
    </label>
    <select name="transaction_type[]" class="form-select form-control" aria-label="Default select example" multiple="">
      <option disabled><?= __('Выбрать тип') ?></option>
      <? foreach($transaction_types['choices'] as $k => $type) { ?>
      <option value="<?=$k?>"
        <?=isset($_REQUEST['transaction_type']) && in_array($k, (array)$_REQUEST['transaction_type']) ? 'selected' : ''?>>
        <?= $type ?></option>
      <? } ?>
    </select>
  </div>
</div>