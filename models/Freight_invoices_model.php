<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Freight_invoices_model extends App_Model
{
    private $table = 'tms_freight_invoices';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get freight invoice by ID
     */
    public function get($id = '')
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get($this->table)->row();
        }
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Get freight invoice all details by ID
     * @param  integer $id freight invoice id
     * @return object
     */
    public function get_all_details($id = '')
    {
        if(empty($id)){
            return [];
        }

        if ($id) {
            $this->db->select('tms_freight_invoices.*, tms_consignors.company as consignor_name, tms_consignors.address as consignor_address, tms_consignors.city as consignor_city, tms_consignors.state as consignor_state, tms_consignors.country as consignor_country, tms_consignors.postal_code as consignor_postal_code, tms_consignors.gst as consignor_gst');
            $this->db->join(db_prefix() . 'tms_consignors', db_prefix() . 'tms_consignors.id = ' . db_prefix() . 'tms_freight_invoices.consignor_id', 'left');
            $this->db->where('tms_freight_invoices.id', $id);
            // return $this->db->get($this->table)->row();
            $invoice_data = $this->db->get($this->table)->row();
            
            if ($invoice_data && !empty($invoice_data->trips)) {
                // Convert trip_ids string to array if it's stored as comma-separated string
                $trip_ids = unserialize($invoice_data->trips);
                $invoice_data->trips = $this->get_trips($trip_ids);
            } else {
                $invoice_data->trips = [];
            }
            
            return $invoice_data;
        }
        return $this->db->get($this->table)->result_array();
    }


    public function get_trips($ids = '')
    {
        if(empty($ids)){
            return [];
        }

        $this->db->select('*');
        $this->db->from(db_prefix() . 'tms_trips');
        $this->db->where_in('id', $ids);
        return $this->db->get()->result_array();
    }

    /**
     * Add new freight invoice
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        if(isset($data['trips']) && !empty($data['trips'])){
            $trips_array = array_map('intval', explode(',', $data['trips']));
            $data['trips'] = serialize($trips_array);
        }else{
            unset($data['trips']);
        }
        
        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();
        
        if ($insert_id) {
            log_activity('New Freight Invoice Created [ID: ' . $insert_id . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Update freight invoice
     */
    public function update($data, $id)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        // $data['updated_by'] = get_staff_user_id();

        if(isset($data['trips']) && !empty($data['trips'])){
            $trips_array = array_map('intval', explode(',', $data['trips']));
            $data['trips'] = serialize($trips_array);
        }else{
            unset($data['trips']);
        }

        $this->db->where('id', $id);
        $this->db->update($this->table, $data);
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Freight Invoice Updated [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Delete freight invoice
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);
        
        if ($this->db->affected_rows() > 0) {
            log_activity('Freight Invoice Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * Get freight invoices with filtering and pagination
     */
    public function get_invoices($filters = [])
    {
        $this->db->select('*');
        $this->db->from($this->table);
        
        if (isset($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (isset($filters['from_date']) && isset($filters['to_date'])) {
            $this->db->where('created_at >=', $filters['from_date']);
            $this->db->where('created_at <=', $filters['to_date']);
        }
        
        if (isset($filters['search'])) {
            $this->db->like('invoice_number', $filters['search']);
            $this->db->or_like('trip_reference', $filters['search']);
        }
        
        return $this->db->get()->result_array();
    }

    /**
     * Get next Invoice number
     * @return string
     */
    public function get_next_number()
    {
        $this->db->select('invoice_number');
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $last_trip = $this->db->get(db_prefix() . 'tms_freight_invoices')->row();

        if ($last_trip) {
            // Extract number from INV-XXXX format
            $number = intval(str_replace('INV-', '', $last_trip->invoice_number));
            $next_number = $number + 1;
        } else {
            $next_number = 1;
        }

        return str_pad($next_number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Check if invoice number already exists
     * @param  string  $invoice_number Invoice number to check
     * @return boolean
     */
    public function check_invoice_number_exists($invoice_number, $freight_invoice_id = null)
    {
        $this->db->where('invoice_number', $invoice_number);
        if ($freight_invoice_id) {
            $this->db->where('id !=', $freight_invoice_id);
        }
        
        return $this->db->get(db_prefix() . 'tms_freight_invoices')->num_rows() > 0;
    }
} 