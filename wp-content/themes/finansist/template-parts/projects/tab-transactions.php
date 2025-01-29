<?php
/**
 * Template Post Type: post, page, product, projects
 * 
 * The template used for displaying page content for fullwidth pages. It contains 
 * everything after the_content()
 *
 * @package flat-bootstrap
 */
$status = get_field('status');
$settings = get_field('settings_project');
$investors = get_field('investory')['investors'];
$data = [
  'project_id' => $post->ID
];
if (current_user_can('accountant') && !current_user_can('manager')) {
  $usersInGroup = getAdminGroupUsers();
  $investors = array_filter($investors, function($inv) use ($usersInGroup) {
    return in_array($inv['investor'], $usersInGroup);
  });

  $data['user_id'] = implode(', ', array_column($investors, 'investor'));
}
?>
<div class="tab-pane" id="transactions" role="tabpanel" aria-labelledby="transactions-tab" tabindex="0">
  <? echo do_shortcode(sprintf("[transactions project_id='%d' user_id='%s']", $data['project_id'], $data['user_id'] ?? '')) ?>
</div>