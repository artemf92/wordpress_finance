  <thead>
    <tr>
      <th scope="col" class="th-num">№</th>
      <th scope="col" class="th-name"><?= esc_html('Имя транзакции')?></th>
      <th scope="col" class="th-project"><?= esc_html('Проект')?></th>
      <? if (!current_user_can('contributor')) { ?>
      <th scope="col" class="td-investor"><?= esc_html('Инвестор')?></th>
      <? } ?>
      <th scope="col" class="th-sum sort-col">
        <?= esc_html('Сумма')?>
        <label class="sort-label<?=(isset($_REQUEST['sort']) && $_REQUEST['sort'] == 'max_amount'?' active':'')?>">
          <input type="radio" class="sort-input" name="sort" value="max_amount" onclick="formSortHandler(this)">↓</button>
        </label>
        <label class="sort-label<?=(isset($_REQUEST['sort']) && $_REQUEST['sort'] == 'min_amount')?' active':''?>">
          <input type="radio" class="sort-input" name="sort" value="min_amount" onclick="formSortHandler(this)">↑</button>
        </label>
      </th>
      <th scope="col" class="th-date sort-col">
        <?= esc_html('Время создания')?>
        <label class="sort-label<?=(isset($_REQUEST['sort']) && $_REQUEST['sort'] == 'new'?' active':'')?>">
          <input type="radio" class="sort-input" name="sort" value="new" onclick="formSortHandler(this)">↓</button>
        </label>
        <label class="sort-label<?=(isset($_REQUEST['sort']) && $_REQUEST['sort'] == 'old')?' active':''?>">
          <input type="radio" class="sort-input" name="sort" value="old" onclick="formSortHandler(this)">↑</button>
        </label>
      </th>
      <th scope="col" class="th-id">#</th>
    </tr>
  </thead>