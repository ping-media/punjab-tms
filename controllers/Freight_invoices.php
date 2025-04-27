<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Freight_invoices extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('freight_invoices_model');
        $this->load->library(['form_validation']);
        $this->load->model('tms/consignors_model');
        $this->load->model('tms/trips_model');
        $this->load->helper('tms');
    }

    /**
     * List all trip invoices
     */
    public function index()
    {
        if (!has_permission('tms', '', 'view')) {
            access_denied('tms');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('tms', 'tables/freight_invoices'));
        }

        $data['title'] = _l('freight_invoices');
        $this->load->view('freight_invoices/manage', $data);
    }

    /**
     * Create or edit freight invoice
     */
    public function invoice($id = '')
    {
        // if (!has_permission('tms_freight_invoices', '', 'view')) {
        //     access_denied('tms_freight_invoices');
        // }

        if ($this->input->post()) {
            $data = $this->input->post();
            
            if ($id == '') {
                if (!has_permission('tms_freight_invoices', '', 'create')) {
                    access_denied('tms_freight_invoices');
                }

                $id = $this->freight_invoices_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('freight_invoice')));
                    redirect(admin_url('tms/freight_invoices'));
                }
            } else {
                if (!has_permission('tms_freight_invoices', '', 'edit')) {
                    access_denied('tms_freight_invoices');
                }
                $success = $this->freight_invoices_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('freight_invoice')));
                }
                redirect(admin_url('tms/freight_invoices'));
                // redirect(admin_url('tms/freight_invoices/invoice/' . $id));
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('freight_invoice'));
            $data['consignors'] = $this->consignors_model->get();
        } else {
            $data['invoice'] = $this->freight_invoices_model->get($id);
            $title = _l('edit', _l('freight_invoice'));
            $data['consignors'] = $this->consignors_model->get();
            $data['freight_invoice'] = $this->freight_invoices_model->get_all_details($id);
        }

        $data['title'] = $title;
        $data['trips'] = $this->trips_model->get_all_trips();
        $data['next_invoice_number'] = $this->freight_invoices_model->get_next_number();
        $this->load->view('freight_invoices/invoice', $data);
    }

    /**
     * Delete freight invoice
     */
    public function delete($id)
    {
        if (!has_permission('tms_freight_invoices', '', 'delete')) {
            access_denied('tms_freight_invoices');
        }

        $success = $this->freight_invoices_model->delete($id);
        if ($success) {
            set_alert('success', _l('deleted', _l('freight_invoice')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('freight_invoice')));
        }
        
        redirect(admin_url('tms/freight_invoices'));
    }

    /**
     * Generate PDF invoice
     */
    public function pdf($id = null)
    {
        if (!isset($id) || !$id) {
            redirect(admin_url('tms/freight_invoices'));
        }

        $invoice = $this->freight_invoices_model->get_all_details($id);
        
        try {
            $pdf = freight_invoice_pdf($invoice);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_error('Image failed to load. Please check that all images have valid path.');
            }
            die;
        }

        $type = 'D';
        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output('Freight_Invoice_' . $id . '.pdf', $type);
    }

    /**
     * Get freight invoices table data
     */
    public function table()
    {
        if (!has_permission('tms', '', 'view')) {
            ajax_access_denied();
        }

        $this->app->get_table_data(module_views_path('tms', 'tables/freight_invoices'));
    }

    /**
     * Check if invoice number is unique
     * @return json
     */
    public function check_invoice_number()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        if ($this->input->post()) {
            $invoice_number = $this->input->post('invoice_number');
            
            // Check if invoice number exists
            $exists = $this->freight_invoices_model->check_invoice_number_exists($invoice_number);
            
            echo json_encode([
                'unique' => !$exists,
                'message' => $exists ? _l('invoice_number_already_exists') : ''
            ]);
        }
    }
} 