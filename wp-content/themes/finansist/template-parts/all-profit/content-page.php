<div class="filter m-b-3">
	<form method="GET">
		<div class="row">
			<div class="col-xs-6 col-md-3 col-lg-2">
				<select name="year_" class="form-control">
					<option value="" disabled <?=!isset($_GET['year_']) ? 'selected':''?>><?= __('Год')?></option>
					<option value="2023" <?=isset($_GET['year_']) && $_GET['year_'] == 2023 ? 'selected':''?>>2023</option>
					<option value="2024" <?=isset($_GET['year_']) && $_GET['year_'] == 2024 ? 'selected':''?>>2024</option>
				</select>
			</div>
			<div class="col-xs-4 col-md-8 col-lg-9">
				<button>Фильтр</button>
			</div>
		</div>
	</form>
</div>
<table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
  <? get_template_part('template-parts/content', 'header-profit') ?>
  <tbody>
    <? 
			$USER_ID = get_query_var('user_id') !== '' ? get_query_var('user_id') : get_current_user_id();
			$year = isset($_GET['year_']) ? $_GET['year_'] : date('Y');

			$profitData = getProfitValue($year);

			foreach($profitData['rows'] as $row) {
				get_template_part('template-parts/content', 'profit', ['data' => $row]);
				
			}
			get_template_part('template-parts/content', 'profit-total', ['data' => $profitData['medianPerYear']]);
			
			// get_template_part( 'content', 'page-nav' );
		?>
  </tbody>
</table>