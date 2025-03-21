<? 
add_action( 'wp_ajax_get_report', 'get_report_callback' );
function get_report_callback() {
  global $type;
  $type = $_POST['variant'];
  $period = explode(' - ', $_POST['date-range']);
  $periodStart = explode('/',$period[0]);
  $periodEnd = explode('/', $period[1]);
  $post_per_page = getPostsPerPage();
  $paged = $_POST['page-report'] ?: 1;
  $pprVariants = [30, 60, 90, 120];

  $meta_query = $date_query = [];

  if ($type == 'refill') {
    $meta_query = [
      [
        'key' => 'settings_transaction_type',
        'value' => 12
      ]
    ];
  } else if ($type == 'portfolio_investment') {
    $meta_query = [
      [ 
        'key' => 'settings_transaction_type', 
        'value' => [1, 7], 
        'compare' => 'IN' 
      ],
    ];
  } else if ($type == 'investment_projects') {
    $meta_query = [
      [
        'key' => 'settings_transaction_type',
        'value' => 1
      ]
    ];
  }

  if (!empty($periodStart)) {
    $date_query['after'] = [
      'year'  => $periodStart[2],
      'month' => $periodStart[1],
      'day'   => $periodStart[0],
    ];
    $date_query['inclusive'] = true;
  }

  if (!empty($periodEnd)) {
    $date_query['before'] = [
      'year'  => $periodEnd[2],
      'month' => $periodEnd[1],
      'day'   => $periodEnd[0]
    ];
    $date_query['inclusive'] = true;
  }

  global $wpdb, $groupID;
  $groupID = null;

  if ($_POST['group'] == 'null' || current_user_can('contributor')) {
    $meta_query['relation'] = 'AND';
    $meta_query[] = [
      'key' => 'settings_investor',
      'value' => get_current_user_id(),
    ];
  } else if (current_user_can('manager') || current_user_can('administrator')) {
    $groupID = $_POST['group'];

    if ($groupID && $groupID !== 'all') {
      $groupUsers = getUsersByGroup($groupID);

      $meta_query['relation'] = 'AND';
      $meta_query[] = [
        'key' => 'settings_investor',
        'value' => $groupUsers,
        'compare' => 'IN'
      ];
    }
  } else if (current_user_can('project_manager') || current_user_can('accountant')) {
    $groupID = $_POST['group'];

    if (!$groupID) {
      $groupID = get_user_meta(get_current_user_id(), 'pm_group', true)[0];
    }

    if (!$groupID || !in_array($groupID, get_user_meta(get_current_user_id(), 'pm_group', true))) {
      error_log('Ошибка - менеджер проекта не состоит в группе: ' . $groupID);
      wp_die();
    }

    $groupUsers = getUsersByGroup($groupID);

    $meta_query['relation'] = 'AND';
    $meta_query[] = [
      'key' => 'settings_investor',
      'value' => $groupUsers,
      'compare' => 'IN'
    ];
  }

  if (empty($meta_query)) {
    error_log('Ошибка - пустой запрос meta_query');
    wp_die();
  }

  global $wp_query;
  
  $post_per_page = -1;

  $query = [
    'post_type' => 'transactions',
    'post_status' =>  'publish',
    'posts_per_page' => $post_per_page,
    'paged' => $paged,
    'meta_query' => $meta_query,
    'date_query' => $date_query,
  ];
  // debug($query); 

  require_once get_stylesheet_directory() . '/template-parts/analytics/variant-' . $type . '.php';

  ?>
  
  <?
}

?>