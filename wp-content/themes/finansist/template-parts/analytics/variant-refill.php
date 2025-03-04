<? 
global $type, $groupID;
$variants = [
  'refill' => __('Пополнение портфеля'),
  'portfolio_investment' => __('Вложения портфельными средствами'),
  'investment_projects' => __('Инвестиции в проекты за период'),
];

$reportTitle = $variants[$type];
if ($groupID && $groupID !== "all") {
  $reportTitle .= " - группа " .getGroupName($groupID);
}

?>
<div class="s-export">
  <form class="form_export_transactions">
    <? $i = $post_per_page < 0 ? 1 : $post_per_page * $paged - ($post_per_page - 1); ?>
    <div class="ajax-result">
      <h3><?=$reportTitle?></h3>
      <table class="table tablesaw tablesaw-swipe" data-type="report_<?=$_POST['variant']?>" data-tablesaw-mode="swipe"
        data-tablesaw-hide-empty>
        <? 
          global $totalSumm, $arInvestors;
          $totalSumm = 0;
          $wp_query = new WP_Query( $query );
          $arInvestors = [];
          ?>
        <? if ($wp_query->found_posts) {
          while ( have_posts() ) {
            the_post();
            
            $investorID = get_post_meta(get_the_ID(), 'settings_investor', true);
            $sum = get_post_meta(get_the_ID(), 'settings_sum', true);
            if (!isset($arInvestors[$investorID])) {
              $arInvestors[$investorID] = [
                'investor_id' => $investorID,
                'sum' => 0,
                'transaction_type' => '12'
              ];
            }
            $arInvestors[$investorID]['sum'] += $sum;
          }?>
          <tbody>
          <? 
          foreach($arInvestors as $investorID => $data) {
            get_template_part('template-parts/analytics/content', 'transactions', [
              'num' => $i, 
              'view' => 'num,name,investor,group,amount',
              'data' => $data
            ]);
            $totalSumm += $data['sum'];
            $i++;
          }
          get_template_part('template-parts/content', 'total-transactions', ['total' => [
            [ 'span' => 4, 'text' => __('Общий итог:')],
            [ 'span' => 0, 'text' => get_formatted_number($totalSumm)],
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
      <?// require_once get_stylesheet_directory() . '/template-parts/export/transactions/export_actions.php' ?>
    </div>
  </form>
</div>