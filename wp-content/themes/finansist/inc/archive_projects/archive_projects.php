<? 
add_shortcode( 'archive_projects', 'show_archive_projects' );

function show_archive_projects( $atts ){
  $isPageUser = is_page(65);
  $userID = isset($atts['user_id']) ? $atts['user_id'] : wp_get_current_user()->ID;
  
  add_filter('posts_where', 'archive_user_projects_where');

  // Сортировка: по дате создания (created) или дате закрытия (closed)
  $sortBy = isset($_GET['archive_orderby']) && $_GET['archive_orderby'] === 'closed' ? 'closed' : 'created';
  $sortOrder = isset($_GET['archive_order']) && strtolower($_GET['archive_order']) === 'asc' ? 'ASC' : 'DESC';
  $GLOBALS['archive_projects_sort'] = ['by' => $sortBy, 'order' => $sortOrder];
  add_filter('posts_clauses', 'archive_projects_sort_clauses', 10, 2);
  
  if (!is_page('user')) {
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
  } else if (isset($_GET['archive_projects_page'])) {
    $paged = $_GET['archive_projects_page'];
  } else {
    $paged = 1;
  }

  global $wp_query, $USER_ID;

  $USER_ID = $userID;

  $wp_query = new WP_Query( [
    'post_type' => 'projects',
    'post_status' =>  'publish',
    'posts_per_page' => '30',
    'meta_query' => 
      [
        'relation' => 'AND',
        [
          'relation' => 'OR',
            [
                'key' => 'status',
                'value' => 's:1:"5";', 
                'compare' => 'LIKE',
            ],
            [
                'key' => 'status',
                'value' => '5', 
                'compare' => '=', 
            ]
        ],
        [
          'relation' => 'OR',
          [
            'key' => 'investory_investors_$_investor',
            'value'   => $userID,
            'compare' => 'IN',
          ],
          [
            'key' => 'managers_group_managers',
            'value' => '"'.$userID.'";',
            'compare' => 'LIKE'
          ]
        ]
      ],
    'paged' => $paged,
    'orderby' => 'date',
    'order' => $sortOrder,
    ] );
  remove_filter('posts_clauses', 'archive_projects_sort_clauses', 10);
    $i = 30 * $paged - 29;
  ?>
  <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
    <? get_template_part('template-parts/content', 'header-projects', ['view' => 'num,name,status,profit,date,close_date', 'sort' => ['by' => $sortBy, 'order' => $sortOrder]]) ?>
    <tbody>
      <? 
        while ( have_posts() ) {
          the_post();
          
          get_template_part('template-parts/content', 'projects-completed', ['num' => $i]);

          $i++;
        }
      ?>
    </tbody>
  </table>
  <? 
    if (!$isPageUser) {
      get_template_part( 'content', 'page-nav' );
    } else {
      echo '<div class="navigation pagination">';
      $big = 999999999;
      echo paginate_links([
        'base' => add_query_arg([
            'archive_projects_page' => '%#%',
            'tab' => 'archive-projects',
        ]),
        'format' => '?archive_projects_page=%#%',
        'current' => max(1, $paged),
        'total' => $wp_query->max_num_pages,
      ]);
      echo '</div>';
    }
          
    wp_reset_query();
  ?>
<?
}

function archive_user_projects_where( $where ) {
  $where = str_replace("meta_key = 'investory_investors_$", "meta_key LIKE 'investory_investors_%", $where);
  return $where;
}

// Сортировка по дате закрытия: дата последнего опубликованного события "Закрытие проекта"
function archive_projects_sort_clauses( $clauses, $query ) {
  global $wpdb;
  $sort = isset($GLOBALS['archive_projects_sort']) ? $GLOBALS['archive_projects_sort'] : null;

  if ($sort && $sort['by'] === 'closed') {
    $order = $sort['order'] === 'ASC' ? 'ASC' : 'DESC';
    $clauses['orderby'] = "(
      SELECT MAX(e.post_date)
      FROM {$wpdb->posts} e
      INNER JOIN {$wpdb->postmeta} ep ON ep.post_id = e.ID AND ep.meta_key = 'settings_project' AND ep.meta_value = {$wpdb->posts}.ID
      INNER JOIN {$wpdb->postmeta} et ON et.post_id = e.ID AND et.meta_key = 'settings_transaction_type' AND et.meta_value = '5'
      WHERE e.post_type = 'events' AND e.post_status = 'publish'
    ) {$order}, {$wpdb->posts}.ID {$order}";
  }

  return $clauses;
}
