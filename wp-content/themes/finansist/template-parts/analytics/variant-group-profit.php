<? 
global $type, $groupID, $month, $year;
$variants = [
  'refill' => __('Пополнение портфеля'),
  'portfolio_investment' => __('Вложения портфельными средствами'),
  'investment_projects' => __('Инвестиции в проекты за период'),
  'group_profit' => __('Доходность за ' . mb_strtolower(getMonth($month)) . ' ' . $year),
];

$reportTitle = $variants[$type];
if ($groupID && $groupID !== "all") {
  $reportTitle .= " - группа " .getGroupName($groupID);
}

?>
<div class="no-export">
  <form class="form_export_transactions">
    <? $i = $post_per_page < 0 ? 1 : $post_per_page * $paged - ($post_per_page - 1); ?>
    <div class="ajax-result">
      <h3><?=$reportTitle?></h3>
      <table class="table tablesaw tablesaw-swipe" data-type="report_<?=$_POST['variant']?>" data-tablesaw-mode="swipe"
        data-tablesaw-hide-empty>
        <? get_template_part('template-parts/content', 'header-group-profit') ?>
        <tbody>
          <? 
            $summaryData = ['portfolio' => 0, 'capital_in' => 0, 'capital_has' => 0, 'total_profit' => 0, 'percent' => 0];
            $usersInGroup = getUsersByGroup($groupID);
            foreach($usersInGroup as $key => $userID) {
              $profitData = getProfitvalueByMonth($userID, $year, $month);
              $profitData['login'] = userDisplayName(get_user_by('ID', $userID));
              $profitData['user_id'] = $userID;
              $summaryData['portfolio'] += $profitData['portfolio'];
              $summaryData['capital_in'] += $profitData['capital_in'];
              $summaryData['capital_has'] += $profitData['capital_has'];
              $summaryData['total_profit'] += $profitData['total_profit'];
              get_template_part('template-parts/content', 'profit-group', ['data' => $profitData, 'class' => $key, 'key' => $key + 1]);
            }
            $summaryData['percent'] = round($summaryData['total_profit'] * 100 / $summaryData['portfolio'], 2);
            get_template_part('template-parts/content', 'profit-group-total', ['data' => $summaryData]);
          ?>
        </tbody>
    </div>
  </form>
</div>