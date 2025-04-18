<? 
global $wp_query, $type, $groupID;
$variants = [
  'refill' => __('Пополнение портфеля'),
  'portfolio_investment' => __('Вложения портфельными средствами'),
  'investment_projects' => __('Инвестиции в проекты за период'),
];

$reportTitle = $variants[$type];

if ($groupID && $groupID) {
  $reportTitle .= " - группа " .getGroupName($groupID);
}

$query['posts_per_page'] =  -1;

$wp_query = new WP_Query( $query );
// if (!$wp_query->found_posts) wp_die();

$arProjects = [];

while ( have_posts() ) {
  the_post();
  $settings = get_field('settings', $post->ID);
  $project = $settings['project'];

  $arProjects[] = $project->ID;
}
wp_reset_query();
$arProjects = array_values(array_unique($arProjects));

if (!empty($arProjects)) {
  $projectsQuery = [
    'post_type' => 'projects',
    'post_status' =>  'publish',
    'posts_per_page' => '-1',
    'post__in' => $arProjects,
    'paged' => $paged
  ];

  ?>
  <div class="s-export">
    <form class="form_export_transactions">
      <? $i = $post_per_page < 0 ? 1 : $post_per_page * $paged - ($post_per_page - 1); ?>
      <div class="ajax-result">
        <h3><?=$reportTitle?></h3>
        <table class="table tablesaw tablesaw-swipe" data-type="report_<?=$_POST['variant']?>" data-tablesaw-mode="swipe"
          data-tablesaw-hide-empty>
          <? 
            global $totalSumm;
            $wp_query = new WP_Query( $projectsQuery );
            ?>
          <? if ($wp_query->found_posts) { ?>
          <? get_template_part('template-parts/content', 'header-projects', ['view' => 'num,name,status,profit,date']) ?>
          <tbody>
            <? while ( have_posts() ) {
                  the_post();
                  
                  get_template_part('template-parts/content', 'projects', ['num' => $i, 'view' => 'num,name,status,profit,date']);
                  
                  $i++;
                }
              } else {
              echo '<h4 class="text-center">'.esc_html('Транзакций не найдено').'</h4>';
              } ?>
          </tbody>
        </table>
        <? 
            echo '<div class="navigation pagination">';
            $big = 999999999;
            echo paginate_links([
              'base' => add_query_arg([
                  'paged' => '%#%',
              ]),
              'format' => '?paged=%#%',
              'current' => max(1, $paged),
              'total' => $wp_query->max_num_pages,
            ]);
            echo '</div>';
          
            wp_reset_query();
          ?>
        <?// require_once get_stylesheet_directory() . '/template-parts/export/transactions/view.php' ?>
        <?// require_once get_stylesheet_directory() . '/template-parts/export/transactions/export_actions.php' ?>
      </div>
    </form>
  </div>
<?
} else { ?>
  <div class="s-export">
    <form class="form_export_transactions">
      <div class="ajax-result">
        <h3><?=$reportTitle?></h3>
        <table class="table tablesaw tablesaw-swipe" data-type="report_<?=$_POST['variant']?>" data-tablesaw-mode="swipe"
          data-tablesaw-hide-empty>
          <tbody>
            <h4 class="text-center">Транзакций не найдено</h4>
          </tbody>
        </table>
      </div>
    </form>
  </div>
<?
}
?>