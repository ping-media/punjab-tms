<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'gr_number',
    'type',
    'value',
    'expense_date',
    'created_at'
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'tms_trip_expenses';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // ID
    $row[] = $aRow['id'];

    $action = '<a href="' . admin_url('tms/trip_expenses/expense/' . $aRow['id']) . '">View | Edit</a>';

    // GR Number
    $row[] = str_pad($aRow['gr_number'], 4, '0', STR_PAD_LEFT) . '<div class="row-options">' . $action . '</div>';

    // Type
    $row[] = $aRow['type'];

    // Value
    $row[] = !empty($aRow['value']) ? format_rupee($aRow['value']) : format_rupee(0);
    
    // Expense Date
    $row[] = $aRow['expense_date'];

    // Created Date
    $row[] = _dt($aRow['created_at']);
    
    // $row[] = $options;

    $output['aaData'][] = $row;
}

echo json_encode($output);
die; 