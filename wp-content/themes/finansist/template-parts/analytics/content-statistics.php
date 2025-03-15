<section class="statistics">
  <? 
  if (current_user_can('manager') || current_user_can('accountant') || current_user_can('administrator')) {
    get_template_part('template-parts/admin-group/filter', 'groups');
  }

  $filterDate = "";
  $filterGroup = "";
  $filterType = "";

  if (current_user_can('accountant') || current_user_can('manager') || current_user_can('administrator')) {
    $filterType = " type=\"accountant\"";

    if (current_user_can('manager') || current_user_can('administrator')) {
      $filterGroup = isset($_GET['group']) ? explode(',', $_GET['group']):get_user_meta(get_current_user_id(), 'pm_group', true);
    } else {
      $filterGroup = get_user_meta(get_current_user_id(), 'pm_group', true);
    }
    
    if (empty($filterGroup)) {
      echo 'Ничего не найдено';
    }

    echo '<h2>' . __('Статистика') . '</h2>';
    $filterDate = isset($_GET['date']) ? " date=\"".$_GET['date']."\"":"";
    
    foreach($filterGroup as $gid) {
      echo do_shortcode("[statistics gid=\"{$gid}\"{$filterType}{$filterDate}]") ;
    }
  // } else if (current_user_can('project_manager')) {
  }

  ?>
</section>