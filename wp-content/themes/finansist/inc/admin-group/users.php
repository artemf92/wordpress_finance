
<? 
add_shortcode( 'list_users', 'show_all_users' );

function show_all_users( $atts ){
  $filter = [];
  if (isset($_POST['group_id']) && $_POST['group_id'] == 'all') {
    $filter = [];
  } else if (isset($_POST['group_id']) && $_POST['group_id'] == 'empty') {
    $filter = [
      'meta_key' => 'pm_group',
      // 'meta_value' => '',
      'meta_compare' => 'NOT EXISTS'
    ];
  } else if (isset($_POST['group_id']) && $_POST['group_id']) {
    $filter = [
      'meta_key' => 'pm_group',
      'meta_value' => ":\"".$_POST['group_id']."\"",
      'meta_compare' => 'LIKE'
    ];
  } 
  if (isset($_POST['display_name']) && $_POST['display_name']) {
    $filter['search'] = '*'.$_POST['display_name'].'*';
  }
  $users = get_users($filter);
  
  // $group_id = $atts['gid'];
  // if (!$group_id) echo 'Группы не найдены';

  global $wpdb;
  // $users = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value LIKE %s",'pm_group',serialize([$group_id])));
  $all_groups =  $wpdb->get_col($wpdb->prepare("SELECT * FROM `wp_promag_groups`"));
  // $group_name = $group_db[0];
  $n = 0;
  ?>
  <div class="filter m-b-3">
    <form method="POST">
      <div class="row">
        <div class="col-md-4 col-lg-3">
          <select name="group_id" id="" class="form-control">
            <option value="all"<?=(isset($_POST['group_id']) && $_POST['group_id'] == 'all' ? 'selected':'')?>>Все пользователи</option>
            <option value="empty"<?=(isset($_POST['group_id']) && $_POST['group_id'] == 'empty' ? 'selected':'')?>>Без группы</option>
            <optgroup label="<?=__('Группы')?>">
              <? foreach($all_groups as $groupID) {
                $groupName = $wpdb->get_col($wpdb->prepare("SELECT group_name FROM `wp_promag_groups` WHERE id = $groupID"));
                echo '<option value="'.$groupID.'" '.(isset($_POST['group_id']) && $_POST['group_id'] == $groupID ? 'selected':'').'>'.$groupName[0].'</option>';
              } ?>
            </optgroup>
          </select>
        </div>
        <div class="col-md-4 col-lg-3">
          <input type="text" name="display_name" class="form-control" value="<?=isset($_POST['display_name']) && $_POST['display_name'] ?: ''?>" placeholder="<?=__('Имя')?>">
        </div>
        <div class="col">
          <button>Фильтр</button>
        </div>
      </div>
    </form>
  </div>
  <table class="table m-b-3 tablesaw tablesaw-swipe" data-tablesaw-mode="swipe">
    <?
      get_template_part('template-parts/admin-group/content-header', 'users-all');
      foreach($users as $user) {
        get_template_part('template-parts/admin-group/content', 'users-all', ['user' => $user->ID, 'num' => $n]);
        $n++;
      }
    ?>
  </table>
  <?
}