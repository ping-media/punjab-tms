<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Trip_expenses extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tms/Trip_expenses_model', 'trip_expenses_model');
        $this->load->model('tms/trips_model');
        $this->load->helper('tms');
    }

    /* List all trip expenses */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('tms', 'tables/trip_expenses'));
        }

        $data['title'] = _l('trip_expenses');
        $this->load->view('trip_expenses/manage', $data);
    }

    /* Get trip expense */
    public function get($id)
    {
        $trip_expense = $this->trip_expenses_model->get_trip_expense($id);
        
        if ($trip_expense) {
            echo json_encode([
                'success' => true,
                'message' => $trip_expense->message,
                'created_at' => $trip_expense->created_at,
                'updated_at' => $trip_expense->updated_at
            ]);
        } else {
            echo json_encode([
                'success' => false
            ]);
        }
    }

    /* Add new trip expense */
    public function expense($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            // print_r($data);
            // die();
            
            if ($id == '') {
                $id = $this->trip_expenses_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('trip_expense')));
                    redirect(admin_url('tms/trip_expenses'));
                }
            } else {
                $success = $this->trip_expenses_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('trip_expense')));
                }
                redirect(admin_url('tms/trip_expenses'));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('trip_expense'));
            // $data['wa_records'] = $this->wa_records_model->get();
        } else {
            $data['trip_expense'] = $this->trip_expenses_model->get($id);
            $title = _l('edit', _l('trip_expense'));
            $data['trip_expenses'] = $this->trip_expenses_model->get();
        }

        $data['title'] = $title;
        $data['trips'] = $this->trips_model->get();
        $this->load->view('trip_expenses/expense', $data);
    }

    /* Delete trip expense */
    public function delete($id)
    {
        if (!has_permission('tms', '', 'delete')) {
            access_denied('tms');
        }

        $response = $this->trip_expenses_model->delete($id);
        if ($response['success']) {
            set_alert('success', $response['message']);
        } else {
            set_alert('warning', $response['message']);
        }

        redirect(admin_url('tms/trip_expenses'));
    }
} 