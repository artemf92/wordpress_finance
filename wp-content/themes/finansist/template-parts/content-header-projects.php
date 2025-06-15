<? 
$view = $args['view'] ? explode(',', $args['view']) : explode(',', 'num,name,status,amount,investments,profit,date');

?>
<thead>
  <tr>
    <? if (in_array('num', $view)) { ?>
    <th scope="col">#</th>
    <? } ?>
    <? if (in_array('name', $view)) { ?>
    <th scope="col"><?= esc_html('Название проекта')?></th>
    <? } ?>
    <? if (in_array('status', $view)) { ?>
    <th scope="col"><?= esc_html('Статус проекта')?></th>
    <? } ?>
    <? if (in_array('amount', $view)) { ?>
    <th scope="col"><?= esc_html('Сумма проекта')?></th>
    <? } ?>
    <? if (in_array('investments', $view)) { ?>
    <th scope="col"><?= esc_html('Инвестировано / сверх')?></th>
    <? } ?>
    <? if (in_array('profit', $view)) { ?>
    <th scope="col"><?= esc_html('Доходность')?></th>
    <? } ?>
    <? if (in_array('date', $view)) { ?>
    <th scope="col"><?= esc_html('Время создания')?></th>
    <? } ?>
  </tr>
  </thead>