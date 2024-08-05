<? 
require_once('wp-load.php');
function my_saved_post( $post_id, $xml_node, $is_update ) {
  $import_id = wp_all_import_get_import_id(); 
      
  if ( $import_id == '1' ) {
    update_project_after_import($post_id);
    // Convert SimpleXml object to array for easier use.
    // $record = json_decode( json_encode( ( array ) $xml_node ), 1 );
  }
}
add_action( 'pmxi_saved_post', 'my_saved_post', 10, 3 );



function import_get_project_for_event($id) {
  $args = array(
    'post_type' => 'projects',
    'limit' => 1,
    'meta_key' => '_nid',
    'meta_value' => $id
  );
  
  $query = new WP_Query( $args );
  
  if ( $query->have_posts() ) {
      $query->the_post();
      return get_the_ID();
  }
}
// debug(import_get_project_for_event(81));

function import_set_project_investors_from_json() {
  $args = array(
    'post_type' => 'projects',
    'posts_per_page' => -1
  );
  
  $query = new WP_Query( $args );
  
  $file = json_decode(file_get_contents(__DIR__ . '/tmp/paragraph__field_investor.json', true));
  $fileInvest = json_decode(file_get_contents(__DIR__ . '/tmp/paragraph__field_invest.json', true));
  $fileInvestOver = json_decode(file_get_contents(__DIR__ . '/tmp/paragraph__field_invest_over.json', true));
  $data = $file[2]->data;
  $dataInvest = $fileInvest[2]->data;
  $dataInvestOver = $fileInvestOver[2]->data;
  $paragraph_investors = array_column($data, 'field_investor_target_id', 'entity_id');
  $paragraph_invest = array_column($dataInvest, 'field_invest_value', 'entity_id');
  $paragraph_invest_over = array_column($dataInvestOver, 'field_invest_over_value', 'entity_id');
  // debug($paragraph_investors);
  
  if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
      $query->the_post();
      $_investors = explode(', ',get_post_meta(get_the_ID(), '_old_investors')[0]);
      $returnData = [];
      foreach($_investors as $k => $_id) {
        delete_field('investory');
        
        // if (!get_field('investory_investors')) {
          if (isset($paragraph_investors[$_id])) {
            // debug('Project ID - ' . get_the_id());
            // debug($_id);
            $users = get_users(array(
              'meta_key' => 'old_uid',
              'meta_value' => $paragraph_investors[$_id]
            ));
            // debug('USER - '.$users);
            if (!empty($users)) {
              $returnData[$k]['investor'] = $users[0]->ID;
            } 
            // debug('_____');
          }
          if (isset($paragraph_invest[$_id])) {
            $returnData[$k]['invest'] = $paragraph_invest[$_id];
          }
          if (isset($paragraph_invest_over[$_id])) {
            $returnData[$k]['invest_over'] = $paragraph_invest_over[$_id];
          }
        // }
      }
    //   if (!empty($returnData)) 
         update_field('investory', ['investors' => $returnData]);
    }
  }
}

function import_set_stages_for_projects() {
  $args = array(
    'post_type' => 'projects',
    'posts_per_page' => -1,
    // 'p' => 2876
  );
  
  $query = new WP_Query( $args );
  
  $file = json_decode(file_get_contents(__DIR__ . '/tmp/paragraph__field_stage.json', true));
  $data = $file[2]->data;
  $paragraph_stages = array_column($data, 'field_stage_value', 'entity_id');
  if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
      $query->the_post();
      $_stages = explode(', ',get_post_meta(get_the_ID(), '_old_stages')[0]);
      $returnData = [];
      foreach($_stages as $k => $_id) {
        delete_field('stadii_proekta');
        
        if (!get_field('stadii_proekta')) {
          if (isset($paragraph_stages[$_id])) {
            $returnData[$k]['etap'] = $paragraph_stages[$_id];
          }
        }
      }
      if (!empty($returnData)) 
        update_field('stadii_proekta', $returnData);
    }
  }
}

function import_get_managers_for_project() {
  $args = array(
    'post_type' => 'projects',
    'posts_per_page' => -1,
    // 'p' => 2876
  );
  
  $query = new WP_Query( $args );
  if ( $query->have_posts() ) {
    while ( $query->have_posts() ) {
      $query->the_post();

      $_managers = explode(', ', get_post_meta(get_the_ID(), 'old_managers')[0]);
      
      $returnData = [];

      foreach($_managers as $k => $_id) {
        // $users = get_users(array(
        //   'meta_key' => 'old_uid',
        //   'meta_value' => $_id
        // ));
        // if (!empty($users)) {
          $returnData[$k] = $_id;
        // }
      }
      
      update_field('managers_group', ['managers' => $returnData]);
    }
  }
	// if (!empty($returnData)) 
}

function update_project_after_import($postID) {
  // update_field('status', get_field('status', $postID), $postID);
}

function update_all_projects_after_import() {
  $posts = get_posts(['post_type' => 'projects', 'posts_per_page' => -1, 'post_status' => 'any']);
  foreach($posts as $post){
    update_field('status', get_field('status', $post->ID), $post->ID);
    // update_field('settings_project', ['sum' => get_field('settings_project_sum', $post->ID)], $post->ID);
  }
}

function import_set_group_for_user() {
  $file = json_decode(file_get_contents(__DIR__ . '/tmp/user__field_group.json', true));
  $data = $file[2]->data;

  $groups = [
    // old => new
    1 => 10,
    2 => 8,
    3 => 9,
    4 => 2,
    5 => 6,
    6 => 5,
    7 => 4,
    8 => 7,
    9 => 3,
  ];
  // NEW 
  // 2 => 'Хабаровск'
  // 3 => 'ФинХаб'
  // 4 => 'Уссурийск'
  // 5 => 'Марафон Хабаровск 2'
  // 6 => 'Марафон Хабаровск'
  // 7 => 'Марафон Комсомольск 2'
  // 8 => 'Марафон Комсомольск 1'
  // 9 => 'Комсомольск'
  // 10 => 'Клуб'
  $arr = [];

  foreach($data as $user) {
    $old_id = $user->entity_id;
    $old_group_id = $user->field_group_target_id;

    $new_user = get_users([
      'meta_key' => 'old_uid',
      'meta_value' => $old_id
    ]);
    $new_user_id = $new_user[0]->ID;
    if (!$new_user_id) continue;

    
    $user_groups = $arr[$new_user_id] ? $arr[$new_user_id]['user_groups'] : [];
    
    $has = false;
    foreach($user_groups as $g) {
      if ($g === $groups[$old_group_id])
      $has = true;
      break;
    }
    if (!$has) {
      $user_groups[] = $groups[$old_group_id];
      $arr[$new_user_id] = [
        'old_id' => $old_id,
        'old_group_id' => $old_group_id,
        'new_user_id' => $new_user_id,
        'user_groups' => $user_groups
      ];
    }
    // wp_update_user(array('ID' => $new_user_id));

    // debug($user_groups);
  }
  // debug(count($arr));
  // debug(array_keys($arr));
}

///////////// Импорт проектов
// import_set_project_investors_from_json(); // Заполняем все данные инвесторов в проектах
// import_set_stages_for_projects(); // Заполняем стадии
// import_get_managers_for_project(); // Заполняем менеджеров
// update_all_projects_after_import(); // Обновляем все статусы у проектов
////////////////////////////

// function import_set_user_group_from_json() {
//   $file = json_decode(file_get_contents(__DIR__ . '/tmp/user__field_group.json', true));
//   $data = $file[2]->data;
//   $group_user = array_column($data, 'field_group_target_id', 'entity_id');
  
//   foreach($group_user as $user => $group)   {

//   }
// }

function update_all_events_after_import() {
  $posts = get_posts(['post_type' => 'events', 'posts_per_page' => -1, 'post_status' => 'any']);
  foreach($posts as $post){
    update_field('settings', ['project' => get_field('settings_project', $post->ID)], $post->ID);
  }
}

//////////// Импорт событий
// update_all_events_after_import(); // Заполняем ID проекта в событии
//////////////////////////////

function import_dohodnost_for_users() {
  $data = json_decode(file_get_contents(__DIR__ . '/tmp/dohodnost_export.json', true));
  // debug($data);
  foreach ($data as $key => $value) {
    $oldUserID = $value->field_user_id[0]->target_id;
    if (!$oldUserID) continue;
    $users = get_users([
      'meta_key' => 'old_uid',
      'meta_value' => $oldUserID,
    ]);
    $userID = $users[0]->ID;

    // delete_field('profit_data', 'user_' . $userID);

    $monthly_data = get_field('profit_data', 'user_' . $userID) ?? [];

    $dates = [];
    $target_date = date('Y-n', strtotime($value->field_date[0]->value));
    if (!empty($monthly_data)) {
      foreach($monthly_data as $month) {
        $dates[] = date('Y-n', strtotime($month['data_month']));
      }
      $has = in_array($target_date, $dates);
    } else {
      $has = false;
    }

    if ($has) continue;

    $monthly_data[] = [
      'data_month' => date('Y-m-d', strtotime($value->field_date[0]->value)),
      'data_invested' => $value->field_user_overdep[0]->value,
      'data_user_contributed' => $value->field_user_contributed[0]->value,
      'data_user_money' => $value->field_user_money[0]->value,
    ];

    update_field('profit_data', $monthly_data, 'user_' . $userID);
  }
}

function update_user_passwords() {
    // Определяем путь к файлу для записи паролей
    $file_path = ABSPATH . 'wp-content/uploads/user_passwords.txt';
    
    // Открываем файл для записи
    $file = fopen($file_path, 'w');
    
    if (!$file) {
        error_log("Не удалось открыть файл для записи: " . $file_path);
        return;
    }
    
    // Получаем всех пользователей, кроме администраторов
    $users = get_users(array(
        'exclude' => array(1), // Исключаем администратора с ID 1, можете изменить по необходимости
        'role__not_in' => array('Administrator') // Исключаем пользователей с ролью администратора
    ));
    
    foreach ($users as $user) {
        // Генерируем новый пароль
        $new_password = wp_generate_password();
        
        // Обновляем пароль пользователя
        wp_set_password($new_password, $user->ID);
        
        // Записываем login:password в файл
        fwrite($file, $user->display_name . ':' . $user->user_login . ':' . $new_password . PHP_EOL);
    }
    
    // Закрываем файл
    fclose($file);
    
    // Сообщаем об успешном завершении
    error_log("Пароли пользователей успешно обновлены и сохранены в: " . $file_path);
}

// Запускаем функцию после инициализации WordPress
// update_user_passwords();
// import_dohodnost_for_users();

function updateEmptiesInvestorsInProjects() {
  $args = array(
    'post_type' => 'projects',
    'posts_per_page' => -1,
  );
  
  $query = new WP_Query( $args );
  $res = [];
  $file = json_decode(file_get_contents(__DIR__ . '/tmp/paragraph__field_investor.json', true));
  $data = $file[2]->data;
  $paragraph_investors = array_column($data, 'field_investor_target_id', 'entity_id');
  
  if ( $query->have_posts() ) {
      while ( $query->have_posts() ) {
          $query->the_post();
  
          $iinvestors = explode(',', get_post_meta(get_the_ID(), '_old_investors', true));
          $count = count($iinvestors);
  
          for($i = 0; $i < $count - 1; $i++) {
            $investor = get_post_meta(get_the_ID(), 'investory_investors_'.$i.'_investor', true);
            if (!$investor) {
              // debug(get_the_ID());
              $p = intval($iinvestors[$i]);
              $old_user_id = $paragraph_investors[$p];
              $users = get_users(array(
                'meta_key' => 'old_uid',
                'meta_value' => $old_user_id
              ));
  
              if (!empty($users)) {
                $userID = $users[0]->ID;
              } 
  
              if ($userID) {
                update_post_meta(get_the_ID(), 'investory_investors_'.$i.'_investor', $userID);
              }
  
              // $res[] = get_the_ID();
            }
          }
      }
  } 
  
  wp_reset_postdata(); // Обязательно вызовите эту функцию после окончания работы с WP_Query
}
// updateEmptiesInvestorsInProjects();

function updateEmptiesInvestorsInTransactions() {
  global $wpdb;
  $res = [];
  $wrongUsers = [];

  // Запрос для получения всех постов типа 'transactions', у которых метаполе 'settings_investor' пустое
  $query = "
      SELECT p.ID 
      FROM {$wpdb->posts} p
      LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'settings_investor'
      WHERE p.post_type = 'transactions'
      AND (pm.meta_value IS NULL OR pm.meta_value = '')
  ";

  $results = $wpdb->get_results($query);

  if ( ! empty( $results ) ) {
      foreach ( $results as $post ) {
        $res[] = $post->ID;
          
        $old_user_id = get_post_meta($post->ID, '_old_contributor', true);
        $users = get_users(array(
          'meta_key' => 'old_uid',
          'meta_value' => $old_user_id
        ));

        if (!empty($users)) {
          $userID = $users[0]->ID;
        }

        if ($userID) {
          update_post_meta($post->ID, 'settings_investor', $userID);
        } else {
          $wrongUsers[] = $old_user_id;
        }

      }
  } else {
      echo 'No transactions found with empty settings_investor meta field.';
  }

  debug(array_unique($wrongUsers));
}

// updateEmptiesInvestorsInTransactions();
?>