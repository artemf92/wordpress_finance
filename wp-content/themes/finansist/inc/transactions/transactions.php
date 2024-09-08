<? 
add_shortcode( 'transactions', 'show_transactions' );

// // Создадим новую функцию которая добавит условие where в запрос
// function filter_where( $where = '' ) {
// 	// с 1 марта по 15 марта 2010 года
//   if (isset($_REQUEST['from'])) {
//     $where .= " AND post_date >= '".$_REQUEST['from'].'"';
//   }
//   if (isset($_REQUEST['to'])) {
//     $where .= " AND post_date < '".$_REQUEST['to']."'";
//   }
// 	return $where;
// }

function show_transactions( $atts ){
  $userID = isset($atts['user_id']) ? $atts['user_id'] : wp_get_current_user()->ID;
  $projectID = $atts['project_id'] ?? 0;
  
  if (!is_page('user')) {
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
  } else if (strpos($_SERVER['REQUEST_URI'], '/page/') !== false) {
    $paged = 1;
    $url = explode('page', $_SERVER['REQUEST_URI']);
    $page = explode('/', $url[1]);
    $paged = $page[1];
  } else {
    $paged = 1;
  }

  global $wp_query, $USER_ID;
  $USER_ID = $userID;

  $query = [
    'post_type' => 'transactions',
    'post_status' =>  'publish',
    'posts_per_page' => '30',
    'paged' => $paged,
  ];

  if (!$projectID) {
    $query['meta_query'] = [
      'relation' => 'AND',
      [
        'key' => 'settings_investor',
        'value'   => $userID,
        'compare' => '=',
      ],
    ];
  } else {
    $query['meta_query'] = [
      'relation' => 'AND',
      [
        'key' => 'settings_project',
        'value'   => $projectID,
        'compare' => '=',
      ],
    ];
  }

  if (isset($_REQUEST['transaction_type']) && $_REQUEST['transaction_type'] > 0) {
    $filter_type = $_REQUEST['transaction_type'];
    $query['meta_query'][] = [
      'key' => 'settings_transaction_type',
      'value' => $filter_type,
      'compare' => '=',
    ];
  }
  if (isset($_REQUEST['from'])) {
    $query['date_query'][] = [
      'after' => $_REQUEST['from'],
      'inclusive' => true
    ];
  }
  if (isset($_REQUEST['to'])) {
    $query['date_query'][] = [
      'after' => $_REQUEST['before'],
      'inclusive' => true
    ];
  }

  $wp_query = new WP_Query( $query );

  $i = 30 * $paged - 29;

  $transaction_types = [];
  $fields = acf_get_fields(50);
  foreach($fields[0]['sub_fields'] as $field) {
    if ($field['name'] == 'transaction_type') {
      $transaction_types = $field;
    }
  }
  ?>
  <form class="form-filter">
    <div class="row">
      <div class="col-md-4">
        <div class="mb-3">
          <label for="transaction_type" class="form-label"><? echo __('Тип транзакции:') ?></label>
          <select name="transaction_type" class="form-select form-control" aria-label="Default select example">
            <option selected><?= __('Выбрать тип') ?></option>
            <? foreach($transaction_types['choices'] as $k => $type) { ?>
              <option value="<?=$k?>" <?=isset($_REQUEST['transaction_type']) && $_REQUEST['transaction_type'] == $k?'selected':''?>><?= $type ?></option>
            <? } ?>
          </select>
        </div>
      </div>
      <div class="col-md-5">
        <div class="mb-3">
          <label for="from" class="form-label"><? echo __('Время создания:') ?></label>
          <div class="">
            <span><?=__('От')?>:</span>
            <input type="date" name="from" class="form-control input-date" value="<?=isset($_REQUEST['from']) && $_REQUEST['from'] ?: ''?>" placeholder="От">
            <span><?=__('До')?>:</span>
            <input type="date" name="to" class="form-control input-date" value="<?=isset($_REQUEST['to']) && $_REQUEST['to'] ?: ''?>" placeholder="До">
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <button type="submit" class="btn btn-info btn-md d-block m-a-2">
          <i class="fa-solid fa-filter"></i>
          <?= __('Показать') ?>
        </button>
      </div>
    </div>
    <hr>
  </form>
  <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
    <? get_template_part('template-parts/content', 'header-transactions') ?>
    <tbody>
      <? 
        while ( have_posts() ) {
          the_post();
          
          get_template_part('template-parts/content', 'transactions', ['num' => $i]);

          $i++;
        }
      ?>
    </tbody>
  </table>
  <? 
    get_template_part( 'content', 'page-nav' );
        
    wp_reset_query();
  ?>
<?
}

add_shortcode('event_transactions', 'show_event_transactions');

function show_event_transactions($atts) {
    $projectID = $atts['project_id'] ?? 0;
    $eventID = $atts['event_id'] ?? 0;

    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $query_args = [
        'post_type' => 'transactions',
        'post_status' => 'publish',
        'posts_per_page' => 30,
        'paged' => $paged,
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => 'settings_project',
                'value' => $projectID,
                'compare' => '=',
            ],
            [
                'key' => 'settings_event',
                'value' => $eventID,
                'compare' => '=',
            ],
        ],
    ];

    $transactions_query = new WP_Query($query_args);

    ob_start(); // Начало буферизации вывода
    ?>
    <form method="post" action="">
        <table class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
            <?php get_template_part('template-parts/content', 'header-event-transactions'); ?>
            <tbody>
                <?php 
                    if ($transactions_query->have_posts()) {
                        $i = 30 * $paged - 29;
                        while ($transactions_query->have_posts()) {
                            $transactions_query->the_post();
                            get_template_part('template-parts/content', 'event-transactions', ['num' => $i]);
                            $i++;
                        }
                        wp_reset_postdata();
                    } else {
                        echo '<tr><td class="text-center" colspan="5">'.__('Транзакции не найдены').'</td></tr>';
                    }
                ?>
            </tbody>
        </table>
        <? get_template_part('content', 'page-nav-single', ['query' => $transactions_query]); ?>
    </form>
    <?php
    return ob_get_clean(); 
}