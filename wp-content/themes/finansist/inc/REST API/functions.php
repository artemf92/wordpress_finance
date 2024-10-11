<? 
add_filter('rest_prepare_user', 'add_acf_fields_to_rest_user', 10, 3);

function add_acf_fields_to_rest_user($response, $user, $request) {
    $meta_keys = array('money', 'refund', 'profit', 'refund_over', 'contributed', 'overdep', 'tg_login', 'pm_group');
    
    foreach ($meta_keys as $meta_key) {
        $response->data[$meta_key] = get_user_meta($user->ID, $meta_key, true);
    }

    return $response;
}

add_filter('rest_request_after_callbacks', 'add_custom_field_to_profilegrid_groups', 10, 3);

function add_custom_field_to_profilegrid_groups($response, $handler, $request) {
    if ($request->get_route() === '/profilegrid/v1/groups' && $response instanceof WP_REST_Response) {
        
      $data = $response->get_data();

      foreach ($data as &$group) {
        $group_id = $group['value'];
        $tmp = get_field('group_' . $group_id, 'option');
        $arGroups[$group->id] = [
          'telegram_id' => $tmp['telegram_channel_' . $group->id],
          'telegram_link' => $tmp['telegram_channel_name_' . $group->id],
        ];
        $group['group_id'] = $group_id;
        $group['telegram_id'] = isset($tmp['telegram_channel_'.$group_id]) ? $tmp['telegram_channel_'.$group_id] : null;
        $group['telegram_link'] = isset($tmp['telegram_channel_name_'.$group_id]) ? $tmp['telegram_channel_name_'.$group_id] : null;
      }

      $response->set_data($data);
    }

    return $response;
}