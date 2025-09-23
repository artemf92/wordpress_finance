<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Получает данные для расчета финсвободы
 */
function getFinancialFreedomData() {
    global $wpdb;
    
    $userID = getFinancialFreedomUserID();
    
    // Получаем текущий капитал пользователя
    $current_capital = getCurrentUserCapital($userID);
    
    // Получаем среднее пополнение за последние 6 месяцев
    $avg_monthly_contribution = getAverageMonthlyContribution($userID);
    
    // Определяем следующую цель
    $goals = [1000000, 5000000, 10000000, 20000000, 35000000, 60000000];
    $next_goal = getNextGoal($current_capital, $goals);
    
    return [
        'current_capital' => $current_capital,
        'avg_monthly_contribution' => $avg_monthly_contribution,
        'next_goal' => $next_goal,
        'user_id' => $userID
    ];
}

/**
 * Получает ID пользователя для расчета финсвободы
 */
function getFinancialFreedomUserID() {
    return get_query_var('user_id') !== '' ? get_query_var('user_id') : get_current_user_id();
}

/**
 * Получает текущий капитал пользователя
 */
function getCurrentUserCapital($userID) {
    global $wpdb;
    
    // Получаем последние данные пользователя
    $result = $wpdb->get_row($wpdb->prepare("
        SELECT user_money, user_contributed, user_overdep, user_refund
        FROM af_profit_data 
        WHERE user_id = %d 
        ORDER BY date DESC 
        LIMIT 1
    ", $userID));
    
    if ($result) {
        return floatval($result->user_money) + floatval($result->user_contributed) + floatval($result->user_overdep) + floatval($result->user_refund);
    }
    
    return 0;
}

/**
 * Получает среднее пополнение за последние 6 месяцев
 */
function getAverageMonthlyContribution($userID) {
    global $wpdb;
    
    // Получаем транзакции пополнения (тип 12) за последние 6 месяцев
    $six_months_ago = date('Y-m-d', strtotime('-6 months'));
    
    $transactions = $wpdb->get_results($wpdb->prepare("
        SELECT 
            DATE_FORMAT(p.post_date, '%Y-%m') as month,
            SUM(CAST(pm_sum.meta_value AS DECIMAL(10,2))) as total
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm_user ON p.ID = pm_user.post_id
        INNER JOIN {$wpdb->postmeta} pm_type ON p.ID = pm_type.post_id
        INNER JOIN {$wpdb->postmeta} pm_sum ON p.ID = pm_sum.post_id
        WHERE p.post_type = 'transactions'
        AND p.post_status = 'publish'
        AND pm_user.meta_key = 'settings_investor'
        AND pm_user.meta_value = %d
        AND pm_type.meta_key = 'settings_transaction_type'
        AND pm_type.meta_value = '12'
        AND pm_sum.meta_key = 'settings_sum'
        AND p.post_date >= %s
        GROUP BY month
        ORDER BY month DESC
        LIMIT 6
    ", $userID, $six_months_ago));
    
    if (empty($transactions)) {
        return 0;
    }
    
    $total_contribution = 0;
    $months_count = count($transactions);
    
    foreach ($transactions as $transaction) {
        $total_contribution += floatval($transaction->total);
    }
    
    return $months_count > 0 ? $total_contribution / $months_count : 0;
}

/**
 * Определяет следующую цель для достижения
 */
function getNextGoal($current_capital, $goals) {
    foreach ($goals as $goal) {
        if ($current_capital < $goal) {
            return $goal;
        }
    }
    
    return end($goals); // Если все цели достигнуты, возвращаем последнюю
}

/**
 * Рассчитывает прогресс достижения цели
 */
function calculateGoalProgress($goal, $financial_data) {
    $current_capital = $financial_data['current_capital'];
    $avg_monthly_contribution = $financial_data['avg_monthly_contribution'];
    
    // Если цель уже достигнута
    if ($current_capital >= $goal) {
        return [
            'achieved' => true,
            'needed' => 0,
            'months' => 0,
            'date' => 'Достигнуто'
        ];
    }
    
    // Если нет пополнений, цель недостижима
    if ($avg_monthly_contribution <= 0) {
        return [
            'achieved' => false,
            'needed' => $goal - $current_capital,
            'months' => '∞',
            'date' => 'Недостижимо'
        ];
    }
    
    $needed = $goal - $current_capital;
    $months = floor($needed / $avg_monthly_contribution);
    
    // Рассчитываем дату достижения
    $target_date = date('Y-m-d', strtotime("+{$months} months"));
    $formatted_date = getFinancialFreedomMonthYear($target_date);
    
    return [
        'achieved' => false,
        'needed' => $needed,
        'months' => $months,
        'date' => $formatted_date
    ];
}

/**
 * Форматирует дату в формат "Месяц Год"
 */
function getFinancialFreedomMonthYear($date_string) {
    $date = new DateTime($date_string);
    $month_names = [
        1 => 'январь', 2 => 'февраль', 3 => 'март', 4 => 'апрель',
        5 => 'май', 6 => 'июнь', 7 => 'июль', 8 => 'август',
        9 => 'сентябрь', 10 => 'октябрь', 11 => 'ноябрь', 12 => 'декабрь'
    ];
    
    $month = $month_names[intval($date->format('n'))];
    $year = $date->format('Y');
    
    return $month . ' ' . $year;
}

/**
 * Получает месячные данные для таблицы
 */
function getFinancialFreedomMonthlyData($userID, $page = 1, $per_page = 12) {
    global $wpdb;
    
    // Получаем последние данные пользователя для расчета прогноза
    $latest_data = $wpdb->get_row($wpdb->prepare("
        SELECT 
            date,
            user_money,
            user_contributed,
            user_overdep,
            user_profit,
            user_refund
        FROM af_profit_data 
        WHERE user_id = %d 
        ORDER BY date DESC 
        LIMIT 1
    ", $userID));
    
    if (!$latest_data) {
        return [];
    }
    
    // Получаем среднее пополнение за последние 6 месяцев для прогноза
    $avg_contribution = getAverageMonthlyContribution($userID);
    
    // Начинаем с текущего месяца
    $current_date = new DateTime();
    $current_date->modify('first day of this month');
    
    // Добавляем offset месяцев для пагинации
    $offset_months = ($page - 1) * $per_page;
    $current_date->modify("+{$offset_months} months");
    
    $monthly_data = [];
    // Используем полную формулу капитала как в getCurrentUserCapital
    $current_capital = floatval($latest_data->user_money) + floatval($latest_data->user_contributed) + floatval($latest_data->user_overdep) + floatval($latest_data->user_refund);
    
    // Добавляем капитал за все предыдущие месяцы (за предыдущие страницы)
    $previous_months = ($page - 1) * $per_page;
    $current_capital += $previous_months * $avg_contribution;
    
    // Генерируем данные для будущих месяцев
    for ($i = 0; $i < $per_page; $i++) {
        $month_date = clone $current_date;
        $month_date->modify("+{$i} months");
        
        $month_year = $month_date->format('F Y');
        
        // Для будущих месяцев используем прогноз
        $monthly_contribution = $avg_contribution;
        
        // Увеличиваем капитал только на среднее пополнение
        $current_capital += $monthly_contribution;
        
        $monthly_data[] = [
            'month_year' => $month_year,
            'capital' => $current_capital,
            'contribution' => $monthly_contribution,
            'goals_status' => getGoalsStatusForMonth($current_capital)
        ];
    }
    
    return $monthly_data;
}

/**
 * Получает пополнение за конкретный месяц
 */
function getMonthlyContribution($userID, $month_start, $month_end) {
    global $wpdb;
    
    $result = $wpdb->get_var($wpdb->prepare("
        SELECT SUM(CAST(pm_sum.meta_value AS DECIMAL(10,2)))
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm_user ON p.ID = pm_user.post_id
        INNER JOIN {$wpdb->postmeta} pm_type ON p.ID = pm_type.post_id
        INNER JOIN {$wpdb->postmeta} pm_sum ON p.ID = pm_sum.post_id
        WHERE p.post_type = 'transactions'
        AND p.post_status = 'publish'
        AND pm_user.meta_key = 'settings_investor'
        AND pm_user.meta_value = %d
        AND pm_type.meta_key = 'settings_transaction_type'
        AND pm_type.meta_value = '12'
        AND pm_sum.meta_key = 'settings_sum'
        AND p.post_date >= %s
        AND p.post_date <= %s
    ", $userID, $month_start, $month_end));
    
    return $result ? floatval($result) : 0;
}

/**
 * Получает статус целей для конкретного месяца
 */
function getGoalsStatusForMonth($capital) {
    $goals = [1000000, 5000000, 10000000, 20000000, 35000000, 60000000];
    $status = [];
    
    foreach ($goals as $goal) {
        $status[] = $capital >= $goal ? '✅' : '⏳';
    }
    
    return $status;
}

/**
 * AJAX обработчик для загрузки месячных данных
 */
function financial_freedom_ajax_handler() {
    // Проверяем nonce
    if (!wp_verify_nonce($_POST['nonce'], 'financial_freedom_nonce')) {
        wp_die('Security check failed');
    }
    
    $userID = getFinancialFreedomUserID();
    $page = intval($_POST['page']);
    $per_page = intval($_POST['per_page']);
    
    $monthly_data = getFinancialFreedomMonthlyData($userID, $page, $per_page);
    
    // Проверяем, есть ли еще данные
    $total_count = getFinancialFreedomTotalCount($userID);
    $has_more = ($page * $per_page) < $total_count;
    
    // Формируем HTML для таблицы
    $html = '';
    foreach ($monthly_data as $data) {
        $html .= '<tr>';
        $html .= '<td>' . esc_html(getFinancialFreedomMonthYear($data['month_year'])) . '</td>';
        $html .= '<td>' . get_formatted_number($data['capital']) . '</td>';
        $html .= '<td>' . get_formatted_number($data['contribution']) . '</td>';
        
        foreach ($data['goals_status'] as $status) {
            $html .= '<td>' . $status . '</td>';
        }
        
        $html .= '</tr>';
    }
    
    wp_send_json_success([
        'html' => $html,
        'has_more' => $has_more,
        'total_count' => $total_count
    ]);
}

/**
 * Получает общее количество будущих месяцев для прогноза
 */
function getFinancialFreedomTotalCount($userID) {
    // Возвращаем количество месяцев для прогноза (например, 5 лет = 60 месяцев)
    return 60;
}

// Регистрируем AJAX обработчик
add_action('wp_ajax_load_financial_freedom_data', 'financial_freedom_ajax_handler');
add_action('wp_ajax_nopriv_load_financial_freedom_data', 'financial_freedom_ajax_handler');
