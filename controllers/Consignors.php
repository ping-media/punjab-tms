<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Consignors extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tms/consignors_model');
        $this->load->helper('tms');
    }

    /* List all consignors */
    public function index()
    {
        if (!has_permission('tms', '', 'view')) {
            access_denied('tms');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('tms', 'tables/consignors'));
        }

        $data['title'] = _l('consignors');
        $this->load->view('consignors/manage', $data);
    }

    /* Get consignor */
    public function get($id)
    {
        if (!has_permission('tms', '', 'view')) {
            ajax_access_denied();
        }

        $consignor = $this->consignors_model->get_consignor_trips($id);
        
        if ($consignor) {
            echo json_encode([
                'success' => true,
                'address' => $consignor->address,
                'city' => $consignor->city,
                'state' => $consignor->state,
                'gst' => $consignor->gst,
                'trips' => $consignor->trips
            ]);
        } else {
            echo json_encode([
                'success' => false
            ]);
        }
    }

    /* Add new consignor */
    public function create()
    {
        if (!has_permission('tms', '', 'create')) {
            access_denied('tms');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($this->consignors_model->add($data)) {
                set_alert('success', _l('added_successfully', _l('consignor')));
            }
            redirect(admin_url('tms/consignors'));
        }

        $data['title'] = _l('new_consignor');
        $this->load->view('consignors/create', $data);
    }

    /* Edit consignor */
    public function edit($id)
    {
        if (!has_permission('tms', '', 'edit')) {
            access_denied('tms');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($this->consignors_model->update($data, $id)) {
                set_alert('success', _l('updated_successfully', _l('consignor')));
            }
            redirect(admin_url('tms/consignors'));
        }

        $data['consignor'] = $this->consignors_model->get($id);
        $data['title'] = _l('edit_consignor');
        $this->load->view('consignors/edit', $data);
    }

    /* Delete consignor */
    public function delete($id)
    {
        if (!has_permission('tms', '', 'delete')) {
            access_denied('tms');
        }

        $response = $this->consignors_model->delete($id);
        if ($response['success']) {
            set_alert('success', $response['message']);
        } else {
            set_alert('warning', $response['message']);
        }

        redirect(admin_url('tms/consignors'));
    }

    /* Change consignor status / ajax */
    public function change_status()
    {
        if (!has_permission('tms', '', 'edit')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');

            $success = $this->consignors_model->change_status($id, $status);

            if ($success) {
                $message = _l('consignor_status_changed_success');
                echo json_encode([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                $message = _l('consignor_status_changed_fail');
                echo json_encode([
                    'success' => false,
                    'message' => $message
                ]);
            }
        }
    }
} 