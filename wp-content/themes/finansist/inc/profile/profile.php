<? 

// add_action('acf/save_post', 'my_custom_function', 5); // Устанавливаем приоритет 20, чтобы быть уверенными, что хук сработает после сохранения поля ACF
add_action('acf/update_value', 'my_custom_function2', 10, 3); // Устанавливаем приоритет 20, чтобы быть уверенными, что хук сработает после сохранения поля ACF

function my_custom_function($post_id) {
  
  if ($_POST['from'] === 'profile' && isset($_POST['_acf_post_id'])) {
    user_profile_actions_callback($post_id);
  }

}

function my_custom_function2( $value, $post_id, $field  ) {
  
  if ($_POST['from'] === 'profile' && isset($_POST['_acf_post_id'])) {
    user_profile_actions_callback($post_id);
  }

  return $value;
}

function user_profile_actions_callback($user_id) {
  $old_data = [
    'field_65e2eb0b120be' => [
      'name' => 'money',
      'prev_value' => get_field('money', $user_id),
      'event' => 12, // 12 : Изменение портфеля
    ],
    // 'field_65e2ebb33dc2a' => [
    //   'name' => 'refund',
    //   'prev_value' => get_field('refund', $user_id),
    // ],
    // 'field_65e2ebcf3dc2b' => [
    //   'name' => 'profit',
    //   'prev_value' => get_field('profit', $user_id),
    // ],
    // 'field_65e2ebdd3dc2c' => [
    //   'name' => 'refund_over',
    //   'prev_value' => get_field('refund_over', $user_id),
    // ],
    // 'field_65ff467aefb07' => [
    //   'name' => 'contributed',
    //   'prev_value' => get_field('contributed', $user_id),
    //   'event' => 7, // 7 : Изменение вложений администратором
    // ],
    // 'field_65ff46b0efb08' => [
    //   'name' => 'overdep',
    //   'prev_value' => get_field('overdep', $user_id),
    //   'event' => 8, // 8 : Изменение вложений (сверх) администратором
    // ],
  ];
  // Проверяем, было ли изменено поле ACF
  foreach($old_data as $key => $arr) {
    if (isset($_POST['acf'][$key]) && $_POST['acf'][$key] !== $arr['prev_value']) {
      $change = floatval($_POST['acf'][$key]) - floatval($arr['prev_value']);
      create_transaction(false, $_POST['user_id'], false, $change, $arr['event']);
    }
  }
}

function custom_user_profile_fields($user) {
  ?>
  <table class="form-table">
      <tr>
          <th>
              <label for="custom_button"><?php _e('Перейти в личный кабинет пользователя'); ?></label>
          </th>
          <td>
              <a href="/user/<?=$user->ID?>/" target="_blank" id="custom_button" class="button button-warning"><?php _e('Перейти'); ?></a>
          </td>
      </tr>
  </table>
  <?php
}

add_action('show_user_profile', 'custom_user_profile_fields');
add_action('edit_user_profile', 'custom_user_profile_fields');

add_action( 'wp_ajax_user_edit_profile', 'user_edit_profile_callback' );
function user_edit_profile_callback() {
  $user_id = isset($_GET['user_ud']) && $_GET['user_id'] !== '' ? $_GET['user_id'] : get_current_user_id();
  $portfolio = get_portfolio($user_id);
  ?>
  <form class="form" method="post">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <input type="hidden" name="from" value="profile">
    <input type="hidden" name="_acf_post_id" value="user_<?php echo $user_id; ?>">
    <h3><?=esc_html('Изменение портфеля')?></h3>
    <div class="item m-b-1" data-field="money">
      <div class="field__label m-b-1"><?=esc_html('Сумма на руках')?></div>
      <div class="bg-gray field__item p-a-1">
        <input type="number" onwheel="return false;" class="form-control" name="acf[field_65e2eb0b120be]" step="any" min="<?=$portfolio['money']?>" value="<?=$portfolio['money']?>">
      </div>
      <button type="submit" class="m-t-2 btn btn-block"><?=esc_html('Сохранить') ?></button>
    </div>
  </form>
  <?
  wp_die();
}

add_action('template_redirect', 'save_acf_user_fields');
function save_acf_user_fields() {
    if (isset($_POST['from']) && $_POST['from'] === 'profile' && isset($_POST['user_id'])) {
        $user_id = intval($_POST['user_id']);
        
        if (isset($_POST['acf']['field_65e2eb0b120be'])) {
          $money = sanitize_text_field($_POST['acf']['field_65e2eb0b120be']);
          update_field('field_65e2eb0b120be', $money, 'user_' . $user_id);
        }

        wp_redirect(get_permalink());
        exit;
    }
}
?>