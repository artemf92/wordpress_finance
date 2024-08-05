<?php
/**
 * Template Post Type: post, page, product, projects
 * 
 * The template used for displaying page content for fullwidth pages. It contains 
 * everything after the_content()
 *
 * @package flat-bootstrap
 */
global $USER_ID;
?>
<div class="tab-pane" id="active-projects" role="tabpanel" aria-labelledby="active-projects-tab" tabindex="0">
  <div class="content p-y-2">
    <? echo do_shortcode('[active_projects user_id='.$USER_ID.']') ?>
  </div>
  <?// get_template_part('template-parts/user/actions') ?>
</div>