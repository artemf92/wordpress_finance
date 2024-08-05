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
$time = get_post_full_time();
?>
<div class="tab-pane" id="investors" role="tabpanel" aria-labelledby="investors-tab" tabindex="0">
  <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
      <? get_template_part('template-parts/content', 'header-investors', 'active'); ?>
      <tbody>
        <? foreach($investors as $i => $invest) { ?>
          <? $invUser = get_user_by('id', $invest['investor']) ?>
          <tr>
            <td><?= $i + 1 ?></td>
            <td><a href="/user/<?=$invest['investor']?>/"><?= $invUser->display_name?></a></td>
            <td><?= get_formatted_number($invest['invest']) . ' / ' . get_formatted_number($invest['invest_over']) ?></td>
            <td><?= $time ?></td>
          </tr>
        <? } ?>
      </tbody>
  </table>
</div>