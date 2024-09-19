<div class="items-view">
  <label for="per_page" class="d-flex align-items-center">
    <?= esc_html('Показать:') ?>&nbsp;&nbsp;&nbsp;
    <select name="per_page" id="per_page" class="form-select form-control" onchange="this.closest('form').submit()">
      <? foreach($pprVariants as $item) { ?>
      <option value="<?=$item?>" <?=$item == $post_per_page ? 'selected':''?>><?= $item ?></option>
      <? } ?>
    </select>
  </label>
</div>