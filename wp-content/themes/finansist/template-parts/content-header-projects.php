<? 
$view = $args['view'] ? explode(',', $args['view']) : explode(',', 'num,name,status,amount,investments,profit,date');


$sort = isset($args['sort']) ? $args['sort'] : null;

// Ссылка сортировки по колонке: повторный клик меняет направление
$sort_link = function($key, $label) use ($sort) {
  $active = $sort['by'] === $key;
  $next = $active && $sort['order'] === 'DESC' ? 'asc' : 'desc';
  $url = add_query_arg(['archive_orderby' => $key, 'archive_order' => $next]);
  $url = remove_query_arg(['paged', 'archive_projects_page'], $url);
  $arrow = $active ? ($sort['order'] === 'ASC' ? ' ▲' : ' ▼') : '';
  return '<a href="'.esc_url($url).'">'.esc_html($label).$arrow.'</a>';
};
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
    <th scope="col"><?= $sort ? $sort_link('created', 'Дата создания') : esc_html(in_array('close_date', $view) ? 'Дата создания' : 'Время создания')?></th>
    <? } ?>
    <? if (in_array('close_date', $view)) { ?>
    <th scope="col"><?= $sort ? $sort_link('closed', 'Дата закрытия') : esc_html('Дата закрытия')?></th>
    <? } ?>
  </tr>
  </thead>