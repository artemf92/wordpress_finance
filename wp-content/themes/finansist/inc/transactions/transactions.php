<? 

add_shortcode( 'transactions', 'show_transactions' );
add_action('wp_ajax_transactions_ajax_filter', 'show_transactions');

function show_transactions( $atts ){
  $currentPage = parse_url($_SERVER['REQUEST_URI']);
  $currentUserID = getUserID();
  $post_per_page = getPostsPerPage();
  $pprVariants = [30, 60, 90, 120];

  if (isset($_REQUEST['per_page']) &&
    $post_per_page != $_REQUEST['per_page'] &&
    in_array($_REQUEST['per_page'], $pprVariants)
  ) {
    setPostsPerPage($_REQUEST['per_page']);
    $post_per_page = $_REQUEST['per_page'];
  }

  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

  global $wp_query;

  $query = [
    'post_type' => 'transactions',
    'post_status' =>  'publish',
    'posts_per_page' => $post_per_page,
    'paged' => $paged,
    'meta_query' => [
      'relation' => 'AND',
    ]
  ];
  ?>
  <div class="s-export">
    <form class="form_export_transactions">
      <div class="hidden-filter">
        <div class="row">
          <? require_once get_stylesheet_directory() . '/template-parts/export/transactions/filter_by_transaction_types.php' ?>
          <? require_once get_stylesheet_directory() . '/template-parts/export/transactions/filter_by_date.php' ?>
          <? require_once get_stylesheet_directory() . '/template-parts/export/transactions/filter_by_year_month.php' ?>
          <? require_once get_stylesheet_directory() . '/template-parts/export/transactions/filter_by_user.php' ?>
          <? require_once get_stylesheet_directory() . '/template-parts/export/transactions/filter_by_project.php' ?>
          <? require_once get_stylesheet_directory() . '/template-parts/export/transactions/form_actions.php' ?>
        </div>
      </div>
      <div class="row m-t-3">
        <div class="col-md-2">
          <button type="button" class="btn btn-primary" data-filter-toggle onclick="toggleFilter(this)">
            <i class="fa-solid fa-filter"></i>
          </button>
        </div>
        <? require_once get_stylesheet_directory() . '/template-parts/export/transactions/sort.php' ?>
        <? require_once get_stylesheet_directory() . '/template-parts/export/transactions/export_actions.php' ?>
      </div>
      <hr>
      <? 
      // debug($query);
      // return;
      $wp_query = new WP_Query( $query );

      $i = $post_per_page * $paged - ($post_per_page - 1);
      ?>
      <div class="ajax-result">
        <? if ($wp_query->found_posts) { ?>
        <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
          <? get_template_part('template-parts/content', 'header-transactions') ?>
          <tbody>
            <? 
              while ( have_posts() ) {
                the_post();
                
                get_template_part('template-parts/content', 'transactions', ['num' => $i]);

                $i++;
              }
            ?>
          </tbody>
        </table>
        <? } else {
          echo '<h4 class="text-center">'.esc_html('Транзакций не найдено').'</h4>';
        } ?>
        <? 
          get_template_part( 'content', 'page-nav' );
              
          wp_reset_query();
        ?>
        <? require_once get_stylesheet_directory() . '/template-parts/export/transactions/view.php' ?>
      </div>
    </form>
  </div>
  <?
}

add_shortcode('transactions_tab', 'show_transactions_tab');

function show_transactions_tab($atts) {
    $userID = isset($atts['user_id']) ? $atts['user_id'] : wp_get_current_user()->ID;
    $projectID = $atts['project_id'] ?? 0;

    $paged = isset($_GET['transactions_page']) ? intval($_GET['transactions_page']) : 1;

    $args = [
        'post_type' => 'transactions',
        'post_status' => 'publish',
        'posts_per_page' => 30,
        'paged' => $paged,
        'meta_query' => [
            'relation' => 'AND',
        ],
    ];

    if (!$projectID) {
        $args['meta_query'][] = [
            'key' => 'settings_investor',
            'value' => $userID,
            'compare' => '='
        ];
    } else {
        $args['meta_query'][] = [
            'key' => 'settings_project',
            'value' => $projectID,
            'compare' => '='
        ];
    }

    if (isset($_REQUEST['transaction_type']) && $_REQUEST['transaction_type'] > 0) {
        $filter_type = $_REQUEST['transaction_type'];
        $args['meta_query'][] = [
            'key' => 'settings_transaction_type',
            'value' => $filter_type,
            'compare' => '='
        ];
    }

    if (isset($_REQUEST['from'])) {
        $args['date_query'][] = [
            'after' => $_REQUEST['from'],
            'inclusive' => true
        ];
    }

    if (isset($_REQUEST['to'])) {
        $args['date_query'][] = [
            'before' => $_REQUEST['to'],
            'inclusive' => true
        ];
    }

    // Запрос через WP_Query
    $wp_query = new WP_Query($args);
    $i = 30 * $paged - 29;

    $transaction_types = [];
    $fields = acf_get_fields(50);
    foreach ($fields[0]['sub_fields'] as $field) {
        if ($field['name'] == 'transaction_type') {
            $transaction_types = $field;
        }
    }

    ob_start();
    ?>
    <form class="form-filter">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="transaction_type" class="form-label"><?php echo __('Тип транзакции:') ?></label>
                    <select name="transaction_type" class="form-select form-control" aria-label="Default select example">
                        <option selected><?php echo __('Выбрать тип') ?></option>
                        <?php foreach ($transaction_types['choices'] as $k => $type) { ?>
                            <option value="<?php echo $k ?>" <?php echo isset($_REQUEST['transaction_type']) && $_REQUEST['transaction_type'] == $k ? 'selected' : '' ?>>
                                <?php echo $type ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <div class="mb-3">
                    <label for="from" class="form-label"><?php echo __('Время создания:') ?></label>
                    <div class="">
                        <span><?php echo __('От') ?>:</span>
                        <input type="date" name="from" class="form-control input-date" value="<?php echo isset($_REQUEST['from']) ? $_REQUEST['from'] : '' ?>" placeholder="От">
                        <span><?php echo __('До') ?>:</span>
                        <input type="date" name="to" class="form-control input-date" value="<?php echo isset($_REQUEST['to']) ? $_REQUEST['to'] : '' ?>" placeholder="До">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-info btn-md d-block m-a-2">
                    <i class="fa-solid fa-filter"></i>
                    <?php echo __('Показать') ?>
                </button>
            </div>
        </div>
        <hr>
    </form>

    <? if ($wp_query->found_posts) { ?>
    <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
        <?php get_template_part('template-parts/content', 'header-transactions') ?>
        <tbody>
            <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                <?php get_template_part('template-parts/content', 'transactions', ['num' => $i]); ?>
                <?php $i++; ?>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="navigation pagination">
      <?php
      $big = 999999999;
      echo paginate_links([
        'base' => add_query_arg([
            'transactions_page' => '%#%',
            'tab' => 'transactions',
        ]),
        'format' => '?transactions_page=%#%',
        'current' => max(1, $paged),
        'total' => $wp_query->max_num_pages,
      ]);
      ?>
    </div>
    <? } else {
      echo '<h4 class="text-center">'.esc_html('Транзакций не найдено').'</h4>';
    } ?>

    <?
    wp_reset_postdata();
    return ob_get_clean();
}

add_shortcode('event_transactions', 'show_event_transactions');

function show_event_transactions($atts) {
    $projectID = $atts['project_id'] ?? 0;
    $eventID = $atts['event_id'] ?? 0;

    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $query_args = [
        'post_type' => 'transactions',
        'post_status' => 'publish',
        'posts_per_page' => 30,
        'paged' => $paged,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'settings_project',
                'value' => $projectID,
                'compare' => '=',
            ],
            [
                'key' => 'settings_event',
                'value' => $eventID,
                'compare' => '=',
            ],
        ],
    ];

    $transactions_query = new WP_Query($query_args);

    ob_start(); // Начало буферизации вывода
    if ($transactions_query->found_posts) { ?>
    <form method="post" action="">
        <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
            <?php get_template_part('template-parts/content', 'header-event-transactions'); ?>
            <tbody>
                <?php 
                    if ($transactions_query->have_posts()) {
                        $i = 30 * $paged - 29;
                        while ($transactions_query->have_posts()) {
                            $transactions_query->the_post();
                            get_template_part('template-parts/content', 'event-transactions', ['num' => $i]);
                            $i++;
                        }
                        wp_reset_postdata();
                    } else {
                        echo '<tr><td class="text-center" colspan="5">'.__('Транзакции не найдены').'</td></tr>';
                    }
                ?>
            </tbody>
        </table>
        <? get_template_part('content', 'page-nav-single', ['query' => $transactions_query]); ?>
    </form>
    <? } else {
    echo '<h4 class="text-center">'.esc_html('Транзакций не найдено').'</h4>';
    }

    return ob_get_clean(); 
}

function getUsersForExport() {
  $users = [];

  if (current_user_can('accountant')) {
    global $wpdb;
    $userID = getUserID();
    $groups = get_user_meta($userID, 'pm_group', true);
    foreach($groups as $group_id) {
      $tmp = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value LIKE %s",'pm_group',serialize([$group_id])));
      $tmp2 = [];
      foreach($tmp as $uID) {
        if ($uID == $userID) continue;
        $tmp2[] = get_user_by('ID', $uID);
      }
      $users = array_merge($users, $tmp2);
    }
  } else if (current_user_can('manager') || current_user_can('administrator')) {
    $users = get_users();
  }

  return $users;
}

function getProjectsForExport($userID) {
  global $wp_query;

  $arProjects = [];

  add_filter('posts_where', 'active_user_projects_where');

  $wp_query = new WP_query([
    'post_type' => 'projects',
    'post_status' =>  'publish',
    'posts_per_page' => '-1',
    'meta_query' => 
      [
        'relation' => 'AND',
        [
          'key' => 'investory_investors_$_investor',
          'value'   => $userID,
          'compare' => 'IN',
        ]
      ]
    ] 
  );

  while ( have_posts() ) {
    the_post();

    global $post;
    
    $arProjects[] = $post;
  }

  return $arProjects;
}