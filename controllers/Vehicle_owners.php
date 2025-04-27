<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Vehicle_owners extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tms/vehicle_owners_model');
        $this->load->helper('tms');
    }

    /* List all vehicle owners */
    public function index()
    {
        if (!has_permission('tms', '', 'view')) {
            access_denied('tms');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('tms', 'tables/vehicle_owners'));
        }

        $data['title'] = _l('vehicle_owners');
        $this->load->view('vehicle_owners/manage', $data);
    }

    /* Get vehicle owner */
    public function get($id)
    {
        $vehicle_owner = $this->vehicle_owners_model->get_vehicle_owner($id);
        
        if ($vehicle_owner) {
            echo json_encode([
                'success' => true,
                'name' => $vehicle_owner->name,
                'phone' => $vehicle_owner->phone,
                'email' => $vehicle_owner->email,
                'address' => $vehicle_owner->address,
                'city' => $vehicle_owner->city,
                'state' => $vehicle_owner->state,
                'gst' => $vehicle_owner->gst,
                'trips' => $vehicle_owner->trips
            ]);
        } else {
            echo json_encode([
                'success' => false
            ]);
        }
    }

    /* Add new vehicle owner */
    public function vehicle_owner($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            
            if ($id == '') {
                $id = $this->vehicle_owners_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('vehicle_owner')));
                    redirect(admin_url('tms/vehicle_owners'));
                }
            } else {
                $success = $this->vehicle_owners_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('vehicle_owner')));
                }
                redirect(admin_url('tms/vehicle_owners'));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('vehicle_owner'));
            $data['vehicle_owners'] = $this->vehicle_owners_model->get();
        } else {
            $data['vehicle_owner'] = $this->vehicle_owners_model->get($id);
            $title = _l('edit', _l('vehicle_owner'));
            $data['vehicle_owners'] = $this->vehicle_owners_model->get();
        }

        $data['title'] = $title;
        $this->load->view('vehicle_owners/vehicle_owner', $data);
    }

    /* Delete vehicle owner */
    public function delete($id)
    {
        if (!has_permission('tms', '', 'delete')) {
            access_denied('tms');
        }

        $response = $this->vehicle_owners_model->delete($id);
        if ($response['success']) {
            set_alert('success', $response['message']);
        } else {
            set_alert('warning', $response['message']);
        }

        redirect(admin_url('tms/vehicle_owners'));
    }

    /* Change vehicle owner status / ajax */
    public function change_status()
    {
        if (!has_permission('tms', '', 'edit')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');

            $success = $this->vehicle_owners_model->change_status($id, $status);

            if ($success) {
                $message = _l('vehicle_owner_status_changed_success');
                echo json_encode([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                $message = _l('vehicle_owner_status_changed_fail');
                echo json_encode([
                    'success' => false,
                    'message' => $message
                ]);
            }
        }
    }
} 