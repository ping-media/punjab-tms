<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Transport Management System
Description: A comprehensive transport management system for Perfex CRM
Version: 1.0.0
Requires at least: 2.3.*
*/

// Load module hooks
require_once(__DIR__ . '/hooks.php');

function load_tms_admin_styles()
{
    echo '<link href="' . module_dir_url('tms', 'assets/css/tms-style.css') . '"  rel="stylesheet" type="text/css" />';
}

// Register the function to load styles in the admin area
hooks()->add_action('app_admin_head', 'load_tms_admin_styles');


hooks()->add_action('admin_init', 'tms_module_init_menu_items');
hooks()->add_action('admin_init', 'tms_permissions');

/**
 * Register activation module hook
 */
register_activation_hook('tms', 'tms_module_activation_hook');

function tms_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files
 */
register_language_files('tms', ['tms']);

/**
 * Add module permissions
 */
function tms_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('tms', $capabilities, _l('transport_management'));
}

/**
 * Add module menu items
 */
function tms_module_init_menu_items()
{
    $CI = &get_instance();

    if (has_permission('tms', '', 'view')) {

        // Drivers Submenu
        $CI->app_menu->add_sidebar_menu_item('drivers', [
            'slug'     => 'drivers',
            'name'     => _l('drivers'),
            'href'     => admin_url('tms/drivers'),
            'position' => 1,
            'icon'     => 'fa fa-user'
        ]);

        // Vehicle Owners  Submenu
        $CI->app_menu->add_sidebar_menu_item('vehicle_owners', [
            'slug'     => 'vehicle_owners',
            'name'     => _l('vehicle_owners'),
            'href'     => admin_url('tms/vehicle_owners'),
            'position' => 2,
            'icon'     => 'fa-regular fa-user'
        ]);

        // Consignors Submenu
        $CI->app_menu->add_sidebar_menu_item('consignors', [
            'slug'     => 'consignors',
            'name'     => _l('consignors'),
            'href'     => admin_url('tms/consignors'),
            'position' => 5,
            'icon'     => 'fa fa-users'
        ]);

        // Consignee Submenu
        $CI->app_menu->add_sidebar_menu_item('consignees', [
            'slug'     => 'consignees',
            'name'     => _l('consignees'),
            'href'     => admin_url('tms/consignees'),
            'position' => 5,
            'icon'     => 'fa fa-users'
        ]);

        // Parties Submenu
        $CI->app_menu->add_sidebar_menu_item('parties', [
            'slug'     => 'parties',
            'name'     => _l('parties'),
            'href'     => admin_url('tms/parties'),
            'position' => 6,
            'icon'     => 'fa fa-users'
        ]);

       

        // Trips Submenu
        $CI->app_menu->add_sidebar_menu_item('trips', [
            'slug'     => 'trips',
            'name'     => _l('trips'),
            'href'     => admin_url('tms/trips'),
            'position' => 7,
            'icon'     => 'fa fa-truck'
        ]);

        // Trip Expenses Submenu
        $CI->app_menu->add_sidebar_menu_item('trip-expenses', [
            'slug'     => 'trip-expenses',
            'name'     => _l('trip_expenses'),
            'href'     => admin_url('tms/trip_expenses'),
            'position' => 7,
            'icon'     => 'fa fa-dollar-sign'
        ]);

        // Whatsapp Records Submenu
        $CI->app_menu->add_sidebar_menu_item('wa-records', [
            'slug'     => 'wa-records',
            'name'     => _l('wa_records'),
            'href'     => admin_url('tms/wa_records'),
            'position' => 7,
            'icon'     => 'fab fa-whatsapp'
        ]);

        

        // Party Trips Submenu
        // $CI->app_menu->add_sidebar_menu_item('party-trips', [
        //     'slug'     => 'party-trips',
        //     'name'     => _l('party_trips'),
        //     'href'     => admin_url('tms/party-trips'),
        //     'position' => 8,
        //     'icon'     => 'fa fa-truck'
        // ]);

        // Trips Invoices
        $CI->app_menu->add_sidebar_menu_item('freight-invoices', [
            'slug'     => 'freight-invoices',
            'name'     => _l('freight_invoices'),
            'href'     => admin_url('tms/freight_invoices'),
            'position' => 8,
            'icon'     => 'fa fa-file-invoice'
        ]);

        // POD Records
        $CI->app_menu->add_sidebar_menu_item('pod-records', [
            'slug'     => 'pod-records',
            'name'     => _l('pod_records'),
            'href'     => admin_url('tms/pod_records'),
            'position' => 9,
            'icon'     => 'fa fa-file-invoice'
        ]);

        
    }
} 