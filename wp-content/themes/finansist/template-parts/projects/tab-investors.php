<?php
/**
 * Template Post Type: post, page, product, projects
 * 
 * The template used for displaying page content for fullwidth pages. It contains 
 * everything after the_content()
 *
 * @package flat-bootstrap
 */
$investors = get_field('investory')['investors'];
if (current_user_can('accountant') && !current_user_can('manager')) {
  $usersInGroup = getAdminGroupUsers();
  $investors = array_filter($investors, function($inv) use ($usersInGroup) {
    return in_array($inv['investor'], $usersInGroup);
  });

  $investors = array_values($investors);
}
$time = get_post_full_time();
$showLink = !current_user_can('contributor');
?>
<div class="tab-pane" id="investors" role="tabpanel" aria-labelledby="investors-tab" tabindex="0">
  <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
      <? get_template_part('template-parts/content', 'header-investors', 'active'); ?>
      <tbody>
        <? foreach($investors as $i => $invest) { ?>
          <? $invUser = get_user_by('id', $invest['investor']) ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><?= ($showLink ? '<a href="/user/'.$invest['investor'].'/">': '') . $invUser->display_name . ($showLink ? '</a>':'')?></td>
            <td><?= get_formatted_number($invest['invest']) . ' / ' . get_formatted_number($invest['invest_over']) ?></td>
            <td><?= $time ?></td>
          </tr>
        <? } ?>
      </tbody>
  </table>
</div>