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
$sTable       = db_prefix() . 'tms_consignees';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], []);

$output  = $result['output'];
$rResult = $result['rResult'];

$output['aaData'] = [];

if (!empty($rResult)) {
    foreach ($rResult as $aRow) {
        $row = [];

        $row[] = $aRow['id'];
        $action = '<a href="' . admin_url('tms/consignees/edit/' . $aRow['id']) . '">View | Edit</a>';
        $row[] = $aRow['company'] . '<div class="row-options">' . $action . '</div>';
        $row[] = $aRow['contact_person'];
        $row[] = $aRow['phone'];
        $row[] = '<span class="text-uppercase">' . $aRow['gst'] . '</span>';
        $row[] = $aRow['city'];
        $row[] = $aRow['state'];

        $checked = $aRow['status'] == 1 ? 'checked' : '';
        $row[] = '<div class="onoffswitch">
            <input type="checkbox" 
                data-switch-url="' . admin_url() . 'tms/consignees/change_status" 
                name="onoffswitch" 
                class="onoffswitch-checkbox status-switch" 
                id="c_' . $aRow['id'] . '" 
                data-id="' . $aRow['id'] . '" 
                ' . $checked . '>
            <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
        </div>';

        $row[] = _dt($aRow['created_at']);

        $options = '';
        if (has_permission('tms', '', 'edit')) {
            $options .= icon_btn('tms/consignees/edit/' . $aRow['id'], 'fa fa-pencil', 'btn-default', [
                'title' => _l('edit'),
                'style' => 'color: white; background-color: #03a9f4;'
            ]);
        }
        // if (has_permission('tms', '', 'delete')) {
        //     $options .= icon_btn('tms/consignees/delete/' . $aRow['id'], 'fa fa-trash', 'btn-danger _delete', [
        //         'title' => _l('delete'),
        //         'style' => 'color: white;'
        //     ]);
        // }
        // $row[] = $options;

        $output['aaData'][] = $row;
    }
}

echo json_encode($output);
die(); 