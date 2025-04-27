<?php

defined('BASEPATH') or exit('No direct script access allowed');

hooks()->add_action('app_init', 'tms_change_default_url_to_admin');
function tms_change_default_url_to_admin()
{
    $CI = &get_instance();

    if (!is_client_logged_in() && !$CI->uri->segment(1)) {
        redirect(site_url('admin/authentication'));
    }
}

