<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'tms_trips.id',
    db_prefix() . 'tms_trips.gr_number',
    db_prefix() . 'tms_consignors.company as consignor',
    db_prefix() . 'tms_drivers.name as driver',
    db_prefix() . 'tms_trips.vehicle_number',
    db_prefix() . 'tms_trips.from_city',
    db_prefix() . 'tms_trips.to_city',
    db_prefix() . 'tms_trips.status',
    db_prefix() . 'tms_trips.party_id',
    db_prefix() . 'tms_trips.total_freight',
    db_prefix() . 'tms_trips.created_at'
];

$sIndexColumn = db_prefix() . 'tms_trips.id';
$sTable       = db_prefix() . 'tms_trips';

$join = [
    'LEFT JOIN ' . db_prefix() . 'tms_consignors ON ' . db_prefix() . 'tms_consignors.id = ' . db_prefix() . 'tms_trips.consignor_id',
    'LEFT JOIN ' . db_prefix() . 'tms_drivers ON ' . db_prefix() . 'tms_drivers.id = ' . db_prefix() . 'tms_trips.driver_id'
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], [db_prefix() . 'tms_trips.id']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $view_whatsapp_records = '<a href="' . admin_url('tms/wa_records/') . '" >Whatsapp Records</a>';
    $view_pod_records = '<a href="' . admin_url('tms/pod_records/') . '" >POD Records</a>';

    $edit_trip = '<a href="' . admin_url('tms/trips/trip/' . $aRow[db_prefix() . 'tms_trips.id']) . '" >Edit</a>';
    // GR Number
    $row[] = 'GR-' . str_pad($aRow[db_prefix() . 'tms_trips.gr_number'], 5, '0', STR_PAD_LEFT) . '<div class="row-options">' . $edit_trip . ' | ' . $view_whatsapp_records . ' | ' . $view_pod_records . '</div>';
    
    // Consignor
    $row[] = $aRow['consignor'];
    
    // Driver
    $row[] = $aRow['driver'] . ' <br><b class="text-primary">' . $aRow[db_prefix() . 'tms_trips.vehicle_number'] . '</b>';
    
    // Vehicle Number
    $row[] = $aRow[db_prefix() . 'tms_trips.party_id'] == 0 ? '<span class="label label-info">Our Trip</span>' : '<span class="label label-success">Third Party Trip</span>';
    
    // From City
    $row[] = $aRow[db_prefix() . 'tms_trips.from_city'];
    
    // To City
    $row[] = $aRow[db_prefix() . 'tms_trips.to_city'];
    
    // Status
    $status_label = '';
    
    switch($aRow[db_prefix() . 'tms_trips.status']) {
        case 'in_transit':
            $status_label = '<span class="label label-info">In Transit</span>';
            break;
        case 'delivered':
            $status_label = '<span class="label label-success">Delivered</span>';
            break;
        case 'cancelled':
            $status_label = '<span class="label label-danger">Cancelled</span>';
            break;
        case 'delayed':
            $status_label = '<span class="label label-warning">Delayed</span>';
            break;
        default:
            $status_label = '<span class="label label-default">Scheduled</span>';
    }
    $row[] = $status_label;

    $row[] = !empty($aRow[db_prefix() . 'tms_trips.total_freight']) ? format_rupee($aRow[db_prefix() . 'tms_trips.total_freight']) : format_rupee(0);

    // Created Date
    $row[] = _dt($aRow[db_prefix() . 'tms_trips.created_at']);
    
    // Options
    // $options = '';
    // if (has_permission('tms', '', 'edit')) {
    //     $options .= icon_btn('tms/trips/trip/' . $aRow['id'], 'fa fa-pencil', 'btn-default', [
    //         'title' => _l('edit'),
    //         'style' => 'color: white; background-color: #03a9f4;'
    //     ]);
    // }
    // if (has_permission('tms', '', 'delete')) {
    //     $options .= icon_btn('tms/trips/delete/' . $aRow['id'], 'fa fa-trash', 'btn-danger _delete', [
    //         'title' => _l('delete'),
    //         'style' => 'color: white;'
    //     ]);
    // }
    // $row[] = $options;

    $output['aaData'][] = $row;
}

echo json_encode($output);
die; 