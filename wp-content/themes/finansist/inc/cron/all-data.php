<? 
require $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

function users_daily_data()
{
  global $wpdb;
  $current_datetime = new DateTime();

  $users = get_users();

  foreach ($users as $user) {
    $money = get_user_meta($user->ID, 'money', true);
    $contributed = get_user_meta($user->ID, 'contributed', true);
    $overdep = get_user_meta($user->ID, 'overdep', true);
    $profit = get_user_meta($user->ID, 'profit', true);
    $refund = get_user_meta($user->ID, 'refund', true);
    $refund_over = get_user_meta($user->ID, 'refund_over', true);

    $data = [
      'user_id' => $user->ID,
      'date' => date('Y-m-d'),
      'user_money' => $money,
      'user_contributed' => $contributed,
      'user_overdep' => $overdep,
      'user_profit' => $profit,
      'user_refund' => $refund,
      'user_refund_over' => $refund_over,
    ];

    $wpdb->insert('af_users_data', $data);
  }
}

users_daily_data();
?>