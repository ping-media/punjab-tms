<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/pdf/App_pdf.php');

class Trip_pdf extends App_pdf
{
    protected $trip;

    private $trip_number;

    public function __construct($trip, $tag = '', $copy_type = 'consignee')
    {
        // $this->load_language($trip->clientid);
        $trip                = hooks()->apply_filters('trip_html_pdf_data', $trip);
        $GLOBALS['trip_pdf'] = $trip;

        parent::__construct();

        if (!class_exists('Trips_model', false)) {
            $this->ci->load->model('trips_model');
        }

        $this->tag            = $tag;
        $this->trip           = $trip;
        $this->trip_number    = format_trip_number($this->trip->id);
        $this->copy_type      = $copy_type;

        $this->SetTitle($this->trip_number);
    }

    public function prepare()
    {
        // $this->with_number_to_word($this->trip->clientid);

        $this->set_view_vars([
            'status'      => $this->trip->status,
            'trip_number' => $this->trip_number,
            'trip'        => $this->trip,
            'copy_type'   => $this->copy_type,
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'trip';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_trippdf.php';
        $actualPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/trippdf.php';

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
