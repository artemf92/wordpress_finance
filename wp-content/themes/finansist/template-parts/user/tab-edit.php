<?php
/**
 * Template Post Type: post, page, product, projects
 * 
 * The template used for displaying page content for fullwidth pages. It contains 
 * everything after the_content()
 *
 * @package flat-bootstrap
 */
?>  
<div class="tab-pane" id="edit" role="tabpanel" aria-labelledby="edit-tab" tabindex="0">
  <?// echo do_shortcode('[wppb-edit-profile]') ?>
  <? echo do_shortcode('[profilegrid_profile]') ?>
  <a href="/auth?action=logout" class="align-right btn btn-info"><?= __('Выйти') ?></a>
  <script>
    jQuery(document).ready(function($) {
      $('#last_name').parent().removeClass('pm_required')
      $('label[for="last_name"] sup').remove()
    })
  </script>
</div>