<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'gr_number',
    'vehicle_no',
    'vehicle_owner',
    'driver_phone',
    'in_datetime',
    'out_datetime',
    'onplace_status',
    'whatsapp_pod',
    'received_pod',
    'paid_pod',
    'created_at'
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'tms_wa_records';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // ID
    $row[] = $aRow['id'];

    // Company with link
    $row[] = '<a href="' . admin_url('tms/wa_records/wa_record/' . $aRow['id']) . '">' . str_pad($aRow['gr_number'], 4, '0', STR_PAD_LEFT)  . '</a>';
    
    // Contact Person
    $row[] = $aRow['vehicle_no'];
    
    // Phone
    $row[] = $aRow['vehicle_owner'];
    
    // GST
    $row[] = $aRow['driver_phone'];
    
    // City
    $row[] =  $aRow['in_datetime'];
    
    // State
    $row[] = $aRow['out_datetime'];
    
    // On Place Status
    $row[] = $aRow['onplace_status'];
    
    // Whatsapp POD
    $row[] = $aRow['whatsapp_pod'];
    
    // Received POD
    $row[] = $aRow['received_pod'];
    
    // Paid POD
    $row[] = $aRow['paid_pod'];

    // Created Date
    $row[] = _dt($aRow['created_at']);
    
    // Options
    $options = '';
    
    if (has_permission('tms', '', 'edit')) {
        $options .= icon_btn('tms/wa_records/wa_record/' . $aRow['id'], 'fa fa-pencil', 'btn-default', [
            'title' => _l('edit'),
            'style' => 'color: white; background-color: #03a9f4;'
        ]);
    }
    
    $row[] = $options;

    $output['aaData'][] = $row;
}

echo json_encode($output);
die; 