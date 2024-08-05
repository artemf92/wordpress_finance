<? 
global $USER_ID; 

$userData = get_portfolio($USER_ID);
?>
<div class="form-actions js-form-wrapper form-wrapper m-b-1" id="checkout-actions">
  <? if ($userData['profit'] > 0 || $userData['refund'] > 0 || $userData['refund_over'] > 0) { ?>
  <button class="btn btn-primary m-r-1 m-t-1 button js-form-submit form-submit" data-user="<?=$USER_ID?>" id="checkout_full"
    value="Получить полностью">Получить полностью</button>
  <button class="use-ajax btn btn-info m-r-1 m-t-1" data-user="<?=$USER_ID?>" data-dialog-type="modal" id="checkout_partial"
    data-once="ajax">Получить частично</button>
  <? } ?>
  <? if ($userData['profit'] > 0) { ?>
  <button class="btn btn-warning m-r-1 m-t-1 button js-form-submit form-submit" data-user="<?=$USER_ID?>" id="checkout_profit" value="Получить доход">Получить доход</button>
  <? } ?>
  <? if ($userData['refund'] > 0) { ?>
  <button class="btn btn-warning m-r-1 m-t-1 button js-form-submit form-submit" data-user="<?=$USER_ID?>" id="checkout_contrib"
    value="Получить инвестиции (портфель)">Получить инвестиции (портфель)</button>
  <? } ?>
  <? if ($userData['refund_over'] > 0) { ?>
  <button class="btn btn-warning m-r-1 m-t-1 button js-form-submit form-submit" data-user="<?=$USER_ID?>" id="checkout_contrib_over"
    value="Получить инвестиции (сверх)">Получить инвестиции (сверх)</button>
  <? } ?>
</div>