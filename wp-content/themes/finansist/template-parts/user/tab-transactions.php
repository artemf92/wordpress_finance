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
<div class="tab-pane" id="transactions" role="tabpanel" aria-labelledby="transactions-tab" tabindex="0">
  <div class="content p-y-2">
    <? echo do_shortcode('[transactions user_id='.$USER_ID.']') ?>
  </div>
</div>