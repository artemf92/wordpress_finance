<? 
add_action('acf/validate_save_post', 'validate_save_post_projects', 20);

function validate_save_post_projects() {
  if (!isset($_POST['post_id']))
    return;

  $post_id = $_POST['post_id'];
  $post_type = get_post_type($post_id);

  if ($post_type != 'projects') {
      return;
  }

  if( current_user_can('manage_options') ) {
    acf_reset_validation_errors();
  }

  // Получаем старые данные вложений до сохранения
  $old_investors = get_field('investory', $post_id);
  $old_investor_data = [];
  if ($old_investors) {
    foreach ($old_investors['investors'] as $old_investor) {
      $old_investor_data[$old_investor['investor']] = [
        'invest' => $old_investor['invest'],
        'invest_over' => $old_investor['invest_over']
      ];
    }
  }

  // Новые данные
  $project_amount = round(floatval($_POST['acf']['field_65e390383e640']['field_65e390623e641']), 2);
  $new_investors = $_POST['acf']['field_65e391123e644']['field_65e391333e645'];
  $stages = $_POST['acf']['field_65e391fa3e649'];

  $total_invested = 0;

  foreach ($new_investors as $key => $row) {
    $investor_id = $row['field_65e391643e646'];
    $new_invested = round(floatval($row['field_65e391993e647']), 2);
    $new_invested_over = round(floatval($row['field_65e391c33e648']), 2);

    // Рассчитываем разницу между новыми и старыми данными
    $old_invested = isset($old_investor_data[$investor_id]) ? round(floatval($old_investor_data[$investor_id]['invest']), 2) : 0;
    // $old_invested_over = isset($old_investor_data[$investor_id]) ? round(floatval($old_investor_data[$investor_id]['invest_over']), 2) : 0;

    // $invested_difference = ($new_invested + $new_invested_over) - ($old_invested + $old_invested_over);
    $invested_difference = $new_invested - $old_invested;

    $total_invested += round($new_invested + $new_invested_over, 2);

    if (round(get_field('money', 'user_' . $investor_id), 2) < $invested_difference) {
      acf_add_validation_error('acf[field_65e391123e644][field_65e391333e645]['.$key.']', 'У инвестора недостаточно денег для вложения.');
    }
  }

  $total_invested = round($total_invested, 2);

  if ($total_invested != $project_amount) {
    acf_add_validation_error('acf[field_65e390383e640][field_65e390623e641]', sprintf("Проверьте сумму, должна быть: %s, получена сумма: %s", get_formatted_number($project_amount), get_formatted_number($total_invested)));
  }

  if (empty($stages)) {
    acf_add_validation_error('acf[field_65e391fa3e649]', 'Поле не должно быть пустым!');
  }

  if (empty($new_investors)) {
    acf_add_validation_error('acf[field_65e391123e644][field_65e391333e645]', 'Поле не должно быть пустым!');
  }
}

add_action('transition_post_status', 'add_created_event', 10, 3);

function add_created_event($new_status, $old_status, $post) {
  if ('projects' !== $post->post_type)
    return;

  if ('publish' === $new_status && $old_status !== 'publish')
    return;

  $post_title = get_the_title($post->ID);

  if ($post_title === 'Черновик')
    return;

  $posts = get_posts([
    'post_type' => 'events',
    'meta_query' => [
      'relation' => 'AND',
      [
        'key' => 'settings_project',
        'value' => $post->ID,
      ],
      [
        'key' => 'settings_transaction_type',
        'value' => '0'
      ]
    ],
  ]);

  if (count($posts) > 0)
    return;

  $post_data = array(
    'post_title'    => sanitize_text_field('Создание проекта (проект на рассмотрении) (проект «' . $post_title . '»)' ),
    'post_status'   => 'publish',
    'post_type'     => 'events',
    'post_author'   => 1,
  );

  $new_post_id = wp_insert_post($post_data, true);

  if (is_wp_error($new_post_id)) {
    echo $new_post_id->get_error_message();
  } else {
    update_field('settings', [
      'project' => $post->ID,
      'sum' => 0,
      'transaction_type' => 0
    ], $new_post_id);
  }
}
?>
