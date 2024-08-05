<? 
add_shortcode( 'project_events', 'show_project_events' );

function show_project_events( $atts ){
  
  $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
  
  global $post;

  $events_query = new WP_Query( [
    'post_type' => 'events',
    'post_status' =>  'publish',
    'posts_per_page' => '30',
    'meta_query' => 
      [
        'relation' => 'AND',
        [
          'key' => 'settings_project',
          'value'   => $post->ID,
          'compare' => 'IN',
        ]
      ],
    'paged' => $paged,
    ] );
  $i = 1;

  ob_start(); // Начало буферизации вывода
    ?>
    <form method="post" action="">
        <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
        <? get_template_part('template-parts/content', 'header-history', 'active'); ?>
            <tbody>
                <?php 
                    if ($events_query->have_posts()) {
                        $i = 30 * $paged - 29;
                        while ($events_query->have_posts()) {
                            $events_query->the_post();
                            get_template_part('template-parts/content', 'projects-history', ['num' => $i]);
                            $i++;
                        }
                        wp_reset_postdata();
                    } else {
                        echo '<tr><td class="text-center" colspan="5">'.__('События не найдены').'</td></tr>';
                    }
                ?>
            </tbody>
        </table>
        <? get_template_part('content', 'page-nav-single', ['query' => $events_query]); ?>
    </form>
    <?php
    return ob_get_clean(); 
}

add_action( 'pre_trash_post', 'before_delete_event_action', 10, 3 );

/**
 * Function for `before_delete_post` action-hook.
 * 
 * @param bool|null $trash           Whether to go forward with trashing.
 * @param WP_Post   $post            Post object.
 * @param string    $previous_status The status of the post about to be trashed.
 *
 * @return void
 */
function before_delete_event_action( $trash, $post, $previous_status ){

	$postid = $post->ID;

	if( ! $post || $post->post_type !== 'events' ) 
		return;

  global $wpdb;
    
  $query = "
      SELECT p.ID 
      FROM {$wpdb->posts} p
      LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'settings_event'
      WHERE p.post_type = 'transactions'
      AND pm.meta_value = $postid
  ";

  $results = $wpdb->get_results($query);

  if ( ! empty( $results ) ) {
    foreach ( $results as $post ) {
      $transactionID = $post->ID;
      wp_trash_post($transactionID);
    }
  }
  
}
add_action( 'pre_untrash_post', 'before_untrash_event_action', 10, 3 );

/**
 * Function for `before_delete_post` action-hook.
 * 
 * @param bool|null $untrash         Whether to go forward with untrashing.
 * @param WP_Post   $post            Post object.
 * @param string    $previous_status The status of the post at the point where it was trashed.
 *
 * @return void
 */
function before_untrash_event_action( $untrash, $post, $previous_status ){
  $postid = $post->ID;

	if( ! $post || $post->post_type !== 'events' ) 
		return;

  global $wpdb;
    
  $query = "
      SELECT p.ID 
      FROM {$wpdb->posts} p
      LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'settings_event'
      WHERE p.post_type = 'transactions'
      AND pm.meta_value = $postid
  ";

  $results = $wpdb->get_results($query);

  if ( ! empty( $results ) ) {
    foreach ( $results as $post ) {
      $transactionID = $post->ID;
      
      wp_untrash_post($transactionID);
    }
  }

}
?>