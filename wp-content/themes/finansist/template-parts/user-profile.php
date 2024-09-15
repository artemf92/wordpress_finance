<?php
/**
 * Theme: Flat Bootstrap
 * 
 * The template used for displaying page content in page.php
 *
 * @package flat-bootstrap
 */
global $USER_ID;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

  <div id="xsbf-entry-content" class="entry-content">

    <? get_template_part('template-parts/user/navbar', 'user') ?>

		<!-- Tab panes -->
		<div class="tab-content">

			<? get_template_part('template-parts/user/tab', 'info') ?>

      <? if (wp_get_current_user()->ID === $USER_ID) {
			  get_template_part('template-parts/user/tab', 'edit');
      } else {
			  get_template_part('template-parts/user/tab', 'active-projects');
			  get_template_part('template-parts/user/tab', 'archive-projects');
        if (hasAccess())
			    get_template_part('template-parts/user/tab', 'transactions');
      } ?>

    </div>
  </div>

  <?php the_content(); ?>

  <?php get_template_part( 'content', 'page-nav' ); ?>

  <div class="edit-link">
    <? if (user_can(get_current_user_id(), 'administrator') || user_can(get_current_user_id(), 'manager')  ) { ?>
      <a class="post-edit-link d-block m-b-2" href="/wp-admin/user-edit.php?user_id=<?=$USER_ID?>"><span class="glyphicon glyphicon-edit"></span> <?=_('Изменить портфель')?></a>
    <? } else if (wp_get_current_user()->ID === $USER_ID) { ?>
      <a class="post-edit-link d-block m-b-2" id="user_edit_profile" data-user="<?=$USER_ID?>" data-once="ajax" href="javascript:void(0)"><span class="glyphicon glyphicon-edit"></span> <?=_('Изменить портфель')?></a>
    <? } ?>
  </div>

  <?//php edit_post_link( __( '<span class="glyphicon glyphicon-edit"></span> Edit', 'flat-bootstrap' ), '<footer class="entry-meta"><div class="edit-link">', '</div></footer>' ); ?>

  </div><!-- .entry-content -->

</article><!-- #post-## -->