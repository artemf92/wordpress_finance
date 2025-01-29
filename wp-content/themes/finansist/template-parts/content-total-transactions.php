<?php
$data = $args['total'];

if (!empty($data)) {
  echo '<tr style="background-color: #e2e2e2;">';
  foreach($data as $col) {
    echo '<td '.($col['span'] ? 'colspan="'.$col['span'].'"':'').'><strong>'.$col['text'].'</strong></td>';
  }
  echo '</tr>';
}