<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'company',
    'contact_person',
    'phone',
    'gst',
    'city',
    'state',
    'status',
    'created_at'
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'tms_parties';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    // ID
    $row[] = $aRow['id'];

    // Company with link
    $action = '<a href="' . admin_url('tms/parties/party/' . $aRow['id']) . '">View | Edit</a>';
    
    $row[] = $aRow['company'] . '<div class="row-options">' . $action . '</div>';

    // Contact Person
    $row[] = $aRow['contact_person'];
    
    // Phone
    $row[] = $aRow['phone'];
    
    // GST
    $row[] = '<span class="text-uppercase">' . $aRow['gst'] . '</span>';
    
    // City
    $row[] =  $aRow['city'];
    
    // State
    $row[] = $aRow['state'];
    
    // Status toggle
    $checked = $aRow['status'] == 1 ? 'checked' : '';
    $row[] = '<div class="onoffswitch">
        <input type="checkbox" 
            data-switch-url="' . admin_url() . 'tms/parties/change_status" 
            name="onoffswitch" 
            class="onoffswitch-checkbox status-switch" 
            id="c_' . $aRow['id'] . '" 
            data-id="' . $aRow['id'] . '" 
            ' . $checked . '>
        <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
    </div>';

    // Created Date
    $row[] = _dt($aRow['created_at']);
    
    // Options
    $options = '';
    
    if (has_permission('tms', '', 'edit')) {
        $options .= icon_btn('tms/parties/party/' . $aRow['id'], 'fa fa-pencil', 'btn-default', [
            'title' => _l('edit'),
            'style' => 'color: white; background-color: #03a9f4;'
        ]);
    }
    
    // $row[] = $options;

    $output['aaData'][] = $row;
}

echo json_encode($output);
die; 