<? 
$pprVariants = [30, 60, 90, 120];

if (isset($_REQUEST['per_page']) &&
  $post_per_page != $_REQUEST['per_page'] &&
  in_array($_REQUEST['per_page'], $pprVariants)
) {
  setPostsPerPage($_REQUEST['per_page']);
  $post_per_page = $_REQUEST['per_page'];
}
?>
<div class="col-md-2">
  <div class="mb-3">
    <div class="">
      <label for="per_page" class="d-flex align-items-center">
        <?= esc_html('Показать:') ?>&nbsp;&nbsp;&nbsp;
        <select name="per_page" id="per_page" class="form-select form-control" onchange="this.closest('form').submit()">
          <? foreach($pprVariants as $item) { ?>
          <option value="<?=$item?>" <?=$item == $post_per_page ? 'selected':''?>><?= $item ?></option>
          <? } ?>
        </select>
      </label>
    </div>
  </div>
</div>