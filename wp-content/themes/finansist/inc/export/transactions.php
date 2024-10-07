<?
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Number;

add_action('wp_ajax_export_transactions', 'export_transactions_callback');

function export_transactions_callback() {
    if (defined('CBXPHPSPREADSHEET_PLUGIN_NAME') && file_exists(CBXPHPSPREADSHEET_ROOT_PATH . 'lib/vendor/autoload.php')) {
        require_once(CBXPHPSPREADSHEET_ROOT_PATH . 'lib/vendor/autoload.php');

        $directory = ABSPATH . 'tmp/export/';
        $fileName = "exp_tr_" . date('d_m_Y_H_i') . substr(hash('md5', getUserID()), 0, 5) .  ".xlsx";
        $xls = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = setupSheet($xls);
        
        $arTransactions = getTransactionsData();
        if (empty($arTransactions)) {
          echo json_encode(['success' => false, 'msg' => 'Транзакций нет']);
          wp_die();
        }
        $data = prepareData($arTransactions);
        addDataToSheet($sheet, $data);
        addHyperlinksToProjects($sheet, $arTransactions);

        applyStyles($sheet, $data);
        saveSpreadsheet($xls, $fileName, $directory);

        echo json_encode(['success' => true, 'msg' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $directory).$fileName]);
        wp_die();
    }
}

function setupSheet($xls) {
    $sheet = $xls->getActiveSheet();
    $sheet->setTitle('Экспорт транзакций');
    $header = ['№', 'Имя транзакции', 'Проект', 'Сумма транзакции', 'Дата создания', 'Уникальный номер транзакции'];
    $sheet->fromArray($header, null, 'A2');
    
    $sheet->getColumnDimension("A")->setAutoSize(true);
    $sheet->getColumnDimension("B")->setWidth(280, 'pt');
    $sheet->getColumnDimension("C")->setWidth(200, 'pt');
    $sheet->getColumnDimension("D")->setAutoSize(true);
    $sheet->getColumnDimension("E")->setWidth(120, 'pt');
    $sheet->getColumnDimension("F")->setWidth(90, 'pt');
    $sheet->getRowDimension(2)->setRowHeight(40, 'pt');

    return $sheet;
}

function getTransactionsData() {
    $query = $_REQUEST;
    unset($query['action']);
    $query['posts_per_page'] = '-1';
    return get_posts($query);
}

function prepareData($arTransactions) {
    $data = [];
    $i = 1;
    foreach ($arTransactions as $tr) {
        $id = $tr->ID;
        $name = html_entity_decode(get_the_title($id), ENT_QUOTES, 'UTF-8');
        $project = get_post_meta($id, 'settings_project', true);
        $projectName = $project ? html_entity_decode(get_the_title($project), ENT_QUOTES, 'UTF-8') : '';
        $sum = floatval(get_post_meta($id, 'settings_sum', true));
        $created = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(get_post_timestamp($tr));
        $data[] = [$i++, $name, $projectName, $sum, $created, $id];
    }
    return $data;
}

function addDataToSheet($sheet, $data) {
    $sheet->fromArray($data, null, 'A3');
}

function addHyperlinksToProjects($sheet, $arTransactions) {
    for ($row = 3; $row <= count($arTransactions) + 2; $row++) {
        $projectId = $arTransactions[$row - 3]->ID;
        $projectUrl = get_permalink(get_post_meta($projectId, 'settings_project', true));
        if ($projectUrl) {
            $sheet->getCell('C' . $row)->getHyperlink()->setUrl($projectUrl);
            $sheet->getCell('C' . $row)->getHyperlink()->setTooltip('Перейти к проекту');
        }
    }
}

function applyStyles($sheet, $data) {
    $headerStyle = [
        'font' => ['bold' => true],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => ['rgb' => '000000']
            ]
        ]
    ];

    $bodyStyle = [
        'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ]
    ];

    $sheet->getStyle('A2:F2')->applyFromArray($headerStyle);

    $cells = ['A', 'B', 'C', 'D', 'E', 'F'];
    $rowCount = count($data) + 2;
    for ($j = 3; $j <= $rowCount; $j++) {
        foreach ($cells as $cell) {
            $sheet->getStyle($cell . $j)->applyFromArray($bodyStyle);
        }
    }

    $sheet->getStyle("A3:A" . $rowCount)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("D3:D" . $rowCount)->getNumberFormat()->setFormatCode((string) new Number(2, Number::WITHOUT_THOUSANDS_SEPARATOR));
    $sheet->getStyle("D3:D" . $rowCount)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    $sheet->getStyle("E3:E" . $rowCount)->getNumberFormat()->setFormatCode(
        \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DATETIME
    );
    $sheet->getStyle("F2")->getAlignment()->setWrapText(true);
}

function saveSpreadsheet($xls, $fileName, $directory) {
  $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($xls);
  
  if (!is_dir($directory)) {
    if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
      throw new \RuntimeException(sprintf('Directory "%s" was not created', $directory));
    }
  }
  
  $filePath = $directory . $fileName;
  
  $writer->save($filePath);
}