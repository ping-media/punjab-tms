<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Drivers extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tms/drivers_model');
        $this->load->helper('tms');
    }

    /* List all drivers */
    public function index()
    {
        if (!has_permission('tms', '', 'view')) {
            access_denied('tms');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('tms', 'tables/drivers'));
        }

        $data['title'] = _l('drivers');
        $this->load->view('drivers/manage', $data);
    }

    /* Add new driver */
    // public function create()
    // {
    //     if (!has_permission('tms', '', 'create')) {
    //         access_denied('tms');
    //     }

    //     if ($this->input->post()) {
    //         $data = $this->input->post();
    //         if ($this->drivers_model->add($data)) {
    //             set_alert('success', _l('added_successfully', _l('driver')));
    //         }
    //         redirect(admin_url('tms/drivers'));
    //     }

    //     $data['title'] = _l('new_driver');
    //     $data['staff_members'] = $this->staff_model->get();
    //     $this->load->view('drivers/create', $data);
    // }

    /*  driver */
    public function driver($id = '')
    {
        if (!has_permission('tms', '', 'view')) {
            access_denied('tms');
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            if ($id == '') {
                $id = $this->drivers_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('driver')));
                    redirect(admin_url('tms/drivers'));
                }
            } else {
                $success = $this->drivers_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('driver')));
                }
                redirect(admin_url('tms/drivers'));
            }


        }

        if ($id == '') {
            $title = _l('add_new', _l('driver'));
            $data['drivers'] = $this->drivers_model->get();
        } else {
            $data['driver'] = $this->drivers_model->get($id);
            $title = _l('edit', _l('driver'));
            $data['drivers'] = $this->drivers_model->get();
        }

        $data['title'] = $title;
        $this->load->view('drivers/driver', $data);
    }

    /* Delete driver */
    public function delete($id)
    {
        if (!has_permission('tms', '', 'delete')) {
            access_denied('tms');
        }

        $response = $this->drivers_model->delete($id);
        if ($response['success']) {
            set_alert('success', $response['message']);
        } else {
            set_alert('warning', $response['message']);
        }

        redirect(admin_url('tms/drivers'));
    }

    /* View driver details */
    public function view($id)
    {
        if (!has_permission('tms', '', 'view')) {
            access_denied('tms');
        }

        $data['driver'] = $this->drivers_model->get($id);
        $data['driver_stats'] = $this->drivers_model->get_driver_statistics($id);
        $data['title'] = _l('driver_details');
        $this->load->view('drivers/view', $data);
    }
} 