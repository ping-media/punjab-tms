<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Parties extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tms/parties_model');
        $this->load->helper('tms');
    }

    /* List all parties */
    public function index()
    {
        if (!has_permission('tms', '', 'view')) {
            access_denied('tms');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('tms', 'tables/parties'));
        }

        $data['title'] = _l('parties');
        $this->load->view('parties/manage', $data);
    }

    /* Get party */
    public function get($id)
    {
        $party = $this->parties_model->get_party($id);
        
        if ($party) {
            echo json_encode([
                'success' => true,
                'address' => $party->address,
                'city' => $party->city,
                'state' => $party->state,
                'gst' => $party->gst,
                'trips' => $party->trips
            ]);
        } else {
            echo json_encode([
                'success' => false
            ]);
        }
    }

    /* Add new party */
    public function party($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            
            if ($id == '') {
                $id = $this->parties_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('party')));
                    redirect(admin_url('tms/parties'));
                }
            } else {
                $success = $this->parties_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('party')));
                }
                redirect(admin_url('tms/parties'));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('party'));
            $data['parties'] = $this->parties_model->get();
        } else {
            $data['party'] = $this->parties_model->get($id);
            $title = _l('edit', _l('party'));
            $data['parties'] = $this->parties_model->get();
        }

        $data['title'] = $title;
        $this->load->view('parties/party', $data);
    }

    /* Delete party */
    public function delete($id)
    {
        if (!has_permission('tms', '', 'delete')) {
            access_denied('tms');
        }

        $response = $this->parties_model->delete($id);
        if ($response['success']) {
            set_alert('success', $response['message']);
        } else {
            set_alert('warning', $response['message']);
        }

        redirect(admin_url('tms/parties'));
    }

    /* Change party status / ajax */
    public function change_status()
    {
        if (!has_permission('tms', '', 'edit')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');

            $success = $this->parties_model->change_status($id, $status);

            if ($success) {
                $message = _l('party_status_changed_success');
                echo json_encode([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                $message = _l('party_status_changed_fail');
                echo json_encode([
                    'success' => false,
                    'message' => $message
                ]);
            }
        }
    }
} 