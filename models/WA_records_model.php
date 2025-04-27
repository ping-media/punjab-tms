<?php

defined('BASEPATH') or exit('No direct script access allowed');

class WA_records_model extends App_Model
{
    private $db_fields = [

        'vehicle_no',
        'vehicle_owner',
        'driver_phone',
        'trip_id',
        'gr_number',
        'in_datetime',
        'out_datetime',
        'onplace_status',
        'whatsapp_pod',
        'received_pod',
        'paid_pod',
        'created_at',
        'updated_at'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get party by ID
     * @param  mixed $id party id
     * @return mixed     object or false if not found
     */
    public function get($id = '')
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->get(db_prefix() . 'tms_wa_records')->row();
        }
        return $this->db->get(db_prefix() . 'tms_wa_records')->result_array();
    }

    /**
     * Add new wa record
     * @param array $data wa record data
     */
    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');

        // Filter out any data not in $db_fields
        $data = array_intersect_key($data, array_flip($this->db_fields));

        // print_r($data);
        // die();

        $this->db->insert(db_prefix() . 'tms_wa_records', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New WA Record Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update wa record
     * @param  array $data wa record data
     * @param  mixed $id   wa record id
     * @return boolean
     */
    public function update($data, $id)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $data['status'] = isset($data['status']) ? 1 : 0;

        // Filter out any data not in $db_fields
        $data = array_intersect_key($data, array_flip($this->db_fields));

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_wa_records', $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('WA Record Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete wa record
     * @param  mixed $id wa record id
     * @return boolean
     */
    public function delete($id)
    {
        // Check if consignor has any related trips
        $this->db->where('consignor_id', $id);
        $trips = $this->db->get(db_prefix() . 'tms_trips')->result_array();

        if (count($trips) > 0) {
            return [
                'success' => false,
                'message' => _l('consignor_has_trips')
            ];
        }

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'tms_wa_records');

        if ($this->db->affected_rows() > 0) {
            log_activity('Consignor Deleted [ID: ' . $id . ']');
            return [
                'success' => true,
                'message' => _l('deleted_successfully', _l('consignor'))
            ];
        }

        return [
            'success' => false,
            'message' => _l('problem_deleting', _l('consignor'))
        ];
    }

    /**
     * Get consignors for dropdown
     * @return array
     */
    public function get_consignors_for_dropdown()
    {
        $this->db->select('id, company');
        $this->db->where('status', 1);
        $consignors = $this->db->get(db_prefix() . 'tms_consignors')->result_array();
        
        $dropdown = [];
        foreach ($consignors as $consignor) {
            $dropdown[$consignor['id']] = $consignor['company'];
        }

        return $dropdown;
    }

    /**
     * Change consignor status
     * @param  mixed $id     consignor id
     * @param  mixed $status status
     * @return boolean
     */
    public function change_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'tms_consignors', ['status' => $status]);
        
        return $this->db->affected_rows() > 0;
    }


} 