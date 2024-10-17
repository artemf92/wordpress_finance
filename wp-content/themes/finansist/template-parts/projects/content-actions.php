<? 
global $post;
if (current_user_can('administrator') || current_user_can('manager')/* || isProjectManager($post->ID, getUserID())*/) { ?>
<div class="form-actions js-form-wrapper form-wrapper mb-3">
  <? if ($args['status'] == 0) { ?>
  <button class="use-ajax btn btn-primary m-r-1 m-t-2" data-dialog-type="modal" id="project_start" data-project="<?=$post->ID?>" data-once="ajax"><?= __('Старт проекта')?></a>
  <? } ?>
  <? if ($args['status'] < 5 && $args['status'] > 0) { ?>
    <button class="use-ajax btn btn-primary m-r-1 m-t-2" data-dialog-type="modal" id="project_profit" data-project="<?=$post->ID?>" data-once="ajax"><?= __('Приход денег')?></a>
    <button class="use-ajax btn btn-danger m-r-1 m-t-2" data-dialog-type="modal" id="project_stop" data-project="<?=$post->ID?>" data-once="ajax"><?= __('Закрыть проект')?></a>
  <? } ?>
  <? if ($args['status'] == 5) { ?>
    <button class="use-ajax btn btn-primary m-r-1 m-t-2" data-dialog-type="modal" id="project_restart" data-project="<?=$post->ID?>" data-once="ajax"><?= __('Возобновить проект')?></a>
  <? } ?>
</div>
<? } ?>