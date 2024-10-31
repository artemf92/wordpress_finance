<?php
/**
 * Template Post Type: post, page, product, projects
 * 
 * The template used for displaying page content for fullwidth pages. It contains 
 * everything after the_content()
 *
 * @package flat-bootstrap
 */
global $USER_ID;
$rowFields = [
  [
    'key' => 'money',
    'label' => 'Сумма на руках',
  ],
  [
    'key' => 'profit',
    'label' => 'Доход',
  ],
  [
    'key' => 'contributed',
    'label' => 'Вложено из портфеля',
  ],
  [
    'key' => 'refund',
    'label' => 'Возврат инвестиций (портфель)',
  ],
  [
    'key' => 'overdep',
    'label' => 'Вложено сверх',
  ],
  [
    'key' => 'refund_over',
    'label' => 'Возврат инвестиций (сверх)',
  ]
];
?>
<div class="tab-pane active" id="info" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
  <div class="content p-y-2">
    <div class="row">
      <? foreach($rowFields as $k => $row) { ?>
      <? $value = get_field($row['key'], 'user_' . $USER_ID); ?>
      <div class="col-sm-6">
        <div class="item m-b-1" data-field="<?=$row['key']?>">
          <div class="field__label m-b-1"><?= $row['label'] ?></div>
          <div class="bg-gray field__item p-a-1"><?= get_formatted_number($value)?></div>
        </div>
      </div>
      <? } ?>
    </div>
  </div>
  <? 
  if (current_user_can('manager') || current_user_can('administrator')) {
    get_template_part('template-parts/user/actions');
  }
  ?>
</div>