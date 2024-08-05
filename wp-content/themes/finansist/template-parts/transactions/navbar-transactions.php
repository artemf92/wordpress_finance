<!-- Nav tabs -->
<ul class="nav nav-tabs m-b-3" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link btn-info m-r-1 m-b-2 btn-lg active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true"><?= __('Информация') ?></button>
  </li>
  <li class="nav-item" role="presentation">
    <? wp_delete_post_link() ?>
  </li>
  <? /* ?>
  <li class="nav-item" role="presentation">
    <button class="nav-link btn-info m-r-1 m-b-2 btn-lg" id="stages-tab" data-bs-toggle="tab" data-bs-target="#stages" type="button" role="tab" aria-controls="stages" aria-selected="false">Стадии проекта</button>
  </li>
  <? */ ?>
</ul>