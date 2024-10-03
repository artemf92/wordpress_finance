<? 
add_action( 'wp_ajax_project_restart', 'project_restart_callback' );
// add_action( 'wp_ajax_nopriv_project_restart', 'project_restart_callback' );
function project_restart_callback() {
	$project_id = $_REQUEST['project_id'];
  
  if (!current_user_can('administrator') && !current_user_can('manager')  && !isProjectManager($project_id, wp_get_current_user()->ID)) return;
  
  $settings = get_field('settings_project', $project_id);
  $investors = get_field('investory_investors', $project_id);

  // Находим событие закрытия проекта
  $events = get_posts([
    'post_type' => 'events',
     'meta_query' => [
      'relation' => "AND",
      [
        'key' => 'settings_project',
        'value' => $project_id,
      ],
      [
        'key' => 'settings_transaction_type',
        'value' => 5,
      ],
     ]
  ]);
  $event = $events[0];
  if (!$event) {
    echo '<h3>Возобновление проекта</h3>
    <p>Что-то пошло не так..</p>';
    wp_die();
  }

  $event_id = $event->ID;
  $sum = get_field('settings_sum', $event_id);


  wp_trash_post($event_id);

  // Установливаем сумму проекта
  update_field('settings_project', ['sum' => $sum], $project_id); // 
  
  // Проводим транзакции "Возврат инвестиций в проект" для всех инвесторов в соотв. с их вложенями
  foreach($investors as $key => $investor) {
    $userID = $investor['investor'];
    $invest = $investor['invest'];
    $invest_over = $investor['invest_over'];
    $contributed = get_field('contributed', 'user_' . $userID);
    $overdep = get_field('overdep', 'user_' . $userID);
    $refund = get_field('refund', 'user_' . $userID);
    $refund_over = get_field('refund_over', 'user_' . $userID);

    $transactions = get_posts([
      'post_type' => 'transactions',
      'meta_query' => [
        'relation' => 'AND',
        [
          'key' => 'settings_project',
          'value' => $project_id,
        ],
        [
          'key' => 'settings_transaction_type',
          'value' => [3,6],
          'compare' => 'IN',
        ],
      ]
    ]);
    foreach($transactions as $tr) {
      wp_trash_post($tr->ID);
    }

    if ($invest > 0) {
      // create_transaction($project_id, $userID, $event_id, $invest, 3); // Возврат инвестиций по проекту
      update_field('contributed', $contributed - $invest , 'user_' . $userID);
      update_field('refund', $refund + $invest , 'user_' . $userID);
    }

    if ($invest_over > 0) {
      // create_transaction($project_id, $userID, $event_id, $invest_over, 6); // Возврат инвестиций по проекту (сверх)
      update_field('overdep', $overdep - $invest_over , 'user_' . $userID);
      update_field('refund_over', $refund_over + $invest_over , 'user_' . $userID);
    }
  }
  
  // перевести статус в активный
  update_field('status', 1, $project_id);
  
  $status = get_field('status', $project_id);
  
  echo '<h3>Возобновление проекта</h3>';
  echo '<p>Проект возобновлен!</p>';
  echo '<input type="hidden" name="result" value="'.$status['label'].'" />';

	wp_die();
}
?>