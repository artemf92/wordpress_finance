<? 
// add_shortcode( 'project_ivestors', 'show_project_investors' );

function show_project_investors( $atts ){
  
  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

  global $post, $wp_query;

  $wp_query = new WP_Query( [
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
  ?>
  <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
    <? get_template_part('template-parts/content', 'header-investors', 'active'); ?>
    <tbody>
      <? 
        while ( have_posts() ) {

          the_post();

          get_template_part('template-parts/content', 'projects-investors', ['num' => $i]);

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