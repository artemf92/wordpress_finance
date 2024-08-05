<?php
/**
 * Template Post Type: post, page, product, projects
 * 
 * The template used for displaying page content for fullwidth pages. It contains 
 * everything after the_content()
 *
 * @package flat-bootstrap
 */
$project = get_post_meta($post->ID, 'settings_project', true);
?>
<div class="table-transactions" id="event-history">
  <? echo do_shortcode('[event_transactions project_id="'.$project.'" event_id="'.get_the_ID().'"]') ?>
</div>