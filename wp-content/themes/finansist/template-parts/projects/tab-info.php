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
$stages = get_field('stadii_proekta');

if (empty($settings) && get_post_meta($post->ID,'settings_project_sum', true)) {
  update_field('settings_project', [
    'sum' => get_post_meta($post->ID,'settings_project_sum', true),
    'profit' => get_post_meta($post->ID, 'settings_project_profit', true),
  ], $post->ID);

  $settings = get_field('settings_project');
}
?>  
<div class="tab-pane active" id="info" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
  <div class="row">
    <div class="col-md-8">
      <?php the_content(); ?>

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
    <div class="col-md-4">
      <div class="item m-b-1">
        <div class="field__label m-b-1">
          <? echo esc_html('Статус проекта') ?>
        </div>
        <div class="bg-gray field__item p-a-1" id="project_status">
          <? echo $status['label'] ?>
        </div>
      </div>
      <div class="item m-b-1">
        <div class="field__label m-b-1">
          <? echo esc_html('Сумма проекта') ?>
        </div>
        <div class="bg-gray field__item p-a-1">
          <? echo get_formatted_number($settings['sum']) ?>
        </div>
      </div>
      <div class="item m-b-1">
        <div class="field__label m-b-1">
          <? echo esc_html('Доходность') ?>
        </div>
        <div class="bg-gray field__item p-a-1">
          <? echo get_formatted_number($settings['profit'], '%') ?>
        </div>
      </div>
      <? if (current_user_can('contributor')) { ?>
      <? 
      $userID = getUserID();
      $investors = get_field('investory');
      $inv = getUserInvestedInProjects($userID, $post->ID);
      ?>
      <div class="item m-b-1">
        <div class="field__label m-b-1">
          <? echo esc_html('Инвестировано') ?>
        </div>
        <div class="bg-gray field__item p-a-1">
          <? echo get_formatted_number($inv['invest']) ?>
        </div>
      </div>
      <div class="item m-b-1">
        <div class="field__label m-b-1">
          <? echo esc_html('Инвестировано сверх') ?>
        </div>
        <div class="bg-gray field__item p-a-1">
          <? echo get_formatted_number($inv['invest_over']) ?>
        </div>
      </div>
      <? } ?>

      <? get_template_part('template-parts/projects/content', 'actions', ['status' => $status['value']]) ?>

      <? if (!empty($stages)) { ?>
        <div class="h3"><? echo esc_html('Стадии проекта') ?></div>
        <? foreach($stages as $stage) { ?>
        <hr>
        <div class="alert alert-info d-flex align-items-center stage" role="alert">
          <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
          <div class="text-center">
            <?= $stage['etap'] ?>
          </div>
        </div>
        <? } ?>
      <? } ?>
    </div>
  </div>
  <? if (current_user_can('contributor') || current_user_can('accountant')) {
    get_template_part('template-parts/projects/content', 'transactions-current-user');
  } ?>
</div>

<svg xmlns="http://www.w3.org/2000/svg" class="d-none">
  <symbol id="check-circle-fill" viewBox="0 0 16 16">
    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
  </symbol>
  <symbol id="info-fill" viewBox="0 0 16 16">
    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
  </symbol>
  <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
  </symbol>
</svg>