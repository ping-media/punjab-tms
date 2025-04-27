<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'id',
    'name',
    'license_number',
    'phone',
    'city',
    'state',
    'status',
    'created_at'
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'tms_drivers';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], []);

$output  = $result['output'];
$rResult = $result['rResult'];

if (empty($rResult)) {
    $output['aaData'] = [];
    echo json_encode($output);
    die();
}

foreach ($rResult as $aRow) {
    $row = [];

    // ID
    $row[] = $aRow['id'];

    // Driver Name with link
    $edit_link = '<a href="' . admin_url('tms/drivers/driver/' . $aRow['id']) . '">View | Edit</a>';
    
    $row[] =  $aRow['name'] . '<div class="row-options"> ' . $edit_link . '</div>';
    
    // License Number
    $row[] = $aRow['license_number'];
    
    // Phone
    $row[] = $aRow['phone'];
    
    // City
    $row[] = $aRow['city'];
    
    // State
    $row[] = $aRow['state'];
    
    // Status toggle
    $checked = $aRow['status'] == 1 ? 'checked' : '';
    $row[] = '<div class="onoffswitch">
        <input type="checkbox" 
            data-switch-url="' . admin_url() . 'tms/drivers/change_status" 
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
        $options .= icon_btn('tms/drivers/edit/' . $aRow['id'], 'fa fa-pencil', 'btn-default', [
            'title' => _l('edit'),
            'style' => 'color: white; background-color: #03a9f4;'
        ]);
    }
    
    // $row[] = $options;

    $output['aaData'][] = $row;
}

echo json_encode($output);
die; 