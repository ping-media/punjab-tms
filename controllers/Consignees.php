<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Consignees extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tms_model');
        $this->load->model('consignees_model');
        $this->load->helper('tms');
    }

    public function index()
    {
        if (!has_permission('tms', '', 'view')) {
            access_denied('tms');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('tms', 'tables/consignees'));
        }

        $data['title'] = _l('consignees');
        $this->load->view('consignees/manage', $data);
    }

     /* Get consignee */
     public function get($id)
     {
         if (!has_permission('tms', '', 'view')) {
             ajax_access_denied();
         }
 
         $consignee = $this->consignees_model->get_consignee_trips($id);
         
         if ($consignee) {
             echo json_encode([
                 'success' => true,
                 'address' => $consignee->address,
                 'city' => $consignee->city,
                 'state' => $consignee->state,
                 'gst' => $consignee->gst,
                 'trips' => $consignee->trips
             ]);
         } else {
             echo json_encode([
                 'success' => false
             ]);
         }
     }

    public function create()
    {
        if (!has_permission('tms', '', 'create')) {
            access_denied('tms');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($this->consignees_model->add($data)) {
                set_alert('success', _l('added_successfully', _l('consignee')));
                redirect(admin_url('tms/consignees'));
            }
        }

        $data['title'] = _l('new_consignee');
        $this->load->view('consignees/create', $data);
    }

    public function edit($id)
    {
        if (!has_permission('tms', '', 'edit')) {
            access_denied('tms');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            if ($this->consignees_model->update($data, $id)) {
                set_alert('success', _l('updated_successfully', _l('consignee')));
            }
            redirect(admin_url('tms/consignees'));
        }

        $data['consignee'] = $this->consignees_model->get($id);
        $data['title'] = _l('edit_consignee');
        $this->load->view('consignees/edit', $data);
    }

    public function delete($id)
    {
        if (!has_permission('tms', '', 'delete')) {
            access_denied('tms');
        }

        $response = $this->consignees_model->delete($id);
        if ($response['success']) {
            set_alert('success', $response['message']);
        } else {
            set_alert('warning', $response['message']);
        }
        redirect(admin_url('tms/consignees'));
    }

    public function change_status()
    {
        if (!has_permission('tms', '', 'edit')) {
            ajax_access_denied();
        }

        if ($this->input->post()) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');

            $success = $this->consignees_model->change_status($id, $status);

            if ($success) {
                $message = _l('consignees_status_changed_success');
                echo json_encode([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                $message = _l('consignees_status_changed_fail');
                echo json_encode([
                    'success' => false,
                    'message' => $message
                ]);
            }
        }
    }

    /* Get consignee address */
    // public function get_address($id)
    // {
    //     if (!has_permission('tms', '', 'view')) {
    //         ajax_access_denied();
    //     }

    //     $consignee = $this->consignees_model->get($id);
        
    //     if ($consignee) {
    //         echo json_encode([
    //             'success' => true,
    //             'address' => $consignee->address,
    //             'city' => $consignee->city,
    //             'state' => $consignee->state
    //         ]);
    //     } else {
    //         echo json_encode([
    //             'success' => false
    //         ]);
    //     }
    // }
} 