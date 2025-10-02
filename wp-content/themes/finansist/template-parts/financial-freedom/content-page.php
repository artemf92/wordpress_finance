<?php
/**
 * Financial Freedom Page Content
 */

// Получаем данные для расчета
$financial_freedom_data = getFinancialFreedomData();
$goals = [1000000, 5000000, 10000000, 20000000, 35000000, 60000000]; // Цели в рублях
?>

<div class="financial-freedom-container">
    
    <!-- Информация о текущем состоянии -->
    <div class="current-status m-b-3">
        <div class="row">
            <div class="col-md-4">
                <div class="status-card">
                    <h4>Текущий капитал</h4>
                    <div class="amount"><?php echo get_formatted_number($financial_freedom_data['current_capital'], ' ₽', 0); ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="status-card">
                    <h4>Среднее пополнение (6 мес.)</h4>
                    <div class="amount"><?php echo get_formatted_number($financial_freedom_data['avg_monthly_contribution'], ' ₽', 0); ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="status-card">
                    <h4>Следующая цель</h4>
                    <div class="amount"><?php echo get_formatted_number($financial_freedom_data['next_goal'], ' ₽', 0); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Прогноз достижения целей -->
    <div class="goals-forecast m-b-3">
        <h3>Прогноз достижения целей</h3>
        <div class="table-responsive">
            <table class="table table-bordered goals-table">
                <thead>
                    <tr>
                        <th>Цель</th>
                        <th>Текущий капитал</th>
                        <th>Необходимо накопить</th>
                        <th>Месяцев до цели</th>
                        <th>Дата достижения</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($goals as $goal): ?>
                        <?php 
                        $goal_data = calculateGoalProgress($goal, $financial_freedom_data);
                        ?>
                        <tr class="<?php echo $goal_data['achieved'] ? 'goal-achieved' : ''; ?>">
                            <td><?php echo get_formatted_number($goal, ' ₽', 0); ?></td>
                            <td><?php echo get_formatted_number($financial_freedom_data['current_capital'], ' ₽', 0); ?></td>
                            <td><?php echo get_formatted_number($goal_data['needed'], ' ₽', 0); ?></td>
                            <td><?php echo $goal_data['months']; ?></td>
                            <td><?php echo $goal_data['date']; ?></td>
                            <td>
                                <?php if ($goal_data['achieved']): ?>
                                    <span class="badge badge-success">✅ Готово</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">В процессе</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Таблица с месячными данными -->
    <div class="monthly-data m-b-3">
        <h3>Месячные данные портфеля</h3>
        <div class="table-responsive">
            <table class="table table-striped monthly-table" id="monthly-table">
                <thead>
                    <tr>
                        <th>Месяц. Год</th>
                        <th>Капитал (портфель + сверх)</th>
                        <th>Пополнение за месяц</th>
                        <?php foreach ($goals as $goal): ?>
                            <th><?php echo get_formatted_number($goal, ' ₽', 0); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody id="monthly-table-body">
                    <!-- Данные будут загружены через AJAX -->
                </tbody>
            </table>
        </div>
        
        <!-- Кнопка "Показать больше" -->
        <div class="text-center m-t-3" id="load-more-container" style="display: none;">
            <button class="btn btn-primary" id="load-more-btn" data-page="1" data-per-page="12">
                Показать больше
            </button>
        </div>
    </div>
</div>

<!-- JavaScript для AJAX подгрузки -->
<script>
jQuery(document).ready(function($) {
    let isLoading = false;
    let allDataLoaded = false;
    
    // Загружаем первые данные
    loadMonthlyData(1, 12);
    
    // Обработчик кнопки "Показать больше"
    $('#load-more-btn').on('click', function() {
        if (!isLoading && !allDataLoaded) {
            const currentPage = parseInt($(this).data('page'));
            const perPage = parseInt($(this).data('per-page'));
            loadMonthlyData(currentPage + 1, perPage);
        }
    });
    
    function loadMonthlyData(page, perPage) {
        if (isLoading) return;
        
        isLoading = true;
        $('#load-more-btn').prop('disabled', true).text('Загрузка...');
        
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: {
                action: 'load_financial_freedom_data',
                page: page,
                per_page: perPage,
                nonce: '<?php echo wp_create_nonce('financial_freedom_nonce'); ?>'
            },
            success: function(response) {
                if (response.success) {
                    if (page === 1) {
                        $('#monthly-table-body').html(response.data.html);
                    } else {
                        $('#monthly-table-body').append(response.data.html);
                    }
                    
                    // Обновляем состояние кнопки
                    if (response.data.has_more) {
                        $('#load-more-btn').data('page', page);
                        $('#load-more-container').show();
                    } else {
                        allDataLoaded = true;
                        $('#load-more-container').hide();
                    }
                } else {
                    console.error('Ошибка загрузки данных:', response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX ошибка:', error);
            },
            complete: function() {
                isLoading = false;
                $('#load-more-btn').prop('disabled', false).text('Показать больше');
            }
        });
    }
});
</script>

<style>
.financial-freedom-container {
    padding: 20px 0;
}

.status-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
}

.status-card h4 {
    color: #6c757d;
    font-size: 14px;
    margin-bottom: 10px;
}

.status-card .amount {
    font-size: 24px;
    font-weight: bold;
    color: #28a745;
}

.goals-table th,
.goals-table td {
    text-align: center;
    vertical-align: middle;
}

.goal-achieved {
    background-color: #d4edda !important;
}

.goal-achieved td {
    color: #155724;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.monthly-table th {
    text-align: center;
    background-color: #f8f9fa;
}

.monthly-table td {
    text-align: center;
    vertical-align: middle;
}

.m-t-3 {
    margin-top: 20px;
}

.m-b-3 {
    margin-bottom: 20px;
}
</style>
