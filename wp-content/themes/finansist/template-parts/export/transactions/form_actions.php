<? 
$class = 'col-md-offset-2';
if ($all_time && (current_user_can('administrator') || current_user_can('manager'))) {
  $class = 'col-md-offset-1';
}
?>
<div class="col-xs-6 col-md-3 <?=$class?>">
  <div class="d-flex m-t-3">

    <button type="submit" class="btn btn-info m-r-2">
      <i class="fa-solid fa-filter"></i>
      <?= __('Показать') ?>
    </button>
    <button type="reset" class="btn btn-clear d-block">
      <?= __('Сбросить') ?>
    </button>
  </div>
</div>