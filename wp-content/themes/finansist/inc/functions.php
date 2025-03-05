<? 
function debug($obj) {
  echo '<pre>';
  print_r($obj, false);
  echo '</pre>';
}

function get_formatted_number($num, $after = ' ₽') {
  if ((int)$num && !is_numeric(abs((int)$num))) return;
  return number_format((float)$num, 2, '.', ' ') . (abs((int)$num) > 0 ? ' '.$after:'');
}

function get_post_full_time($post = null) {
  return get_post_time('j F Y - G:i', false, $post, true);
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

  foreach($arUpdate as $key => $upd) {
    update_post_meta($post_id, 'settings_'.$key, $upd);
  }

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

function getAdminGroupUsers($user_id = null) {
  if (!$user_id) {
    $user_id = wp_get_current_user()->ID;
  }

  global $wpdb;

  $group_id = get_user_meta($user_id, 'pm_group', true);

  $usersInGroup = [];

  foreach ($group_id as $key => $g_id) {
    $tmpArr = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value LIKE %s",'pm_group',serialize([$g_id])));
    $usersInGroup = array_merge($usersInGroup, $tmpArr);
  }
  return array_unique($usersInGroup);
}

function getUserGroups($user_id = null) {
  if (!$user_id) {
    $user_id = wp_get_current_user()->ID;
  }

  
  $groups = get_user_meta($user_id, 'pm_group', true);
  $arGroups = [];
  
  if (!is_array($groups)) return $arGroups;

  foreach($groups as $group) {
    global $wpdb;

    $tmpArr = $wpdb->get_results($wpdb->prepare("SELECT * FROM `wp_promag_groups` WHERE id = %s", $group));

    $arGroups[] = [
      'id' => $group,
      'name' => $tmpArr[0]->group_name
    ];
  }
  
  return $arGroups;
}

function userDisplayName($user) {
  if (!$user) {
    $user = get_user_by('ID', wp_get_current_user()->ID);
  }

  $groups = getUserGroups($user->ID);

  if (empty($groups)) {
    return $user->display_name;
  }

  return $user->display_name . '/' . $groups[0]['name'];
}

function getLastPortfolioUpdate($userID) {
  $posts = get_posts([
    'post_type' => 'transactions', 
    'posts_per_page' => 1, 
    'post_status' => 'any',
    'meta_query' => [
      'relation' => 'AND',
      [
        'key' => 'settings_investor',
        'value' => $userID
      ],
      [
        'key' => 'settings_transaction_type',
        'value' => 12
      ]
    ]
  ]);

  if (!empty($posts)) {
    return $posts[0]->post_date;
  }

  return null;
}

function getUsersByGroup($group_id) {
	global $wpdb;

  $query = $wpdb->prepare(
    "SELECT DISTINCT user_id 
     FROM $wpdb->usermeta 
     WHERE meta_key = %s 
     AND meta_value LIKE %s",
    'pm_group',
    '%"' . intval($group_id) . '"%'
  );

  $tmpArr = $wpdb->get_col($query);
  if ( !empty($tmpArr) ) {
      $tmpArr = array_map('intval', $tmpArr); // Преобразуем результат в целые числа
  }

  return $tmpArr;

}

function getGroupName($group_id) {
  global $wpdb;

  return $wpdb->get_var("SELECT group_name FROM `wp_promag_groups` WHERE id = {$group_id}");
}
