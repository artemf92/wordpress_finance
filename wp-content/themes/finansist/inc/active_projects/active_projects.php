<? 
add_shortcode( 'active_projects', 'show_active_projects' );

function show_active_projects( $atts ){
  $isPageUser = is_page(65);
  $userID = isset($atts['user_id']) ? $atts['user_id'] : wp_get_current_user()->ID;
  
  add_filter('posts_where', 'active_user_projects_where');

  global $wp_query, $USER_ID;
  $USER_ID = $userID;
  
  if (!is_page('user')) {
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
  } else if (isset($_GET['active_projects_page'])) {
    $paged = $_GET['active_projects_page'];
  } else {
    $paged = 1;
  }
  
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
                'value' => 's:1:"1";',
                'compare' => 'LIKE',
            ],
            [
                'key' => 'status',
                'value' => '1',
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
    ] );
    $i = 30 * $paged - 29;
  ?>
  <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
    <? get_template_part('template-parts/content', 'header-projects', ['view' => 'num,name,status,amount,investments,profit,date']); ?>
    <tbody>
      <? 
        while ( have_posts() ) {
          the_post();
          
          get_template_part('template-parts/content', 'projects-active', ['num' => $i]);

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
            'active_projects_page' => '%#%',
            'tab' => 'active-projects',
        ]),
        'format' => '?active_projects_page=%#%',
        'current' => max(1, $paged),
        'total' => $wp_query->max_num_pages,
      ]);
      echo '</div>';
    }
          
    wp_reset_query();
  ?>
<?
}

function active_user_projects_where( $where ) {
  $where = str_replace("meta_key = 'investory_investors_$", "meta_key LIKE 'investory_investors_%", $where);
  return $where;
}