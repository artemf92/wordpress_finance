<?php
add_action( 'wp_ajax_project_stop_partial_prepare', 'project_stop_callback_partial_prepare' );
function project_stop_callback_partial_prepare() {
  $project_id = $_REQUEST['project_id'];

  if (!current_user_can('administrator') && !current_user_can('manager') && !isProjectManager($project_id, wp_get_current_user()->ID) ) return;

  $html = '';
  $html .= '<h3>'.__('Форма начисления убытка для проекта').'</h3>';
  $html .= '<form class="project_loss_prepare">';
  $html .= '  <input type="hidden" name="project_id" value="'.$project_id.'" />';
  $html .= '  <div class="input-group m-b-3">';
  $html .= '    <label class="input-group-text" form="input_auto">';
  $html .= '      <input class="form-check-input" name="auto" id="input_auto_loss" type="checkbox" aria-label="'.__('Автоматический расчет').'"/>';
  $html .= '      <span class="">'.__('Автоматический расчет').'</span>';
  $html .= '    </label>';
  $html .= '  </div>';
  $html .= '  <div class="investors_loss">';
  $investors = get_field('investory_investors', $project_id);
  foreach($investors as $key => $investor) {
    $user = get_userdata($investor['investor']);

    $html .= '<fieldset class="m-b-2">';
    $html .= '  <legend>'.__('Инвестор'). ' - '. $user->user_firstname . ' ' . $user->user_lastname.'</legend>';
    if ($investor['invest'] > 0) {
    $html .= '    <div class="mb-2">';
    $html .= '      <label for="loss_user_'.$key.'" class="form-label">'.__('Убыток').'</label>';
    $html .= '      <input type="number" onwheel="return false;" step="any" name="loss[user]['.$key.']" class="form-control" id="loss_user_'.$key.'" value="0" placeholder="'.__('Убыток').'">';
    $html .= '    </div>';
    }
    if ($investor['invest_over'] > 0) {
    $html .= '    <div class="mb-2">';
    $html .= '      <label for="loss_over_user_'.$key.'" class="form-label">'.__('Убыток (сверх)').'</label>';
    $html .= '      <input type="number" onwheel="return false;" step="any" name="loss_over[user]['.$key.']" class="form-control" id="loss_over_user_'.$key.'" value="0" placeholder="'.__('Убыток (сверх)').'">';
    $html .= '    </div>';
    }
    $html .= '</fieldset>';
  }
  $html .= '</div>';
  $html .= '  <div class="form__amount m-b-2" style="display:none;">';
  $html .= '    <label for="summa" class="form-label">'.__('Сумма').'</label>';
  $html .= '    <input type="number" onwheel="return false;" step="any" class="form-control" name="loss_project" id="summa" value="0">';
  $html .= '  </div>';
  $html .= '  <div class="project_loss_prepare__actions">';
  $html .= '    <button class="btn btn-primary m-r-2" type="submit">'.__('Убыток').'</button>';
  $html .= '    <button class="btn btn-warning" type="button" data-fancybox-close>'.__('Закрыть').'</button>';
  $html .= '  </div></form>';

  echo $html;
  wp_die();
}

add_action( 'wp_ajax_project_stop_partial', 'project_stop_callback_partial' );
function project_stop_callback_partial() {
  $project_id = $_REQUEST['project_id'];

  if (!current_user_can('administrator') && !current_user_can('manager')  && !isProjectManager($project_id, wp_get_current_user()->ID)) return;

  $loss_project = floatval($_REQUEST['loss_project']);
  $auto = isset($_REQUEST['auto']);
  $tmpUsers = $_REQUEST['loss']['user'];
  $tmpUsersOver = $_REQUEST['loss_over']['user'];
  
  if ($auto) {
    $tmpInvestors = get_field('investory_investors', $project_id);
    foreach($tmpInvestors as $key => $investor) {
      $users[$key] = $investor['investor'];
    }
  } else {
    $users = array_filter($tmpUsers, function($value) {
        return $value !== '0';
    });
    $usersOver = array_filter($tmpUsersOver, function($value) {
        return $value !== '0';
    });
  }
  
  $loss_project = $auto ? $loss_project : floatval(array_sum(array_values($users))) + floatval(array_sum(array_values($usersOver)));

  // Создаем событие "Убыток по проекту"
  $event_id = create_project_event($project_id, $loss_project, 6);
  $projectSum = floatval(get_field('settings_project_sum', $project_id));
  if (!$event_id || !$loss_project || $loss_project > $projectSum) {
    echo '<h3>Убыток по проекту</h3>';
    echo '<p>Что-то пошло не так..</p>';
    wp_die();
  }
  // Установливаем сумму проекта - убыток
  update_field('settings_project', ['sum' => $projectSum - $loss_project], $project_id); //

  $investors = get_field('investory_investors', $project_id);
  $investments = [];
  foreach ($investors as $key => $investor) {
    $investments[$key] = [
      'invest' => $investor['invest'],
      'invest_over' => $investor['invest_over'],
    ];
  }

  if ($auto) {
    foreach(array_keys($users) as $id) {
      $userID = get_post_meta($project_id, 'investory_investors_'.$id.'_investor', true);
      $invest = floatval($investments[$id]['invest']);
      $investPercent = ($invest * 100 / $projectSum) * 1000 / 1000;
      $invest_over = floatval($investments[$id]['invest_over']);
      $invest_overPercent = ($invest_over * 100 / $projectSum) * 1000 / 1000;
      $contributed = floatval(get_field('contributed', 'user_' . $userID));
      $overdep = floatval(get_field('overdep', 'user_' . $userID));
      
      if ($invest > 0) {
        $event_sum = round($investPercent * $loss_project / 100, 2);
        
        create_transaction($project_id, $userID, $event_id, $event_sum, 13); // Убыток по проекту
        update_field('contributed', $contributed - $event_sum, 'user_' . $userID);
        
        update_post_meta($project_id, 'investory_investors_' . $id . '_invest', $invest - $event_sum);
      }

      if ($invest_over > 0) {
        $event_sum = round($invest_overPercent * $loss_project / 100, 2);
        
        create_transaction($project_id, $userID, $event_id, $event_sum, 15); // Убыток по проекту
        update_field('overdep', $overdep - $event_sum, 'user_' . $userID);

        update_post_meta($project_id, 'investory_investors_' . $id . '_invest_over', $invest_over - $event_sum);
      }
    }
  } else {
    foreach($users as $key => $event_sum) {
      $userID = get_post_meta($project_id, 'investory_investors_'.$key.'_investor', true);
      $invest = floatval($investments[$key]['invest']);
      $contributed = floatval(get_field('contributed', 'user_' . $userID));

      if ($invest > 0) {
        create_transaction($project_id, $userID, $event_id, $event_sum, 13); // Убыток по проекту
        update_field('contributed', $contributed - $event_sum, 'user_' . $userID);
        
        update_post_meta($project_id, 'investory_investors_' . $key . '_invest', $invest - $event_sum);
      }

    }
    foreach($usersOver as $key => $event_sum) {
      $userID = get_post_meta($project_id, 'investory_investors_'.$key.'_investor', true);
      $invest_over = floatval($investments[$key]['invest_over']);
      $overdep = floatval(get_field('overdep', 'user_' . $userID));
      
      if ($invest_over > 0) {
        create_transaction($project_id, $userID, $event_id, $event_sum, 15); // Убыток по проекту
        update_field('overdep', $overdep - $event_sum, 'user_' . $userID);
  
        update_post_meta($project_id, 'investory_investors_' . $key . '_invest_over', $invest_over - $event_sum);
      }
    }
  }

  $status = get_field('status', $project_id);
  
  echo '<h3 class="pt-4">Убыток по проекту</h3>';
  echo '<p>Убыток по проекту проведен!</p>';
  echo '<input type="hidden" name="result" value="'.$status['label'].'" />';
  echo '<input type="hidden" name="project_sum" value="'.get_formatted_number(floatval($projectSum - $loss_project)).'" />';

  wp_die();
}
?>