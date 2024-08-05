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
<div class="tab-pane" id="history" role="tabpanel" aria-labelledby="history-tab" tabindex="0">
  <? echo do_shortcode('[project_events]') ?>

  <? get_template_part('template-parts/projects/content', 'actions', ['status' => $status['value']]) ?>
</div>