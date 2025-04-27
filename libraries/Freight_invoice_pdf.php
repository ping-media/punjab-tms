<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Freight_invoice_pdf extends App_pdf
{
    protected $freight_invoice;

    private $freight_invoice_number;

    public function __construct($freight_invoice, $tag = '')
    {
        // $this->load_language($trip->clientid);
        $freight_invoice                = hooks()->apply_filters('freight_invoice_html_pdf_data', $freight_invoice);
        $GLOBALS['freight_invoice_pdf'] = $freight_invoice;

        parent::__construct();

        if (!class_exists('Trips_model', false)) {
            $this->ci->load->model('trips_model');
        }

        $this->tag            = $tag;
        $this->freight_invoice           = $freight_invoice;
        $this->freight_invoice_number    = format_freight_invoice_number($this->freight_invoice->id);
        $this->formatArray = $this->get_format_array();

        $this->SetTitle($this->freight_invoice_number);
    }

    public function prepare()
    {
        // $this->with_number_to_word($this->trip->clientid);

        $this->set_view_vars([
            'status'      => $this->freight_invoice->status,
            'freight_invoice_number' => $this->freight_invoice_number,
            'freight_invoice'        => $this->freight_invoice,
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'freight_invoice';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_trippdf.php';
        $actualPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/freightinvoicepdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }

    private function get_payment_modes()
    {
        $this->ci->load->model('payment_modes_model');
        $payment_modes = $this->ci->payment_modes_model->get();

        // In case user want to include {trip_number} or {client_id} in PDF offline mode description
        foreach ($payment_modes as $key => $mode) {
            if (isset($mode['description'])) {
                $payment_modes[$key]['description'] = str_replace('{trip_number}', $this->trip_number, $mode['description']);
                $payment_modes[$key]['description'] = str_replace('{client_id}', $this->trip->clientid, $mode['description']);
            }
        }

        return $payment_modes;
    }
}
