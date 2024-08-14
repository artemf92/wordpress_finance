<? 
function debug($obj) {
  echo '<pre>';
  print_r($obj, false);
  echo '</pre>';
}

function get_formatted_number($num, $after = ' ₽') {
  if (!is_numeric($num)) return;
  return number_format($num, 2, '.', ' ') . ($num > 0 ? ' '.$after:'');
}

function get_post_full_time() {
  return get_post_time('j F Y - G:i', false, null, true);
}

// Создание транзакции
function create_transaction($projectID = false, $user, $eventID = false, $amount, $type) {
  $transaction_entity = getAcfFieldEntity(50);

  $name = $transaction_entity['transaction_type']['choices'][$type];

  if ($projectID) {
    $name .= ' (проект «'.get_the_title($projectID).'»)';
  }

  $post_id = wp_insert_post([
    'post_type' => 'transactions',
    'post_title' => $name,
    'post_author' => 1,
    'post_status' => 'publish'
  ], true);

  if (is_wp_error($post_id)) {
    error_log($post_id->get_error_message());
    return ['error' => $post_id->get_error_message()];
  }

  // 1 : Вложение в проект
  // 2 : Вложение в проект (сверх)
  // 3 : Возврат инвестиций по проекту
  // 6 : Возврат инвестиций по проекту (сверх)
  // 4 : Доход по проекту
  // 5 : Выход из проекта
  // 7 : Изменение вложений администратором
  // 8 : Изменение вложений (сверх) администратором
  // 9 : Доход от инвестиций
  // 10 : Выдача денег (в портфель)
  // 11 : Выдача денег (сверх)
  // 12 : Изменение портфеля
  // 13 : Убыток по проекту

  $arUpdate = [
    'investor' => $user,
    'sum' => $amount,
    'transaction_type' => $type
  ];

  if ($projectID) 
    $arUpdate['project'] = $projectID;

  if ($eventID) 
    $arUpdate['event'] = $eventID;

  update_field('settings', $arUpdate, $post_id);

  return $post_id;
}

// Вложение денег в проект
function withdraw_money($userID, $amount, $over = false) {
  $money = get_field('money', 'user_' . $userID);

  if (!$over)
    $result = update_field('money', $money - $amount, 'user_' . $userID);

  if (is_wp_error($result)) {
    echo $result->get_error_message();
    wp_die();
  }

  unset($result);

  $invested = get_field($over ? 'overdep':'contributed', 'user_' . $userID);
  $result = update_field($over ? 'overdep':'contributed', $invested + $amount, 'user_' . $userID);

  if (is_wp_error($result)) {
    echo $result->get_error_message();
    wp_die();
  }
}

function create_project_event($projectID, $amount, $type) {
  $event_entity = getAcfFieldEntity(43);

  $name = $event_entity['transaction_type']['choices'][$type];

  $name .= ' (проект «'.get_the_title($projectID).'»)';

  $post_id = wp_insert_post([
    'post_type' => 'events',
    'post_title' => $name,
    'post_author' => 1,
    'post_status' => 'publish'
  ], true);

  if (is_wp_error($post_id)) {
    error_log($post_id->get_error_message());
    return ['error' => $post_id->get_error_message()];
  }

  // 0 : Создание проекта (проект на рассмотрении)
  // 1 : Вложение в проект (старт)
  // 2 : Изменение проекта
  // 3 : Возврат инвестиций (осн. сумма)
  // 4 : Доход (прибыль/убыток)
  // 5 : Закрытие проекта

  update_field('settings', [
    'project' => $projectID,
    'sum' => $amount,
    'transaction_type' => $type
  ], $post_id);

  return $post_id;
}

function getAcfFieldEntity($group_id) {
  $specifications_fields = array();

  $fields = acf_get_fields( $group_id );

  foreach ( $fields as $field ) {
    if (isset($field['sub_fields'])) {
      foreach($field['sub_fields'] as $sub_field) {
        $field_value = $sub_field['name'];
  
        if ( ! empty( $field_value ) ) {
            $specifications_fields[ $sub_field['name'] ] = $sub_field;
        }
      }
    } else {
      $field_value = get_field( $field['name'] );
  
        if ( ! empty( $field_value ) ) {
            $specifications_fields[ $field['name'] ] = $field_value;
        }
    }
  }
  return $specifications_fields;
}

function get_portfolio($user) {
  return [
    'money' => get_field('money', 'user_' . $user),
    'profit' => get_field('profit', 'user_' . $user),
    'refund' => get_field('refund', 'user_' . $user),
    'refund_over' => get_field('refund_over', 'user_' . $user),
  ];
}