<? if ($args != 'archive' ) { ?>

  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col"><?= esc_html('Название проекта')?></th>
      <th scope="col"><?= esc_html('Статус проекта')?></th>
      <th scope="col"><?= esc_html('Сумма проекта')?></th>
      <th scope="col"><?= esc_html('Инвестировано / сверх')?></th>
      <th scope="col"><?= esc_html('Доходность')?></th>
      <th scope="col"><?= esc_html('Время создания')?></th>
    </tr>
  </thead>

<? } else { ?>
  <thead>
      <tr>
        <th scope="col">#</th>
        <th scope="col"><?= esc_html('Название проекта')?></th>
        <th scope="col"><?= esc_html('Статус проекта')?></th>
        <th scope="col"><?= esc_html('Доходность')?></th>
        <th scope="col"><?= esc_html('Время создания')?></th>
      </tr>
    </thead>
<? } ?>