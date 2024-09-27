<? 
add_shortcode( 'active_group_projects', 'show_active_group_projects' );

function show_active_group_projects( $atts ){
  $userID = isset($atts['user_id']) ? $atts['user_id'] : wp_get_current_user()->ID;
  $group_id = $atts['group_id'] ?: 0;
  
  add_filter('posts_where', 'active_group_projects_where');
  
  global $wpdb, $users;
  $users = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value LIKE %s",'pm_group',serialize([$group_id])));
  get_template_part('template-parts/admin-group/filter', 'projects');

  global $wp_query, $userID;
  $userID = urldecode(intval($_GET['investor']));
  
  if (!is_page('user')) {
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
  } else if (strpos($_SERVER['REQUEST_URI'], '/page/') !== false) {
    $paged = 1;
    $url = explode('page', $_SERVER['REQUEST_URI']);
    $page = explode('/', $url[1]);
    $paged = $page[1];
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
          'key' => 'investory_investors_$_investor',
          'value'   => $userID > 0 ? $userID : $users,
          'compare' => 'IN',
        ]
      ],
    'paged' => $paged,
    ] );
    $i = 1;
  ?>
  <? if ($userID > 0) { ?>
  <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
    <? get_template_part('template-parts/admin-group/content', 'header-projects-user', 'active'); ?>
    <tbody>
      <? 
        while ( have_posts() ) {
          the_post();
          
          get_template_part('template-parts/admin-group/content', 'projects-active-user', ['num' => $i]);

          $i++;
        }
      ?>
    </tbody>
  </table>
  <? 
  get_template_part( 'content', 'page-nav' );
  
  wp_reset_query();
  } else { ?>
    <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
    <? get_template_part('template-parts/admin-group/content', 'header-projects', 'active'); ?>
    <tbody>
      <? 
        while ( have_posts() ) {
          the_post();
          
          get_template_part('template-parts/admin-group/content', 'projects-active', ['num' => $i]);

          $i++;
        }
      ?>
    </tbody>
  </table>
  <? 
    get_template_part( 'content', 'page-nav' );
    
    wp_reset_query();
  }
  ?>
<?
}

function active_group_projects_where( $where ) {
  $where = str_replace("meta_key = 'investory_investors_$", "meta_key LIKE 'investory_investors_%", $where);
  return $where;
}