<?php
$data = $args['data'];
$view = $args['view'] ? explode(',', $args['view']) : explode(',', 'num,name,project,investor,amount,date,id') ;
$investorID = $data['investor_id'];
$investor = get_user_by('ID', $investorID);
$acf_transactions = acf_get_fields(50)[0]['sub_fields'];
$transaction_type = explode('/', $data['transaction_type']);
foreach($acf_transactions as $field) {
  if ($field['name'] !== 'transaction_type') continue;

  if (count($transaction_type)) {
    foreach($transaction_type as $type) {
      $transaction_name[] = $field['choices'][$type];
    }
  }
}
$sum = get_formatted_number($data['sum']);

$groupName = isset(getUserGroups($investor->ID)[0]) ? getUserGroups($investor->ID)[0]['name'] : '';

echo '<tr data-transaction-id="'.$post->ID.'">';
if (in_array('num', $view)) {
  echo '  <td scope="row" class="td-num">'.$args['num'].'</td>';
}
if (in_array('name', $view)) {
  // echo '  <td class="td-transaction"><a href="/transactions/'.$post->ID.'/">'.get_the_title().'</a></td>';
  echo '  <td class="td-transaction">'.implode('/', $transaction_name).'</td>';
}
if (in_array('project', $view)) {
  if ($project) {
    echo '  <td class="td-project"><a href="'.get_the_permalink($project).'">'.get_the_title($project).'</a></td>';
  } else {
    echo '  <td class="td-project"></td>';
  }
}
if (in_array('investor', $view) && (current_user_can('project_manager') || current_user_can('manager') || current_user_can('administrator') )) {
  echo '  <td class="td-investor"><a href="/user/'.$investorID.'/">'.userDisplayName($investor).'</a></td>';
}
if (in_array('group', $view) && (current_user_can('project_manager') || current_user_can('manager') || current_user_can('administrator') ) && $groupName !== '') {
  echo '  <td class="td-group">'.$groupName.'</a></td>';
}
if (in_array('amount', $view)) {
  echo '  <td class="td-amount">'.$sum.'</td>';
}
if (in_array('date', $view)) {
  echo '  <td class="td-date">'.$time.'</td>';
}
if (in_array('id', $view)) {
  echo '  <td scope="row" class="td-id">'.$post->ID.'</td>';
}
echo '</tr>';