<? 
// Проверка изменения кастомного поля при обновлении записи типа 'projects'
function check_project_field_change($post_id) {
  $post = get_post($post_id);
  error_log('check_project_field_change ' . var_export($post_id, true));
  
  if ($post && $post->post_type == 'projects') {
      $old_value = get_field('investory_investors', $post_id);
      $status = get_field('status', $post_id);
      if ($status['value'] != 1) return;
      
      $data = $_POST['acf'];
      $eventSum = 0;
      $projectSum = get_field('settings_project_sum', $post_id);
      if ($projectSum != $data['field_65e390383e640']['field_65e390623e641']) { // Новая сумма проекта
        $eventSum = $data['field_65e390383e640']['field_65e390623e641'] - $projectSum;
      } 

      $investors = $data['field_65e391123e644']['field_65e391333e645'];

      $n = 0;
      $eventID = NULL;
      $arrChanges = [];
      $eventCreated = $hasChange = false;
      foreach($investors as $investor) {
        $investorID = $investor['field_65e391643e646'];
        $invest = (float) $investor['field_65e391993e647'];
        $invest_over = (float) $investor['field_65e391c33e648'];

        if ((float) $old_value[$n]['invest'] != $invest) {
          $hasChange = true;
          $arrChanges[] = [
            'investor' => $investorID,
            'sum' => $invest - (float) $old_value[$n]['invest'],
            'event' => 7 // 7 : Изменение вложений администратором
          ];
        }

        if ((float) $old_value[$n]['invest_over'] !== $invest_over) {
          $hasChange = true;
          $arrChanges[] = [
            'investor' => $investorID,
            'sum' => $invest_over - (float) $old_value[$n]['invest_over'],
            'event' => 8 // 8 : Изменение вложений (сверх) администратором
          ];
        }
        
        $n++;
      }

      if ($hasChange && !$eventCreated) {
        $eventID = create_project_event($post_id, $eventSum, 2); // 2 : Изменение проекта
        $eventCreated = $eventID > 0;
      }

      if (!empty($arrChanges) && $eventCreated && $eventID > 0) {
        foreach($arrChanges as $ch) {
          // error_log(print_r($ch, true));
          create_transaction($post_id, $ch['investor'], $eventID, $ch['sum'], $ch['event']);
          // Возможно тут надо обновлять значения в портфеле
          $money = get_field('money', 'user_' . $ch['investor']);
          $contributed = get_field('contributed', 'user_' . $ch['investor']);
          $overdep = get_field('overdep', 'user_' . $ch['investor']);
          $refund = get_field('refund', 'user_' . $ch['investor']);
          $refund_over = get_field('refund_over', 'user_' . $ch['investor']);
          
          if ($ch['sum'] > 0) { // Вложение денег в проект
            if ($ch['event'] == 7) { // Вложение
              update_field('money', $money - $ch['sum'], 'user_' . $ch['investor']);
              update_field('contributed', $contributed + $ch['sum'], 'user_' . $ch['investor']);
            } else if ($ch['event'] == 8) { // Вложение сверх
              update_field('overdep', $overdep + $ch['sum'], 'user_' . $ch['investor']);
            }
          } else { // Возврат денег
            $sum = abs($ch['sum']);
            if ($ch['event'] == 7) { // Вложение
              update_field('money', $money + $sum, 'user_' . $ch['investor']);
              if ($contributed >= $sum) {
                update_field('contributed', $contributed - $sum, 'user_' . $ch['investor']);
              }
            } else if ($ch['event'] == 8) { // Вложение сверх
              if ($overdep >= $sum) {
                update_field('overdep', $overdep - $sum, 'user_' . $ch['investor']);
              }
            }
          }
        }
      }
  }
}

function update_project_managers() {

}

// Добавляем хук на событие сохранения поста
add_action('acf/save_post', 'check_project_field_change', 5);

function update_project_manager_roles($value, $post_id) {
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $value;
  }
  
  $post = get_post($post_id);
  
  if (str_contains($post_id, 'user_') || ($post && $post->post_type !== 'projects')) {
    return $value;
  }
  
  $current_status = get_post_meta($post_id, 'status', true);
  
  if ($current_status != '1') {
    return $value;
  }

  update_projects_manager_role();

  return $value;
}

add_action('acf/update_value/name=status', 'update_project_manager_roles', 20, 2);

// Запрет редактирования полей инвестирования и параметров проекта для роли project_manager
function restrict_pm_readonly_fields($field) {
  $user = wp_get_current_user();
  if (in_array('project_manager', (array) $user->roles)) {
    $field['readonly'] = 1;
  }
  return $field;
}
// Инвестирование / Инвестирование сверх
add_filter('acf/prepare_field/key=field_65e391993e647', 'restrict_pm_readonly_fields');
add_filter('acf/prepare_field/key=field_65e391c33e648', 'restrict_pm_readonly_fields');
// Сумма проекта / Доходность
add_filter('acf/prepare_field/key=field_65e390623e641', 'restrict_pm_readonly_fields');
add_filter('acf/prepare_field/key=field_65e390b43e642', 'restrict_pm_readonly_fields');

// Восстановление оригинальных значений при сохранении project_manager-ом
function prevent_pm_fields_change($post_id) {
  $post = get_post($post_id);
  if (!$post || $post->post_type !== 'projects') return;

  $user = wp_get_current_user();
  if (!in_array('project_manager', (array) $user->roles)) return;

  // Восстановление invest / invest_over
  $old_investors = get_field('investory_investors', $post_id);
  if (!empty($old_investors) && isset($_POST['acf']['field_65e391123e644']['field_65e391333e645'])) {
    foreach ($old_investors as $n => $old_investor) {
      if (isset($_POST['acf']['field_65e391123e644']['field_65e391333e645'][$n])) {
        $_POST['acf']['field_65e391123e644']['field_65e391333e645'][$n]['field_65e391993e647'] = $old_investor['invest'];
        $_POST['acf']['field_65e391123e644']['field_65e391333e645'][$n]['field_65e391c33e648'] = $old_investor['invest_over'];
      }
    }
  }

  // Восстановление суммы проекта и доходности
  if (isset($_POST['acf']['field_65e390383e640'])) {
    $_POST['acf']['field_65e390383e640']['field_65e390623e641'] = get_post_meta($post_id, 'settings_project_sum', true);
    $_POST['acf']['field_65e390383e640']['field_65e390b43e642'] = get_post_meta($post_id, 'settings_project_profit', true);
  }
}
add_action('acf/save_post', 'prevent_pm_fields_change', 1);