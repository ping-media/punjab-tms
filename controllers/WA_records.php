<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Wa_records extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tms/WA_records_model', 'wa_records_model');
        $this->load->model('tms/trips_model');
        $this->load->helper('tms');
    }

    /* List all whatsapp records */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('tms', 'tables/wa_records'));
        }

        $data['title'] = _l('wa_records');
        $this->load->view('wa_records/manage', $data);
    }

    /* Get whatsapp record */
    public function get($id)
    {
        $wa_record = $this->wa_records_model->get_wa_record($id);
        
        if ($wa_record) {
            echo json_encode([
                'success' => true,
                'message' => $wa_record->message,
                'created_at' => $wa_record->created_at,
                'updated_at' => $wa_record->updated_at
            ]);
        } else {
            echo json_encode([
                'success' => false
            ]);
        }
    }

    /* Add new whatsapp record */
    public function wa_record($id = '')
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            // print_r($data);
            // die();
            
            if ($id == '') {
                $id = $this->wa_records_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('wa_record')));
                    redirect(admin_url('tms/wa_records'));
                }
            } else {
                $success = $this->wa_records_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('wa_record')));
                }
                redirect(admin_url('tms/wa_records'));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('wa_record'));
            // $data['wa_records'] = $this->wa_records_model->get();
        } else {
            $data['wa_record'] = $this->wa_records_model->get($id);
            $title = _l('edit', _l('wa_record'));
            $data['wa_records'] = $this->wa_records_model->get();
        }

        $data['title'] = $title;
        $data['trips'] = $this->trips_model->get();
        $this->load->view('wa_records/wa_record', $data);
    }

    /* Delete party */
    public function delete($id)
    {
        if (!has_permission('tms', '', 'delete')) {
            access_denied('tms');
        }

        $response = $this->wa_records_model->delete($id);
        if ($response['success']) {
            set_alert('success', $response['message']);
        } else {
            set_alert('warning', $response['message']);
        }

        redirect(admin_url('tms/wa_records'));
    }
} 