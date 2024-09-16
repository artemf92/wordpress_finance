<? 
$userID = getUserID();
$paged = isset($_GET['tip']) ? intval($_GET['tip']) : 1;

$args = [
    'post_type' => 'transactions',
    'post_status' => 'publish',
    'posts_per_page' => 10,
    'paged' => $paged,
    'meta_query' => [
        'relation' => 'AND',
        [
          'key' => 'settings_investor',
          'value' => $userID,
          'compare' => '='
        ],
        [
          'key' => 'settings_project',
          'value' => $post->ID,
          'compare' => '='
        ]
    ],
];

// Запрос через WP_Query
$transactions_query = new WP_Query($args);
$i = 10 * $paged - 9;

$transaction_types = [];
$fields = acf_get_fields(50);
foreach ($fields[0]['sub_fields'] as $field) {
    if ($field['name'] == 'transaction_type') {
        $transaction_types = $field;
    }
}
?>
<hr>
<table id="user-transactions" class="table tablesaw tablesaw-swipe" data-tablesaw-mode="swipe" data-tablesaw-hide-empty>
    <?php get_template_part('template-parts/content', 'header-transactions') ?>
    <tbody>
        <?php while ($transactions_query->have_posts()) : $transactions_query->the_post(); ?>
            <?php get_template_part('template-parts/content', 'transactions', ['num' => $i]); ?>
            <?php $i++; ?>
        <?php endwhile; ?>
    </tbody>
</table>

<div class="navigation pagination">
  <?php
  $big = 999999999;
  echo paginate_links([
    'base' => add_query_arg([
        'tip' => '%#%',
        'tab' => 'info',
    ]) . '#user-transactions',
    'format' => '?tip=%#%',
    'current' => max(1, $paged),
    'total' => $transactions_query->max_num_pages,
  ]);
  ?>
</div>

<?
wp_reset_postdata();
?>