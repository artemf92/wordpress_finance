<? 
global $USER_ID;
?>
<!-- Nav tabs -->
<ul class="nav nav-tabs m-b-3" id="user-tab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link btn-info m-r-1 m-b-2 btn-lg active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true"><?=__('Портфель')?></button>
  </li>
  <? if (wp_get_current_user()->ID === $USER_ID) { ?>
  <li class="nav-item" role="presentation">
    <button class="nav-link btn-info m-r-1 m-b-2 btn-lg" id="edit-tab" data-bs-toggle="tab" data-bs-target="#edit" type="button" role="tab" aria-controls="edit" aria-selected="false"><?= __('Данные') ?></button>
  </li>
  <? } else { ?>
    <li class="nav-item" role="presentation">
      <button class="nav-link btn-info m-r-1 m-b-2 btn-lg" id="active-projects-tab" data-bs-toggle="tab" data-bs-target="#active-projects" type="button" role="tab" aria-controls="edit" aria-selected="false"><?= __('Активные проекты') ?></button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link btn-info m-r-1 m-b-2 btn-lg" id="archive-projects-tab" data-bs-toggle="tab" data-bs-target="#archive-projects" type="button" role="tab" aria-controls="edit" aria-selected="false"><?= __('Архив проектов') ?></button>
    </li>
    <? if (user_can(get_current_user_id(), 'accountant') || user_can(get_current_user_id(), 'administrator') || user_can(get_current_user_id(), 'manager') ) { ?>
      <li class="nav-item" role="presentation">
        <button class="nav-link btn-info m-r-1 m-b-2 btn-lg" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab" aria-controls="edit" aria-selected="false"><?= __('Все транзакции') ?></button>
      </li>
    <? } ?>
  <? } ?>
</ul>