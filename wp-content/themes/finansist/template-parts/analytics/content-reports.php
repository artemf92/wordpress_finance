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

  if (current_user_can('accountant') || current_user_can('administrator')) {
    $variants['group_profit'] = __('Доходность группы');
  }
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
          <input type="text" class="form-input form-control" id="date-range" name="date-range" autocomplete="off" placeholder="Выберите период" value="<?=$_GET['date-range']?>" required>
        </div>
      </div>
      <div class="col-md-4 col-lg-3">
        <div class="mb-3">
          <label for="variant" class="form-label">
            <? echo __('Вариант отчета:') ?>
          </label>
          <select name="variant" class="form-select form-control" aria-label="<?= __('Выберите вариант') ?>" required>
            <option disabled><?= __('Выберите вариант') ?></option>
            <? foreach($variants as $key => $variant) { ?>
            <option value="<?=$key?>" <?=isset($_GET['variant']) && $_GET['variant'] == $key ? 'selected':''?>><?= $variant ?></option>
            <? } ?>
          </select>
        </div>
      </div>
      
      <?
      global $wpdb;
      $filterGroups = [];

      if (current_user_can('manager') || current_user_can('administrator')) {
        $filterGroups = $wpdb->get_results("SELECT id, group_name FROM `wp_promag_groups`", 'ARRAY_A');
      } else if (current_user_can('project_manager')) {
        $tmpFilterGroups = get_user_meta(get_current_user_id(), 'pm_group', true);
        foreach($tmpFilterGroups as $groupID) {
          $group_name = $wpdb->get_var("SELECT group_name FROM `wp_promag_groups` WHERE id = {$groupID}");
          $filterGroups[] = [
            'id' => $groupID,
            'group_name' => $group_name
          ];
        }
      }

      if (!empty($filterGroups)) {
      ?>
        <div class="col-md-4 col-lg-3">
          <div class="mb-3">
            <label for="variant" class="form-label">
              <? echo __('Группа:') ?>
            </label>
            <select name="group" class="form-select form-control" aria-label="<?= __('Выберите вариант') ?>" required>
              <option disabled><?= __('Выберите группу') ?></option>
              <? foreach($filterGroups as $key => $group) { ?>
              <option value="<?=$group['id']?>" <?=isset($_GET['group']) && $_GET['group'] == $group['id'] ? 'selected':''?>><?= $group['group_name'] ?></option>
              <? } ?>
              <optgroup label="<?= __('___') ?>">
                <? if (current_user_can('manager') || current_user_can('administrator')) { ?>
                <option value="all" <?=isset($_GET['group']) && $_GET['group'] == 'all' ? 'selected':''?>><?= __('Все группы') ?></option>
                <? } ?>
                <option value="null" <?=isset($_GET['group']) && $_GET['group'] == 'null' ? 'selected':''?>><?= __('Без группы') ?></option>
              </optgroup>
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
  jQuery(document).ready(function($) {
    const end = moment();
    
    // Функция для получения параметров URL
    function getUrlParameter(name) {
      name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
      var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
      var results = regex.exec(location.search);
      return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
    
    // Функция для инициализации правильного режима picker'а
    function initializePicker() {
      const variant = getUrlParameter('variant') || $('select[name="variant"]').val();
      
      if (variant === 'group_profit') {
        createMonthPicker();
      } else {
        createStandardPicker();
      }
    }
    
    // Функция для создания daterangepicker с месяцами
    function createMonthPicker() {
      $('#date-range').daterangepicker('destroy');
      
      const currentYear = moment().year();
      const currentMonth = moment().month();
      const lastMonth = moment().subtract(1, 'month');
      
      // Определяем максимальную дату для выбора
      let maxDate = end;
      if (moment().year() === currentYear) {
        // Если текущий год, то максимум - прошлый месяц
        maxDate = lastMonth.endOf('month');
      }
      
      $('#date-range').daterangepicker({
        opens: "center",
        drops: "up",
        startDate: moment('2024-10-01'),
        endDate: maxDate,
        locale: {
            format: 'MM/YYYY',
            applyLabel: 'Применить',
            cancelLabel: 'Отмена',
            customRangeLabel: 'Выбрать месяц',
            daysOfWeek: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек']
        },
        autoUpdateInput: false,
        showDropdowns: true,
        singleDatePicker: true,
        showWeekNumbers: false,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker24Hour: true,
        minYear: 2024,
        maxYear: currentYear
      });
      
      // Привязываем обработчики после создания picker
      bindDateRangeHandlers();
    }
    
    // Функция для создания стандартного daterangepicker
    function createStandardPicker() {
      $('#date-range').daterangepicker('destroy');
      $('#date-range').daterangepicker({
        opens: "center",
        drops: "up",
        endDate: end,
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'Применить',
            cancelLabel: 'Отмена',
            customRangeLabel: 'Выбрать даты',
            daysOfWeek: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
            monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек']
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
      
      // Привязываем обработчики после создания picker
      bindDateRangeHandlers();
    }
    
    // Функция для привязки обработчиков событий daterangepicker
    function bindDateRangeHandlers() {
      // Удаляем старые обработчики перед привязкой новых
      $('#date-range').off('apply.daterangepicker cancel.daterangepicker');
      
      // Привязываем новые обработчики
      $('#date-range').on('apply.daterangepicker', function(ev, picker) {
        const selectedVariant = $('select[name="variant"]').val();
        if (selectedVariant === 'group_profit') {
          // Автоматически устанавливаем начало и конец выбранного месяца
          const selectedMonth = picker.startDate.month();
          const selectedYear = picker.startDate.year();
          
          picker.startDate = moment([selectedYear, selectedMonth, 1]);
          picker.endDate = moment([selectedYear, selectedMonth]).endOf('month');
          
          // Обновляем значение в поле
          $(this).val(picker.startDate.format('M/YYYY') + ' - ' + picker.endDate.format('M/YYYY'));
          
          // Предотвращаем стандартную обработку
          ev.preventDefault();
          return false;
        } else {
          $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        }
      });

      $('#date-range').on('cancel.daterangepicker', function(ev, picker) {
          $(this).val('');
      });
      
      // Обработчик изменения года в dropdown
      $('#date-range').on('show.daterangepicker', function(ev, picker) {
        const selectedVariant = $('select[name="variant"]').val();
        if (selectedVariant === 'group_profit') {
          const currentYear = moment().year();
          const selectedYear = picker.startDate.year();
          
          // Ограничиваем выбор года: минимум 2024, максимум текущий год
          if (selectedYear < 2024) {
            picker.startDate = moment('2024-10-01');
          } else if (selectedYear > currentYear) {
            picker.startDate = moment(currentYear + '-01-01');
          }
          
          // Если выбран 2024 год, ограничиваем выбор месяцев (минимум октябрь)
          if (selectedYear === 2024) {
            picker.startDate = moment('2024-10-01');
            picker.endDate = moment('2024-12-31');
          } else if (selectedYear === currentYear) {
            // Если выбран текущий год, ограничиваем выбор месяцев
            const lastMonth = moment().subtract(1, 'month');
            picker.endDate = lastMonth.endOf('month');
          } else {
            picker.endDate = moment(selectedYear, 'YYYY').endOf('year');
          }
        }
      });
      
      // Обработчик изменения года в dropdown
      $('#date-range').on('changeDate.daterangepicker', function(ev, picker) {
        const selectedVariant = $('select[name="variant"]').val();
        if (selectedVariant === 'group_profit') {
          const currentYear = moment().year();
          const selectedYear = picker.startDate.year();
          
          // Ограничиваем выбор года: минимум 2024, максимум текущий год
          if (selectedYear < 2024) {
            picker.startDate = moment('2024-10-01');
          } else if (selectedYear > currentYear) {
            picker.startDate = moment(currentYear + '-01-01');
          }
          
          // Если выбран 2024 год, ограничиваем выбор месяцев (минимум октябрь)
          if (selectedYear === 2024) {
            if (picker.startDate.month() < 9) { // 9 = октябрь (0-based)
              picker.startDate = moment('2024-10-01');
            }
            picker.endDate = moment('2024-12-31');
          } else if (selectedYear === currentYear) {
            // Если выбран текущий год, ограничиваем выбор месяцев
            const lastMonth = moment().subtract(1, 'month');
            if (picker.startDate.isAfter(lastMonth)) {
              picker.startDate = lastMonth.startOf('month');
            }
            picker.endDate = lastMonth.endOf('month');
          } else {
            picker.endDate = moment(selectedYear, 'YYYY').endOf('year');
          }
        }
      });
      
      // Обработчик для ограничения выбора только полными месяцами
      $('#date-range').on('apply.daterangepicker', function(ev, picker) {
        const selectedVariant = $('select[name="variant"]').val();
        if (selectedVariant === 'group_profit') {
          // Автоматически устанавливаем начало и конец выбранного месяца
          const selectedMonth = picker.startDate.month();
          const selectedYear = picker.startDate.year();
          
          picker.startDate = moment([selectedYear, selectedMonth, 1]);
          picker.endDate = moment([selectedYear, selectedMonth]).endOf('month');
          
          // Обновляем значение в поле
          $(this).val(picker.startDate.format('M/YYYY') + ' - ' + picker.endDate.format('M/YYYY'));
          
          // Предотвращаем стандартную обработку
          ev.preventDefault();
          return false;
        }
      });
    }

    // Инициализация picker'а в зависимости от параметров URL
    initializePicker();
    
    // Устанавливаем значение поля даты из URL параметров
    const dateRangeParam = getUrlParameter('date-range');
    if (dateRangeParam) {
      $('#date-range').val(dateRangeParam);
    }

    // Обработчик изменения селекта variant
    $('select[name="variant"]').on('change', function() {
      const selectedValue = $(this).val();
      
      if (selectedValue === 'group_profit') {
        createMonthPicker();
      } else {
        createStandardPicker();
      }
      
      // Очищаем поле даты при смене типа отчета
      $('#date-range').val('');
    });

  });
</script>