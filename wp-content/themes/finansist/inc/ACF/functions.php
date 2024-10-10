
<? 
if( function_exists('acf_add_options_page') ) {
  acf_add_options_page(array(
      'page_title'    => 'Глобальные настройки',
      'menu_title'    => 'Глобальные настройки',
      'menu_slug'     => 'site-settings',
      'capability'    => 'edit_posts',
      'redirect'      => false
  ));
}

if (function_exists('acf_add_local_field_group')) {
  acf_add_local_field_group(array(
      'key' => 'group_settings',
      'title' => 'Настройка групп участников',
      'fields' => array(),
      'location' => array(
        array(
          array(
            'param' => 'options_page',
            'operator' => '==',
            'value' => 'site-settings',
          ),
        ),
      ),
  ));

  global $wpdb;
  $groups = $wpdb->get_results("SELECT id, group_name FROM {$wpdb->prefix}promag_groups");

  foreach ($groups as $group) {
      acf_add_local_field(array(
          'key' => 'group_' . $group->id,
          'label' => $group->group_name,
          'name' => 'group_' . $group->id,
          'type' => 'group',
          'sub_fields' => array(
              array(
                  'key' => 'field_telegram_channel_' . $group->id,
                  'label' => 'Telegram ID',
                  'name' => 'telegram_channel_' . $group->id,
                  'type' => 'text',
                  'wrapper' => array(
                      'width' => '15',
                  ),
              ),
              array(
                  'key' => 'field_telegram_channel_name_' . $group->id,
                  'label' => '@telegram',
                  'name' => 'telegram_channel_name_' . $group->id,
                  'type' => 'text',
                  'wrapper' => array(
                      'width' => '50',
                  ),
              ),
          ),
          'parent' => 'group_settings',
      ));
  }
}