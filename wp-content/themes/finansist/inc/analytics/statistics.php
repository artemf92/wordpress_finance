
<? 
add_shortcode( 'statistics', 'show_statistics' );

function show_statistics( $atts ){
  $group_id = $atts['gid'];
  $date = $atts['date'];

  if (!$group_id) echo 'Группы не найдены';

  global $wpdb, $group_name;
  $users = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value LIKE %s",'pm_group',serialize([$group_id])));
  $group_db =  $wpdb->get_col($wpdb->prepare("SELECT group_name FROM `wp_promag_groups` WHERE id = " . $group_id));
  $group_name = $group_db[0];
  $n = 0;

  global $totalMoney, $totalContributed, $totalOverdep;
  $totalMoney = $totalContributed = $totalOverdep = 0;
  ?>
  <div class="align-items-center d-flex justify-content-between">
    <h3><?= $group_name ?></h3>
    <?/*<a href="/group-projects-active/?group=<?=$group_id?>" class="btn btn-warning">Активные проекты группы</a>*/?>
  </div>
  <table class="table m-b-3 tablesaw tablesaw-swipe" data-tablesaw-mode="swipe">
    <?
      get_template_part('template-parts/admin-group/content-header', 'users-extends');
      foreach($users as $user_id) {
        get_template_part('template-parts/admin-group/content', 'users-extends', ['user' => $user_id, 'num' => $n, 'date' => $date]);
        $n++;
      }
      get_template_part('template-parts/admin-group/content', 'total-extends');
    ?>
  </table>
  <hr>
  <?
  unset($totalMoney);
  unset($totalContributed);
  unset($totalOverdep);

}