<? 
require $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';

function projects_cron()
{
  global $wpdb;
  $current_datetime = new DateTime();
  
  if ($current_datetime->format('j') == 1) {
    $users = get_users();

    foreach ($users as $user) {
      $money = get_user_meta($user->ID, 'money', true);
      $contributed = get_user_meta($user->ID, 'contributed', true);
      $overdep = get_user_meta($user->ID, 'overdep', true);

      $data = [
        'user_id' => $user->ID,
        'date' => date('Y-m-d'),
        'user_money' => $money,
        'user_contributed' => $contributed,
        'user_overdep' => $overdep,
      ];

      $wpdb->insert('af_profit_data', $data);
    }
  }
}

projects_cron();
?>