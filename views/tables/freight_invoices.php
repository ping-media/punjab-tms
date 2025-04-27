<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    db_prefix() . 'tms_freight_invoices.id as id',
    db_prefix() . 'tms_freight_invoices.invoice_number as invoice_number',
    db_prefix() . 'tms_consignors.company as company',
    db_prefix() . 'tms_freight_invoices.total_amount as total_amount',
    db_prefix() . 'tms_consignors.phone as phone',
    db_prefix() . 'tms_freight_invoices.status as status',
    db_prefix() . 'tms_freight_invoices.created_at as created_at',
    db_prefix() . 'tms_freight_invoices.due_date as due_date'
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'tms_freight_invoices';

$join = [
    'LEFT JOIN ' . db_prefix() . 'tms_consignors ON ' . db_prefix() . 'tms_consignors.id = ' . db_prefix() . 'tms_freight_invoices.consignor_id'
];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, [], []);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $invoice_url = admin_url('tms/freight_invoices/invoice/' . $aRow['id']);

    // Invoice Number
    $row[] = '<a href="' . $invoice_url . '" class="row-link">INV-' . str_pad($aRow['invoice_number'], 5, '0', STR_PAD_LEFT) . '</a>';
    
    // Consignor
    $row[] = '<a href="' . $invoice_url . '" class="row-link">' . $aRow['company'] . '</a>';

    // Total
    $row[] = '<a href="' . $invoice_url . '" class="row-link">' . number_format($aRow['total_amount'], 2) . '</a>';
    
    // phone
    $row[] = '<a href="' . $invoice_url . '" class="row-link">' . $aRow['phone'] . '</a>';
    
    // Status
    switch($aRow['status']) {
        case 'draft':
            $status_label = '<span class="label label-info">Draft</span>';
            break;
        case 'unpaid':
            $status_label = '<span class="label label-danger">Unpaid</span>';
            break;
        case 'paid':
            $status_label = '<span class="label label-success">Paid</span>';
            break;
        case 'cancelled':
            $status_label = '<span class="label label-danger">Cancelled</span>';
            break;
        default:
            $status_label = '<span class="label label-danger">Unpaid</span>';
    }
    $row[] = '<a href="' . $invoice_url . '" class="row-link">' . $status_label . '</a>';

    // Created Date
    $row[] = '<a href="' . $invoice_url . '" class="row-link">' . _dt($aRow['created_at']) . '</a>';

    // Due Date
    $row[] = '<a href="' . $invoice_url . '" class="row-link">' . $aRow['due_date'] . '</a>';
    
    // Options
    $options = '';
    if (has_permission('tms', '', 'edit')) {
        $options .= icon_btn('tms/freight_invoices/pdf/' . $aRow['id'] . '?output_type=I', 'fa fa-file-pdf', 'btn-default', [
            'title' => _l('pdf'),
            'style' => 'color: white; background-color:rgb(32, 113, 16);',
            'target' => '_blank'
        ]);
    }

    if (has_permission('tms', '', 'edit')) {
        $options .= icon_btn('tms/freight_invoices/invoice/' . $aRow['id'], 'fa fa-pencil', 'btn-default', [
            'title' => _l('edit'),
            'style' => 'color: white; background-color: #03a9f4;'
        ]);
    }
    
    // if (has_permission('tms', '', 'delete')) {
    //     $options .= icon_btn('tms/freight_invoices/delete/' . $aRow['id'], 'fa fa-trash', 'btn-danger _delete', [
    //         'title' => _l('delete'),
    //         'style' => 'color: white;'
    //     ]);
    // }
    $row[] = $options;

    $output['aaData'][] = $row;
}

echo json_encode($output);
die; 