<? 
add_action( 'wp_ajax_project_start', 'project_start_callback' );
// add_action( 'wp_ajax_nopriv_project_start', 'project_start_callback' );
function project_start_callback() {
  if (!current_user_can('administrator') && !current_user_can('manager') ) return;

	$project_id = $_REQUEST['project_id'];
  $status = get_field('status', $project_id);

  if ($status['value'] > 0) {
    echo '<h3>Ошибка запуска проекта</h3>';
    echo '<p>Проект уже запущен!</p>';  
    echo '<input type="hidden" name="result" value="'.$status['label'].'" />';
  } else {
    // Изменяем статус проекта
    update_field('status', 1, $project_id);

    $sum = get_field('settings_project_sum', $project_id); // Сумма проекта

    $event_id = create_project_event($project_id, $sum, 1);

    // Создание транзакции у инвесторов «Запуск»
    $investors = get_field('investory_investors', $project_id);

    foreach($investors as $i => $investor) {
      $user = $investor['investor'];
      $invest = $investor['invest'];
      $invest_over = $investor['invest_over'];

      if ($invest > 0) {
        $transaction_id = create_transaction( $project_id, $user, $event_id, $invest, 1);
        if (is_array($transaction_id)) {
          echo $transaction_id['error'];
          wp_die();
        }
        withdraw_money($user, $invest);
      }

      if ($invest_over > 0) {
        $transaction_id = create_transaction($project_id, $user, $event_id, $invest_over, 2);
        if (is_array($transaction_id)) {
          echo $transaction_id['error'];
          wp_die();
        }
        withdraw_money($user, $invest_over, true);
      }

    }
    
    echo '<h3>Запуск проекта</h3>';
    echo '<p>Проект успешно запущен!</p>';
    echo '<input type="hidden" name="result" value="'.$status['label'].'" />';
  }

	wp_die();
}

?>