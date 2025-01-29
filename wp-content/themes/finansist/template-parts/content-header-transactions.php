<? 
$view = $args['view'] ? explode(',', $args['view']) : explode(',', 'num,name,project,investor,amount,date,id') ;
?>
<thead>
    <tr>
      <? if (in_array('num', $view)) { ?>
        <th scope="col" class="th-num">№</th>
      <? } ?>
      <? if (in_array('name', $view)) { ?>
      <th scope="col" class="th-name"><?= esc_html('Имя транзакции')?></th>
      <? } ?>
      <? if (in_array('project', $view)) { ?>
      <th scope="col" class="th-project"><?= esc_html('Проект')?></th>
      <? } ?>
      <? if (in_array('investor', $view) && !current_user_can('contributor')) { ?>
        <th scope="col" class="td-investor"><?= esc_html('Инвестор')?></th>
      <? } ?>
      <? if (in_array('amount', $view)) { ?>
      <th scope="col" class="th-sum sort-col">
        <?= esc_html('Сумма')?>
        <label class="sort-label<?=(isset($_REQUEST['sort']) && $_REQUEST['sort'] == 'max_amount'?' active':'')?>">
          <input type="radio" class="sort-input" name="sort" value="max_amount" onclick="formSortHandler(this)">↓</button>
        </label>
        <label class="sort-label<?=(isset($_REQUEST['sort']) && $_REQUEST['sort'] == 'min_amount')?' active':''?>">
          <input type="radio" class="sort-input" name="sort" value="min_amount" onclick="formSortHandler(this)">↑</button>
        </label>
      </th>
      <? } ?>
      <? if (in_array('date', $view)) { ?>
      <th scope="col" class="th-date sort-col">
        <?= esc_html('Время создания')?>
        <label class="sort-label<?=(isset($_REQUEST['sort']) && $_REQUEST['sort'] == 'new'?' active':'')?>">
          <input type="radio" class="sort-input" name="sort" value="new" onclick="formSortHandler(this)">↓</button>
        </label>
        <label class="sort-label<?=(isset($_REQUEST['sort']) && $_REQUEST['sort'] == 'old')?' active':''?>">
          <input type="radio" class="sort-input" name="sort" value="old" onclick="formSortHandler(this)">↑</button>
        </label>
      </th>
      <? } ?>
      <? if (in_array('id', $view)) { ?>
      <th scope="col" class="th-id">#</th>
      <? } ?>
    </tr>
  </thead>