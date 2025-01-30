<section class="statistics">
  <? 
  if (current_user_can('manager')) {
    get_template_part('template-parts/admin-group/filter', 'groups');
    get_template_part('template-parts/admin-group/filter', 'date');
  }

  $filterDate = "";
  $filterGroup = "";
  $filterType = "";

  if (current_user_can('accountant')) {
    $filterType = " type=\"accountant\"";

    if (current_user_can('manager')) {
      $filterGroup = isset($_GET['group']) ? explode(',', $_GET['group']):get_user_meta(get_current_user_id(), 'pm_group', true);
      $filterDate = isset($_GET['date']) ? " date=\"".$_GET['date']."\"":"";
    } else {
      $filterGroup = get_user_meta(get_current_user_id(), 'pm_group', true);
    }
    
    if (empty($filterGroup)) {
      echo 'Ничего не найдено';
    }

    echo '<h2>' . _e('Статистика') . '</h2>';
    
    foreach($filterGroup as $gid) {
      echo do_shortcode("[statistics gid=\"{$gid}\"{$filterType}{$filterDate}]") ;
    }
  // } else if (current_user_can('project_manager')) {
  }
  ?>
</section>