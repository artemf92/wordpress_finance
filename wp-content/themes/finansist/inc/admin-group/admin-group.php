
<? 
add_shortcode( 'admin-group', 'show_admin_group' );

function show_admin_group( $atts ){
  $group_id = $atts['gid'];
  if (!$group_id) echo 'Группы не найдены';

  global $wpdb, $group_name;
  $users = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value LIKE %s",'pm_group',serialize([$group_id])));
  $group_db =  $wpdb->get_col($wpdb->prepare("SELECT group_name FROM `wp_promag_groups` WHERE id = " . $group_id));
  $group_name = $group_db[0];
  $n = 0;
  ?>
  <div class="align-items-center d-flex justify-content-between">
    <h3><?= $group_name ?></h3>
    <?/*<a href="/group-projects-active/?group=<?=$group_id?>" class="btn btn-warning">Активные проекты группы</a>*/?>
  </div>
  <table class="table m-b-3 tablesaw tablesaw-swipe" data-tablesaw-mode="swipe">
    <?
      get_template_part('template-parts/admin-group/content-header', 'users');
      foreach($users as $user_id) {
        get_template_part('template-parts/admin-group/content', 'users', ['user' => $user_id, 'num' => $n]);
        $n++;
      }
    ?>
  </table>
  <hr>
  <?
}