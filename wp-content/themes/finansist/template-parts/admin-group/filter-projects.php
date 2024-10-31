<? global $users; ?>
<form class="form-filter">
  <div class="row">
    <div class="col-md-4">
      <div class="mb-3">
        <label for="investor" class="form-label">
          <? echo __('Инвестор:') ?>
        </label>
        <select name="investor" class="form-select form-control" aria-label="Default select example">
          <option selected disabled><?= __('Выберите инвестора') ?></option>
          <? foreach($users as $id) { ?>
          <option value="<?=$id?>" <?=$_REQUEST['investor'] == $id?'selected':''?>><?= userDisplayName(get_userdata($id)) ?></option>
          <? } ?>
        </select>
      </div>
    </div>
    <div class="col-md-3">
      <div class="d-flex">
        <button type="submit" class="btn btn-info btn-md d-block m-a-2">
          <i class="fa-solid fa-filter"></i>
          <?= __('Показать') ?>
        </button>
      </div>
      </div>
  </div>
  <hr>
</form>