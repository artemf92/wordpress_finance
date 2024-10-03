<!-- Nav tabs -->
<ul class="nav nav-tabs m-b-3" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link btn-info m-r-1 m-b-2 btn-lg active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="true">Информация</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link btn-info m-r-1 m-b-2 btn-lg" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">История проекта</button>
  </li>
  <? if (current_user_can('administrator') || current_user_can('mananger') || current_user_can('accountant') || isProjectManager(get_the_ID(), getUserID())) { ?>
  <li class="nav-item" role="presentation">
    <button class="nav-link btn-info m-r-1 m-b-2 btn-lg" id="investors-tab" data-bs-toggle="tab" data-bs-target="#investors" type="button" role="tab" aria-controls="investors" aria-selected="false">Инвесторы</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link btn-info m-r-1 m-b-2 btn-lg" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions" type="button" role="tab" aria-controls="investors" aria-selected="false">Транзакции</button>
  </li>
  <? } ?>
  <? /* ?>
  <li class="nav-item" role="presentation">
    <button class="nav-link btn-info m-r-1 m-b-2 btn-lg" id="stages-tab" data-bs-toggle="tab" data-bs-target="#stages" type="button" role="tab" aria-controls="stages" aria-selected="false">Стадии проекта</button>
  </li>
  <? */ ?>
</ul>