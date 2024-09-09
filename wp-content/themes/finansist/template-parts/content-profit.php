<?php
$data = $args['data'];

echo '<tr>';
echo '  <td>' . $data['year'] . '</td>';
echo '  <td>' . $data['month'] .'</td>';
echo '  <td>' . $data['portfolio'] . '</td>';
echo '  <td>' . $data['capital_in'] . '</td>';
echo '  <td>' . $data['capital_has'] . '</td>';
echo '  <td>' . $data['total'] . '</td>';
echo '  <td>' . $data['percent'] . '</td>';
echo '</tr>';