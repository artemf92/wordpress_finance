<? 
add_shortcode( 'active_projects', 'show_active_projects' );

function show_active_projects( $atts ){
  $userID = isset($atts['user_id']) ? $atts['user_id'] : wp_get_current_user()->ID;
  
  add_filter('posts_where', 'active_user_projects_where');

  global $wp_query, $USER_ID;
  $USER_ID = $userID;
  
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
          'key' => 'status',
          'value' => '1'
        ],
        [
          'key' => 'investory_investors_$_investor',
          'value'   => $userID,
          'compare' => 'IN',
        ]
      ],
    'paged' => $paged,
    ] );
    $i = 1;
  ?>
  <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
    <? get_template_part('template-parts/content', 'header-projects', 'active'); ?>
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
  get_template_part( 'content', 'page-nav' );
        
  wp_reset_query();
  ?>
<?
}

function active_user_projects_where( $where ) {
  $where = str_replace("meta_key = 'investory_investors_$", "meta_key LIKE 'investory_investors_%", $where);
  return $where;
}