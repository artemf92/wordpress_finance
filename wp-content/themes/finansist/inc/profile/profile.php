<? 

add_action('acf/save_post', 'my_custom_function', 5); // Устанавливаем приоритет 20, чтобы быть уверенными, что хук сработает после сохранения поля ACF

function my_custom_function($post_id) {
  if ($_POST['from'] === 'profile' && isset($_POST['_acf_post_id'])) {
    $old_data = [
      'field_65e2eb0b120be' => [
        'name' => 'money',
        'prev_value' => get_field('money', $post_id),
        'event' => 12, // 12 : Изменение портфеля
      ],
      // 'field_65e2ebb33dc2a' => [
      //   'name' => 'refund',
      //   'prev_value' => get_field('refund', $post_id),
      // ],
      // 'field_65e2ebcf3dc2b' => [
      //   'name' => 'profit',
      //   'prev_value' => get_field('profit', $post_id),
      // ],
      // 'field_65e2ebdd3dc2c' => [
      //   'name' => 'refund_over',
      //   'prev_value' => get_field('refund_over', $post_id),
      // ],
      // 'field_65ff467aefb07' => [
      //   'name' => 'contributed',
      //   'prev_value' => get_field('contributed', $post_id),
      //   'event' => 7, // 7 : Изменение вложений администратором
      // ],
      // 'field_65ff46b0efb08' => [
      //   'name' => 'overdep',
      //   'prev_value' => get_field('overdep', $post_id),
      //   'event' => 8, // 8 : Изменение вложений (сверх) администратором
      // ],
    ];
    // Проверяем, было ли изменено поле ACF
    foreach($old_data as $key => $arr) {
      if (isset($_POST['acf'][$key]) && $_POST['acf'][$key] !== $arr['prev_value']) {
        $change = intval($_POST['acf'][$key]) - intval($arr['prev_value']);
        create_transaction(false, $_POST['user_id'], false, $change, $arr['event']);
      }
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
?>