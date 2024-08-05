<?php

// Добавление правил рерайта для проектов
function projects_rewrite() {
  $queryarg = 'post_type=projects&p=';
  add_rewrite_tag('%projects_id%', '([^/]+)', $queryarg);
  add_permastruct('projects', '/projects/%projects_id%/', false);
}
add_action('init', 'projects_rewrite');

// Добавление правил рерайта для транзакций
function transactions_rewrite() {
  $queryarg = 'post_type=transactions&p=';
  add_rewrite_tag('%transactions_id%', '([^/]+)', $queryarg);
  add_permastruct('transactions', '/transactions/%transactions_id%/', false);
}
add_action('init', 'transactions_rewrite');

// Добавление правил рерайта для событий
function events_rewrite() {
  $queryarg = 'post_type=events&p=';
  add_rewrite_tag('%events_id%', '([^/]+)', $queryarg);
  add_permastruct('events', '/events/%events_id%/', false);
}
add_action('init', 'events_rewrite');

// Функция для формирования постоянных ссылок
function custom_permalink($post_link, $post, $leavename) {
  global $wp_rewrite;

  if ($post->post_type == 'projects') {
    $newlink = $wp_rewrite->get_extra_permastruct('projects');
    $newlink = str_replace('%projects_id%', $post->ID, $newlink);
    return home_url(user_trailingslashit($newlink));
  }

  if ($post->post_type == 'transactions') {
    $newlink = $wp_rewrite->get_extra_permastruct('transactions');
    $newlink = str_replace('%transactions_id%', $post->ID, $newlink);
    return home_url(user_trailingslashit($newlink));
  }

  if ($post->post_type == 'events') {
    $newlink = $wp_rewrite->get_extra_permastruct('events');
    $newlink = str_replace('%events_id%', $post->ID, $newlink);
    return home_url(user_trailingslashit($newlink));
  }

  return $post_link;
}
add_filter('post_type_link', 'custom_permalink', 10, 3);