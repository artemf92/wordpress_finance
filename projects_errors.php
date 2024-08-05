<? 
require_once('wp-load.php');

function check_project_investments() {
    $args = array(
        'post_type' => 'projects',
        'numberposts' => -1,
        'meta_key' => 'status',
        'meta_value' => '1'
    );
    
    $projects = get_posts($args);
    $output = array();
    
    foreach ($projects as $project) {
        $project_id = $project->ID;
        $project_sum = floatval(get_post_meta($project_id, 'settings_project_sum', true));
        $investors = get_field('investory_investors', $project_id);
        
        $total_invest = 0;
        if (!empty($investors) && is_array($investors)) {
            foreach ($investors as $investor) {
                $invest = isset($investor['invest']) ? floatval($investor['invest']) : 0;
                $invest_over = isset($investor['invest_over']) ? floatval($investor['invest_over']) : 0;
                $total_invest += $invest + $invest_over;
            }
        }
        
        $matches = (abs($total_invest - $project_sum) < 0.01);

        if (!$matches && $total_invest > 0) {
            $output[] = array(
                'project_name' => get_the_title($project_id),
                'project_id' => $project_id,
                'project_sum' => $project_sum,
                'total_invest' => $total_invest,
                'matches' => $matches,
            );
        }
    }
    
    echo 'Неправильных проектов: ' . count($output);
    foreach ($output as $result) {
        echo '<pre>';
        echo 'Проект: <a href="/projects/'.$result['project_id'].'/">' . $result['project_name'] . "</a></br>";
        echo "Сумма проекта: " . $result['project_sum']."</br>";
        echo "Инвестировано: " . $result['total_invest']."</br>";
        echo "____________________";
        echo '</pre>';
    }
}

check_project_investments();