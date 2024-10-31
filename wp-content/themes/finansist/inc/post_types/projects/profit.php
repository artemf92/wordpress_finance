<?
add_action( 'wp_ajax_project_profit', 'project_profit_prepare' );
// add_action( 'wp_ajax_nopriv_project_profit', 'project_profit_prepare' );

function project_profit_prepare() {
  $project_id = $_REQUEST['project_id'];

  if (!current_user_can('administrator') && !current_user_can('manager') && !isProjectManager($project_id, wp_get_current_user()->ID) ) return;

  $html = '';
  $html .= '<h3>'.__('Форма начисления дохода для проекта').'</h3>';
  $html .= '<h4>'.__('Тип транзакции').'</h4>';

  $html .= '<form class="project_profit_prepare">';
  $html .= '  <input type="hidden" name="project_id" value="'.$project_id.'" />';
  $html .= '  <div class="input-group">';
  $html .= '    <label class="input-group-text" for="input_refund">';
  $html .= '      <input class="form-check-input" id="input_refund" name="transaction_type" type="radio" value="3" aria-label="'.__('Возврат инвестиций').'">';
  $html .= '      <span class="">'.__('Возврат инвестиций').'</span>';
  $html .= '    </label>';
  $html .= '  </div>';
  $html .= '  <div class="input-group">';
  $html .= '    <label class="input-group-text" for="input_profit">';
  $html .= '      <input class="form-check-input" id="input_profit" name="transaction_type" type="radio" value="4" aria-label="'.__('Доход').'" checked>';
  $html .= '      <span class="">'.__('Доход').'</span>';
  $html .= '    </label>';
  $html .= '  </div>';
  $html .= '  <br/>';
  $html .= '  <div class="input-group m-b-3">';
  $html .= '    <label class="input-group-text" form="input_auto">';
  $html .= '      <input class="form-check-input" name="auto" id="input_auto" type="checkbox" aria-label="'.__('Автоматический расчет').'"/>';
  $html .= '      <span class="">'.__('Автоматический расчет').'</span>';
  $html .= '    </label>';
  $html .= '  </div>';
  $html .= '  <div class="investors_profit">';
  $investors = get_field('investory_investors', $project_id);
  foreach($investors as $key => $investor) {
    $user = get_userdata($investor['investor']);

    $html .= '<fieldset class="m-b-2">';
    $html .= '  <legend>'.__('Инвестор'). ' - '. $user->user_firstname . ' ' . $user->user_lastname.'</legend>';
    $html .= '    <div class="">';
    $html .= '      <input type="number" onwheel="return false;" step="any" name="profit[user]['.$key.']" class="form-control" id="profit_user_'.$key.'" value="0">';
    $html .= '    </div>';
    $html .= '</fieldset>';
  }
  $html .= '</div>';
  $html .= '  <div class="investors_refund" style="display:none;">'; // style="display:none;"
  $investors = get_field('investory_investors', $project_id);
  foreach($investors as $key => $investor) {
    $user = get_userdata($investor['investor']);
    $invest = get_post_meta($project_id, 'investory_investors_'.$key.'_invest', true);
    $invest_over = get_post_meta($project_id, 'investory_investors_'.$key.'_invest_over', true);
    $class = $invest > 0 && $invest_over > 0 ? 'col-sm-6':'col-sm-12';

    $html .= '<fieldset class="m-b-2">';
    $html .= '  <legend>'.__('Инвестор'). ' - '. $user->user_firstname . ' ' . $user->user_lastname.'</legend>';
    $html .= '  <div class="row">';
    if ($invest > 0) {
      $html .= '    <div class="'.$class.'">';
      $html .= '      <label for="profit_user_'.$key.'_over" class="form-label">Портфель</label>';
      $html .= '      <input type="number" onwheel="return false;" step="any" name="refund[user]['.$key.'][portfel]" class="form-control" id="user_'.$key.'_portfel" value="0">';
      $html .= '    </div>';
    }
    if  ($invest_over > 0) {
      $html .= '    <div class="'.$class.'">';
      $html .= '      <label for="profit_user_'.$key.'_over" class="form-label">Сверх</label>';
      $html .= '      <input type="number" onwheel="return false;" step="any" name="refund[user]['.$key.'][over]" class="form-control" id="user_'.$key.'_over" value="0">';
      $html .= '    </div>';
    }
    $html .= '  </div>';
    $html .= '</fieldset>';
  }
  $html .= '  </div>';
  $html .= '  <div class="form__amount m-b-2" style="display:none;">';
  $html .= '    <label for="summa" class="form-label">'.__('Сумма').'</label>';
  $html .= '    <input type="number" onwheel="return false;" step="any" class="form-control" name="summa" id="summa" value="0">';
  $html .= '  </div>';
  $html .= '  <div class="project_profit_prepare__actions">';
  $html .= '    <button class="btn btn-primary m-r-2" type="submit">'.__('Доход').'</button>';
  $html .= '    <button class="btn btn-warning" type="button" data-fancybox-close>'.__('Закрыть').'</button>';
  $html .= '  </div></form>';

  echo $html;
  wp_die();
}

add_action( 'wp_ajax_project_profit_final', 'project_profit_callback' );
// add_action( 'wp_ajax_nopriv_project_profit_final', 'project_profit_callback' );

function project_profit_callback() {
  $project_id = $_REQUEST['project_id'];
  
  if (!current_user_can('administrator') && !current_user_can('manager')  && !isProjectManager($project_id, wp_get_current_user()->ID)) return;

  $type = $_REQUEST['transaction_type'];
  $tmpUsers = $type == 4 ? $_REQUEST['profit']['user']:$_REQUEST['refund']['user'];
  $auto = isset($_REQUEST['auto']);
  if ($auto) {
    $users = $tmpUsers;
  } else {
    $users = array_filter($tmpUsers, function($value) {
        return $value !== '0';
    });
  }
  $sums = $_REQUEST['summa'];
  // $percentage = get_field('settings_project_profit', $project_id);
  $html = '';
  $arr = [];
  $investors = get_field('investory_investors', $project_id);
  $investments = [];
  foreach ($investors as $key => $investor) {
    $investments[$key] = [
      'contributed' => $investor['invest'],
      'contributed_over' => $investor['invest_over'],
    ];
  }

  $project_sum = $tmp_project_sum = get_field('settings_project_sum', $project_id);

  if ($auto) {
    if ($type == 4) { //  Доход
      foreach(array_keys($users) as $id) { 
        $userId = get_post_meta($project_id, 'investory_investors_'.$id.'_investor', true);
        $invest = $investments[$id]['contributed'];
        $invest_over = $investments[$id]['contributed_over'];
        $sum1 = $sum2 = 0;
  
        if ($invest > 0) {
          $sum1 = round(($invest * $sums) / $tmp_project_sum, 2);
        }
        if ($invest_over > 0) {
          $sum2 = round(($invest_over * $sums) / $tmp_project_sum, 2);
        }
        $arr[$userId] = [
          'profit' => $sum1,
          'profit_over' => $sum2,
        ];
      }  
    } else if ($type == 3) { // Возврат
      foreach(array_keys($users) as $id) { 
        $userId = get_post_meta($project_id, 'investory_investors_'.$id.'_investor', true);
        $invest = $investments[$id]['contributed'];
        $invest_over = $investments[$id]['contributed_over'];
        $sum1 = $sum2 = 0;

        if ($invest > 0) {
          $sum1 = round(($invest * $sums) / $tmp_project_sum, 2);
        }
        if ($sum1 > $investments[$id]['contributed']) {
          echo sprintf(
            'Возврат для пользователя %s (%d) превышает долг(%d).',
            get_userdata($userId)->user_firstname . ' ' . get_userdata($userId)->user_lastname,
            round($sum1, 2), 
            round($investments[$id]['contributed'], 2)
          );
          wp_die();
        }

        if ($invest_over > 0) {
          $sum2 = round(($invest_over * $sums) / $tmp_project_sum, 2);
        }
        if ($sum2 > $investments[$id]['contributed_over']) {
          echo sprintf(
            'Возврат (сверх) для пользователя %s (%d) превышает долг(%d).',
            get_userdata($userId)->user_firstname . ' ' . get_userdata($userId)->user_lastname,
            round($sum2, 2), 
            round($investments[$id]['contributed_over'], 2)
          );
          wp_die();
        }

        $arr[$userId] = [
          'contributed' => $sum1,
          'contributed_over' => $sum2,
        ];

        $project_sum = $project_sum - $sum1 - $sum2;

        update_field('settings_project_sum', $project_sum, $project_id);
        update_field('investory_investors_'.$id.'_invest', $investments[$id]['contributed'] - $sum1, $project_id);
        update_field('investory_investors_'.$id.'_invest_over', $investments[$id]['contributed_over'] - $sum2, $project_id);
      }  
    }
  } else {
    if ($type == 4) {
      foreach($users as $key => $event_sum) {
        $userId = get_post_meta($project_id, 'investory_investors_'.$key.'_investor', true);
        $invest = floatval($investments[$key]['contributed']);
        $invest_over = floatval($investments[$key]['contributed_over']);

        $sums += $event_sum;
        $arr[$userId] = [
          'profit' => $event_sum,
        ];
      }
    } else if ($type == 3) {
      foreach($users as $key => $event_sum) {
        $userId = get_post_meta($project_id, 'investory_investors_'.$key.'_investor', true);
        $invest = $investments[$key]['contributed'];
        $invest_over = $investments[$key]['contributed_over'];
        $sum1 = $sum2 = 0;
  
        $sum1 = isset($_REQUEST['refund']['user'][$key]['portfel']) ? $_REQUEST['refund']['user'][$key]['portfel'] : 0;
        if ($sum1 > $investments[$key]['contributed']) {
          echo sprintf(
            'Возврат для пользователя %s (%d) превышает долг(%d).',
            get_userdata($userId)->user_firstname . ' ' . get_userdata($userId)->user_lastname,
            round($sum1, 2), 
            round($investments[$key]['contributed'], 2)
          );
          wp_die();
        }
        $sum2 = isset($_REQUEST['refund']['user'][$key]['over']) ? $_REQUEST['refund']['user'][$key]['over'] : 0;
        if ($sum1 > $investments[$key]['contributed']) {
          echo sprintf(
            'Возврат для пользователя %s (%d) превышает долг(%d).',
            get_userdata($userId)->user_firstname . ' ' . get_userdata($userId)->user_lastname,
            round($sum1, 2), 
            round($investments[$key]['contributed'], 2)
          );
          wp_die();
        }
  
        $sums += (isset($event_sum['portfel']) ? $event_sum['portfel']:0) + (isset($event_sum['over'])?$event_sum['over']:0);
        $arr[$userId] = [
          'contributed' => $sum1,
          'contributed_over' => $sum2,
        ];

        $project_sum = $project_sum - $sum1 - $sum2;
        
        update_field('settings_project_sum', $project_sum, $project_id);
        update_field('investory_investors_'.$key.'_invest', $investments[$key]['contributed'] - $sum1, $project_id);
        update_field('investory_investors_'.$key.'_invest_over', $investments[$key]['contributed_over'] - $sum2, $project_id);
      }
    }

  }

  $event_id = create_project_event($project_id, $sums, $type);

  calculateProfit($event_id, $project_id, $arr);

  $html .= '<div class="fonsa">';
  $html .= '  <i class="bg-white border-1 border-primary color-lightgreen fa-4x fa-check fa-solid m-x-3 m-y-3"></i>';
  $html .= '</div>';
  echo $html;
}

function calculateProfit($eventID, $projectID, $users) {
  foreach($users as $userID => $arSum) {
    foreach($arSum as $type => $value) {
      if ($value == 0) continue;
      switch($type) {
        case 'contributed':
          $typeID = 3;
          $contributed = get_field('contributed', 'user_'.$userID);
          $result = update_field('contributed', $contributed - $value, 'user_' . $userID);
          $refund = get_field('refund', 'user_'.$userID);
          $result = update_field('refund', $refund + $value, 'user_' . $userID);
          break;
        case 'contributed_over':
          $typeID = 6;
          $overdep = get_field('overdep', 'user_'.$userID);
          $result = update_field('overdep', $overdep - $value, 'user_' . $userID);
          $refund_over = get_field('refund_over', 'user_'.$userID);
          $result = update_field('refund_over', $refund_over + $value, 'user_' . $userID);
          break;
        case 'profit':
        case 'profit_over':
          $typeID = $type == 'profit' ? 4 : 14;
          $profit = get_field('profit', 'user_'.$userID);
          $result = update_field('profit', $profit + $value, 'user_' . $userID);
          break;
        default:
          $typeID = 4;
          $profit = get_field('profit', 'user_'.$userID);
          $result = update_field('profit', $profit + $value, 'user_' . $userID);
          break;
      }

      if (is_wp_error($result)) {
        echo $result->get_error_message();
        wp_die();
      }

      create_transaction($projectID, $userID, $eventID, $value, $typeID); 

    }
  }
}
?>