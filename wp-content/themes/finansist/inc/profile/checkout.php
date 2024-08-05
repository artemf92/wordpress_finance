<?php
add_action( 'wp_ajax_checkout_full', 'checkout_full_callback' );
add_action( 'wp_ajax_checkout_profit', 'checkout_profit_callback' );
add_action( 'wp_ajax_checkout_contrib', 'checkout_contrib_callback' );
add_action( 'wp_ajax_checkout_contrib_over', 'checkout_contrib_over_callback' );
add_action( 'wp_ajax_checkout_partial', 'checkout_partial_callback' );
add_action( 'wp_ajax_checkout_partial_calc', 'checkout_partial_calc_callback' );

function checkout_partial_callback() {
  $userID = $_REQUEST['user_id'];
  $userData = get_portfolio($userID);
  $html = '';

  if ($userData['profit'] > 0 || $userData['refund'] > 0 || $userData['refund_over'] > 0) {
    $html .= '<form class="user-checkout-form">';
    $html .= '  <input type="hidden" name="user_id" value="'.$userID.'">';
    $html .= '  <h3>'.__('Форма расчёта для пользователя').'</h3>';
    if ($userData['profit'] > 0) {
      $html .= '  <div class="m-b-1">';
      $html .= '    <label for="checkout-profit-1" class="form-label">'.__('Доход').'</label>';
      $html .= '    <input type="number" step="any" min="0" max="'.$userData['profit'].'" name="checkout[checkout_profit]" class="form-control" id="checkout-profit-'.$userID.'" value="'.$userData['profit'].'">';
      $html .= '  </div>';
    }
    if ($userData['refund'] > 0) {
      $html .= '  <div class="m-b-1">';
      $html .= '    <label for="checkout-refund-1" class="form-label">'.__('Возврат инвестиций').'</label>';
      $html .= '    <input type="number" step="any" min="0" max="'.$userData['refund'].'" name="checkout[checkout_contrib]" class="form-control" id="checkout-refund-'.$userID.'" value="'.$userData['refund'].'">';
      $html .= '  </div>';
    }
    if ($userData['refund_over'] > 0) {
      $html .= '  <div class="m-b-3">';
      $html .= '    <label for="checkout-refund_over-1" class="form-label">'.__('Возврат инвестиций (сверх)').'</label>';
      $html .= '    <input type="number" step="any" min="0" max="'.$userData['refund_over'].'" name="checkout[checkout_contrib_over]" class="form-control" id="checkout-refund_over-'.$userID.'" value="'.$userData['refund_over'].'">';
      $html .= '  </div>';
    }
    $html .= '  <div class="checkout_prepare__actions">';
    $html .= '    <button class="btn btn-primary m-r-2" type="submit">'.__('Доход').'</button>';
    $html .= '    <button class="btn btn-warning" type="button" data-fancybox-close>'.__('Закрыть').'</button>';
    $html .= '  </div></form>';
  } else {
    $html .= '<p>Получать нечего :(</p>';
  }

  echo $html;
  wp_die();
}

function checkout_partial_calc_callback() {
  $userID = $_REQUEST['user_id'];

  foreach($_REQUEST['checkout'] as $field => $value) {
    $field($userID, $value);
  }

  checkout_result_callback($userID);

  get_template_part('template-parts/success');
  wp_die();
}

function checkout_full_callback() {
  $userID = $_REQUEST['user_id'];

  checkout_profit($userID);
  checkout_contrib($userID);
  checkout_contrib_over($userID);
  checkout_result_callback($userID);
  
  get_template_part('template-parts/success');
  wp_die();
}

function checkout_profit_callback() {
  $userID = $_REQUEST['user_id'];
  
  checkout_profit($userID);
  checkout_result_callback($userID);

  get_template_part('template-parts/success');
  wp_die();
}

function checkout_contrib_callback() {
  $userID = $_REQUEST['user_id'];
  
  checkout_contrib($userID);
  checkout_result_callback($userID);

  get_template_part('template-parts/success');
  wp_die();
}

function checkout_contrib_over_callback() {
  $userID = $_REQUEST['user_id'];
  
  checkout_contrib_over($userID);
  checkout_result_callback($userID);

  get_template_part('template-parts/success');
  wp_die();
}

function checkout_profit($user, $sum = NULL) {
  if (get_field('profit', 'user_'.$user) > 0) {
    create_transaction(false, $user, false, $sum ?? get_field('profit', 'user_'.$user), 9 ); // Доход от инвестиций
    if ($sum) 
      $result = get_field('profit', 'user_'.$user) - $sum;
    else
      $result = 0;

    update_field('profit', $result, 'user_' . $user);
  }

}

function checkout_contrib($user, $sum = NULL) {
  if (get_field('refund', 'user_'.$user) > 0) {
    create_transaction(false, $user, false, $sum ?? get_field('refund', 'user_'.$user), 10 ); // Выдача денег в портфель
    update_field('money', get_field('money', 'user_'.$user) + ($sum ?? get_field('refund', 'user_'.$user)), 'user_' . $user);

    if ($sum) 
      $result = get_field('refund', 'user_'.$user) - $sum;
    else
      $result = 0;

    update_field('refund', $result, 'user_' . $user);
  }
}

function checkout_contrib_over($user, $sum = NULL) {
  if (get_field('refund_over', 'user_'.$user) > 0) {
    create_transaction(false, $user, false, $sum ?? get_field('refund_over', 'user_'.$user), 11 ); // Выдача денег в портфель (сверх)
    // update_field('money', get_field('money', 'user_'.$user) + ($sum ?? get_field('refund_over', 'user_'.$user)), 'user_' . $user);

    if ($sum) 
      $result = get_field('refund_over', 'user_'.$user) - $sum;
    else
      $result = 0;

    update_field('refund_over', $result, 'user_' . $user);
    
  }
}

function checkout_result_callback($user) {
  $fields = ['money', 'profit', 'contributed', 'refund', 'overdep','refund_over'];
  foreach($fields as $field) {
    $value = get_field($field, 'user_' . $user);
    echo '<input type="hidden" name="result['.$field.']" value="'.$value.'" />';
  }

}

