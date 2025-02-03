<div class="s-export">
  <form class="form_export_transactions">
    <? $i = $post_per_page < 0 ? 1 : $post_per_page * $paged - ($post_per_page - 1); ?>
    <div class="ajax-result">
      <table class="table tablesaw tablesaw-swipe" data-type="report_<?=$_POST['variant']?>" data-tablesaw-mode="swipe"
        data-tablesaw-hide-empty>
        <? 
          global $totalSumm;
          $totalSumm = 0;
          $wp_query = new WP_Query( $query );
          ?>
        <? if ($wp_query->found_posts) { ?>
        <? get_template_part('template-parts/content', 'header-transactions', ['view' => 'num,name,investor,amount,date,id']) ?>
        <tbody>
          <? while ( have_posts() ) {
                the_post();
                
                get_template_part('template-parts/content', 'transactions', ['num' => $i, 'view' => 'num,name,investor,amount,date,id']);
                
                $i++;
              }
              get_template_part('template-parts/content', 'total-transactions', ['total' => [
                [ 'span' => 3, 'text' => __('Общий итог:')],
                [ 'span' => 0, 'text' => get_formatted_number($totalSumm)],
                [ 'span' => 2, 'text' => ''],
              ]]);
            } else {
            echo '<h4 class="text-center">'.esc_html('Транзакций не найдено').'</h4>';
            } ?>
        </tbody>
      </table>
      <? 
          echo '<div class="navigation pagination">';
          $big = 999999999;
          echo paginate_links([
            'base' => add_query_arg([
                'paged' => '%#%',
            ]),
            'format' => '?paged=%#%',
            'current' => max(1, $paged),
            'total' => $wp_query->max_num_pages,
          ]);
          echo '</div>';
        
          wp_reset_query();
        ?>
      <?// require_once get_stylesheet_directory() . '/template-parts/export/transactions/view.php' ?>
      <? require_once get_stylesheet_directory() . '/template-parts/export/transactions/export_actions.php' ?>
    </div>
  </form>
</div>