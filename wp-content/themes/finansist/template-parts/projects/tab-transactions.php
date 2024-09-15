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
?>
<div class="tab-pane" id="transactions" role="tabpanel" aria-labelledby="transactions-tab" tabindex="0">
  <? echo do_shortcode('[transactions_tab project_id='.$post->ID.']') ?>
</div>