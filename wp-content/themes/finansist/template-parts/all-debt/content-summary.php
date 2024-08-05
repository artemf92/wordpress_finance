<? 
global $all_profit, $all_refund, $all_refund_over;
?>
<div class="row">
  <div class="col-md-4">
    <div class="item m-b-1">
      <div class="field__label m-b-1">
        <strong><? echo __('Доход') ?></strong>
      </div>
      <div class="bg-gray field__item p-a-1">
        <?= get_formatted_number($all_profit) ?>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="item m-b-1">
      <div class="field__label m-b-1">
        <strong><? echo __('Возврат инвестиций (портфель)') ?></strong>
      </div>
      <div class="bg-gray field__item p-a-1">
        <?= get_formatted_number($all_refund) ?>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="item m-b-1">
      <div class="field__label m-b-1">
        <strong><? echo __('Возврат инвестиций (сверх)') ?></strong>
      </div>
      <div class="bg-gray field__item p-a-1">
        <?= get_formatted_number($all_refund_over) ?>
      </div>
    </div>
  </div>
</div>