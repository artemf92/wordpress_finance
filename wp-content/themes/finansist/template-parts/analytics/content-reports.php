<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<section class="reports">
  <h2><?= _e('Отчеты') ?></h2>
  <? 
  $variants = [
    'refill' => __('Пополнение портфеля'),
    'portfolio_investment' => __('Вложения портфельными средствами'),
    'investment_projects' => __('Инвестиции в проекты за период'),
  ];
  ?>
  <form class="form-reports">
    <div class="row">
      <div class="col-md-2">
        <div class="mb-3">
          <label for="date" class="form-label">
            <? echo __('Дата:') ?>
          </label>
          <br>
          <? /* ?>
          <input type="date" class="form-input form-control" min="2025-01-29" name="date" value="<?=isset($_GET['date']) ? $_GET['date']:""?>" required>
          <? */ ?>
          <input type="text" class="form-input form-control" id="date-range" name="date-range" placeholder="Выберите период" value="<?=$_GET['date-range']?>" required>
        </div>
      </div>
      <div class="col-md-4 col-lg-3">
        <div class="mb-3">
          <label for="variant" class="form-label">
            <? echo __('Вариант отчета:') ?>
          </label>
          <select name="variant" class="form-select form-control" aria-label="<?= __('Выберите вариант') ?>" required>
            <option selected disabled><?= __('Выберите вариант') ?></option>
            <? foreach($variants as $key => $variant) { ?>
            <option value="<?=$key?>" <?=isset($_GET['variant']) && $_GET['variant'] == $key ? 'selected':''?>><?= $variant ?></option>
            <? } ?>
          </select>
        </div>
      </div>
      
      <?
      global $wpdb;
      $filterGroups = [];

      if (current_user_can('project_manager')) {
        $tmpFilterGroups = get_user_meta(get_current_user_id(), 'pm_group', true);
        foreach($tmpFilterGroups as $groupID) {
          $group_name = $wpdb->get_var("SELECT group_name FROM `wp_promag_groups` WHERE id = {$groupID}");
          $filterGroups[] = [
            'id' => $groupID,
            'group_name' => $group_name
          ];
        }
      } else if (current_user_can('manager') || current_user_can('administrator')) {
        $filterGroups = $wpdb->get_results("SELECT id, group_name FROM `wp_promag_groups`", 'ARRAY_A');
      }

      if (!empty($filterGroups) && count($filterGroups) > 1) {
      ?>
        <div class="col-md-4 col-lg-3">
          <div class="mb-3">
            <label for="variant" class="form-label">
              <? echo __('Группа:') ?>
            </label>
            <select name="group" class="form-select form-control" aria-label="<?= __('Выберите вариант') ?>" required>
              <option selected disabled><?= __('Выберите группу') ?></option>
              <? foreach($filterGroups as $key => $group) { ?>
              <option value="<?=$group['id']?>" <?=isset($_GET['group']) && $_GET['group'] == $group['id'] ? 'selected':''?>><?= $group['group_name'] ?></option>
              <? } ?>
              <? if (current_user_can('manager') || current_user_can('administrator')) { ?>
                <optgroup label="<?= __('Все группы') ?>">
                  <option value="all" <?=isset($_GET['group']) && $_GET['group'] == 'all' ? 'selected':''?>><?= __('Все группы') ?></option>
                </optgroup>
              <? } ?>
            </select>
          </div>
        </div>
      <? } ?>
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
    <input type="hidden" name="page-report" value="<?=$_GET['page-report'] ?: 1?>">
  </form>
  <div class="ajax-report"></div>
</section>

<script>
  jQuery(function($) {
    const end = moment();
      $('#date-range').daterangepicker({
        opens: "center",
        drops: "up",
        endDate: end,
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'Применить',
            cancelLabel: 'Отмена',
            customRangeLabel: 'Выбрать даты'
        },
        autoUpdateInput: false,
        ranges: {
            'Сегодня': [moment(), moment()],
            'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
            'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
            'Этот месяц': [moment().startOf('month'), moment().endOf('month')],
            'Прошлый месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
      });

      $('#date-range').on('apply.daterangepicker', function(ev, picker) {
          $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
      });

      $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
          $(this).val('');
      });

  });
</script>