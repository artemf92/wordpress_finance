<? 
add_action( 'pre_trash_post', 'before_delete_transaction_action', 10, 3 );

/**
 * Function for `before_delete_post` action-hook.
 * 
 * @param bool|null $trash           Whether to go forward with trashing.
 * @param WP_Post   $post            Post object.
 * @param string    $previous_status The status of the post about to be trashed.
 *
 * @return void
 */
function before_delete_transaction_action( $trash, $post, $previous_status ){

	$postid = $post->ID;

	if( ! $post || $post->post_type !== 'transactions' ) 
		return;

  deleteTransactionHandler($postid);
}
add_action( 'pre_untrash_post', 'before_untrash_transaction_action', 10, 3 );

/**
 * Function for `before_delete_post` action-hook.
 * 
 * @param bool|null $untrash         Whether to go forward with untrashing.
 * @param WP_Post   $post            Post object.
 * @param string    $previous_status The status of the post at the point where it was trashed.
 *
 * @return void
 */
function before_untrash_transaction_action( $untrash, $post, $previous_status ){
  $postid = $post->ID;

	if( ! $post || $post->post_type !== 'transactions' ) 
		return;

  restoreTransactionHandler($postid);
}

function deleteTransactionHandler($postID) {
  $projectID = get_post_meta($postID, 'settings_project', true);
  // $settingsProject = get_field('settings_project', $projectID);
  $investorID = get_post_meta($postID, 'settings_investor', true);
  $sum = get_post_meta($postID, 'settings_sum', true);;
  $absSum = abs($sum);
  $transactionType = get_post_meta($postID, 'settings_transaction_type', true);

  $money = get_field('money', 'user_' . $investorID);
  $contributed = get_field('contributed', 'user_' . $investorID);
  $overdep = get_field('overdep', 'user_' . $investorID);
  $refund = get_field('refund', 'user_' . $investorID);
  $refund_over = get_field('refund_over', 'user_' . $investorID);
  $profit = get_field('profit', 'user_' . $investorID);

  // Если событие 
  // 1 или 7 : Вложение в проект - портфель (money) и вложения (contributed) изменить на заданную сумму, 
  // 2 или 8 : Вложение в проект (сверх) - портфель (money) и вложения сверх (overdep) изменить на заданную сумму, 
  // 3  : Возврат инвестиций по проекту - вложено из портфеля (contributed) и Возврат инвестиций (портфель) (refund) изменяем на заданную сумму
  // 6  : Возврат инвестиций по проекту (сверх) - вложено сверх (overdep) и Возврат инвестиций (сверх) (refund_over) изменяем на заданную сумму
  // 4  : Доход по проекту - доход (profit) изменяем на заданную сумму
  // 5  : Выход из проекта - ???
  // 7  : Изменение вложений администратором
  // 8  : Изменение вложений (сверх) администратором
  // 9  : Доход от инвестиций - доход (profit) изменяем на нужную сумму
  // 10 : Выдача денег (в портфель) - возврат инвестиий (refund) и портфель изменяем на заданную сумму
  // 11 : Выдача денег (сверх) - возврат инвестиий (сверх) (refund_over) и портфель изменяем на заданную сумму
  // 12 : Изменение портфеля - портфель изменяем на заданную сумму
  // 13 : Убыток по проекту - ???
  // 14 : Доход по проекту (сверх) - доход (profit) изменяем на заданную сумму

  switch($transactionType) {
    case '1':
      update_field('money', $money + $sum, 'user_'.$investorID);
      update_field('contributed', $contributed - $sum, 'user_'.$investorID);
      break;
    case '2':
      update_field('money', $money + $sum, 'user_'.$investorID);
      update_field('overdep', $overdep - $sum, 'user_'.$investorID);
      break;
    case '3':
      // ?
      update_field('contributed', $contributed + $sum, 'user_'.$investorID);
      update_field('refund', $refund - $sum, 'user_'.$investorID);

      $projectSum = get_field('settings_project_sum', $projectID);
      $projectInvestors = get_field('investory_investors', $projectID);
      update_field('settings_project', ['sum' => $projectSum + $sum], $projectID);

      foreach($projectInvestors as $key => $inv) {
        if ($inv['investor'] != $investorID) continue;
        $tmpSum = get_post_meta($projectID, 'investory_investors_'.$key.'_invest', true);
        update_post_meta($projectID, 'investory_investors_'.$key.'_invest', $tmpSum + $sum);
      }

      break;
    case '4':
    case '9':
    case '14':
      update_field('profit', $profit - $sum, 'user_'.$investorID);
      break;
    case '6':
      // ?
      update_field('overdep', $overdep + $sum, 'user_'.$investorID);
      update_field('refund_over', $refund_over - $sum, 'user_'.$investorID);

      $projectSum = get_field('settings_project_sum', $projectID);
      $projectInvestors = get_field('investory_investors', $projectID);
      update_field('settings_project', ['sum' => $projectSum + $sum], $projectID);

      foreach($projectInvestors as $key => $inv) {
        if ($inv['investor'] != $investorID) continue;
        $tmpSum = get_post_meta($projectID, 'investory_investors_'.$key.'_invest_over', true);
        update_post_meta($projectID, 'investory_investors_'.$key.'_invest_over', $tmpSum + $sum);
      }

      break;
    case '7':
      update_field('money', $money + $sum, 'user_'.$investorID);
      update_field('contributed', $contributed - $sum, 'user_'.$investorID);

      $projectSum = get_field('settings_project_sum', $projectID);
      $projectInvestors = get_field('investory_investors', $projectID);
      update_field('settings_project', ['sum' => $projectSum - $sum], $projectID);

      foreach($projectInvestors as $key => $inv) {
        if ($inv['investor'] != $investorID) continue;
        $tmpSum = get_post_meta($projectID, 'investory_investors_'.$key.'_invest', true);
        update_post_meta($projectID, 'investory_investors_'.$key.'_invest', $tmpSum - $sum);
      }

      break;
    case '8':
      update_field('money', $money + $sum, 'user_'.$investorID);
      update_field('overdep', $overdep - $sum, 'user_'.$investorID);

      $projectSum = get_field('settings_project_sum', $projectID);
      $projectInvestors = get_field('investory_investors', $projectID);
      update_field('settings_project', ['sum' => $projectSum - $sum], $projectID);

      foreach($projectInvestors as $key => $inv) {
        if ($inv['investor'] != $investorID) continue;
        $tmpSum = get_post_meta($projectID, 'investory_investors_'.$key.'_invest_over', true);
        update_post_meta($projectID, 'investory_investors_'.$key.'_invest_over', $tmpSum - $sum);
      }

      break;
    case '10':
      update_field('money', $money - $sum, 'user_'.$investorID);
      update_field('refund', $refund + $sum, 'user_'.$investorID);
      break;
    case '11':
      update_field('money', $money - $sum, 'user_'.$investorID);
      update_field('refund_over', $refund_over + $sum, 'user_'.$investorID);
      break;
    case '12':
      update_field('money', $money + $sum, 'user_'.$investorID);
      break;
  }
}

function restoreTransactionHandler($postID) {
  $projectID = get_post_meta($postID, 'settings_project', true);
  // $settingsProject = get_field('settings_project', $projectID);
  // error_log(var_export($settings, true));
  $investorID = get_post_meta($postID, 'settings_investor', true);
  $sum = intval(get_post_meta($postID, 'settings_sum', true));
  $absSum = abs($sum);
  $transactionType = get_post_meta($postID, 'settings_transaction_type', true);

  $money = get_field('money', 'user_' . $investorID);
  $contributed = get_field('contributed', 'user_' . $investorID);
  $overdep = get_field('overdep', 'user_' . $investorID);
  $refund = get_field('refund', 'user_' . $investorID);
  $refund_over = get_field('refund_over', 'user_' . $investorID);
  $profit = get_field('profit', 'user_' . $investorID);

  $result = [
    'money' => $money,
    'contributed' => $contributed,
    'overdep' => $overdep,
    'refund' => $refund,
    'refund_over' => $refund_over,
    'profit' => $profit,
  ];

  switch($transactionType) {
    case '1':
      update_field('money', $money - $sum, 'user_'.$investorID);
      update_field('contributed', $contributed + $sum, 'user_'.$investorID);
      break;
    case '2':
      update_field('money', $money - $sum, 'user_'.$investorID);
      update_field('overdep', $overdep + $sum, 'user_'.$investorID);
      break;
    case '3':
      // ?
      update_field('contributed', $contributed - $sum, 'user_'.$investorID);
      update_field('refund', $refund + $sum, 'user_'.$investorID);
      break;
    case '4':
    case '9':
    case '14':
      update_field('profit', $profit + $sum, 'user_'.$investorID);
      break;
    case '6':
      // ?
      update_field('overdep', $overdep - $sum, 'user_'.$investorID);
      update_field('refund_over', $refund_over + $sum, 'user_'.$investorID);
      break;
    case '7':
      update_field('money', $money - $sum, 'user_'.$investorID);
      update_field('contributed', $contributed + $sum, 'user_'.$investorID);

      $projectSum = get_field('settings_project_sum', $projectID);
      $projectInvestors = get_field('investory_investors', $projectID);
      update_field('settings_project', ['sum' => $projectSum + $sum], $projectID);

      foreach($projectInvestors as $key => $inv) {
        if ($inv['investor'] != $investorID) continue;
        $tmpSum = get_post_meta($projectID, 'investory_investors_'.$key.'_invest', true);
        update_post_meta($projectID, 'investory_investors_'.$key.'_invest', $tmpSum + $sum);
      }

      break;
    case '8':
      update_field('money', $money - $sum, 'user_'.$investorID);
      update_field('overdep', $overdep + $sum, 'user_'.$investorID);

      $projectSum = get_field('settings_project_sum', $projectID);
      $projectInvestors = get_field('investory_investors', $projectID);
      update_field('settings_project', ['sum' => $projectSum + $sum], $projectID);

      foreach($projectInvestors as $key => $inv) {
        if ($inv['investor'] != $investorID) continue;
        $tmpSum = get_post_meta($projectID, 'investory_investors_'.$key.'_invest_over', true);
        update_post_meta($projectID, 'investory_investors_'.$key.'_invest_over', $tmpSum + $sum);
      }

      break;
    case '10':
      update_field('money', $money + $sum, 'user_'.$investorID);
      update_field('refund', $refund - $sum, 'user_'.$investorID);
      break;
    case '11':
      update_field('money', $money + $sum, 'user_'.$investorID);
      update_field('refund_over', $refund_over - $sum, 'user_'.$investorID);
      break;
    case '12':
      update_field('money', $money - $sum, 'user_'.$investorID);
      break;
  }
}
?>