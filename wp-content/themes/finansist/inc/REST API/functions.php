<? 
add_filter('rest_prepare_user', 'add_acf_fields_to_rest_user', 10, 3);

function add_acf_fields_to_rest_user($response, $user, $request) {
    $meta_keys = array('money', 'refund', 'profit', 'refund_over', 'contributed', 'overdep', 'tg_login', 'pm_group');
    
    foreach ($meta_keys as $meta_key) {
        $response->data[$meta_key] = get_user_meta($user->ID, $meta_key, true);
    }

    return $response;
}

add_action('rest_api_init', function() {
	register_rest_route('profilegrid/v1', '/groups_links', [
			'methods'      => 'GET',
			'callback' => 'add_tg_link_to_group',
	]);
});

function add_tg_link_to_group() {
  global $wpdb;
  $groups = $wpdb->get_results("SELECT id, group_name FROM {$wpdb->prefix}promag_groups");
  $arGroups = [];

	foreach($groups as $group) {
    $tmp = get_field('group_' . $group->id, 'option');
    $arGroups[$group->id] = [
      'telegram_id' => $tmp['telegram_channel_' . $group->id],
      'telegram_link' => $tmp['telegram_channel_' . $group->id],
    ];
  }
  
  return $arGroups;
}