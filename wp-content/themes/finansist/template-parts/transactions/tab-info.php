<?php
/**
 * Template Post Type: post, page, product, projects
 * 
 * The template used for displaying page content for fullwidth pages. It contains 
 * everything after the_content()
 *
 * @package flat-bootstrap
 */
// $status = get_field('status');
$settings = get_field('settings');
if (empty($settings)) {
  update_field('settings', [
    'project' => get_post_meta($post->ID, 'settings_project', true),
    'investor' => get_post_meta($post->ID, 'settings_investor', true),
    'event' => get_post_meta($post->ID, 'settings_event', true),
    'sum' => get_post_meta($post->ID, 'settings_sum', true),
    'transaction_type' => get_post_meta($post->ID, 'settings_transaction_type', true),
  ]);
  $settings = get_field('settings');
};
$project = $settings['project'];
$user = get_userdata($settings['investor'] ?? get_post_meta($post->ID, 'settings_investor', true));
// $stages = get_field('stadii_proekta');
?>  
<div class="tab-pane active" id="info" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
  <div class="row">
      <div class="col-md-4">
        <div class="item m-b-1">
          <div class="field__label m-b-1">
            <? echo __('Дата:') ?>
          </div>
          <div class="bg-gray field__item p-a-1" id="project_status">
            <? echo get_post_full_time() ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="item m-b-1">
          <div class="field__label m-b-1">
            <? echo __('Проект') ?>
          </div>
          <div class="bg-gray field__item p-a-1">
            <? echo '  <td><a href="'.get_the_permalink($project).'" />'.get_the_title($project).'</a></td>'; ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="item m-b-1">
          <div class="field__label m-b-1">
            <? echo __('Инвестор') ?>
          </div>
          <div class="bg-gray field__item p-a-1">
          <? echo '  <td><a href="/user/'.$user->ID.'/" />'.$user->display_name.'</a></td>'; ?>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="item m-b-1">
          <div class="field__label m-b-1">
            <? echo __('Сумма:') ?>
          </div>
          <div class="bg-gray field__item p-a-1">
          <? echo get_formatted_number($settings['sum']); ?>
          </div>
        </div>
      </div>

      <? /* ?>
      <button type="button" class="btn btn-secondary"
        id="example"
        data-bs-toggle="tooltip" data-bs-placement="top"
        data-bs-custom-class="custom-tooltip"
        data-bs-title="This top tooltip is themed via CSS variables.">
        Custom tooltip
      </button>
      <? */ ?>
    </div>
  </div>
</div>
