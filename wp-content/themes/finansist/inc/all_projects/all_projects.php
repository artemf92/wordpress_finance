<? 
add_shortcode( 'all_projects', 'show_all_projects' );

function show_all_projects( $atts ){
  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
  if (strpos($_SERVER['REQUEST_URI'], '/page/') !== false) {
    $paged = 1;
    $url = explode('page', $_SERVER['REQUEST_URI']);
    $page = explode('/', $url[1]);
    $paged = $page[1];
  } else {
    $paged = 1;
  }

  global $wp_query;
  $filter = [
    'post_type' => 'projects',
    'post_status' =>  'publish',
    'posts_per_page' => '30',
    'paged' => $paged,
    's' => 'займ'
  ];

  if ($_POST['s']) {
    // $filter['s'] = $_POST['s'];
  }
  // debug($filter);
  // return;
  $wp_query = new WP_Query( $filter );
  $i = 30 * $paged - 29;
  ?>
  <div class="filter m-b-3">
    <form method="GET">
      <div class="row">
        <div class="col-xs-8 col-md-4 col-lg-3">
          <input type="text" name="s" class="form-control" value="<?=$_POST['s']?>" placeholder="<?= __('Название проекта')?>">
        </div>
        <div class="col-xs-4 col-md-8 col-lg-9">
          <button>Фильтр</button>
        </div>
      </div>
    </form>
  </div>
  <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
    <? get_template_part('template-parts/content', 'header-projects', ['view' => 'num,name,status,profit,date']) ?>
    <tbody>
      <? 
        while ( have_posts() ) {
          the_post();
          
          get_template_part('template-parts/content', 'projects', ['num' => $i]);

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

// function archive_user_projects_where( $where ) {
//   $where = str_replace("meta_key = 'investory_investors_$", "meta_key LIKE 'investory_investors_%", $where);
//   return $where;
// }