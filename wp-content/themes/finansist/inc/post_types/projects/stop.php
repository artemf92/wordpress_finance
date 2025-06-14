<? 
add_action( 'wp_ajax_project_stop', 'project_stop_callback' );
// add_action( 'wp_ajax_nopriv_project_stop', 'project_stop_callback' );
function project_stop_callback() {
	$project_id = $_REQUEST['project_id'];
  
  if (!current_user_can('administrator') && !current_user_can('manager')  && !isProjectManager($project_id, wp_get_current_user()->ID)) return;

  $settings = get_field('settings_project', $project_id);
  $investors = get_field('investory_investors', $project_id);

  // Создаем событие "Закрытие проекта"
  $event_id = create_project_event($project_id, $settings['sum'], 5); 
  if (!$event_id) {
    echo '<h3>Завершение проекта</h3>';
    echo 'Что-то пошло не так..';
    wp_die();
  }

  // Установливаем сумму проекта - 0
  update_field('settings_project', ['sum' => 0], $project_id); // 
  
  // Проводим транзакции "Возврат инвестиций в проект" для всех инвесторов в соотв. с их вложенями
  foreach($investors as $key => $investor) {
    $userID = $investor['investor'];
    $invest = $investor['invest'];
    $invest_over = $investor['invest_over'];
    $contributed = get_field('contributed', 'user_' . $userID);
    $overdep = get_field('overdep', 'user_' . $userID);
    $refund = get_field('refund', 'user_' . $userID);
    $refund_over = get_field('refund_over', 'user_' . $userID);

    if ($invest > 0) {
      create_transaction($project_id, $userID, $event_id, $invest, 3); // Возврат инвестиций по проекту
      update_field('contributed', $contributed - $invest , 'user_' . $userID);
      update_field('refund', $refund + $invest , 'user_' . $userID);

      update_post_meta($project_id, 'investory_investors_' . $key . '_invest', '0.00');
    }

    if ($invest_over > 0) {
      create_transaction($project_id, $userID, $event_id, $invest_over, 6); // Возврат инвестиций по проекту (сверх)
      update_field('overdep', $overdep - $invest_over , 'user_' . $userID);
      update_field('refund_over', $refund_over + $invest_over , 'user_' . $userID);

      update_post_meta($project_id, 'investory_investors_' . $key . '_invest_over', '0.00');
    }
  }

  // error_log('project_stop_callback - project ID ' . $project_id);
  
  // перевести статус в завершенный
  update_field('status', 5, $project_id);
  $status = get_field('status', $project_id);
  // error_log('new status ' . var_export($status, true));
  
  echo '<h3>Завершение проекта</h3>';
  echo '<p>Проект завершен!</p>';
  echo '<input type="hidden" name="result" value="'.$status['label'].'" />';

	wp_die();
}

add_action( 'wp_ajax_project_stop_prepare', 'project_stop_callback_prepare' );
function project_stop_callback_prepare() {
  $project_id = $_REQUEST['project_id'];

  if (!current_user_can('administrator') && !current_user_can('manager')  && !isProjectManager($project_id, wp_get_current_user()->ID)) return;

  echo '<h3>Завершение проекта</h3>';
  echo '<p>Завершить проект с убытком?</p>';
  echo '<p>При завершении проекта все вложенные средства будут возвращены инвесторам.</p>';
  echo '<div class="d-flex form-group gap-3 justify-content-center">';
  echo '  <button data-dialog-type="modal" id="project_stop" data-project="'.$project_id.'" class="btn">Нет</button>';
  echo '  <button data-dialog-type="modal" id="project_stop_with_loss" data-project="'.$project_id.'" class="btn btn-danger" onclick="jQuery(this).closest(\'form\').find(\'input[name=loss_project]\').val(1);">Да, с убытком</button>';
  echo '</div>';

  wp_die();

  // $settings
}

add_action( 'wp_ajax_project_stop_with_loss', 'project_stop_callback_with_loss' );
function project_stop_callback_with_loss() {
  $project_id = $_REQUEST['project_id'];

  if (!current_user_can('administrator') && !current_user_can('manager')  && !isProjectManager($project_id, wp_get_current_user()->ID)) return;

  // Создаем событие "Закрытие проекта"
  $event_id = create_project_event($project_id, 0, 5);
  if (!$event_id) {
    echo '<h3>Завершение проекта</h3>';
    echo 'Что-то пошло не так..';
    wp_die();
  }

  // Установливаем сумму проекта - 0
  update_field('settings_project', ['sum' => 0], $project_id); //

  // Проводим транзакции "Возврат инвестиций в проект" для всех инвесторов в соотв. с их вложенями
  $investors = get_field('investory_investors', $project_id);
  foreach($investors as $key => $investor) {
    $userID = $investor['investor'];
    $invest = $investor['invest'];
    $invest_over = $investor['invest_over'];
    $contributed = get_field('contributed', 'user_' . $userID);
    $overdep = get_field('overdep', 'user_' . $userID);

    if ($invest > 0) {
      create_transaction($project_id, $userID, $event_id, $invest, 13); // Убыток по проекту
      update_field('contributed', $contributed - $invest , 'user_' . $userID);

      update_post_meta($project_id, 'investory_investors_' . $key . '_invest', '0.00');
    }

    if ($invest_over > 0) {
      create_transaction($project_id, $userID, $event_id, $invest_over, 13); // Убыток по проекту
      update_field('overdep', $overdep - $invest_over , 'user_' . $userID);

      update_post_meta($project_id, 'investory_investors_' . $key . '_invest_over', '0.00');
    }
  }

  // перевести статус в завершенный
  update_field('status', 5, $project_id);
  $status = get_field('status', $project_id);
  
  echo '<h3 class="pt-4">Завершение проекта</h3>';
  echo '<p>Проект завершен с убытком!</p>';
  echo '<input type="hidden" name="result" value="'.$status['label'].'" />';

  wp_die();
}
?>